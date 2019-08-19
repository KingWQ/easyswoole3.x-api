<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class MsgModel extends BaseModel
{
    protected $table = 'umeng_push';

    public function add($data): ?bool
    {
        $res = $this->getDb()->insert($this->getTable(), $data);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
}

