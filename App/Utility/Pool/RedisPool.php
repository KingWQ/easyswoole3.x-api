<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/19
 * Time: 14:51
 */
namespace App\Utility\Pool;

use EasySwoole\Component\Pool\AbstractPool;

class RedisPool extends AbstractPool
{
    protected function createObject()
    {
        // TODO: Implement createObject() method.
        $redis = new RedisObject();
        $conf = \Yaconf::get('redis');
        if( $redis->connect($conf['host'], $conf['port']) ){
            if(!empty($conf['auth'])){
                $redis->auth($conf['auth']);
            }
            return $redis;
        }else{
            return null;
        }
    }
}