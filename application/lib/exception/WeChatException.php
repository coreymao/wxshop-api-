<?php
/**
 * Created by PhpStorm.
 * User: 七月
 * Date: 2017/2/12
 * Time: 18:29
 */

namespace app\lib\exception;


//code码 错误  微信返回失败信息
class WeChatException extends BaseException
{
    public $code = 400;
    public $msg = "微信接口调用失败";
    public $errorCode = 999;
}