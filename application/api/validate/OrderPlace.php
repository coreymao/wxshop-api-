<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午6:33
 */

namespace app\api\validate;

use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{

      //模拟客户端传入数据
//    protected $products=[
//        [
//            'product_id' =>1,
//            'count' =>3
//        ],
//        [
//            'product_id' =>2,
//            'count' =>31
//        ],
//        [
//            'product_id' =>3,
//            'count' =>32
//        ]
//
//    ];

    protected $rule = [
        'products' => 'checkProducts',
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];



    protected function checkProducts($value)
    {
        if ( empty($value) || !is_array($value)) {
            throw new ParameterException([
                'msg' => '传入参数格式不正确且不能为空'
            ]);
        }
        foreach ($value as $k => $v) {
            $this->checkProduct($v);
        }
        return true;

    }

    protected function checkProduct($v)
    {
        $validate=new BaseValidate($this->singleRule);
        $res=$validate->check($v);
        if(!$res){
            throw new ParameterException([
                'msg'=>'商品参数有错误必须为正整数'
            ]);

        }


    }


}
