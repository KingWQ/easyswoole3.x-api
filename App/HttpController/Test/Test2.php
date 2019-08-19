<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/30
 * Time: 18:11
 */
namespace App\HttpController\Test;

use App\Model\MsgLangModel;
use App\Service\FlowService;
use App\Task\ServiceTask;
use App\Utility\Pool\MysqlPool;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Logic\HotelLogic;

class Test2 extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        echo \Yaconf::get("msg.hotel_order_ok.ticker_zh")."\n";
        echo sprintf(\Yaconf::get("msg.hotel_order_ok.text_zh"), date('Y-m-d H:i:s'),'科尔海悦酒店');
        $res = FlowService::getPackByType(2);
        $this->response()->write(json_encode($res));
    }

    public function newUser()
    {
        $params = ['user_id'=>371,'order_sn'=>'HOT201908081513699696'];
        $taskClass = new ServiceTask(['type'=>'new_user', 'action'=>'hotel_order','data'=>$params]);
        $res = TaskManager::async($taskClass);
        $this->response()->write(json_encode($res));
    }

    public function giveFlow()
    {
        $params = [
            'order_sn'=>'HOT201908061524753067',
            'user_id'=>371,
            'dur'=>1,
            'country_code'=>'CN'
        ];
        $taskClass = new ServiceTask(['type'=>'give_flow', 'action'=>'hotel_order','data'=>$params]);
        $res = TaskManager::async($taskClass);
        $this->response()->write(json_encode($res));
    }

    public function appMsg()
    {
        $params = [
            'status'=>1,
            'hotel_name_en'=>'test hotel',
            'hotel_name_zh'=>'测试酒店',
            'order_sn'=>'HOT201908081513699696',
            'user_id'=>371,
            'lang'=>'en',
            'start_time'=>1565280000
        ];
        $taskClass = new ServiceTask(['type'=>'app_msg', 'action'=>'hotel_order','data'=>$params]);
        TaskManager::async($taskClass);
    }

    public function email()
    {
        $params = [
            'status'=>1,
            'order_sn'=>'HOT201908061524753067',
            'user_id'=>371,
            'lang'=>'zh-cn',
            'email'=>'2971749820@qq.com'
        ];

        $taskClass = new ServiceTask(['type'=>'email', 'action'=>'hotel_order','data'=>$params]);
        TaskManager::async($taskClass);
        echo "start task email\n";
    }

    public function orderOk()
    {
        $params['order_id'] = 1501;
        $params['hotel_code'] = '180660';
        $params['locator'] = 'test';
        $params['reference_source'] = 'test-test';
        $params['cnacel_json'] = 'a bold attempt is half success';

        $res = HotelLogic::orderOk($params);
        return $this->writeJson(200, $res['data'], $res['msg']);
    }

    public function orderFail()
    {
        $params['order_id'] = 1;
        $params['order_sn'] = 'HOT201812191107421809';
        $params['user_id'] = 28;
        $params['currency'] = 'USD';
        $params['pay_style'] = 1;
        $params['refund_amount'] = 4.28;

        $res = HotelLogic::orderFail($params);
        return $this->writeJson(200, $res['data'], $res['msg']);
    }

    public function countryFlow()
    {
        $res = FlowService::isHaveCountry('HK');
        $this->response()->write(json_encode($res));
    }

    public function addMulti()
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