<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午3:52
 */

namespace app\api\model;

class Category extends BaseModel
{


    protected $hidden = ['topic_img_id', 'create_time', 'update_time', 'delete_time'];

    //关联 Image
    public function img()
    {

        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
    //关联 Product
    public function Product()
    {
        return $this->hasMany('Product','category_id','id');

    }





}
