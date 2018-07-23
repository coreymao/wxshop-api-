<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午8:58
 */
namespace app\api\validate;
use app\api\validate\BaseValidate;

class AppTokenGet extends BaseValidate
{


    protected $rule=[
        'ac'=>'require|isNotEmpty',
        'se'=>'require|isNotEmpty',

    ];








}