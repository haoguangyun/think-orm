<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Copyright © 2020 http://haoguangyun.com All rights reserved.
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-27 16:28
 ** ----------------------------------------------------------------------
 **/

namespace rayswoole\orm\facade;

use Swoole\Coroutine;

class Singleton
{
    private static $instance = [];
    private $container = array();
    private $connect = [];
    private $dbLog = [];
    private $event = [];
    private $queryTimes = 0;

    private $maker = [];
    private $macro = [];

    /**
     * @param bool $isImport 是否导入父协程的上下文
     * @return self
     */
    static function getInstance()
    {
        $cid = Coroutine::getCid();
        if(!isset(static::$instance[$cid])){
            self::$instance[$cid] = new static();
            if($cid > 0){
                Coroutine::defer(function ()use($cid){
                    unset(self::$instance[$cid]);
                });
            }
        }
        return self::$instance[$cid];
    }

    public function connect($name = 'default', $connect = null)
    {
        if (!is_null($connect)){
            $this->connect[$name] = $connect;
        }
        return $this->connect[$name] ?? null;
    }
    
    public function reConnect($name = 'default'):void
    {
        if ($name !== ''){
            $this->connect[$name] = null;
        } else {
            foreach ($this->connect as $k=>$connect){
                $this->connect[$k] = null;
            }
            $this->connect = [];
        }
    }

    public function setDbLog($key, $value):void
    {
        $this->dbLog[$key][] = $value;
    }

    public function getDbLog():array
    {
        return $this->dbLog;
    }

    public function clearDbLog():void
    {
        $this->dbLog = [];
    }

    public function setEvent($key, $value):void
    {
        $this->event[$key][] = $value;
    }

    public function getEvent():array
    {
        return $this->event;
    }

    public function clearEvent():void
    {
        $this->event = [];
    }

    public function getQueryTimes()
    {
        return $this->queryTimes;
    }

    public function updateQueryTimes()
    {
        $this->queryTimes++;
    }

    public function clearQueryTimes()
    {
        $this->queryTimes = 0;
    }

    public function setMaker($value):void
    {
        $this->maker[] = $value;
    }

    public function getMaker():array
    {
        return $this->maker;
    }

    public function clearMaker():void
    {
        $this->maker = [];
    }

    public function setMacro($class, $method, $value):void
    {
        $this->macro[$class][$method] = $value;
    }

    public function getMacro($class, $method = '')
    {
        return isset($this->macro[$class]) ? ($this->macro[$class][$method] ?? null) : null;
    }

    public function clearMacro():void
    {
        $this->macro = [];
    }

    public function get($key)
    {
        return $this->container[$key] ?? null;
    }

    public function set($key, $value){
        $this->container[$key] = $value;
    }

    function delete($key):void
    {
        unset( $this->container[$key]);
    }

    function clear():void
    {
        $this->container = array();
    }
}