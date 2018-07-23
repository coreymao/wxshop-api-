<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:38
 */

namespace app\api\model;
use app\api\model\BaseModel;

class ThirdApp extends BaseModel
{

    public static function check($ac,$se){
        $app=self::where('app_id','=',$ac)
            ->where('app_secret','=',$se)
            ->find();
        return $app;

    }





}