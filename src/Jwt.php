<?php
/**
 * | ---------------------------------------------------------------------------------------------------
 * | Author：johnxu <fsyzxz@163.com>.
 * | ---------------------------------------------------------------------------------------------------
 * | Home: https://www.johnxu.net.
 * | ---------------------------------------------------------------------------------------------------
 * | Data: 2019-04-16
 * | ---------------------------------------------------------------------------------------------------
 * | Desc:
 * | ---------------------------------------------------------------------------------------------------
 */

namespace johnxu\tool;

class Jwt
{
    private static $instance = null;

    private $config = [
        'header' => [
            'alg' => 'HS256',
            'typ' => 'jwt',
        ],
        'key'    => 'fsyzxz@163.com',
    ];

    private $algs = [
        'SH256' => 'sha256',
    ];

    /**
     * Jwt constructor.
     * @param array $config
     */
    private function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param array $config
     * @return Jwt
     */
    public static function getInstance(array $config = []): Jwt
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * 获取token
     * @param array $payload 参数
     * @return string
     * @example
     *                       [
     *                       'iss' => 'johnxu', // 该jwt的签发者
     *                       'iat' => time(), // 签发时间
     *                       'exp' => time() + 7200, // 过期时间
     *                       'nbf' => time() + 60, // 该时间之前不接收处理该Token
     *                       'sub' => 'www.johnxu.net', // 面向的用户
     *                       'jti' => md5(uniqid('jwt').time()) // 该token的唯一值
     *                       ]
     */
    public function getToken(array $payload)
    {
        $token = $this->getSplicing($payload);

        return sprintf('%s.%s', $token, $this->signature($token));
    }

    /**
     * @param array $payload
     * @return string
     */
    public function getSplicing(array $payload)
    {
        return sprintf('%s.%s',
            $this->safeBase64Encode(json_encode($this->getHeader(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            $this->safeBase64Encode(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
    }

    /**
     * @param string $input
     * @return string
     */
    public function safeBase64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * @param string $input
     * @return string
     */
    public function safeBase64Decode(string $input): string
    {
        if ($remainder = strlen($input) % 4) {
            $input .= str_repeat('=', (4 - $remainder));
        }

        return base64_decode(
            strtr($input, '-_', '+/')
        );
    }

    /**
     * @param string $input
     * @param string $alg
     * @return string
     */
    public function signature(string $input, string $alg = 'SH256'): string
    {
        // TODO: 这里可以分流
        return $this->safeBase64Encode(hash_hmac($this->algs[$alg], $input, $this->getKey(), true));
    }

    /**
     * @param string $token
     * @return bool|mixed
     */
    public function verify(string $token)
    {
        $token = explode('.', $token);
        if (count($token) < 3) {
            return false;
        }
        list($headerBase64URLEncodeStr, $payloadBase64URLEncodeStr, $sign) = $token;
        $header  = json_decode($this->safeBase64Decode($headerBase64URLEncodeStr), true);
        $payload = json_decode($this->safeBase64Decode($payloadBase64URLEncodeStr), true);
        // 验证是否有header参数中是否有alg
        if (!isset($header['alg'])) {
            return false;
        }
        // 进行hash判断
        if ($this->signature($headerBase64URLEncodeStr.'.'.$payloadBase64URLEncodeStr) !== $sign) {
            return false;
        }

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()) {
            return false;
        }

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()) {
            return false;
        }

        return $payload;
    }

    /**
     * @param array $key
     * @return Jwt
     */
    public function setKey(array $key): Jwt
    {
        $this->config['key'] = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->config['key'];
    }

    /**
     * @param array $header
     * @return Jwt
     */
    public function setHeader(array $header): Jwt
    {
        $this->config['header'] = array_merge($this->config['header'], $header);

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->config['header'];
    }
}
