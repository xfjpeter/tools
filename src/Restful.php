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

    /**
     * @var string 返回类型
     */
    protected $responseType = 'json';

    /**
     * 返回正确值
     *
     * @param int   $responseCode 状态码
     * @param array $data         返回的数据
     */
    protected function ok( int $responseCode, array $data )
    {
        $this->result( $responseCode, $data );
    }

    /**
     * 返回失败
     *
     * @param int    $responseCode 状态码
     * @param  int   $errCode      失败码
     * @param string $message      错误消息
     * @param array  $data         额外数据
     */
    protected function fail( int $responseCode, int $errCode, string $message, array $data = [] )
    {
        $this->result( $responseCode, $data, $errCode, $message );
    }

    /**
     * 返回api数据
     *
     * @param int         $responseCode 状态码
     * @param array       $data         返回数据
     * @param int         $errCode      错误码
     * @param string|null $message      消息
     */
    protected function result( int $responseCode, array $data = [], int $errCode = null, string $message = null )
    {
        if ( !headers_sent() )
        {
            http_response_code( $responseCode );
            header( 'Content-Type: application/json' );
        }
        if ( is_null( $errCode ) )
        {
            $result = $data;
        }
        else
        {
            $result = [
                'err_code' => $errCode,
                'message'  => $message
            ];
            $result = array_merge( $result, $data );
        }

        $response = Response::create( $result, $this->responseType, $responseCode );

        throw new HttpResponseException( $response );
    }
}
