<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/9
 * Time: 16:59
 */
namespace App\Service;

class MsgTempletService
{
    public static function getData(String $type, Array $params):array
    {
        try{
            switch($type){
                case 'hotel_order': $res = self::hotelOrder($type, $params);break;
                default: throw new \Exception("msgTask data的action不合法 action:{$type}");break;
            }
            return ['status'=>1, 'msg'=>'ok', 'data'=>$res];
        }catch (\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }

    private static function hotelOrder(String $type, Array $params):array
    {
        $orderType      = 0;
        $action         = ($params['status'] == 1) ? ("{$type}_ok") : ("{$type}_fail");
        $hotelNameZh    = $params['hotel_name_zh'];
        $hotelNameEn    = $params['hotel_name_en'];
        $startTimeZh    = date("Y年m月d日", $params['start_time']);
        $startTimeEn    = date("Y年m月d日", $params['start_time']);

        $titleZh        = \Yaconf::get("msg.{$action}.title_zh");
        $titleEn        = \Yaconf::get("msg.{$action}.title_en");
        $tickerZh       = \Yaconf::get("msg.{$action}.ticker_zh");
        $tickerEn       = \Yaconf::get("msg.{$action}.ticker_en");
        if($params['status'] == 1){
            $textZh = sprintf(\Yaconf::get("msg.{$action}.text_zh"), $startTimeZh, $hotelNameZh);
            $textEn = sprintf(\Yaconf::get("msg.{$action}.text_en"), $hotelNameEn, $startTimeEn);
        }else{
            $textZh = sprintf(\Yaconf::get("msg.{$action}.text_zh"),  $hotelNameZh);
            $textEn = sprintf(\Yaconf::get("msg.{$action}.text_en"),  $hotelNameEn);
        }



        return [
            'order_type'=>$orderType,
            'title_zh'=>$titleZh,
            'title_en'=>$titleEn,
            'ticker_zh'=>$tickerZh,
            'ticker_en'=>$tickerEn,
            'text_zh'=>$textZh,
            'text_en'=>$textEn
        ];
    }


}