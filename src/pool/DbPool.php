<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Copyright © 2020 http://haoguangyun.com All rights reserved.
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-11 16:49
 ** ----------------------------------------------------------------------
 **/

namespace rayswoole\orm\pool;

class DbPool
{
    private static $instance = [];
    private static $poolConfig;

    /**
     * 实例化数据库连接池, 支持分布式数据库
     * @param int $linkNum
     * @param DbPoolConfig|null $dbPoolConfig
     * @return DbPoolManager
     */
    static function getInstance($linkNum = 0)
    {
        if(!isset(self::$instance[$linkNum])){
            self::$instance[$linkNum] = new DbPoolManager(self::$poolConfig);
        }
        return self::$instance[$linkNum];
    }
    
    static function setPoolConfig(DbPoolConfig $PoolConfig)
    {
        self::$poolConfig = $PoolConfig;
    }
}