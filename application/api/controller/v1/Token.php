<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 下午2:31
 */

namespace app\api\controller\v1;

use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\MissException;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;

class  Token
{
    //小程序登陆用获取token
    public function getToken($code = '')
    {
        //携带code appid appSecret http请求微信服务器获取 openid,session_key,
        (new TokenGet())->goCheck();
        $userToken = new UserToken($code);
        $token = $userToken->get();
        if (!$token) {
            throw new MissException([
                'msg' => 'Token不存在'
            ]);
        }
        return [
            'token' => $token
        ];

    }


    /**
     *cms 第三方登陆获取令牌
     * @url /app_token
     * @post
     */
    public function getAppToken($ac = '', $se = '')
    {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');        //解决跨越问题
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        return [
            'token' => $token
        ];

    }

    /**
     * 验证token令牌是否有效
     * @param string 'token'
     * @return array  true 有效够 false 失效
     * @throws ParameterException
     */
    public function verifyToken($token = '')
    {
        if (!$token) {
            throw  new ParameterException([
                'msg' => 'token不能为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return [
            'isValid' => $valid
        ];


    }


}