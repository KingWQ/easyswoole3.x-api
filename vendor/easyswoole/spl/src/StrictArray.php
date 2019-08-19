<?php


namespace EasySwoole\Spl;


class StrictArray implements \ArrayAccess ,\Countable ,\Iterator
{
    private $class;
    private $data = [];
    private $currentKey;
    private $keys = [];

    function __construct(string $itemClass)
    {
        $this->class = $itemClass;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        if(isset($this->data[$offset])){
            return $this->data[$offset];
        }else{
            return null;
        }
    }

    public function offsetSet($offset, $value)
    {
        if(is_a($value,$this->class)){
            $this->data[$offset] = $value;
            return true;
        }
        throw new \Exception("StrictArray can only set {$this->class} object");
    }

    public function offsetUnset($offset)
    {
        if(isset($this->data[$offset])){
            unset($this->data[$offset]);
            return true;
        }else{
            return false;
        }
    }

    public function count()
    {
        return count($this->data);
    }

    public function current()
    {
        return $this->data[$this->currentKey];
    }

    public function next()
    {
        $this->currentKey = array_shift($this->keys);
    }

    public function key()
    {
        if($this->currentKey === null){
            $this->rewind();
        }
        return $this->currentKey;
    }

    public function valid()
    {
        return isset($this->data[$this->currentKey]);
    }

    public function rewind()
    {
        $this->currentKey = null;
        $this->keys = [];
        $this->keys = array_keys($this->data);
        $this->currentKey = array_shift($this->keys);
    }
}