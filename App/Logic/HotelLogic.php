<?php
/**
 * @Note 酒店业务逻辑类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/29
 * Time: 18:43
 */
namespace App\Logic;

use App\Model\HotelOrderModel;
use App\Model\HotelOrderSubModel;
use App\Model\RefundEventModel;
use App\Model\StandardHotelModel;
use App\Utility\Pool\MysqlPool;

class HotelLogic
{
    public static function orderOk($params)
    {

        $db = MysqlPool::defer();
        $hotelOrderModel = new HotelOrderModel($db);
        $hotelOrderSubModel = new HotelOrderSubModel($db);
        $standardHotelModel = new StandardHotelModel($db);

        $db->startTransaction();
        try{
            $orderId = $params['order_id'];
            $hotelCode = $params['hotel_code'];
            $locator = $params['locator'];
            $refer   = $params['reference_source'];
            $cancel  = json_encode($params['cnacel_json']);

            $orderRes = $hotelOrderModel->update($orderId, ['booking_status'=>1, 'locator'=>$locator, 'reference_source'=>$refer]);
            if(!$orderRes) throw new \Exception("订购成功时：修改订单主表报错, {$orderRes}, sql：".$db->getLastQuery());

            $mapSub['where'][] =['hotel_order_id', $orderId];
            $subRes = $hotelOrderSubModel->updateByMap($mapSub, ['booking_status'=>1, 'cnacel_json'=>$cancel, 'booking_time'=>time()]);
            if(!$subRes) throw new \Exception("订购成功时：修改订单子表报错, {$subRes}, sql：".$db->getLastQuery());

            $hotelSub['where'][] =['hotel_code', $hotelCode];
            $hotelRes = $standardHotelModel->incByMap($hotelSub, 'num');
            if(!$hotelRes) throw new \Exception("订购成功时：修改订酒店表报错, {$hotelRes}, sql：".$db->getLastQuery());

            $db->commit();
            return ['status'=>1, 'msg'=>'ok','data'=>[]];
        }catch (\Exception $e){
            $db->rollback();
            return ['status'=>0, 'msg'=>$e->getMessage(),'data'=>[]];
        }

    }

    public static function orderFail($params)
    {
        $db = MysqlPool::defer();
        $hotelOrderModel = new HotelOrderModel($db);
        $hotelOrderSubModel = new HotelOrderSubModel($db);
        $refundEventModel = new RefundEventModel($db);

        $db->startTransaction();
        try{
            $orderId = $params['order_id'];

            $orderRes = $hotelOrderModel->update($orderId, ['booking_status'=>2,'pay_status'=>5]);
            if($orderRes !== true) throw new \Exception("订购失败时：修改订单主表报错, {$orderRes}, sql：".$db->getLastQuery());

            $mapSub['where'][] =['hotel_order_id', $orderId];
            $subRes = $hotelOrderSubModel->updateByMap(['hotel_order_id'=>$orderId], ['booking_status'=>4,'booking_time'=>time()]);
            if($subRes !== true) throw new \Exception("订购失败时：修改订单子表报错, {$subRes}，sql：".$db->getLastQuery());

            $refundData['order_sn']         = $params['order_sn'];
            $refundData['order_type']       = 1;
            $refundData['user_id']          = $params['user_id'];
            $refundData['currency']         = $params['currency'];
            $refundData['pay_style']        = $params['pay_style'];
            $refundData['refund_amount']    = $params['refund_amount'];
            $refundData['addtime']          = time();
            $refundRes = $refundEventModel->add($refundData);
            if($refundRes !== true) throw new \Exception("订购失败时：修改订退款表报错, {$refundRes}，sql：".$db->getLastQuery());

            $db->commit();
            return ['status'=>1, 'msg'=>'ok','data'=>[]];
        }catch (\Exception $e){
            $db->rollback();
            return ['status'=>0, 'msg'=>$e->getMessage(),'data'=>[]];
        }
    }
}