# 字符串操作

## 截取字符串
```php
$res = Str::instance()->cut('http://www.johnxu.net', 0, 3);
var_dump($res); // htt
```

## 转换编码
```php
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
- generateTradeNo(length) // 创建订单号

## RSA生成签名串
```php
$data = '这是要签名的数据，字符串类型';
$private_key = ''; // 有两种方式，第一种是文件，写入文件路径即可；第二种是字符串，填写密钥字符串
$sign = Rsa::getInstance()->sign($data, $private_key); // 返回的签名后的base64字符串
```

## RSA校验签名
```php
$sign = ''; // 签名串
$public_key = ''; // 同private_key
$data = '这是要签名的数据，字符串类型';
$result = Rsa::getInstance()->sign($data, $public_key, $sign); // 返回验签的结果
```