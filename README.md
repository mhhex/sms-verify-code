# sms-verify-code

适用于 thinkphp >= 6.0.0 的短信验证码生成、缓存、验证类库

## 主要特性

* 支持 7 种验证码类型
* 基于 ThinkPHP 缓存
* 灵活的配置机制

## 安装

~~~php
composer require mhhex/sms-verify-code
~~~

## 使用文档

### 声明

~~~php
use mhhex\SmsVerifyCode;
~~~

### 生成验证码验证一

~~~php
// 生成验证码并缓存
// 默认生成 6 位数字验证码，默认获取前端输入的手机号字段名为 mobile
$code = (new SmsVerifyCode())->create();

// 验证短信验证码
// 默认获取前端输入的手机号字段名为 mobile，验证码字段名为 code
$smsVerifyCode = new SmsVerifyCode();
if(! $smsVerifyCode->check()){
    // 验证失败，获取失败信息
    $msg = $smsVerifyCode->getErrorMsg();
}
~~~

### 生成验证码验证二

~~~php
// 手动传入号码示例
$code = (new SmsVerifyCode())->mobile('18888888888')->create();

// 手动传入号码和验证码进行验证示例
$smsVerifyCode = new SmsVerifyCode();
$smsVerifyCode->mobile('18888888888')->code('123456')->check();
~~~

### 验证场景

~~~php
// 生成登录（login）场景的验证码并缓存
$code = (new SmsVerifyCode())->scene('login')->create();

// 验证登录（login）场景短信验证码
$smsVerifyCode = new SmsVerifyCode();
if(! $smsVerifyCode->scene('login')->check()){
    // 验证失败，获取失败信息
    $msg = $smsVerifyCode->getErrorMsg();
}
~~~

### 验证码类型

| type 值 | 验证码类型              |
|--------|--------------------|
| 1      | 纯数字型验证码            |
| 2      | 纯小写字母型验证码          |
| 3      | 纯大写字母型验证码          |
| 4      | 数字与小写字母混合型验证码      |
| 5      | 数字与大写字母混合型验证码      |
| 6      | 小写字母与大写字母混合型验证码    |
| 7      | 数字、小写字母和大写字母混合型验证码 |

~~~php
// 生成数字与大写字母混合型验证码并缓存
$code = (new SmsVerifyCode(['type'=>5]))->create();
~~~

### 动态配置

| 配置项        | 默认值    | 说明            |
|------------|--------|---------------|
| expire     | 180    | 验证码过期时间（秒）    |
| length     | 6      | 验证码长度         |
| type       | 1      | 验证码类型         |
| mobileName | mobile | 获取前端传入的手机字段名  |
| codeName   | code   | 获取前端传入的短信验证码名 |

~~~php
$config = ['type'=>1,'length'=>4];
$smsVerifyCode = new SmsVerifyCode($config);
~~~

## 版权信息

sms-verify-code遵循Apache2开源协议发布，并提供免费使用。
