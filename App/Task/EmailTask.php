<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/2
 * Time: 15:41
 */
namespace App\Task;

use App\Utility\Curl;

class EmailTask
{
    /**
     * @param $taskData
     * @param array['type'=>'email', 'action'=>'hotel_order', 'data'=>['status'=>1, 'order_sn'=>'Hot123456', 'user_id'=>12,'lang'=>'zh-cn', 'email'=>'29717498@qq.com']]
     * @param data: status,order_sn,user_id,lang,email
     */
    public static function exec($taskData): array
    {
        try{
            switch ($taskData['action']){
                case 'hotel_order':$res = self::hotelOrder($taskData['data']);break;
                default: throw new \Exception('emailTask的action不合法'.json_encode($taskData));break;
            }
            if($res['status'] != 1) throw new \Exception($res['msg'].'###'.json_encode($res['data']));

            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data'], 'param'=>$taskData];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[], 'param'=>$taskData];
        }
    }

    private static function hotelOrder($params): array
    {
            $url = ($params['status']==1) ? (\Yaconf::get('service.email.order_hotel_ok')) : (\Yaconf::get('service.email.order_hotel_fail'));
            $postData = [
                'order_sn'  => $params['order_sn'],
                'user_id'   => $params['user_id'],
                'lang'      => $params['lang'],
                'email'     => $params['email']
            ];

            $res = Curl::post($url, $postData);
            $res = json_decode($res, true);

            if($res['code'] != 2000) return ['status'=>0, 'msg'=>$res['msg'],  'data'=>$res['data']];
            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data']];
    }
}