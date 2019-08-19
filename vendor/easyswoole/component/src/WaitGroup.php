<?php


namespace EasySwoole\Component;


use Swoole\Coroutine\Channel;

class WaitGroup
{
    private $count = 0;
    private $chan;
    private $success = 0;

    public function __construct(int $size = 8)
    {
        $this->chan = new Channel($size);
    }

    public function add()
    {
        $this->count++;
    }

    function successNum():int
    {
        return $this->success;
    }

    public function done()
    {
        $this->chan->push(1);
    }

    public function wait(?float $timeout = 15)
    {
        if($timeout <= 0){
            $timeout = PHP_INT_MAX;
        }
        $this->success = 0;
        $left = $timeout;
        while(($this->count > 0) && ($left > 0))
        {
            $start = round(microtime(true),3);
            if($this->chan->pop($left) === 1)
            {
                $this->count--;
                $this->success++;
            }
            $left = $left - (round(microtime(true),3) - $start);
        }
    }
}