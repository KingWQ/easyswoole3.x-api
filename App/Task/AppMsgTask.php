<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/2
 * Time: 15:42
 */
namespace App\Task;

use App\Model\AccountExtendModel;
use App\Model\MsgLangModel;
use App\Model\MsgModel;
use App\Service\MsgTempletService;
use App\Utility\Curl;
use App\Utility\Pool\MysqlPool;

class AppMsgTask
{
    /**
     * @note 处理任务
     * @param $taskData  array['type'=>'email', 'action'=>'hotel_order','data'=>[]]
     * @param  data: status,
     */
    public static function exec($taskData): array
    {
        try{
            //1：处理task数据
            $dealData = self::dealData($taskData['data'], $taskData['action']);

            //2：记得消息（中英文）到消息表
            $recordRes = self::recordMsg($dealData['msg_data'], $dealData['lang_data']);
            if($recordRes['status'] != 1) throw new \Exception($recordRes['msg']);

            //3：推送友盟消息
            $pushRes = self::pushMsg($dealData['push_data'], $taskData['data']);
            if($pushRes['status'] != 1) throw new \Exception($pushRes['msg']);

            return ['status'=>1, 'msg'=>'ok', 'data'=>$pushRes['data'], 'param'=>$taskData];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[], 'param'=>$taskData];
        }
    }

    /**
     * @note 处理task数据
     * @param array $params
     * @param int $orderType
     * @param string $msgType
     * @return array
     */
    private static function dealData(array $params, string $msgType):array
    {
        $userId     = $params['user_id'];
        $orderSn    = $params['order_sn'];
        $lang       = $params['lang'];
        $afterOpen  = 'go_custom';
        $key        = "custom{$userId}";
        $displayType= 'notification';


        //1：根据不同消息类型到不同的模板
        $tempRes = MsgTempletService::getData($msgType, $params);
        if($tempRes['status'] != 1) throw new \Exception($tempRes['msg']);
        $tempData = $tempRes['data'];

        //2：插入数据表的数据
        $msgData= ['user_id'=>$userId, 'order_sn'=>$orderSn, 'order_type'=>$tempData['order_type'], 'status'=>1,'add_time'=>time()];
        $langData=[
            ['lang'=>'zh-cn','title'=>$tempData['title_zh'],'ticker'=>$tempData['ticker_zh'], 'text'=>$tempData['text_zh']],
            ['lang'=>'en-us','title'=>$tempData['title_en'],'ticker'=>$tempData['ticker_en'], 'text'=>$tempData['text_en']],
        ];

        //3：友盟推送需要的数据
        $content = ($lang=='en') ? $langData[1] : $langData[0];
        $pushData=[
            'ticker'        => $content['ticker'],
            'title'         => $content['title'],
            'text'          => $content['text'],
            'after_open'    => $afterOpen,
            'key'           => $key,
            'order_sn'      => $orderSn,
            'order_type'    => $tempData['order_type'],
            'display_type'  => $displayType,
            'bind_mogos'    => $params['bind_mogos'] ?? '',
            'flow_name'     => $params['flow_name'] ?? '',
            'expried_time'  => $params['expried_time'] ?? '',
            'user_name'     => $params['user_name'] ?? '',
            'inviter'       => $params['inviter'] ?? '',
        ];


        return ['msg_data'=>$msgData, 'lang_data'=>$langData, 'push_data'=>$pushData];
    }

    /**
     * @note 记录到消息表
     * @param array $msgData
     * @param array $langData
     * @return array
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    private static function  recordMsg(array $msgData, array $langData): array
    {
        $db = MysqlPool::defer();
        $msgModel = new MsgModel($db);
        $langModel = new MsgLangModel($db);

        $db->startTransaction();
        try{
            $msgRes = $msgModel->add($msgData);
            if(!$msgRes) throw new \Exception("消息服务：消息记录到主表失败, {$msgRes}, sql：".$db->getLastQuery());
            $msgId = $db->getInsertId();


            $langData[0]['pid'] = $msgId;
            $langData[1]['pid'] = $msgId;
            $langRes = $langModel->addMulti($langData);
            if(!$langRes) throw new \Exception("消息服务：消息记录到语言表失败, {$langRes}, sql：".$db->getLastQuery());

            $db->commit();
            return ['status'=>1, 'msg'=>'ok','data'=>[]];
        }catch (\Exception $e){
            $db->rollback();
            return ['status'=>0, 'msg'=>$e->getMessage(),'data'=>[]];
        }

    }

    /**
     * @note 请求友盟推送消息
     * @param array $pushData
     * @param array $params
     * @return array
     */
    private static function pushMsg(array $pushData, array $params): array
    {
        try{
            $db = MysqlPool::defer();
            $model = new AccountExtendModel($db);
            $row = $model->getOne($params['user_id'], 'account_id,device_token');

            if(empty($row)) throw new \Exception("msgTask: user_id不存在");
            if(empty($row['device_token'])) throw new \Exception("msgTask: device_token不存在");

            $url = \Yaconf::get('service.app_msg.url');
            $pushData['device_token'] = $row['device_token'];
            $res = Curl::post($url, $pushData);
            var_dump($res);
            return ['status'=>1, 'msg'=>'ok', 'data'=>$res];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(),'data'=>[]];
        }
    }
}