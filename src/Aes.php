<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019-04-15
 * | ---------------------------------------------------------------------------------------------------
 * | Desc:
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

class Aes
{
    /**
     * AES加密
     *
     * @param string $data 数据
     * @param string $key  秘钥：16位
     * @param string $iv   填充向量：16位
     * @return string
     */
    public static function encrypt(string $data, string $key, string $iv = ''): string
    {
        return base64_encode(
            openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv)
        );
    }

    /**
     * AES解密
     *
     * @param string $data 数据
     * @param string $key  秘钥：16位
     * @param string $iv   填充向量：16位
     * @return string
     */
    public static function decrypt(string $data, string $key, string $iv): string
    {
        return openssl_decrypt(
            base64_decode($data),
            'aes-128-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
    }
}