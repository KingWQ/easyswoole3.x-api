<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/31
 * Time: 15:36
 */
namespace App\Model;

class HotelOrderModel extends BaseModel
{
    protected $table = 'hotel_order';


    public function getOne(Int $id, String $columns = '*'): array
    {
        $data = $this->getDb()->where('id', $id)->getOne($this->getTable(), $columns);
        if(empty($data)) return [];

        return $data;
    }
    public function getOneByMap(array $condition=[], $columns='*'): array
    {
        $allow = ['where', 'orWhere', 'join'];
        foreach($condition as $k=>$v){
            if(in_array($k, $allow)){
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }

        $data = $this->getDb()->getOne($this->getTable(), $columns);

        if(empty($data)) return [];
        return $data;
    }


    public function update(Int $id, array $data): ?bool
    {
        $res = $this->getDb()->where('id', $id)->update($this->getTable(), $data);
        if(!$res) return $this->getDb()->getLastError();

        return true;
    }
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


    public function getAll(array $condition=[], int $page=1, int $pageSize=10, $columns='*'): array
    {
        $allow = ['where', 'orWhere', 'join', 'orderBy', 'groupBy'];

        foreach($condition as $k=>$v){
            if(in_array($k, $allow)){
                foreach ($v as $item) {
                    $this->getDb()->$k(...$item);
                }
            }
        }

        $list = $this->getDb()
            ->withTotalCount()
            ->orderBy('id', 'DESC')
            ->get($this->getTable(), [$pageSize*($page-1), $pageSize], $columns);
        $total = $this->getDb()->getTotalCount();

        return ['total'=>$total, 'list'=>$list];
    }

}

