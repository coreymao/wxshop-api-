<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 下午7:02
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    //生成令牌  作为cache的 $key $timestamp时间戳  Salt 类似加密文件
    public static function generateToken()
    {
        $randChar = getRandChar(32);  //32随机数
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];  //当前时间戳
        $tokenSalt = config('secure.token_salt'); // 加密
        return md5($randChar . $timestamp . $tokenSalt);

    }

    //【判断令牌是否过期】根据传入token 获取values值
    public static function getTokenValues($key)
    {
        //从令牌获取  和客户端约定 Token在header 传入
        //令牌作为Key  读取缓存
        $token = Request::instance()->header('token');        //获取token
        $values = Cache::get($token);      // 读取缓存值 为字符串 含有效期 需要判空
        if (!$values) {
            throw  new TokenException([
                'msg' => '获取token值失败,token失效，过期'
            ]);
        }
        if (!is_array($values)) {
            $values = json_decode($values, true);
        }
        if (array_key_exists($key, $values)) {
            return $values[$key];
        } else {
            throw  new  Exception('获取Token对应值失败');
        }

    }

    //获取用户uid
    public static function getCurrentUID()
    {
        $uid = self::getTokenValues('uid');
        return $uid;

    }

    // 用户与cms管理员都可以访问权限
    public static function needPrimaryScope()
    {
        $scope = self::getTokenValues('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {  //16   ==
                return true;
            } else {
                throw new ForbiddenException();
            }

        } else {
            throw  new  TokenException();
        }

    }

    //只有用户才可以访问接口权限
    public static function needExclusiveScope()
    {
        $scope = self::getTokenValues('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }

        } else {

            throw  new  TokenException();
        }

    }

    /**
     * 检查操作UID是否合法
     * @param $checkedUID
     * @return bool
     * @throws Exception
     * @throws ParameterException
     */
    public static function isValidOperate($checkedUID)
    {
        //order  user_id
        if (!$checkedUID) {
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if ($currentOperateUID == $checkedUID) {
            return true;
        }
        return false;
    }

    /**
     *
     * 验证token是否存在
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        $exits = Cache::get($token);
        if ($exits) {
            return true;
        } else {
            return false;
        }


    }






}