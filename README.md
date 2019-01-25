# 字符串操作

## 截取字符串
```php
<?php
use johnxu\tool\Str;
$res = Str::instance()->cut('http://www.johnxu.net', 0, 3);
var_dump($res); // htt
```

## 转换编码
```php
<?php
use johnxu\tool\Str;
$res = Str::instance()->encoding('我们是好朋友', 'GBK');
var_dump($res); // �����Ǻ�����

var_dump(Str::instance()->encoding('�����Ǻ�����', 'UTF-8')); // 我们是好朋友
```

## 内置方法
- cut(string, start , length) 截取字符串
- lower(string) // 转换小写
- upper(string) // 转换大小
- has(string, neddle) // 查找是否存在该字符串
- remove(string, neddle) // 去除字符串
- reverse(string) // 翻转字符串
- arrayToString(array) // 将数组转换成字符串
- stringToArray(string) // 将字符串转换成数组
- ucwords(string) // 将字符串每个单子首字母大写
- ucfirst(string) // 将字符首字母大写
- generateTradeNo(length) // 创建订单号\
- getMachineCode(); // 获取机器码：8E8363C1-094E0EDC-7D67C393
- generateUid(); // 创建用户ID

## RSA生成签名串
```php
<?php
use johnxu\tool\Rsa;
$data = '这是要签名的数据，字符串类型';
$private_key = ''; // 有两种方式，第一种是文件，写入文件路径即可；第二种是字符串，填写密钥字符串
$sign = Rsa::getInstance()->sign($data, $private_key); // 返回的签名后的base64字符串
```

## RSA校验签名
```php
<?php
use johnxu\tool\Rsa;
$sign = ''; // 签名串
$public_key = ''; // 同private_key
$data = '这是要签名的数据，字符串类型';
$result = Rsa::getInstance()->sign($data, $public_key, $sign); // 返回验签的结果
```

## Config配置是用

```php
<?php
$config = array(
	'wx' => array(
		'appid' => 1234,
		'key' => 5678
	),
	'site_url' => 'http://www.johnxu.net'
);
// 批量设置
johnxu\tool\Config::batch($config);

// 单独设置
johnxu\tool\Config::set('wx', $config['wx']);

// 取值
johnxu\tool\Config::get('wx');
johnxu\tool\Config::get('wx.appid');
```

## http请求的使用

```php
<?php 
use johnxu\tool\Http;

// get请求
$response = Http::getInstance()->request('http://www.baidu.com');

if ($response->getCode() == 200) {
	var_dump($response->get('data'));// 原始数据
	var_dump($response->getContent(false)); // 同上
	var_dump($response->getContent()); // json解析后的数据（数组）
}
```

## RESTFul风格的接口（只支持tp5）
```php
<?php
namespace app\api\controller;
use johnxu\tool\Restful;
class Users extends Restful
{
    // 模拟用户组
	private $users
		= array(
			array(
				'uid'           => 1,
				'authorization' => false,
				'nickname'      => 'tom',
				'age'           => 25,
				'sex'           => 'male',
				'email'         => 'tom@163.com'
			),
			array(
				'uid'           => 2,
				'authorization' => false,
				'nickname'      => 'Alice',
				'age'           => 25,
				'sex'           => 'female',
				'email'         => 'Alice@163.com'
			),
			array(
				'uid'           => 3,
				'authorization' => true,
				'nickname'      => 'peter',
				'age'           => 30,
				'sex'           => 'male',
				'email'         => 'peter@163.com'
			),
		);

	// 获取所有的用户
	public function index()
	{
		$this->ok( Restful::HTTP_RESPONSE_OK, $this->users );
	}

	// 获取指定用户的信息
	public function get( $uid )
	{
		foreach ( $this->users as $user )
		{
			if ( $user['uid'] == $uid )
			{
				if ( $user['authorization'] == false )
				{
				    // {"uid":2,"authorization":false,"nickname":"Alice","age":25,"sex":"female","email":"Alice@163.com"}
					$this->ok( Restful::HTTP_RESPONSE_OK, $user );
				}
				else
				{
				    // {"err_code":2001,"message":"UnAuthorization"}
					$this->fail( Restful::HTTP_RESPONSE_UN_AUTHORIZATION, 2001, 'UnAuthorization' );
				}
			}
		}

		// {"err_code":1001,"message":"Not Found"}
		$this->fail( Restful::HTTP_RESPONSE_NOT_FOUND, 1001, 'Not Found' );
	}
}
```
```php
<?php
// 定义路由 route.php
Route::group( 'v1', function () {
    Route::group( 'users', function () {
        Route::get( '/', 'api/users/index' );

        Route::get( '/:uid', 'api/users/get' );
    } );
} );

// 访问：
// 获取所有用户：GET http://localhost/v1/useres
// 获取指定用户：GET http://localhost/v1/users/1
```

