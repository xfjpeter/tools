<?php
namespace johnxu\tool;

/**
 * Support class
 */
class Support
{
    /**
     * request http
     *
     * @access public
     *
     * @param  string    $uri
     * @param  mixed     $data
     * @param  string    $method
     * @param  bool|null $secret
     * @param  string    $key
     *
     * @return mixed
     */
    public function requestApi(string $uri, $data, string $method = 'get', bool $secret = null, string $key = null)
    {
        $method = strtoupper($method);
        $ch     = curl_init();
        if ($method == 'GET') {
            $uri .= '?' . urldecode(http_build_query($data));
        }
        $params[CURLOPT_URL]            = $uri;
        $params[CURLOPT_RETURNTRANSFER] = 1;
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        if ($method == 'POST') {
            $params[CURLOPT_POST]       = 1;
            $params[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? urldecode(http_build_query($data)) : $data);
        }
        if ($secret && $key) {
            $params[CURLOPT_SSLCERTTYPE] = 'PEM';
            $params[CURLOPT_SSLCERT]     = $secret;
            $params[CURLOPT_SSLKEYTYPE]  = 'PEM';
            $params[CURLOPT_SSLKEY]      = $key;
        }
        curl_setopt_array($ch, $params);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * Get real ip
     *
     * @access public
     *
     * @return string
     */
    public static function getClientIp(): string
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknow")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknow")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknow")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknow")) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = "unknow";
        }

        return $ip;
    }
}
