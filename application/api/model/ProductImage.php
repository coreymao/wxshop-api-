<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 上午12:25
 */

namespace app\api\model;

use app\api\model\BaseModel;

class ProductImage extends BaseModel
{
    protected $hidden = ['id','img_id', 'delete_time', 'product_id'];
    //  关联table Image
    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');

    }


}