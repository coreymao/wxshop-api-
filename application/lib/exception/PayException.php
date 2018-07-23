<?php


namespace app\lib\exception;


class PayException extends BaseException
{
    public $code = 404;
    public $msg = '支付失败';
    public $errorCode = 80000;
}