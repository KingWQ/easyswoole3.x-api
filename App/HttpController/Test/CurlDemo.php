<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/30
 * Time: 15:46
 */
namespace App\HttpController\Test;

use App\Utility\Curl;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Spl\SplString;

class CurlDemo extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }

    public function postTest()
    {
        $baseUrl = \Yaconf::get('supplier.base_url');
        $url = $baseUrl.\Yaconf::get('supplier.Gta.order_hotel');

        $request = new Curl();
        $params = ['query'=>[]];



        $res = Curl::post($url, $params);
        $data = json_decode($res,true);
        var_dump($data);
        return;
    }

}