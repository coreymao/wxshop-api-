<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午8:58
 */
namespace app\api\validate;
use app\api\validate\BaseValidate;

class IDCollection extends BaseValidate
{


    protected $rule=[
        'ids'=>'require|checkIDs'
    ];

    protected $message = [
        'ids'=>'ids参数必须用逗号隔开',
        'ids.require'=>'必须存在'

    ];

    //ids=id1,id2,id3...
    public function checkIDs($value){
        $values=explode(',',$value);
        if(empty($values)){
            return false;
        }
        foreach ($values as $k=>$v){
            if(!$this->isPositiveInteger($v)){
                //如果不是正整数
                return false;
            }
        }

        return true;

    }






}