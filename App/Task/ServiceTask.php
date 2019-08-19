<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/26
 * Time: 10:56
 */
namespace App\Task;

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class ServiceTask extends AbstractAsyncTask
{
    /**
     * @param $taskData：['type'=>'email', 'action'=>'hotel_order', 'data'=>[]]
     * @param $taskId
     * @param $fromWorkerId
     * @param null $flags
     * @return array
     */
    function run($taskData, $taskId, $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
        echo "{$fromWorkerId}-{$taskId}\n";
        $res = $this->execTask($taskData);
        return $res;
    }



    function finish($result, $task_id)
    {
        // TODO: Implement finish() method.
//        var_dump($result);

        //TODO：发送钉钉消息

    }

    public function execTask($taskData){
        $type = $taskData['type'];
        switch($type){
            case 'email':       $res = EmailTask::exec($taskData);break;
            case 'app_msg':     $res = AppMsgTask::exec($taskData);break;
            case 'give_flow':   $res = GiveFlowTask::exec($taskData);break;
            case 'new_user':    $res = NewUserTask::exec($taskData);break;
        }
        return $res;
    }
}