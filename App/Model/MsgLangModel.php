<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class MsgLangModel extends BaseModel
{
    protected $table = 'umeng_push_lang';

    public function addMulti($data): ?bool
    {
        $res = $this->getDb()->insertMulti($this->getTable(), $data);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
}

