<?php
namespace App\HttpController\Test;

use EasySwoole\Http\AbstractInterface\Controller;
use App\Utility\JwtAuth;

class JwtTest extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }

    /**
     * @note 生成token
     */
    public function generate()
    {
        $payload=[
            'iss' => "http://easyswoole.test",          //签发者
            'iat' => $_SERVER['REQUEST_TIME'],          //什么时候签发的
            'exp' => $_SERVER['REQUEST_TIME'] + 7200,   //过期时间
            'uid'=>1111
        ];
        $key = '1241245EEwq#12';

        $token = JwtAuth::encode($payload,$key);

        return $this->response()->write($token);


    }

    /**
     * @note 校验token合法性
     */
    public function verify()
    {
        $token = $this->request()->getRequestParam('token');
        $key = '1241245EEwq#12';

        $res = JwtAuth::decode($token,$key);

        return $this->response()->write(json_encode($res));
    }

}