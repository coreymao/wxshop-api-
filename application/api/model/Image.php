<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:38
 */
namespace app\api\model;
use app\api\model\BaseModel;


class Image extends BaseModel {


    protected $hidden=['id','from','create_time','update_time','delete_time'];

    public function getUrlAttr($value,$data){

        return $this->prefixImgUrl($value,$data);

    }



}