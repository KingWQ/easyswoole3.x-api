<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/30
 * Time: 15:46
 */
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Utility\Curl;

class CurlTest extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }

    public function get()
    {
        $url = "https://www.swoole.com";

        $res = Curl::get($url);

        $this->response()->write($res);
    }

    public function post()
    {
        $url = "";
        $postData = [];

        $res = Curl::post($url, $postData);

        $this->writeJson($res);
    }

}