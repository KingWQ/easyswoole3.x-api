<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/19
 * Time: 14:51
 */
namespace App\Utility\Pool;

use Co\Redis;
use EasySwoole\Component\Pool\PoolObjectInterface;

class RedisObject extends Redis implements PoolObjectInterface
{
    function gc()
    {
        // TODO: Implement gc() method.
        $this->close();
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function beforeUse(): bool
    {
        // TODO: Implement beforeUse() method.
        return true;
    }
}