<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class AccountExtendModel extends BaseModel
{
    protected $table = 'account_extend';


    public function getOne(Int $id, String $columns = '*'): array
    {
        $data = $this->getDb()->where('account_id', $id)->getOne($this->getTable(), $columns);
        if (empty($data)) return [];

        return $data;
    }
}
