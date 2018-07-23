<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午10:59
 */
namespace app\lib\exception;
use think\Exception;

class BaseException extends Exception
{
    //http  状态码 404 200,
    public $code=400;
    public $msg='参数错误';
    public $errorCode=10000; //通用类型错误

    /**
     * 构造函数
     * @param array  $params
     * @param  数组只应该包含code,msg,errorCode 且不为空
     */



    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->errorCode = $params['errorCode'];
        }
    }






}