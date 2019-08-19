<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/7
 * Time: 10:47
 */
namespace App\Service;

use App\Utility\Curl;
use App\Utility\Pool\RedisPool;

class FlowService
{
    public static function getFlowByType($type=1)
    {
        try{
            $redis = RedisPool::defer();
            $cache = $redis->get('ebbly_country_flow');

            if(empty($cache)){
                $url = \Yaconf::get('flow.content_url')."?entrance={$type}";
                $res = Curl::get($url);
                $res = json_decode($res, true);
                if(empty($res) || $res['code'] != 200) {
                    throw new \Exception("获取流量与国家/地区的配制列表接口error, url:{$url}, result:".json_encode($res));
                }

                $data = [];
                foreach($res['data'] as $row){
                    $data[$row['country_code']] = $row;
                }
                if(empty($data)) throw new \Exception("获取流量与国家/地区的配制列表接口error, 数据为空：".json_encode($res));

                $redis = RedisPool::defer();
                $redis->setEx('ebbly_country_flow', 7*24*3600, json_encode($data));
            }else{
                $data = json_decode($cache, true);
            }

            return ['status'=>1, 'msg'=>'ok', 'data'=>$data];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }


    public static function getPackByType($type)
    {
        try{
            $url = \Yaconf::get('flow.content_url')."?entrance={$type}";
            $res = Curl::get($url);
            $res = json_decode($res, true);
            if(empty($res) || $res['code'] != 200) {
                throw new \Exception("获取流量与国家/地区的配制列表接口error, url:{$url}, result:".json_encode($res));
            }

            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data']];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }


    public static function isHaveCountry($countryCode)
    {
        try{
            $res = self::getFlowByType(1);
            if($res['status'] == 0) throw new \Exception($res['msg']);

            $row = $res['data'][$countryCode] ?? [];

            return ['status'=>1, 'msg'=>'ok', 'data'=>$row];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }


    public static function grant($entrance, $expiredTime, $userId, $day, $uid, $packageId, $orderSn)
    {
        try{
            $postData = [
                'entrance'=>$entrance,
                'expired_time'=>$expiredTime,
                'ebbly_user_id'=>$userId,
                'day'=>$day,
                'uid'=>$uid,
                'package_id'=>$packageId,
                'order_sn'=>$orderSn
            ];

            $url = \Yaconf::get('flow.grant_url');
            $res = Curl::post($url, $postData);
            $res = json_decode($res, true);

            if(empty($res) || $res['code'] != 200) {
                throw new \Exception("领取流量兑换券-单个接口error, url:{$url}, result:".json_encode($res));
            }

            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data']];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }
}