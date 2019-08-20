<?php
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;

class YaconfTest extends Controller
{
    public function index(){}

    public function demo()
    {
        $name           = \Yaconf::get("demo.name");
        $phpVersion     = \Yaconf::get("demo.php_version");
        $env            = \Yaconf::get("demo.env");

        $this->response()->write($name);
        $this->response()->write($phpVersion);
        $this->response()->write($env);
    }
}