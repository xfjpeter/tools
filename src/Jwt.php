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

use johnxu\tool\exception\InvalidParamException;
use johnxu\tool\exception\InvalidSignatureException;
use johnxu\tool\exception\InvalidVerifyException;

class Jwt
{
    /**
     * @var string 签发人
     */
    private $iss;
    /**
     * @var int 过期时间
     */
    private $exp;
    /**
     * @var string 主题
     */
    private $sub;
    /**
     * @var string 受众
     */
    private $aud;
    /**
     * @var int 生效时间
     */
    private $nbf;
    /**
     * @var int 签发时间
     */
    private $iat;
    /**
     * @var string 编号
     */
    private $jti;
    /**
     * @var mixed 额外参数
     */
    private $payload;
    /**
     * @var string 加密密钥
     */
    private $key = 'fsyzxz@163.com';
    /**
     * @var array jwt 头部信息
     */
    private $header = [
        'alg' => 'HS256',
        'typ' => 'jwt',
    ];
    /**
     * @var array 加密规则映射
     */
    private $keyMap = [
        'HS256' => 'sha256',
    ];

    /**
     * 获取token
     * @return string
     * @throws InvalidParamException
     */
    public function getToken()
    {
        if (!is_array($this->getHeader()) || !isset($this->header['alg'])) {
            throw new InvalidParamException('加密类型错误');
        }

        $token = sprintf('%s.%s',
            $this->baseSafeEncode(json_encode($this->getHeader(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            $this->baseSafeEncode(json_encode($this->getPayload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));

        return sprintf('%s.%s', $token, $this->makeSignature($token));
    }

    /**
     * 验证token
     * @param string $token
     * @return bool|mixed
     * @throws InvalidSignatureException
     * @throws InvalidVerifyException
     */
    public function verify(string $token)
    {
        $token = explode('.', $token);
        if (count($token) < 3) {
            return false;
        }
        list($headerBase64URLEncodeStr, $payloadBase64URLEncodeStr, $sign) = $token;
        $header  = json_decode($this->baseSafeDecode($headerBase64URLEncodeStr), true);
        $payload = json_decode($this->baseSafeDecode($payloadBase64URLEncodeStr), true);
        foreach ($payload as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{'set' . ucfirst($key)}($val);
            }
        }
        // 验证签名是否正确
        // 进行hash判断
        if ($this->makeSignature($headerBase64URLEncodeStr . '.' . $payloadBase64URLEncodeStr) !== $sign) {
            throw new InvalidSignatureException('签名不正确');
        }
        // 验证生效期、有效期
        $this->checkPayload();

        return $this->getPayload();
    }

    /**
     * 安全加密的base64字符串
     * @param string $input
     * @return mixed
     */
    private function baseSafeEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * 安全解析base64字符串
     * @param string $input
     * @return bool|string
     */
    private function baseSafeDecode(string $input)
    {
        if ($remainder = strlen($input) % 4) {
            $input .= str_repeat('=', (4 - $remainder));
        }

        return base64_decode(
            strtr($input, '-_', '+/')
        );
    }

    /**
     * 生成签名串
     * @param string $token
     * @return mixed
     */
    private function makeSignature(string $token)
    {
        $alg = $this->keyMap[$this->header['alg']];

        return $this->baseSafeEncode(hash_hmac($alg, $token, $this->getKey(), true));
    }

    /**
     * token生效期、有效期验证
     * @throws InvalidVerifyException
     */
    private function checkPayload()
    {
        // 验证成效时间
        if ($this->getNbf() && time() <= $this->getNbf()) {
            throw new InvalidVerifyException('该token还没到生效期');
        }
        // 验证过期没有
        if ($this->getExp() && time() >= $this->getExp()) {
            throw  new  InvalidVerifyException('该token已经失效');
        }
    }

    /**
     * @return mixed
     */
    public function getIss()
    {
        return $this->iss;
    }

    /**
     * 签发人
     * @param mixed $iss
     */
    public function setIss($iss)
    {
        $this->iss = $iss;
    }

    /**
     * @return mixed
     */
    public function getExp()
    {
        return $this->exp;
    }

    /**
     * 过期时间
     * @param mixed $exp
     */
    public function setExp($exp)
    {
        $this->exp = $exp;
    }

    /**
     * @return mixed
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * 主题
     * @param mixed $sub
     */
    public function setSub($sub)
    {
        $this->sub = $sub;
    }

    /**
     * @return mixed
     */
    public function getAud()
    {
        return $this->aud;
    }

    /**
     * 受众
     * @param mixed $aud
     */
    public function setAud($aud)
    {
        $this->aud = $aud;
    }

    /**
     * @return mixed
     */
    public function getNbf()
    {
        return $this->nbf;
    }

    /**
     * 生效时间
     * @param mixed $nbf
     */
    public function setNbf($nbf)
    {
        $this->nbf = $nbf;
    }

    /**
     * @return mixed
     */
    public function getIat()
    {
        return $this->iat;
    }

    /**
     * 签发时间
     * @param mixed $iat
     */
    public function setIat($iat)
    {
        $this->iat = $iat;
    }

    /**
     * @return mixed
     */
    public function getJti()
    {
        return $this->jti;
    }

    /**
     * 编号
     * @param mixed $jti
     */
    public function setJti($jti)
    {
        $this->jti = $jti;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        $payload = [
            'iat'     => $this->getIat(),
            'aud'     => $this->getAud(),
            'exp'     => $this->getExp(),
            'sub'     => $this->getSub(),
            'iss'     => $this->getIss(),
            'nbf'     => $this->getNbf(),
            'jti'     => $this->getJti(),
            'payload' => $this->payload,
        ];

        return $payload;
    }

    /**
     * 额外数据
     * @param mixed $payload
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param array $header
     */
    public function setHeader(array $header)
    {
        $this->header = $header;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }
}
