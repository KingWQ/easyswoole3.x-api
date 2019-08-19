<?php


namespace EasySwoole\Spl\Test;


use EasySwoole\Spl\SplBean;

class TestBean extends SplBean
{
    public $a = 2;
    protected $b;
    private $c;
    protected $d_d;

    protected function setKeyMapping(): array
    {
        return [
            'd-d'=>"d_d"
        ];
    }
}