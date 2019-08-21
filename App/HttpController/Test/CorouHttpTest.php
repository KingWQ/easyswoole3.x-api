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

    /**
     * @note 协程demo
     * @result 执行顺序是
     * begin
     * start1
     * start2
     * end
     * ok1
     * ok2
     */
    public function demo()
    {
        echo "begin\n";

        $url = "https://www.easyswoole.com";
        go(function () use($url){
            echo "start1 \n";

            $client = new HttpClient($url);
            $client->get();

            echo " ok1 \n";
        });

        go(function () use($url){
            echo "start2 \n";

            $client = new HttpClient($url);
            $client->get();

            echo " ok2 \n";
        });

        echo "end \n";

        return;
    }

}