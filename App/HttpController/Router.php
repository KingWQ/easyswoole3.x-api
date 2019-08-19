<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/12
 * Time: 17:30
 */
namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
//        $this->setGlobalMode(true);

        // TODO: Implement initialize() method.
        $routeCollector->post('/hotel/order', function (Request $request, Response $response){
            $version = $request->getHeader('api-version')[0];
            return "/hotel/{$version}/Index/placeOrder";
        });

//        $this->setMethodNotAllowCallBack(function (Request $request,Response $response){
//            $response->withHeader('Content-type','application/json;charset=utf-8');
//            $response->write(json_encode(['code'=>400, 'msg'=>'未找到处理方法', 'data'=>[]]));
//
//        });
//        $this->setRouterNotFoundCallBack(function (Request $request,Response $response){
//            $response->withHeader('Content-type','application/json;charset=utf-8');
//            $response->write(json_encode(['code'=>400, 'msg'=>'未找到路由匹配', 'data'=>[]]));
//        });
    }
}