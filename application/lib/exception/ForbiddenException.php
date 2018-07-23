<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午11:03
 */
namespace app\lib\exception;
class ForbiddenException extends BaseException
{
    public $code=401;           //未授权
    public $msg='您权限不行';   //初始化可以被覆盖
    public $errorCode=10001;          //初始化可以被覆盖



}