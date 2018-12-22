<?php
/**
 * @author johnxu <fsyzxz@163.com>
 *
 * @link   https://www.johnxu.net
 */

namespace johnxu\tool;

class Rsa
{
    private static $instance;

    /**
     * 获取对象
     *
     * @access public
     * @return Rsa
     */
    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * RSA数据签名
     *
     * @param String $data        需要签名的数据
     * @param String $private_key 需要签名的私钥
     * @param string $type        签名类型（RSA或RSA2）
     *
     * @return string 返回的签名串
     * @throws \Exception
     */
    public static function sign( String $data, String $private_key, $type = 'RSA' )
    {
        $search = [
            "-----BEGIN RSA PRIVATE KEY-----",
            "-----END RSA PRIVATE KEY-----",
            "\n",
            "\r",
            "\r\n",
        ];
        if ( is_file( $private_key ) )
        {
            $private_key = file_get_contents( $private_key );
        }
        else
        {
            $private_key = str_replace( $search, '', $private_key );
            $private_key = $search[0] . PHP_EOL . wordwrap( $private_key, 64, "\n", true ) . PHP_EOL . $search[1];
        }
        $res = openssl_pkey_get_private( $private_key );
        if ( $res )
        {
            $type == 'RSA' ? openssl_sign( $data, $signature, $res ) : openssl_sign( $data, $signature, $res, OPENSSL_ALGO_SHA256 );
            openssl_pkey_free( $res );
        }
        else
        {
            throw new \Exception( 'private key error !' );
        }

        return base64_encode( $signature );
    }

    /**验证签名是否正确
     *
     * @param String $data       需要验证签名的数据
     * @param String $public_key 签名的公钥
     * @param String $signature  签名串
     * @param string $type       签名类型（RSA或RSA2）
     *
     * @return bool 返回状态
     * @throws \Exception
     */
    public function check( String $data, String $public_key, String $signature, $type = 'RSA' )
    {
        $search = [
            "-----BEGIN PUBLIC KEY-----",
            "-----END PUBLIC KEY-----",
            "\n",
            "\r",
            "\r\n",
        ];
        if ( is_file( $public_key ) )
        {
            $public_key = file_get_contents( $public_key );
        }
        else
        {
            $public_key = str_replace( $search, '', $public_key );
            $public_key = $search[0] . PHP_EOL . wordwrap( $public_key, 64, "\n", true ) . PHP_EOL . $search[1];
        }
        $res = openssl_pkey_get_public( $public_key );
        if ( $res )
        {
            $result = $type == 'RSA'
                ? (bool) openssl_verify( $data, base64_decode( $signature ), $public_key )
                : (bool) openssl_verify( $data,
                    base64_decode( $signature ), $public_key, OPENSSL_ALGO_SHA256 );
            openssl_pkey_free( $res );
        }
        else
        {
            throw new \Exception( 'public key error or signature error or data error !' );
        }

        return $result;
    }
}
