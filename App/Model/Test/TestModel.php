<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/15
 * Time: 18:29
 */
namespace App\Model\Test;

use App\Model\BaseModel;


class TestModel extends BaseModel
{
    protected $table = 'hotel_order';


    public function create(TestBean $testBean)
    {
        return $this->getDb()->insert($this->getTable(), $testBean->toArray());
    }


    public function update(TestBean $testBean, array $data)
    {
        $this->getDb()->where('id', $testBean->getId())->update($this->getTable(), $data);
        return $this->getDb()->getAffectRows();

    }

    public function delete(TestBean $testBean)
    {
        return $this->getDb()->where('id', $testBean->getId())->delete($this->getTable());
    }

    public function getOne(TestBean $testBean): ?TestBean
    {
        $test = $this->getDb()
            ->where('id', $testBean->getId())
            ->getOne($this->getTable());

        if(empty($test)) return null;

        return new TestBean($test);
    }

    public function getAll($condition=[], int $page=1, int $pageSize=10, $columns='*'): array
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