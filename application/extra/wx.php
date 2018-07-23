<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 下午3:01
 */

return [

    'app_id'=>'你的appid',                     //AppID
    'app_secret'=>'你的appsecert',   //AppSecret
    'login_url'=>"https://api.weixin.qq.com/sns/jscode2session?".
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
    'access_token_url'=>'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',





];