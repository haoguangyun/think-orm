<?php
/**
 ** RAYSWOOLE [ HIGH PERFORMANCE CMS BASED ON SWOOLE ]
 ** ----------------------------------------------------------------------
 ** Idea From think/facade
 ** ----------------------------------------------------------------------
 ** Author: haoguangyun <admin@haoguangyun.com>
 ** ----------------------------------------------------------------------
 ** Last-Modified: 2020-08-11 16:49
 ** ----------------------------------------------------------------------
 **/

namespace rayswoole;

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

