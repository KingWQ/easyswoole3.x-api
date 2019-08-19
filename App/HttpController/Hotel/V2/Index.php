<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/12
 * Time: 14:41
 */
namespace App\HttpController\Hotel\V2;

use App\HttpController\Hotel\Base;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;

class Index extends Base
{
    public function index()
    {

    }

    public function placeOrder()
    {
        return $this->response()->write("this is test");
    }


}
