<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午11:41
 */

namespace app\api\model;

use app\api\model\BaseModel;


class Product extends BaseModel
{

    protected $hidden = ['delete_time', 'img_id','create_time', 'update_time', 'from', 'category_id', 'pivot'];


    //一对多 关联product_image 图片详情
    public function imgs()
    {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }
    //关联product_property 商品细节
    public function properties(){

        return $this->hasMany('ProductProperty','product_id','id');
    }
    //读取器补全 图片Url
    public function getMainImgUrlAttr($value, $data)
    {
        return $this->prefixImgUrl($value, $data);
    }


    //最近商品
    public static function getRecentProducts($count)
    {

        $products = self::limit($count)
            ->order('create_time desc')
            ->select();
        return $products;

    }


    //点击分类 获取分类下所有商品
    public static function getProductsByCategoryId($categoryID)
    {
        $products = self::where('category_id', '=', $categoryID)
                     ->select();
        return $products;

    }

    //单个商品的所有信息 无法对关联product_image表 order字段排序
/*    public static function getProductDetails($id){

        $productDetail=self::with(['imgs.imgUrl'])
            ->with(['properties'])
            ->find($id);
        return $productDetail;

    }*/

    public static function getProductDetails($id){

        $productDetail=self::with([
            'imgs'=>function($query){
                $query->with(['imgUrl'])
                 ->order('order','asc');
            }
        ])
            ->with(['properties'])
            ->find($id);
        return $productDetail;

    }



}