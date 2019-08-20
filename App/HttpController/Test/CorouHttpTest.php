<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/30
 * Time: 15:46
 */
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpClient\HttpClient;

class CorouHttpTest extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }

    public function demo()
    {
        echo "begin\n";

        $url = "https://www.easyswoole.com";
        go(function () use($url){
            $client = new HttpClient();
            $client->setUrl($url);
            $client->exec();

            echo "request ok \n";
        });

        echo "start http\n";

        return;
    }

}