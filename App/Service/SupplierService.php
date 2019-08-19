<?php
/**
 * @Note 供应商服务类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/29
 * Time: 16:53
 */
namespace App\Service;

use App\Model\HotelOrderModel;

class SupplierService
{
    public static function placeOrder($serName,$params)
    {
        try{
            $db = MysqlPool::defer();
            $hotelOrderModel = new HotelOrderModel($db);
            $row = $hotelOrderModel->getOneByMap(['order_sn'=>$params['order_sn']],'id,user_id,supplier_type,order_sn,order_type,pay_status,booking_status');

            $suppNameArr = \Yaconf::get('supplier.supplier_name');
            $suppName = $suppNameArr[$row['supplier_type']];
            $className = $suppName.'SuppService';

            $res = $className::placeOrder($serName,$params);
            if($res['status'] == 0) return ['status'=>0, 'msg'=>$res['msg'], 'data'=>$res['data']];

            return ['status'=>1, 'msg'=>'ok', 'data'=>$res['data']];
        }catch (\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }
}