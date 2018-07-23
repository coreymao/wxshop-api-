<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:23
 */
namespace app\api\model;
use app\api\model\BaseModel;

class BannerItem extends BaseModel {
    protected $hidden=['id','banner_id','img_id','create_time','update_time','delete_time'];

    public function img(){

        return $this->belongsTo('Image','img_id','id');

    }






}