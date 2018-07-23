<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午3:43
 */

namespace app\api\service;

use think\Exception;

class AccessToken
{

    private $url;
    const TOKEN_CACHED_KEY = 'access';
    const TOKEN_EXPIRE_IN = 7000;


    public function __construct()
    {
        $url = config('wx.access_token_url');
        $this->url = sprintf($url, config('wx.app_id'), config('wx.app_secret'));
    }

    //微信access_token接口获取是有限制的 2000次/天
    public function get()
    {
        $accessToken = $this->getFromCache();
        if (!$accessToken) {
            return $this->getFromWXServer();
        } else {
            return $accessToken;
        }

    }


    public function getFromCache()
    {
        $accessToken = cache(self::TOKEN_CACHED_KEY);
        return $accessToken;

    }

    public function getFromWXServer()
    {
        $accessToken = curl_get($this->url);
        $accessToken = json_decode($accessToken, true);
        if (!$accessToken) {
            throw new Exception('获取access_token失败');
        }
        if (!empty($accessToken['errcode'])) {
            throw new Exception($accessToken['errmsg']);
        }
        $this->savedToCache($accessToken);
        return $accessToken['access_token'];

    }

    private function savedToCache($accessToken)
    {
        cache(self::TOKEN_CACHED_KEY, $accessToken, self::TOKEN_EXPIRE_IN);
    }


}