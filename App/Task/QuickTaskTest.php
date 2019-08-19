<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/26
 * Time: 14:21
 */
namespace App\Task;

use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;

class QuickTaskTest implements QuickTaskInterface
{
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
        echo "{$fromWorkerId}--{$taskId}: 快速任务模板\n";
        return true;
    }
}