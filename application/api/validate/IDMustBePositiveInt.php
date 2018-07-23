<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午5:55
 */
namespace app\api\validate;
use app\api\validate\BaseValidate;
use think\Validate;

class IDMustBePositiveInt extends BaseValidate {

    protected $rule = [
        'id'=>'require|isPositiveInteger',
    ];

    protected $message = [
        'id'=>'id需要是正整数'

    ];








}
