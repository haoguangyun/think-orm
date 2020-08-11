<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Copyright easyswoole/pool
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-11 16:49
 ** ----------------------------------------------------------------------
 **/

namespace think\pool;


class DbPoolConfig
{
    protected $intervalCheckTime = 15*1000;
    protected $maxIdleTime = 10;
    protected $maxObjectNum = 20;
    protected $minObjectNum = 5;
    protected $getObjectTimeout = 3.0;

    protected $extraConf;

    /**
     * @return float|int
     */
    public function getIntervalCheckTime()
    {
        return $this->intervalCheckTime;
    }

    /**
     * @param $intervalCheckTime
     * @return Config
     */
    public function setIntervalCheckTime($intervalCheckTime): DbPoolConfig
    {
        $this->intervalCheckTime = $intervalCheckTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxIdleTime(): int
    {
        return $this->maxIdleTime;
    }

    /**
     * @param int $maxIdleTime
     * @return Config
     */
    public function setMaxIdleTime(int $maxIdleTime): DbPoolConfig
    {
        $this->maxIdleTime = $maxIdleTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxObjectNum(): int
    {
        return $this->maxObjectNum;
    }

    public function setMaxObjectNum(int $maxObjectNum): DbPoolConfig
    {
        if($this->minObjectNum >= $maxObjectNum){
            throw new \Exception('min num is bigger than max');
        }
        $this->maxObjectNum = $maxObjectNum;
        return $this;
    }

    /**
     * @return float
     */
    public function getGetObjectTimeout(): float
    {
        return $this->getObjectTimeout;
    }

    /**
     * @param float $getObjectTimeout
     * @return Config
     */
    public function setGetObjectTimeout(float $getObjectTimeout): DbPoolConfig
    {
        $this->getObjectTimeout = $getObjectTimeout;
        return $this;
    }

    public function getExtraConf()
    {
        return $this->extraConf;
    }

    /**
     * @param $extraConf
     * @return Config
     */
    public function setExtraConf($extraConf): DbPoolConfig
    {
        $this->extraConf = $extraConf;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinObjectNum(): int
    {
        return $this->minObjectNum;
    }

    public function setMinObjectNum(int $minObjectNum): DbPoolConfig
    {
        if($minObjectNum >= $this->maxObjectNum){
            throw new \Exception('min num is bigger than max');
        }
        $this->minObjectNum = $minObjectNum;
        return $this;
    }
}