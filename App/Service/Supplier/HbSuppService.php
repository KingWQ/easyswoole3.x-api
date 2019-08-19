<?php
/**
 * @Note Hb供应商服务类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/30
 * Time: 10:38
 */
namespace App\Service\Supplier;

use App\Utility\Curl;

class HbSuppService
{
    public static function placeOrder($serName,$params)
    {
        try{
            switch ($serName) {
                case 'hotel':
                    $res = self::orderHotel($params);
                    break;
                default:
                    throw new \Exception('该供应商下的服务不存在');
                    break;
            }

            if($res['status'] == 0) return ['status'=>0, 'msg'=>$res['msg'], 'data'=>$res['data']];

            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data']];

        }catch (\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }

    private static function orderHotel($params)
    {
        try{
            $url = \Yaconf::get('supplier.base_url').\Yaconf::get('supplier.Hb.order_hotel');
            $res = Curl::post($url, $params);
            $resArr = json_decode($res, true);

            if(empty($resArr) || !isset($resArr['code'])) return ['status'=>0, 'msg'=>$res['msg'], 'data'=>$resArr];
            if($resArr['code'] != 2000) return ['status'=>0, 'msg'=>$resArr['msg'], 'data'=>$resArr['data']];

            return ['status'=>1, 'msg'=>'ok', 'data'=>$resArr['data']];
        }catch (\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }
}