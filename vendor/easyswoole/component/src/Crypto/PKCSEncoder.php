<?php

namespace EasySwoole\Component\Crypto;

/**
 * PKCS PADDING
 * Class PKCSEncoder
 * @package EasySwoole\Component\Crypto
 */
class PKCSEncoder
{
    /**
     * PKCS补位
     * @param string $data 需要操作补位的数据
     * @param int $blockSize PKCS#5 = 8 OTHER IS PKCS#7
     * @return string
     */
    public static function encode($data, $blockSize = 32)
    {
        $pad = $blockSize - (strlen($data) % $blockSize);

        // 如果本次操作的数据正好是块大小的整倍数 则补上块大小个字节的补位
        if ($pad == 0) {
            $pad = $blockSize;
        }

        return $data . str_repeat(chr($pad), $pad);
    }

    /**
     * PKCS 移除补位
     * @param string $data
     * @return bool|string
     */
    public static function decode($data)
    {
        // 根据PKCS 补位时如果为整数倍块大小 则补上块大小个字节的补位 最后一位必是补位字节
        $pad = ord($data{strlen($data) - 1});

        if ($pad > strlen($data)) {
            return false;
        }

        if (strspn($data, chr($pad), strlen($data) - $pad) != $pad) {
            return false;
        }

        return substr($data, 0, -1 * $pad);
    }
}