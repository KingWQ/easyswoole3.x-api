<?php
/**
 * @快捷查询条件类
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/16
 * Time: 15:14
 */
namespace App\Model;

use EasySwoole\Spl\SplBean;

class ConditionBean extends SplBean
{
    protected $conditions = [];
    protected $columns = '*';
    protected $pagination = null;

    /**
     * @添加where条件
     * @param $whereProp
     * @param string $whereValue
     * @param string $operator
     * @param string $cond
     * @return ConditionBean
     */
    public function addWhere($whereProp, $whereValue='DBNULL', $operator='=', $cond='AND')
    {
        $this->conditions['where'][] = [ $whereProp, $whereValue, $operator, $cond ];
        return $this;
    }

    /**
     * @添加orWhere条件
     * @param $whereProp
     * @param string $whereValue
     * @param string $operator
     * @return ConditionBean
     */
    function addOrWhere($whereProp, $whereValue = 'DBNULL', $operator = '=')
    {
        $this->conditions['orWhere'][] = [ $whereProp, $whereValue, $operator ];;
        return $this;
    }

    /**
     * @添加Join条件
     * TODO:: 可以继续实现一个字段控制逻辑 即这个where条件需要select什么字段
     * @param $joinTable
     * @param $joinCondition
     * @param string $joinType
     * @return ConditionBean
     */
    function addJoin($joinTable, $joinCondition, $joinType = '')
    {
        $this->conditions['join'][] = [ $joinTable, $joinCondition, $joinType ];
        return $this;
    }

    /**
     * @添加OrderBy条件
     * @param $orderByField
     * @param string $orderByDirection
     * @param null $customFieldsOrRegExp
     * @return ConditionBean
     */
    function addOrderBy($orderByField, $orderByDirection = "DESC", $customFieldsOrRegExp = null)
    {
        $this->conditions['orderBy'][] = [ $orderByField, $orderByDirection, $customFieldsOrRegExp ];
        return $this;
    }

    /**
     * @添加GroupBy条件
     * @param $groupByField
     * @return ConditionBean
     */
    function addGroupBy($groupByField)
    {
        $this->conditions['groupBy'][] = [ $groupByField ];
        return $this;
    }

    /**
     * @获取字段
     * @return string
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @设置字段
     * @param string $columns
     */
    public function setColumns(string $columns="*")
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @获取分页查询
     * @return null
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @设置分页查询
     * @param $page
     * @param int $limit
     */
    public function setPagination($page, $limit=20)
    {
        $this->pagination = [ intval($page) * 1 > 0 ? intval($page - 1) * 1 * $limit : 0, $limit ];
        return $this;
    }


    /**
     * @返回查询条件
     * @param array|null $columns
     * @param null $filter
     */
    function toArray(array $columns = null, $filter = null): array
    {
        return $this->conditions;
    }

}