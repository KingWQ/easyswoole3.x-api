<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/21
 * Time: 18:52
 */
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Utility\Pool\RedisPool;

class RedisPoolTest extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function demo()
    {
        $redis = RedisPool::defer();
        $redis->set('key','swoole');
        $data = $redis->get('key');
        $this->response()->write($data);
    }
}