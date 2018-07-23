<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午8:58
 */

namespace app\api\validate;

class PagingParameter extends BaseValidate
{


    protected $rule = [
        'page' => 'require|isPositiveInteger',
    ];

    protected $message=[
        'page'=>'分页必须是正整数',

    ];


}