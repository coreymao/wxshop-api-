<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/19
 * Time: 下午1:37
 */

namespace app\api\service;
use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{
    public function get($ac, $se)
    {
        $app = ThirdApp::check($ac, $se);
        if(!$app)
        {
            throw new TokenException([
                'msg' => '授权失败',
                'errorCode' => 10004
            ]);
        }
        else{
            $scope = $app->scope;  //权限
            $uid = $app->id;
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    /**保存到缓存
     * @param $values
     * @return string
     * @throws TokenException
     */
    private function saveToCache($values){
        $token = self::generateToken();    //32 位随机数 token令牌
        $expire_in = config('setting.token_expire_in');
        $result = cache($token, json_encode($values), $expire_in);
        if(!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }







}