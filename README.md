apiEasyswoole
===============

> 基于easyswoole框架对api接口开发中造的一些常用的轮子，记录一下，不断完善中！

## 主要特性

* 配置用Yaconf扩展管理配置项
* 验证用think-validate组件来构建验证层
* 使用自定义路由做api的版本管理
* 写一个轮子实现JWT的token授权验证
* 写了http请求同步请求工具类
* 按照协程HTTPClient组件写了http异步请求的例子
* 按照easyswoole文档构建mysql协程连接池
* 使用easyswoole/mysqli组件写好数据模型层
* 按照easyswoole文档构建redis协程连接池
* 按照文档统一封装异步task任务处理层


## 安装
 
首先，要安装easyswoole框架，因为是基于它造的轮子，以便api开发接口更好用。
~~~
easyswoole官方安装文档：https://www.easyswoole.com/Cn/Introduction/install.html
~~~

其次, 安装项目所需的组件
~~~
composer require easyswoole/swoole-ide-helper
composer require easyswoole/mysqli
composer require easyswoole/http-client
composer require topthink/think-validate
~~~

然后，安装php的高性能配置管理扩展Yaconf
~~~
#编译安装（和swoole一样都是PHP的扩展）
/path/to/php7/bin/phpize
./configure --with-php-config=/path/to/php7/bin/php-config
make && make install

#配置PHP.ini：
vi /usr/local/php/etc/php.ini
[Yaconf]
extension=yaconf.so #扩展引用
yaconf.directory=/home/web/conf #conf文件所在目录
yaconf.check_delay=100 #心跳检查时间，若为0则不检查，但如果有修改，需重启PHP
~~~

## 目录说明
~~~
Test                   项目部署目录
├─App                     应用目录
│  ├─HttpController       控制器目录(需要自己创建)
│  │  ├─Test              Test模块
│  │  │  ├─V1             版本
│  ├─Logic                逻辑层，复杂的数据库操作写到这一层
│  ├─Model                数据库模型层
│  ├─Service              服务层，复杂的业务处理写到这一层
│  ├─Task                 异步Task任务处理层
│  ├─Utility              工具层
│  │  ├─Pool              协程连接池（mysql和redis）
│  ├─Validate             验证层
├─ini                     yaconf配置目录(为了安全尽量写在项目之外的目录)
├─Log                     日志文件目录（启动后创建）
├─Temp                    临时文件目录（启动后创建）
├─vendor                  第三方类库目录
├─composer.json           Composer架构
├─composer.lock           Composer锁定
├─EasySwooleEvent.php     框架全局事件
├─easyswoole              框架管理脚本
├─easyswoole.install      框架安装锁定文件
├─dev.php                 开发配置文件
├─produce.php             生产配置文件
~~~

