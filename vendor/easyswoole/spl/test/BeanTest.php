<?php


namespace EasySwoole\Spl\Test;


use PHPUnit\Framework\TestCase;

class BeanTest extends TestCase
{
    function testArrayToBean()
    {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);
        $this->assertEquals([
            'a'=>'a',
            'b'=>'b',
            'd_d'=>'d_d'
        ],$bean->toArray());

        $this->assertEquals([
            'a'=>'a',
            'b'=>'b',
        ],$bean->toArray(['a','b']));

        $this->assertEquals([
            'a'=>'a',
            'd-d'=>'d_d'
        ],$bean->toArrayWithMapping(['a','d-d']));
    }

    function testRestore()
    {
        $bean = new TestBean([
            'a'=>'a',
            'b'=>'b',
            'c'=>'c',
            'd_d'=>'d_d'
        ]);

        $this->assertEquals([
            'a'=>2,
            'b'=>null,
            'd_d'=>null
        ],$bean->restore()->toArray());


        $this->assertEquals([
            'a'=>2
        ],$bean->restore()->toArray(null,$bean::FILTER_NOT_NULL));


        $bean->restore(['a'=>2,'b'=>3]);
    }
}