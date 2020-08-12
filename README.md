# RaySwooleORM
> fork in ThinkORM

基于PHP7.1+ 和PDO实现的ORM，支持多数据库，2.0版本主要特性包括：

* 基于PDO和PHP强类型实现
* 支持原生查询和查询构造器
* 自动参数绑定和预查询
* 简洁易用的查询功能
* 强大灵活的模型用法
* 支持预载入关联查询和延迟关联查询
* 支持多数据库及动态切换
* 支持`MongoDb`
* 支持分布式及事务
* 支持断点重连
* 支持`JSON`查询
* 支持数据库日志
* 支持`PSR-16`缓存及`PSR-3`日志规范


## 安装
~~~
composer require rayswoole/think-orm
~~~

## 文档

### 连接池配置
> 可以在onStart直接配置

```php
//初始化连接配置
$poolConfig = new \rayswoole\pool\DbPoolConfig();
//设置最小连接数
$poolConfig->withMin(5);
//设置最大连接数
$poolConfig->withMax(20);
//设置定时器执行频率(毫秒),创建最小进程、回收空闲进程
$poolConfig->withIntervalTime(15*1000)
//设置连接可空闲时间
$poolConfig->withIdleTime(10)
//获取连接池对象超时时间, 如果连接池占满在指定时间无法释放新的连接, 将输出Exception, 需要自行捕获
$poolConfig->withTimeout(3.0)
//额外配置, 自行实现更多功能时使用
$poolConfig->withExtraConf(null)
//将连接池配置注入到DbPool
\rayswoole\pool\DbPool::setPoolConfig($poolConfig);
```

### 数据库配置
> 推荐在连接池配置完毕后继续注入数据库配置
```php
//$mysqlConfig的组装参考https://www.kancloud.cn/manual/think-orm/1257999
\rayswoole\Db::init($mysqlConfig);

//或者使用think-orm自带的facade
\rayswoole\facade\Db::init($mysqlConfig);
```

### 使用
```php
use rayswoole //或者 use rayswoole\facade
// table方法必须指定完整的数据表名
Db::table('think_user')->where('id', 1)->find();
// 如果设置了数据表前缀（prefix）参数的话 也可以使用
Db::name('user')->where('id', 1)->find();
// 模型写法
$model = new userModel();
$model->where('id', 1)->find();
```
具体语法详细参考 [ThinkORM开发指南](https://www.kancloud.cn/manual/think-orm/content)

