<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;


class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        //mysql
        $mysqlConf = \Yaconf::get('mysql');
        $mysqlPool = PoolManager::getInstance()->register(MysqlPool::class, $mysqlConf['POOL_MAX_NUM']);
        if($mysqlPool === null){
            //当返回null时,代表注册失败,无法进行再次的配置修改
            //注册失败不一定要抛出异常,因为内部实现了自动注册,不需要注册也能使用
            throw new \Exception('注册失败!');
        }
        $mysqlPool->setMaxObjectNum(20)->setMinObjectNum(5);

        //redis
        $redisConf = \Yaconf::get('redis');
        PoolManager::getInstance()->register(RedisPool::class, $redisConf['POOL_MAX_NUM']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.

        //mysql 热启动
        $register->add($register::onWorkerStart, function (\swoole_server $server, int $workerId) {
            if ($server->taskworker == false) {
                //每个worker进程都预创建连接
                PoolManager::getInstance()->getPool(MysqlPool::class)->preLoad(5);//最小创建数量
            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

}