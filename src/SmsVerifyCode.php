<?php


namespace mhhex;

class SmsVerifyCode
{
    protected $config = [];

    protected $cachePrefix = 'sms';
    // 验证码字符池
    protected $character = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // 验证码过期时间（s），默认 3 分钟
    protected $expire = 180;
    // 验证码位数
    protected $length = 6;
    // 验证码类型
    protected $type = 1;
    // 验证码
    protected $code = '';
    // 场景
    protected $scene = '';
    // 错误信息
    protected $error = '';
    // 手机号字段名
    protected $mobileName = 'mobile';
    // 验证码字段名
    protected $codeName = 'code';
    // 手动传入手机号
    protected $_mobile;
    // 手动传入验证码
    protected $_code;

    /**
     * 架构方法，动态配置
     * TpSms constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = config('tpsms');
        }
        $this->config = $config;
        foreach ($config as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * 设置场景值
     * @param string $scene
     * @return $this
     */
    public function scene(string $scene): SmsVerifyCode
    {
        $this->scene = $scene;
        return $this;
    }

    /**
     * 手动传入手机号
     * @param string $mobile
     * @return $this
     */
    public function mobile(string $mobile): SmsVerifyCode
    {
        $this->_mobile = $mobile;
        return $this;
    }

    /**
     * 手动传入验证码
     * @param string $code
     * @return $this
     */
    public function code(string $code): SmsVerifyCode
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * 生成验证码
     * @return string
     * @throws \Exception
     */
    public function create(): string
    {
        $mobile = $this->_mobile ?? input($this->mobileName);

        if (!$mobile) {
            throw new \think\Exception('未传入手机号');
        }

        switch ($this->type) {
            case 1:
                // 纯数字型验证码
                $range = [0, 9];
                break;
            case 2:
                // 纯小写字母型验证码
                $range = [10, 35];
                break;
            case 3:
                // 纯大写字母型验证码
                $range = [36, 61];
                break;
            case 4:
                // 数字与小写字母混合型验证码
                $range = [0, 35];
                break;
            case 5:
                // 数字与大写字母混合型验证码
                $this->character = strtoupper($this->character);
                $range = [0, 35];
                break;
            case 6:
                // 小写字母与大写字母混合型验证码
                $range = [10, 61];
                break;
            case 7:
                // 数字、小写字母和大写字母混合型验证码
                $range = [0, 61];
                break;
            default:
                // 报错：不支持的验证码类型
                throw new \think\Exception('不支持的验证码类型');
        }
        // 拼接验证码
        for ($i = 0; $i < $this->length; $i++) {
            $this->code .= $this->character[random_int($range[0], $range[1])];
        }
        // 缓存
        $cacheKey = $this->cachePrefix . $this->scene . $mobile;
        // 增加ip验证
        $cacheVal = $this->code . ip2long(request()->ip());
        cache($cacheKey, $cacheVal, $this->expire);
        return $this->code;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrorMsg(): string
    {
        return $this->error;
    }

    /**
     * 验证码验证
     * @return bool
     */
    public function check(): bool
    {
        $mobile = $this->_mobile ?? input($this->mobileName);
        if (!$mobile) {
            throw new \think\Exception('未传入手机号');
        }
        $code = $this->_code ?? input($this->codeName);
        if (!$code) {
            throw new \think\Exception('未传入验证码');
        }
        // 获取缓存验证码
        $cacheCode = cache($this->cachePrefix . $this->scene . $mobile);
        if ($cacheCode) {
            // 增加ip验证
            if ($cacheCode === $code . ip2long(request()->ip())) {
                return true;
            }
            $this->error = '验证码不正确';
            return false;
        } else {
            $this->error = '验证码无效或已过期';
            return false;
        }
    }
}
