<?php
/**
 * @Note 酒店业务验证器类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/25
 * Time: 14:25
 */
namespace App\Validate;

use App\Model\AccountExtendModel;
use App\Model\otelOrderModel;
use App\Utility\Pool\MysqlPool;
use think\Validate;

class HotelValidate extends Validate
{
    protected $rule = [
        'user_id' => 'require|number|gt:0|checkUser',
        'order_sn' => 'require|checkOrder',
    ];

    protected $message = [
        'user_id.require' => 'user_id必须',
        'user_id.number' => 'user_id必须是数字',
        'user_id.gt' => 'user_id必须大于0',
        'order_sn.require' => 'order_sn必须',
    ];

    protected $scene = [
        'placeOrder' => ['user_id', 'order_sn'],
    ];

    protected function checkUser($value, $rule, $data)
    {
        $db = MysqlPool::class();
        $model = new AccountExtendModel($db);
        $row = $model->getOne($value, 'account_id,device_token');
        if(empty($row)) return 'user_id不存在';

        return true;
    }

    protected function checkOrder($value, $rule, $data)
    {
        $db = MysqlPool::defer();
        $model = new HotelOrderModel($db);
        $row = $model->getOneByMap(['order_sn'=>$value],'user_id,supplier_type,order_sn,order_type,pay_status,booking_status');

        if(empty($row)) return '订单不存在';
        if($row['user_id'] != $data['user_id']) return '订单和用户不匹配';
        if($row['order_type'] != 1) return '订单类型不正确';

        if(!in_array($row['supplier_type'], \Yaconf::get('supplier.supplier_type'))) return '订单的供应商类型不正确';
        if($row['pay_status'] != 1) return '订单的支付状态不正确';
        if($row['booking_status'] != 5) return '订单的订购状态不正确';

        return true;
    }

}