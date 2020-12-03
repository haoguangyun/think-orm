<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Idea From easyswoole/pool
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-11 16:49
 ** ----------------------------------------------------------------------
 **/


namespace rayswoole\orm\pool;

use Swoole\Coroutine;
use Swoole\Coroutine\Channel;
use Swoole\Timer;

class DbPoolManager
{
    private static $counter = 0;
    private $createdNum = 0;
    /** @var Channel */
    private $poolChannel;
    /** @var DbPoolConfig */
    private $conf;
    private $timerId;
    private $destroy = false;
    private $context = [];
    private $dbConfig = [];

    /**
     * 并发锁定, 防止高并发导致导致的抢占
     * @var bool
     */
    private $createIng = false;

    public function __construct(DbPoolConfig $conf)
    {
        $this->conf = $conf;
    }

    /**
     * 创建\Pdo对象
     * @return \Pdo|\PdoCluster
     * @throws \Exception
     */
    public function getObj()
    {
        $dbConfig = $this->dbConfig;
        try {
            return new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'], $dbConfig['params']);
        } catch (\PDOException $e){
            return null;
        }
    }

    /**
     * 生产新的连接池对象
     * @return bool
     * @throws \Throwable
     */
    private function create(int $tryTimes = 10): bool
    {
        if ($this->destroy || $this->createdNum > $this->conf->getMax()) {
            return false;
        }
        $this->createdNum++;
        $obj = $this->getObj();
        if (is_object($obj)) {
            static::$counter++;
            $obj->_free = true;
            $obj->_lastTime = time();
            if($this->poolChannel->push($obj)){
                return true;
            } else {
                $obj = null;
                unset($obj);
            }
        }
        $this->createdNum--;
        return false;
    }

    /**
     * 消费连接池对象
     * @param int $tryTimes 尝试次数
     * @return \Pdo|null
     * @throws \Throwable
     */
    private function pop(float $timeOut = -1, int $tryTimes = 3)
    {
        if ($this->destroy) {
            return null;
        }
        $this->init();
        $obj = $this->poolChannel->pop($timeOut);
        if (is_object($obj)) {
            $obj->_free = false;
            return $obj;
        } else {
            if ($tryTimes > 0 && $this->createdNum < $this->conf->getMax()) {
                $this->create();
                return $this->pop($timeOut, --$tryTimes);
            }
            return null;
        }
    }

    /**
     * 将消费过的连接池对象入栈到连接池
     * @param $obj \PDO
     * @return bool
     * @throws \Exception
     */
    private function push($obj): bool
    {
        //当标记为销毁后，直接销毁连接
        if ($this->destroy) {
            $this->unset($obj);
            return false;
        }
        //如果不是正在使用的连接则直接跳过
        if (!isset($obj->_free) || $obj->_free === true){
            return true;
        }
        $obj->_lastTime = time();
        $obj->_free = true;
        if($this->poolChannel->push($obj)){
            return true;
        }else{
            $this->unset($obj);
            return false;
        }
    }

    /**
     * 删除连接对象
     * @param $obj \Pdo
     * @return bool
     */
    private function unset(&$obj): bool
    {
        $obj = null;
        $this->createdNum--;
        return true;
    }

    /**
     * 检测空间连接并回收
     * @param int $idleTime
     * @return bool 是否执行了回收
     * @throws \Exception
     */
    private function checkFree():void
    {
        //进程池未初始化、进程池为空、进程池满 时均不处理
        if (!isset($this->poolChannel) || $this->poolChannel->isEmpty() || $this->poolChannel->isFull()){
            return ;
        }
        $idleTime = $this->conf->getIdleTime();
        $size = $this->poolChannel->length();
        $time = time();
        //先将所有空闲超过idleTime的全部回收
        while ($size > 0){
            $size--;
            if(!$obj = $this->poolChannel->pop(0.01)){
                continue;
            }
            if ($time - $obj->_lastTime > $idleTime || !$this->checkPing($obj)) {
                $this->unset($obj);
            } else {
                $this->poolChannel->push($obj);
            }
        }
    }

