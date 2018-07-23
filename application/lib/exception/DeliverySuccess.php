<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午2:43
 */

namespace app\lib\exception;


class DeliverySuccess extends BaseException
{
    public $code = 201;
    public $msg = '发货成功';
    public $errorCode = 60000;
}