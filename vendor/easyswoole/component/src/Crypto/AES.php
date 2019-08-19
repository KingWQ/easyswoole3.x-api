<?php


namespace EasySwoole\Component\Crypto;

/**
 * AES加密处理
 * Class AES
 * @package EasySwoole\Component\Crypto
 */
class AES
{
    protected $method;
    protected $secret_key;
    protected $iv;

    // Electronic Codebook Book
    const AES_128_ECB = 'AES-128-ECB';
    const AES_192_ECB = 'AES-192-ECB';
    const AES_256_ECB = 'AES-256-ECB';

    // Cipher Block Chaining
    const AES_128_CBC = 'AES-128-CBC';
    const AES_192_CBC = 'AES-192-CBC';
    const AES_256_CBC = 'AES-256-CBC';
    const AES_128_CBC_HMAC_SHA1 = 'AES-128-CBC-HMAC-SHA1';
    const AES_256_CBC_HMAC_SHA1 = 'AES-256-CBC-HMAC-SHA1';

    // Counter
    const AES_128_CTR = 'AES-128-CTR';
    const AES_192_CTR = 'AES-192-CTR';
    const AES_256_CTR = 'AES-256-CTR';

    // Cipher FeedBack
    const AES_128_CFB = 'AES-128-CFB';
    const AES_128_CFB1 = 'AES-128-CFB1';
    const AES_128_CFB8 = 'AES-128-CFB8';
    const AES_192_CFB = 'AES-192-CFB';
    const AES_192_CFB1 = 'AES-192-CFB1';
    const AES_192_CFB8 = 'AES-192-CFB8';
    const AES_256_CFB = 'AES-256-CFB';
    const AES_256_CFB1 = 'AES-256-CFB1';
    const AES_256_CFB8 = 'AES-256-CFB8';

    // Output FeedBack
    const AES_128_OFB = 'AES-128-OFB';
    const AES_192_OFB = 'AES-192-OFB';
    const AES_256_OFB = 'AES-256-OFB';

    // XOR-ENCRYPT-XOR Tweakable Codebook mode
    const AES_128_XTS = 'AES-128-XTS';
    const AES_256_XTS = 'AES-256-XTS';

    /**
     * AES constructor.
     * @param string $key 请传入BASE64解码后的KEY
     * @param string $method 加密模式可以常量传入
     * @param string $iv 如加密方式需要，请传入BASE64解码后的IV
     */
    function __construct($key, $method = AES::AES_256_ECB, $iv = null)
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('extension openssl not loaded');
        }
        $this->checkCipherMethods($method);
        $this->iv = $iv;
        $this->method = $method;
        $this->secret_key = $key;
    }

    /**
     * 加密数据
     * @param $data
     * @return string|false
     */
    function encrypt($data)
    {
        return openssl_encrypt($data, $this->method, $this->secret_key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * 解密数据
     * @param $data
     * @return string|false
     */
    function decrypt($data)
    {
        return openssl_decrypt($data, $this->method, $this->secret_key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * 检查加密方式是否被支持
     * @param string $name 需要检查的加密算法名称
     * @return bool
     */
    private function checkCipherMethods($name)
    {
        $methods = openssl_get_cipher_methods();
        if (in_array(strtoupper($name), $methods)) {
            return true;
        }
        throw new \InvalidArgumentException("aes cipher method " . $name . ' is not support!');
    }

}