<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午8:58
 */

namespace app\api\validate;

class AddressNew extends BaseValidate
{


    // 为防止欺骗重写user_id外键
    // rule中严禁使用user_id
    // 获取post参数时过滤掉user_id,uid  [定义公用方法 客户端传入数据字段必须与$rule 字段一致]
    // 所有数据库和user关联的外键统一使用user_id，而不要使用uid
    protected $rule = [
        'name' => 'require|isNotEmpty',
        //'mobile' => 'require|isMobile',   //isMobile容易出问题
        'mobile' => 'require',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty',
    ];
    protected $message=[
        'mobile'=>'手机号码参数错误'
    ];


}