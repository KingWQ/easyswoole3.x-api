<?php


namespace EasySwoole\Component;


use Swoole\Coroutine\Channel;

class Csp
{
    private $chan;
    private $count = 0;
    private $success = 0;
    private $task = [];

    function __construct(int $size = 8)
    {
        $this->chan = new Channel($size);
    }

    function add($itemName,callable $call):Csp
    {
        $this->count = 0;
        $this->success = 0;
        $this->task[$itemName] = $call;
        return $this;
    }

    function successNum():int
    {
        return $this->success;
    }

    function exec(?float $timeout = 5)
    {
        if($timeout <= 0){
            $timeout = PHP_INT_MAX;
        }
        $this->count = count($this->task);
        foreach ($this->task as $key => $call){
            go(function ()use($key,$call){
                $this->chan->push([
                    'key'=>$key,
                    'result'=>call_user_func($call)
                ]);
            });
        }
        $result = [];
        $left = $timeout;
        while(($this->count > 0) && ($left > 0))
        {
            $start = round(microtime(true),3);
            $temp = $this->chan->pop($left);
            if(is_array($temp)){
                $key = $temp['key'];
                $result[$key] = $temp['result'];
                $this->count--;
                $this->success++;
            }
            $left = $left - (round(microtime(true),3) - $start);
        }
        return $result;
    }
}