<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class StandardHotelModel extends BaseModel
{
    protected $table = 'standardization_hotel';


    public function incByMap(array $condition, String $fieldName, Int $num=1): ?bool
    {
        $allow = ['where', 'orWhere'];
        foreach($condition as $k=>$v){
            if(in_array($k, $allow)){
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }
        $res = $this->getDb()->setInc($this->getTable(), $fieldName, $num);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
}

