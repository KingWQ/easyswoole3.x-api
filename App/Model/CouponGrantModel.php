<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class CouponGrantModel extends BaseModel
{
    protected $table = 'invitation_coupon_log';

    public function add($data): ?bool
    {
        $res = $this->getDb()->insert($this->getTable(), $data);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
}