## RSA签名和验签
```php
<?php
use johnxu\tool\Rsa;

$publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/ykZnOUb0ro7WaraW+aPyNCgZgJyvr2CIQhTLRvOeOW4Ba8FaudwWACL0QFfIjW+V3rqMG0SPHGIitHUXIH0tzQZoKp8FfZVQOZxNyBaIjwzgSvuZuBGYZ/rrH53158t7gt58IIHGxcfJehhex/0bk8rUAO2U5kKGKwvEbDMOXwIDAQAB';
$privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAL/KRmc5RvSujtZqtpb5o/I0KBmAnK+vYIhCFMtG8545bgFrwVq53BYAIvRAV8iNb5XeuowbRI8cYiK0dRcgfS3NBmgqnwV9lVA5nE3IFoiPDOBK+5m4EZhn+usfnfXny3uC3nwggcbFx8l6GF7H/RuTytQA7ZTmQoYrC8RsMw5fAgMBAAECgYA1T5ucS86OawsnTMhuVPweciMHW7myGBTEK2IuUw2a2KncWUCI4IrtzqHotQ3xoGb5CM1f7qBzC1e3/+NgR1aj7laAXtg/S1mfJISMIoXkUi/9q+4GwbbaU/vkYyhqnoAa5tL7/X4wuRlWtc7tC9TyqS+EXOa990SZOpuHjqpCiQJBAOLV1+ISWa30MMFWeTD5L6SP6BAE2mrgt6ZaRL2hGUp9QxWX2o89/cne4FEa0RrI1gLfOYHEoHhw5lqSMHHpGJMCQQDYcvBp7idMecg9+NcI1F+P1EDtNsMgpiXmAdV8sNpv/TbhzarLHh7bTnHrg7e2QRhUBuoZmU8Rx3FsUu41Qc6FAkBQyZKKvLhd4QNgSFj/XTBfrrUax2+28vPVdn7W/sJQKk6zKRM5Qv3ZYNyJZkClBnRaL4B+vDXez27rQPeqCjerAkAv9+kH0NusuyCBe3BMaKR0/5kT+RrtVWT4wFdLtvXx87ACAs5jDV3RRGVCyIIiRfLaTF39Jli7m/OrCgX4j4jxAkEAspLOMeD7ZH97cR+Mi3iHnweCQ86tZ6UNCc2+pC7murjpZs+fBP/zDeLTjYmX22QZb1KvPTeAnKkQ2OuyUdTN/w==';

// 私钥签名
$signature = Rsa::signature('123', $privateKey, 'RSA2');
var_dump($signature);

// 公钥验签
$flag = Rsa::verify($signature, $publicKey, 'RSA2');
var_dump($flag);
```

## RSA加密与解密
```php
<?php
use johnxu\tool\Rsa;

$publicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC/ykZnOUb0ro7WaraW+aPyNCgZgJyvr2CIQhTLRvOeOW4Ba8FaudwWACL0QFfIjW+V3rqMG0SPHGIitHUXIH0tzQZoKp8FfZVQOZxNyBaIjwzgSvuZuBGYZ/rrH53158t7gt58IIHGxcfJehhex/0bk8rUAO2U5kKGKwvEbDMOXwIDAQAB';
$privateKey = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAL/KRmc5RvSujtZqtpb5o/I0KBmAnK+vYIhCFMtG8545bgFrwVq53BYAIvRAV8iNb5XeuowbRI8cYiK0dRcgfS3NBmgqnwV9lVA5nE3IFoiPDOBK+5m4EZhn+usfnfXny3uC3nwggcbFx8l6GF7H/RuTytQA7ZTmQoYrC8RsMw5fAgMBAAECgYA1T5ucS86OawsnTMhuVPweciMHW7myGBTEK2IuUw2a2KncWUCI4IrtzqHotQ3xoGb5CM1f7qBzC1e3/+NgR1aj7laAXtg/S1mfJISMIoXkUi/9q+4GwbbaU/vkYyhqnoAa5tL7/X4wuRlWtc7tC9TyqS+EXOa990SZOpuHjqpCiQJBAOLV1+ISWa30MMFWeTD5L6SP6BAE2mrgt6ZaRL2hGUp9QxWX2o89/cne4FEa0RrI1gLfOYHEoHhw5lqSMHHpGJMCQQDYcvBp7idMecg9+NcI1F+P1EDtNsMgpiXmAdV8sNpv/TbhzarLHh7bTnHrg7e2QRhUBuoZmU8Rx3FsUu41Qc6FAkBQyZKKvLhd4QNgSFj/XTBfrrUax2+28vPVdn7W/sJQKk6zKRM5Qv3ZYNyJZkClBnRaL4B+vDXez27rQPeqCjerAkAv9+kH0NusuyCBe3BMaKR0/5kT+RrtVWT4wFdLtvXx87ACAs5jDV3RRGVCyIIiRfLaTF39Jli7m/OrCgX4j4jxAkEAspLOMeD7ZH97cR+Mi3iHnweCQ86tZ6UNCc2+pC7murjpZs+fBP/zDeLTjYmX22QZb1KvPTeAnKkQ2OuyUdTN/w==';

// 公钥加密 === 私钥解密
// 公钥加密
$crypted = Rsa::encrypt('123', $publicKey);
var_dump($crypted);

// 私钥解密
$decrypted = Rsa::decrypt($crypted, $privateKey);
var_dump($decrypted);

// 私钥加密 === 公钥解密
// 私钥加密
$crypted = Rsa::encrypt('123', $privateKey, false);
var_dump($crypted);

// 公钥解密
$decrypted = Rsa::decrypt($crypted, $publicKey, false);
var_dump($decrypted);
```

## 用户授权管理
```php
<?php

class Users extends \johnxu\tool\Api
{
    public function authorization()
    {
	  parent::authorization(); // 继承就行了
	}
	
	public function register()
	{
	  parent::register(); // 继承就行了
	}
}
```
