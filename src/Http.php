<?php

namespace johnxu\tool;

use Exception;
use johnxu\tool\traits\Singleton;

/**
 * Class Http
 *
 * @package johnxu\tool
 */
class Http
{
    private $data      = '';
    private $http_code = 0;
    private $error     = '';

    use Singleton;

    /**
     * 请求
     *
     * @param string      $uri
     * @param array       $data
     * @param string      $method
     * @param bool|null   $secret
     * @param string|null $key
     *
     * @return Http
     * @throws Exception
     */
    public function request(
        string $uri,
        $data = [],
        string $method = 'get',
        string $secret = null,
        string $key = null
    ): Http {
        $method = strtoupper($method);
        $ch     = curl_init();
        if ($method == 'GET') {
            if ($data) {
                if (strpos($uri, '?')) {
                    foreach ($data as $key => $item) {
                        $uri .= "&{$key}={$item}";
                    }
                } else {
                    $uri .= '?' . urldecode(http_build_query($data));
                }
            }
        } else {
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? urldecode(http_build_query($data)) : $data);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($secret && $key) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $secret);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $key);
        }
        $this->data = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->error = curl_error($ch);
            throw new Exception(curl_error($ch));
        }
        $info = curl_getinfo($ch);
        $this->setInfo($info);
        curl_close($ch);

        return $this;
    }

    /**
     * 返回码
     *
     * @return int
     */
    public function getCode(): int
    {
        return (int) $this->http_code;
    }

    /**
     * 错误信息
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 获取类型
     *
     * @param bool $json 当true时json_decode数据，否则原样返回
     *
     * @return mixed
     */
    public function getContent(bool $json = true)
    {
        return $json ? json_decode($this->data, true) : $this->data;
    }

    /**
     * 获取curl_getinfo里的值
     *
     * @param string $name
     *
     * @return string
     */
    public function get(string $name): string
    {
        if (!isset($this->$name)) {
            return false;
        }

        return $this->$name;
    }

    /**
     * @param $info
     */
    private function setInfo($info)
    {
        foreach ($info as $key => $value) {
            $this->$key = $value;
        }
    }
}
