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

namespace rayswoole\facade;

use rayswoole\DbManager;

/**
 * @see \rayswoole\DbManager
 * @mixin \rayswoole\DbManager
 */
class Db {
    /**
     * 始终创建新的对象实例
     * @var bool
     */
    public static $instance;

    public static function init(array $config = null)
    {
        if (!self::$instance) {
            self::$instance = new DbManager();
            self::$instance->setConfig($config);
        }
        return self::$instance;
    }

    /**
     * @param $method
     * @param $params
     * @return DbManager
     */
    public static function __callStatic($method, $params)
    {
        return call_user_func_array([static::init(), $method], $params);
    }
}

