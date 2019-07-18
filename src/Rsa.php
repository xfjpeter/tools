<?php
/**
 * @author johnxu <fsyzxz@163.com>
 *
 * @link   https://www.johnxu.net
 */

namespace johnxu\tool;

use johnxu\tool\exception\InvalidParamException;
use johnxu\tool\exception\InvalidSignatureException;
use johnxu\tool\exception\InvalidVerifyException;

final class Rsa
{

    /**
     * signature
     *
     * @param string $data
     * @param string $privateKey Private key or Private key path.
     * @param string $signType   RSA or RSA2
     *
     * @return string
     * @throws InvalidParamException
     * @throws InvalidSignatureException
     */
    public static function signature(string $data, string $privateKey, $signType = 'RSA2')
    {
        $privateKey   = self::getPrivateKey($privateKey);
        $privateKeyId = openssl_pkey_get_private($privateKey);
        if (!$privateKeyId) {
            throw new InvalidParamException('Private key is error.');
        }
        $flag = openssl_sign($data, $signature, $privateKeyId,
            ($signType == 'RSA2' ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1));

        if (!$flag) {
            throw new InvalidSignatureException('Signature fail,');
        }

        return base64_encode($signature);
    }

    /**
     *
     *
     * @param string $data
     * @param string $signature
     * @param string $publicKey
     * @param string $signType
     *
     * @return bool
     * @throws InvalidParamException
     * @throws InvalidVerifyException
     */
    public static function verify(string $data, string $signature, string $publicKey, $signType = 'RSA2')
    {
        $publicKey   = self::getPublicKey($publicKey);
        $publicKeyId = openssl_pkey_get_public($publicKey);
        if (!$publicKeyId) {
            throw new InvalidParamException('Public key is error.');
        }
        $flag = openssl_verify($data, base64_decode($signature), $publicKeyId,
            ($signType == 'RSA2' ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA1));

        if ($flag !== 1) {
            throw new InvalidVerifyException('Verify fail.');
        }

        return true;
    }

    /**
     * encrypted by public key or private key
     *
     * @param string  $data
     * @param string  $key
     * @param boolean $public
     *
     * @return string
     * @throws InvalidParamException
     */
    public static function encrypt(string $data, string $key, bool $public = true)
    {
        if ($public) {
            $flag = openssl_public_encrypt($data, $crypted, self::getPublicKey($key));
        } else {
            $flag = openssl_private_encrypt($data, $crypted, self::getPrivateKey($key));
        }

        if (!$flag) {
            throw new InvalidParamException('Public key or Private key error.');
        }

        return base64_encode($crypted);
    }

    /**
     * decrypted by public key or private key
     *
     * @param string  $data
     * @param string  $key
     * @param boolean $private
     *
     * @return mixed
     * @throws InvalidParamException
     */
    public static function decrypt(string $data, string $key, bool $private = true)
    {
        $data = base64_decode($data);
        if ($private) {
            $flag = openssl_private_decrypt($data, $decrypted, self::getPrivateKey($key));
        } else {
            $flag = openssl_public_decrypt($data, $decrypted, self::getPublicKey($key));
        }

        if (!$flag) {
            throw new InvalidParamException('Public key or Private key error.');
        }

        return $decrypted;
    }

    /**
     * 格式化公钥
     *
     * @param string $param
     *
     * @return mixed|string
     */
    public static function getPublicKey(string $param): string
    {
        if (is_file($param)) {
            $param = file_get_contents($param);
        }
        $replaceTpl = array(
            "-----BEGIN PUBLIC KEY-----",
            "-----END PUBLIC KEY-----",
            "\r",
            "\n",
            "\r\n",
        );
        // 替换掉所有的模板内容
        $publicKey = str_replace($replaceTpl, '', $param);
        // 拼接成public_key格式
        $publicKey = $replaceTpl[0] . PHP_EOL . wordwrap($publicKey, 64, "\n", true) . PHP_EOL . $replaceTpl[1];

        return $publicKey;
    }

    /**
     * 格式化私钥文件
     *
     * @param string $param
     *
     * @return string
     */
    public static function getPrivateKey(string $param): string
    {
        if (is_file($param)) {
            $param = file_get_contents($param);
        }
        $replaceTpl = array(
            "-----BEGIN PRIVATE KEY-----",
            "-----END PRIVATE KEY-----",
            "\r",
            "\n",
            "\r\n",
        );
        $privateKey = str_replace($replaceTpl, '', $param);
        $privateKey = $replaceTpl[0] . PHP_EOL . wordwrap($privateKey, 64, "\n", true) . PHP_EOL . $replaceTpl[1];

        return $privateKey;
    }
}
