<?php
/**
 * @Note 酒店业务服务类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/25
 * Time: 11:01
 */
namespace App\Service;

use App\Task\ServiceTask;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;

class HotelService
{
    public static function placeOrder($params)
    {
        try{
            //1：供应商下单
            $supplierRes = SupplierService::placeOrder($params);

            //2: 订单数据库处理
            $methodName =  $supplierRes['status']==1 ? 'orderOk':'orderFail';
            $hotelRes = HotelLogic::$methodName($params);
            if($hotelRes['status'] != 1) throw new \Exception('下单完成数据更新失败：'.$hotelRes['msg']);

            //3：发送邮件服务
            $taskClass = new ServiceTask(['type'=>'email', 'action'=>'hotel_order','data'=>$params]);
            TaskManager::async($taskClass);

            //4：推送app消息服务
            $taskClass = new ServiceTask(['type'=>'app_msg', 'action'=>'hotel_order','data'=>$params]);
            TaskManager::async($taskClass);

            //5：成功时的其他服务处理
            if($supplierRes['status']==1){
                //5.1：流量赠送
                $taskClass = new ServiceTask(['type'=>'give_flow', 'action'=>'hotel_order','data'=>$params]);
                TaskManager::async($taskClass);

                //5.2：新用户活动
                $taskClass = new ServiceTask(['type'=>'new_user', 'action'=>'hotel_order','data'=>$params]);
                TaskManager::async($taskClass);

                //5.3：拉新推荐
                $taskClass = new ServiceTask(['type'=>'pull_user', 'action'=>'hotel_order','data'=>$params]);
                TaskManager::async($taskClass);
            }

            return ['status'=>1, 'msg'=>'ok', 'data'=>[]];
        }catch(\Exception $e){
            return ['status'=>0, 'msg'=>$e->getMessage(), 'data'=>[]];
        }
    }


}