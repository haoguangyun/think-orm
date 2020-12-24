<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace rayswoole\orm\facade;

use rayswoole\Logger;
use rayswoole\orm\DbManager;
use rayswoole\orm\pool\DbPool;
use rayswoole\orm\pool\DbPoolConfig;

/**
 * class Db
 * @package rayswoole
 * @mixin \rayswoole\orm\DbManager
 * @mixin \rayswoole\orm\BaseQuery
 * @mixin \rayswoole\orm\Query
 */
class Db {
    /**
     * 始终创建新的对象实例
     * @var DbManager
     */
    public static $instance;

    public static function init(DbPoolConfig $config = null)
    {
        if (!self::$instance && is_object($config)) {
            DbPool::setPoolConfig($config);
            self::$instance = new DbManager();
            $conf = $config->getExtraConf();
            self::$instance->setConfig($conf);
            // 此两个类不存在
            //if ($conf['connections'][$conf['default']]['debug']) {
            //    self::$instance->setLog(\rayswoole\Logger::getInstance());
            //} else {
            //    self::$instance->setCache(\rayswoole\Cache::getInstance());
            //}
        }
        return self::$instance;
    }

    /**
     * @return DbManager
     */
    static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param $method
     * @param $params
     * @return DbManager
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::$instance, $method], $params);
    }
}

