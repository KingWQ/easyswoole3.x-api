<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/21
 * Time: 18:53
 */
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;

class TaskTest extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    public function task()
    {
        TaskManager::async(function () {
            echo "执行异步任务1...\n";
            return true;
        }, function () {
            echo "异步任务执行完毕2...\n";
        });
        // 在定时器中投递的例子
        $a = \EasySwoole\Component\Timer::getInstance()->loop(1000, function () {
            TaskManager::async(function () {
                echo "执行异步任务3...\n";
            });
        });

        echo "success \n";
    }

    public function taskQuick()
    {
        TaskManager::async(\App\Task\QuickTaskTest::class);
    }

    public function taskTemplate()
    {

        $taskClass = new Task(['type'=>'hotel']);
        TaskManager::async($taskClass);

        $taskClass = new Task(['type'=>'flight']);
        TaskManager::async($taskClass);
        //不写echo 执行顺序是 1-1 ，1-0
        //协程 可以用在 一组IO操作 ，利用IO等待时间，挂起去执行其他操作 如：一组sql
        //异步 遇到IO 不会等待该逻辑返回效果 直接往下执行
        echo 'success';
    }

    function multiTaskConcurrency(){
        // 多任务并发
        $tasks[] = function () { sleep(1);return 'this is 1'; }; // 任务1
        $tasks[] = function () { sleep(2);return 'this is 2'; };     // 任务2
        $tasks[] = function () { sleep(3);return 'this is 3'; }; // 任务3
        $results = \EasySwoole\EasySwoole\Swoole\Task\TaskManager::barrier($tasks, 6);
        var_dump($results);
        $this->response()->write('执行并发任务成功');
    }

}