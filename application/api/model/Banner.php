<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午3:52
 */
namespace app\api\model;
use app\api\model\BaseModel;

class Banner extends BaseModel{


    protected $hidden=['create_time','update_time','delete_time'];
    //建立模型关系
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerById($id){
        $banner=self::with(['items','items.img'])
            ->find($id);
        return $banner;

    }



}
