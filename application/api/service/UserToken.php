<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 下午2:46
 * 这是用户令牌
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\MissException;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;  //http url地址 向微信服务器发请求

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);

    }


    public function get()
    {
        $result = curl_get($this->wxLoginUrl);   //http请求 code不合法  微信会返回空值 请求无论成功失败微信返回status=200;
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw  new Exception('获取openid与seesion_key异常,微信内部错误');
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);    //有errcode 微信返回错误 把结果抛给客户端
            } else {
                return $this->grantToken($wxResult);
            }

        }

    }

    //异常错误
    private function processLoginError($wxResult)
    {
        throw new WeChatException([
            'msg' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);

    }


    //生成令牌
    private function grantToken($wxResult)
    {
        //拿到openid
        //查找数据库 如果openid不存在 把openid写入数据库,存在不做处理
        //生成令牌，准备缓存数据 写入缓存 返回令牌
        //key:令牌
        //value: wxResult ,uid , scope
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);  //读取数据库
        if (!$user) {
            //return $user['nickname'];   // 能返回值
            $uid = UserModel::newUser($openid);

        } else {
            $uid = $user->id;
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;

    }


    //准备缓存参数 value
    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;                      //用户uid
        $cachedValue['scope'] = ScopeEnum::User;      //scope 用户权限
        //$cachedValue['scope'] = 14;
        return $cachedValue;
    }


    //写入缓存
    private function saveToCache($cacheValue)
    {
        $key = self::generateToken();                              // 随机数 md5加密
        $value = json_encode($cacheValue);                        //数组转化字符串 缓存值是字符串
        $expire_in = config('setting.token_expire_in');     //缓存有效时间
        $result = cache($key, $value, $expire_in); //写入缓存
        if (!$result) {
            throw new TokenException([
                'msg' => '服务器异常,没有成功写入缓存',
                'errorCode' => '1005'
            ]);
        }
        return $key;

    }


}