<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class AccountModel extends BaseModel
{
    protected $table = 'account';


    public function getOne(Int $id, String $columns = '*'): array
    {
        $data = $this->getDb()->where('account_id', $id)->getOne($this->getTable(), $columns);
        if (empty($data)) return [];

        return $data;
    }

    public function countOrder(array $condition=[], $columns='*'): int
    {
        $allow = ['where', 'orWhere'];

        foreach($condition as $k=>$v){
            if(in_array($k, $allow)){
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }

        $count = $this->getDb()
            ->count($this->pre.'hotel_order', $columns);

        return $count;
    }
}
