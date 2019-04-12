<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2018/11/19
 * | ---------------------------------------------------------------------------------------------------
 * | Desc: 返回RESTFul格式api
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

use think\exception\HttpResponseException;
use think\Response;

abstract class Restful
{
    // 定义返回状态码
    const HTTP_RESPONSE_OK      = 200; // OK
    const HTTP_RESPONSE_CREATED = 201; // POST Created
    const HTTP_RESPONSE_UPDATED = 204; // PUT Updated

    const HTTP_RESPONSE_REDIRECT = 301; // Moved Permanently

    const HTTP_RESPONSE_PARAM_ERROR      = 400; // Request Params Error
    const HTTP_RESPONSE_UN_AUTHORIZATION = 401; // UnAuthorization
    const HTTP_RESPONSE_FORBIDDEN        = 403; // Forbidden
    const HTTP_RESPONSE_NOT_FOUND        = 404; // Not found
    const HTTP_RESPONSE_TIMEOUT          = 408; // Request timeout
    const HTTP_RESPONSE_EXISTS           = 409; // Resource Exists
    const HTTP_RESPONSE_FILE_TOO_LARGE   = 413; // Upload file too large

    const HTTP_RESPONSE_SERVER_ERROR    = 500; // Internal Server Error
    const HTTP_RESPONSE_SERVER_UPGRADED = 503; // Service Unavailable

    /**
     * @var string 返回类型
     */
    protected $responseType = 'json';

    /**
     * 返回正确值
     *
     * @param int   $responseCode 状态码
     * @param mixed $data         返回的数据
     */
    final protected function ok(int $responseCode, $data = '')
    {
        $this->result($responseCode, $data);
    }

    /**
     * 返回失败
     *
     * @param int    $responseCode 状态码
     * @param int    $errCode      失败码
     * @param string $message      错误消息
     * @param mixed  $data         额外数据
     */
    final protected function fail(int $responseCode, int $errCode, string $message, $data = '')
    {
        $this->result($responseCode, $data, $errCode, $message);
    }

    /**
     * 返回api数据
     *
     * @param int         $responseCode 状态码
     * @param mixed       $data         返回数据
     * @param int         $errCode      错误码
     * @param string|null $message      消息
     */
    final protected function result(int $responseCode, $data = '', int $errCode = null, string $message = null)
    {
        if (!headers_sent()) {
            http_response_code($responseCode);
            header('Content-Type: application/json');
        }
        if (is_null($errCode)) {
            $result = $data;
        } else {
            $result = [
                'err_code' => $errCode,
                'message'  => $message,
            ];
            if (is_array($data)) {
                $result = array_merge($result, $data);
            }
        }

        $response = Response::create($result, $this->responseType, $responseCode);

        throw new HttpResponseException($response);
    }
}
