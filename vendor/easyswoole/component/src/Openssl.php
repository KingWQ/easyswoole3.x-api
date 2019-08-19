<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/7/27
 * Time: 下午10:41
 */

namespace EasySwoole\Component;


class Openssl
{
    private $key;
    private $method;

    function __construct($key,$method = 'DES-EDE3')
    {
        $this->key = $key;
        $this->method = $method;
        trigger_error(static::class." is deprecated ,use ".'EasySwoole\Component\Crypto instead',E_USER_DEPRECATED);
    }

    public function encrypt(string $data)
    {
        return openssl_encrypt($data,$this->method,$this->key);
    }

    public function decrypt(string $raw)
    {
        return openssl_decrypt($raw,$this->method,$this->key);
    }
}