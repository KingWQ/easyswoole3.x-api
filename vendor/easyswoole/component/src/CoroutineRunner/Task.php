<?php


namespace EasySwoole\Component\CoroutineRunner;


class Task
{
    protected $timeout = 3;
    /** @var callable */
    protected $call;
    /** @var callable */
    protected $onSuccess;
    /** @var callable */
    protected $onFail;

    function __construct(callable $call)
    {
        $this->call = $call;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return callable
     */
    public function getCall(): callable
    {
        return $this->call;
    }

    /**
     * @param callable $call
     */
    public function setCall(callable $call): void
    {
        $this->call = $call;
    }

    /**
     * @return callable
     */
    public function getOnSuccess(): callable
    {
        return $this->onSuccess;
    }

    /**
     * @param callable $onSuccess
     */
    public function setOnSuccess(callable $onSuccess): void
    {
        $this->onSuccess = $onSuccess;
    }

    /**
     * @return callable
     */
    public function getOnFail(): callable
    {
        return $this->onFail;
    }

    /**
     * @param callable $onFail
     */
    public function setOnFail(callable $onFail): void
    {
        $this->onFail = $onFail;
    }
}