    /**
     * 心跳检查
     * @param $obj \Pdo
     * @return bool
     */
    protected function checkPing($obj):bool
    {
        if( time() - $obj->_lastTime > $this->conf->getPing() ){
            try{
                $obj->_lastTime = time();
                $result = $obj->query('select 1');
            }catch (\Exception | \PDOException $e){
                //防止错误输出
            }finally{
                if ($obj->errorCode() === '00000'){
                    $result->closeCursor();
                    return true;
                } else {
                    return false;
                }
            }
        }else{
            return true;
        }
    }

    /**
     * 保持进程池最少活跃连接量
     * @return int 是否执行了增加连接操作
     * @throws \Throwable
     */
    public function checkMin(): void
    {
        $this->init();
        $min = $this->conf->getMin();
        $free = $this->conf->getFree();
        //如果创建的obj数量少于最小连接
        if ($this->createdNum < $min){
            $left = $min - $this->createdNum;
            while ($left > 0) {
                $this->create();
                $left--;
            }
        }
        //当空闲数量小于要求时
        if ($this->createdNum < $this->conf->getMax()){
            $length = $this->poolChannel->length();
            if ($length < $free){
                $left = $free - $length;
                while ($left > 0) {
                    $this->create();
                    $left--;
                }
            }
        }
    }

    /**
     * 执行检测, 外部调用
     * @throws \Throwable
     */
    public function intervalCheck()
    {
        $this->checkFree();
        $this->checkMin();
    }


    /**
     * @param float|null $timeout
     * @return \Pdo
     * @throws \Throwable
     */
    public function defer(array $dbConfig, float $timeout = null)
    {
        $cid = Coroutine::getCid();
        if (isset($this->context[$cid])) {
            return $this->context[$cid];
        }
        $this->dbConfig = $dbConfig;
        if ($obj = $this->pop($this->conf->getTimeout())) {
            $this->context[$cid] = $obj;
            Coroutine::defer(function () use ($cid) {
                if (isset($this->context[$cid])) {
                    $this->push($this->context[$cid]);
                    unset($this->context[$cid]);
                }
            });
            return $obj;
        } else {
            throw new \Exception(static::class . " pool is empty");
        }
    }

    /**
     * 释放连接对象
     */
    public function unDefer()
    {
        $cid = Coroutine::getCid();
        if (isset($this->context[$cid])) {
            $this->unset($this->context[$cid]);
            unset($this->context[$cid]);
        }
    }

    /**
     * 销毁连接池
     * @throws \Exception
     */
    public function destroy()
    {
        $this->destroy = true;
        if ($this->timerId && Timer::exists($this->timerId)) {
            Timer::clear($this->timerId);
            $this->timerId = null;
        }
        if(isset($this->poolChannel)){
            //将所有连接对象全部消费并断开连接
            while (!$this->poolChannel->isEmpty()) {
                $obj = $this->poolChannel->pop(0.01);
                $this->unset($obj);
            }
            $this->poolChannel->close();
            $this->poolChannel = null;
        }
    }

    /**
     * 重置连接池
     * @return static
     * @throws \Exception
     */
    public function reset(): DbPoolManager
    {
        $this->destroy();
        $this->createdNum = 0;
        $this->destroy = false;
        $this->context = [];
        $this->init();
        return $this;
    }

    /**
     * 获取连接池状态
     * @return array
     * @throws \Exception
     */
    public function status()
    {
        $this->init();
        return [
            'created' => $this->createdNum,
            'used'   => $this->createdNum - (isset($this->poolChannel) ? $this->poolChannel->length() : 0),
            'max'     => $this->conf->getMax(),
            'min'     => $this->conf->getMin()
        ];
    }

    /**
     * 初始化连接池
     * @throws \Exception
     */
    public function init()
    {
        if (!isset($this->poolChannel) && (!$this->destroy)) {
            if ($this->conf->getMin() >= $this->conf->getMax()){
                throw new \Exception('min num is bigger than max');
            }
            if ($this->timerId && Timer::exists($this->timerId)) {
                Timer::clear($this->timerId);
                $this->timerId = null;
            }
            $this->createdNum = 0;
            $this->destroy = false;
            $this->context = [];
            $this->poolChannel = new Channel($this->conf->getMax() + 8);
            $this->checkMin();
            if ($this->conf->getIntervalTime() > 0) {
                $this->timerId = Timer::tick($this->conf->getIntervalTime(), [$this, 'intervalCheck']);
            }
        }
    }
}
