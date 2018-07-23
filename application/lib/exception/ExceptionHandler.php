<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午10:54
 */
namespace app\lib\exception;

use Exception;
use think\exception\Handle;
use think\Request;
use  think\Log;

/**
 * 重写TP 异常类 最后所有异常处理方法通过render
 * 异常分 客户端用户行为异常    服务器端异常
 * Class ExceptionHandler
 * @package app\lib\exception
 */
class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //
    public function render(Exception $e)
    {
        if($e instanceof BaseException)
        {
            //如果是客户端请求发生异常 也就是自定义异常  返回客户端信息 $ex值来自BaseException 成员变量
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;

        }else{
            //处理服务器端异常
            //$switch=true;      // 把控制开关  写入配置文件
            if(config('app_debug'))
            {
                $this->recordErrorLog($e);
                return parent::render($e);  //调用tp5默认render方法 开启tp5 html调试页面

            }
            $this->code=500;
            $this->msg='这是服务器内部错误';
            $this->errorCode=999;
            $this->recordErrorLog($e);    //记录日志
        }

        $request=Request::instance();
        $result=[
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            'request_url'=>$request->url()

        ];
        return json($result,$this->code);

    }

    private function recordErrorLog($e){
        Log::init([
            'type'=>'File',     //记录日志需要初始化 config把日志初始化关闭了
            'path'=>LOG_PATH,
            'level'=>['error']
        ]);

        Log::record($e->getMessage(),'error');

    }








}
