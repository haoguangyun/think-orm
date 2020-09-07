<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Idea From easyswoole/pool
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-12 10:00
 ** ----------------------------------------------------------------------
 **/

namespace rayswoole\orm\pool;


class DbPoolConfig
{
    protected $intervalTime = 15*1000;
    protected $idleTime = 10;
    protected $max = 20;
    protected $min = 5;
    protected $timeout = 3.0;
    protected $free = 5;
    protected $pingTime = 30;

    protected $extraConf;

    /**
     * 获取定时器设置
     * @return float|int
     */
    public function getIntervalTime()
    {
        return $this->intervalTime;
    }

    /**
     * 设置定时器
     * @param $IntervalTime
     * @return Config
     */
    public function withIntervalTime(int $intervalTime): DbPoolConfig
    {
        $this->intervalTime = $intervalTime;
        return $this;
    }

    /**
     * 获取连接最大闲置时间
     * @return int
     */
    public function getIdleTime(): int
    {
        return $this->idleTime;
    }

    /**
     * 设置连接最大闲置时间
     * @param int $idleTime
     * @return Config
     */
    public function withIdleTime(int $idleTime): DbPoolConfig
    {
        $this->idleTime = $idleTime;
        return $this;
    }

    /**
     * 获取连接池最大数量设置
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * 设置连接池最大数量
     * @param int $max
     * @return DbPoolConfig
     * @throws \Exception
     */
    public function withMax(int $max): DbPoolConfig
    {
        $this->max = $max;
        return $this;
    }

    /**
     * 获取连接池最少数量设置
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * 设置最少连接数量
     * @param int $min
     * @return DbPoolConfig
     * @throws \Exception
     */
    public function withMin(int $min): DbPoolConfig
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * @param float $timeout
     * @return Config
     */
    public function withTimeout(float $timeout): DbPoolConfig
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * 获取额外配置信息
     * @return mixed
     */
    public function getExtraConf()
    {
        return $this->extraConf;
    }

    /**
     * 设置额外配置信息
     * @param $extraConf
     * @return Config
     */
    public function withExtraConf($extraConf): DbPoolConfig
    {
        $this->extraConf = $extraConf;
        return $this;
    }

    /**
     * 获取连接池最少数量设置
     * @return int
     */
    public function getFree(): int
    {
        return $this->free;
    }

    /**
     * 设置最少连接数量
     * @param int $min
     * @return RedisConfig
     * @throws \Exception
     */
    public function withFree(int $free): DbPoolConfig
    {
        $this->free = $free;
        return $this;
    }

    /**
     * 获取额外配置信息
     * @return mixed
     */
    public function getPing()
    {
        return $this->pingTime;
    }

    /**
     * 设置额外配置信息
     * @param $extraConf
     * @return Config
     */
    public function withPing(int $time): DbPoolConfig
    {
        if ($time > 0){
            $this->pingTime = $time;
        }
        return $this;
    }
}