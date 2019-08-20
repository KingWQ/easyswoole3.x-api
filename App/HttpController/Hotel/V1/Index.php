<?php
/**
 * @Note 酒店业务控制器
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/12
 * Time: 14:41
 */
namespace App\HttpController\Hotel\V1;

use EasySwoole\Http\Message\Status;

use App\HttpController\Hotel\Base;
use App\Validate\HotelValidate;
use App\Service\HotelService;

class Index extends Base
{
    public function index(){}

    public function placeOrder()
    {
        $data = $this->request()->getParsedBody();

        $validate = new HotelValidate();
        if (!$validate->scene('placeOrder')->check($data)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, [],$validate->getError());
        }

        $res = HotelService::placeOrder();
        if($res['status'] != 1){
            return $this->writeJson(Status::CODE_BAD_REQUEST, [], $res['msg']);
        }

        return $this->writeJson(Status::CODE_OK, $res['data'], 'ok');
    }
}
