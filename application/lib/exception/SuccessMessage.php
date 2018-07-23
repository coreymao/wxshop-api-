<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午2:43
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code = 201;
    public $msg = '添加地址或者修改地址成功';
    public $errorCode = 60000;
}