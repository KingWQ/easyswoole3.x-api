<?php
namespace App\HttpController\Hotel;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

abstract class Base extends Controller
{
    function index(){}

    protected function onRequest(?string $action): ?bool
    {
        if (!empty($this->request()->getRequestParam('code'))) {
            $this->writeJson(Status::CODE_BAD_REQUEST, ['errorCode'=>1, 'data'=>[]],'code 不存在');
            return false;
        }
        return true;
    }

    protected function onException(\Throwable $throwable): void
    {
        $this->response()->withStatus(200);
        $this->response()->write($throwable->getMessage());
    }
}