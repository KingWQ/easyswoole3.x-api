<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class HotelOrderSubModel extends BaseModel
{
    protected $table = 'hotel_order_sub';


    public function updateByMap(array $condition, array $data): ?bool
    {
        $allow = ['where', 'orWhere'];
        foreach($condition as $k=>$v){
            if(in_array($k, $allow)){
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }

        $res = $this->getDb()->update($this->getTable(), $data);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
}

