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

use johnxu\tool\traits\Singleton;
use Exception;

class Aes
{

    const METHOD_AES_128_CBC = 'aes-128-cbc';
    const METHOD_AES_128_ECB = 'aes-128-ecb';

    use Singleton;

    /**
     * 加密
     * @param string $data
     * @param string $key
     * @param string $method
     * @param string $iv
     * @return string
     * @throws Exception
     */
    public function encrypt(string $data, string $key, string $method = self::METHOD_AES_128_ECB, string $iv = '')
    {
        $this->checkMethod($method, $iv);

        return base64_encode(
            openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv)
        );
    }

    /**
     * 解密
     * @param string $data
     * @param string $key
     * @param string $method
     * @param string $iv
     * @return string
     * @throws Exception
     */
    public function decrypt(string $data, string $key, string $method = self::METHOD_AES_128_ECB, string $iv = '')
    {
        $this->checkMethod($method, $iv);

        $data = base64_decode($data);

        return openssl_decrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * 检测method和iv
     * @param string $method
     * @param string $iv
     * @throws Exception
     */
    private function checkMethod(string $method, string $iv)
    {
        // 判断方法存在里面否
        if (!in_array($method, [self::METHOD_AES_128_ECB, self::METHOD_AES_128_CBC])) {
            throw new Exception('not found encrypt method');
        }

        // 判断iv向量
        if ($method == self::METHOD_AES_128_CBC && empty($iv)) {
            throw new Exception('iv darf nicht NULL sein');
        }
    }
}