<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/26
 * Time: 10:56
 */
namespace App\Task;

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class Task extends AbstractAsyncTask
{
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
        var_dump($result);
    }

    public function execTask($taskData){
        $type = $taskData['type'];
        switch($type){
            case 'hotel':
                $res = ['只剩下一个豪华单间'];
                break;
            case 'flight':
                $res = ['订到机票啦'];
                break;
        }
        return $res;
    }
}