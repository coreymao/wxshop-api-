<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午11:03
 */
namespace app\lib\exception;
class BannerMissException extends BaseException
{
    public $code=404;  //无网络资源
    public $msg='请求banner不存在';
    public $errorCode=40000;




}