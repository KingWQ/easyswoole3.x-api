<?php


namespace EasySwoole\Component\CoroutineRunner;


use Swoole\Coroutine\Channel;
use Swoole\Coroutine;

class Runner
{
    protected $concurrency;
    protected $taskChannel;
    protected $isRunning = false;
    protected $runningNum = 0;

    function __construct($concurrency = 64,$taskChannelSize = 1024)
    {
        $this->concurrency = $concurrency;
        $this->taskChannel = new Channel($taskChannelSize);
    }

    function status()
    {
        return [
            'taskNum'=>$this->taskChannel->stats(),
            'concurrency'=>$this->concurrency,
            'runningNum'=>$this->runningNum,
            'isRunning'=>$this->isRunning
        ];
    }

    function addTask(Task $task):Runner
    {
        $this->taskChannel->push($task);
        return $this;
    }

    function start(float $waitTime = 30)
    {
        if(!$this->isRunning){
            $this->isRunning = true;
        }
        $start = time();
        while ($waitTime > 0){
            if($this->runningNum < $this->concurrency && !$this->taskChannel->isEmpty()){
                $task = $this->taskChannel->pop(0.01);
                if($task instanceof Task){
                    go(function ()use($task){
                        $this->runningNum++;
                        $ret = null;
                        try{
                            $ret = call_user_func($task->getCall());
                            if($ret !== null && is_callable($task->getOnSuccess())){
                                call_user_func($task->getOnSuccess(),$ret);
                            }
                        }catch (\Throwable $throwable){
                            if(is_callable($task->getOnFail())){
                                call_user_func($task->getOnFail(),$ret);
                            }
                        }finally{
                            $this->runningNum--;
                        }
                    });
                }
            }else{
                if(time() - $start > $waitTime){
                    break;
                }else if($this->taskChannel->isEmpty() && $this->runningNum <= 0){
                    break;
                }else{
                    /*
                     * 最小调度粒度为0.01
                     */
                    Coroutine::sleep(0.01);
                }
            }
        }
        $this->isRunning = false;
    }
}