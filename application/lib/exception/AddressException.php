<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午11:03
 */
namespace app\lib\exception;
class AddressException extends BaseException
{
    public $code=404;  //无网络资源
    public $msg='请求地址不存在';   //初始化可以被覆盖
    public $errorCode=60000;          //初始化可以被覆盖



}