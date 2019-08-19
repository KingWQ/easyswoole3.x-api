<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/15
 * Time: 17:49
 */
namespace App\Model;

use App\Utility\Pool\MysqlObject;

class BaseModel
{
    private  $db;
    protected $table;
    protected $pre;

    function __construct(MysqlObject $dbObject)
    {
        $this->db = $dbObject;
        $this->pre = \Yaconf::get('mysql.table_pre');
    }

    protected function getDb():MysqlObject
    {
        return $this->db;
    }

    protected function getTable()
    {
        $pre = \Yaconf::get('mysql.table_pre');
        return $pre.$this->table;
    }
}