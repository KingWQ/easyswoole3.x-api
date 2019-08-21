<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/21
 * Time: 18:52
 */
namespace App\HttpController\Test;


use App\Model\MsgLangModel;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Utility\Pool\MysqlPool;

class MysqlPoolTest extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function demo()
    {
        $data = [
            ['pid'=>1,'lang'=>'zh','title'=>'测试','ticker'=>'测试','text'=>'测试'],
            ['pid'=>1,'lang'=>'en','title'=>'test','ticker'=>'test','text'=>'test'],
        ];

        $db = MysqlPool::defer();
        $model = new MsgLangModel($db);
        $res = $model->addMulti($data);
        var_dump($res);
    }
}