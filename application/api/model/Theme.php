<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:38
 */

namespace app\api\model;

use app\api\model\BaseModel;

class Theme extends BaseModel
{


    protected $hidden = ['head_img_id', 'topic_img_id', 'create_time', 'update_time', 'delete_time'];

    public function topicImg()
    {
        return $this->belongsTo('Image', 'topic_img_id', 'id');

    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    //关联Product 中间表theme_product
    public function products(){

        return $this->belongsToMany('Product','theme_product','product_id','theme_id');
    }



    public static function getThemeWithProducts($id)
    {
        $theme= self::with([ 'products','topicImg','headImg' ])->find($id);
        return $theme;

    }


}