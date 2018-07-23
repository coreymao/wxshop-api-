<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/10
 * Time: 下午9:01
 */
namespace app\api\controller;
use think\Controller;
use app\api\service\Token as TokenService;

class BaseController extends Controller{

    //用户和 管理员 权限
    public function checkPrimaryScope()
    {
        TokenService::needPrimaryScope();

    }
    //用户专用权限
    public function checkExclusiveScope()
    {
        TokenService::needExclusiveScope();

    }




}