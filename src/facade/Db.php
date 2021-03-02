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

use rayswoole\orm\DbManager;
use rayswoole\orm\pool\DbPool;
use rayswoole\orm\pool\DbPoolConfig;

/**
 * class Db
 * @package rayswoole
 * @mixin \rayswoole\orm\DbManager
 * @mixin \rayswoole\orm\db\BaseQuery
 * @mixin \rayswoole\orm\db\Query
 */
class Db {
    /**
     * 始终创建新的对象实例
     * @var DbManager
     */
    public static $dbManager;

    public static function init(DbPoolConfig $config = null)
    {
        if (!self::$dbManager && is_object($config)) {
            DbPool::setPoolConfig($config);
            self::$dbManager = new DbManager();
            $conf = $config->getExtraConf();
            self::$dbManager->setConfig($conf);
        }
        return self::$dbManager;
    }

    /**
     * @return DbManager
     */
    static function getInstance()
    {
        return self::$dbManager;
    }

    /**
     * @param $method
     * @param $params
     * @return DbManager
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([self::$dbManager, $method], $params);
    }
}

