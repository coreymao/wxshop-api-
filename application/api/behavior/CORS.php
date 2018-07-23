<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午1:54
 */
namespace app\api\behavior;
use  think\response;

class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET,PUT');
        if(request()->isOptions()){
            exit();
        }
    }
}
