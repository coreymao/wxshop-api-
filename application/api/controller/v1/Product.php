<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 上午12:44
 */

namespace app\api\controller\v1;

use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use app\lib\exception\ProductException;
use think\Controller;
use app\api\model\Product as ProductModel;


class Product extends Controller
{

    /*
     * 获取指定数量商品模块
     * @param $count int
     * url /product/recent?count=..
     * return recent product
     */
    public function getRecent($count = 6)
    {

        (new Count())->goCheck();
        $product = ProductModel::getRecentProducts($count);
        if ($product->isEmpty()) {
            throw  new ProductException();
        }
        return $product->hidden(['summary']);
    }


    /**
     * url:  /product/:id
     * 获取商品详情
     */
    public function getOneProduct($id){
        (new IDMustBePositiveInt())->goCheck();
        $product=ProductModel::getProductDetails($id);
        if(empty($product)){
            throw new ProductException();
        }
        return $product->hidden(['summary']);

    }


    /*
    * 查找具体分类 旗下所有商品
    * url /by_category?id=1
    * @param $id int>0
    * @return $result
    * @throws MissException
    */
    public function getAllInCategory($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products=ProductModel::getProductsByCategoryId($id);
        if($products->isEmpty()){
            throw  new  MissException([
                'msg'=>'分类下商品不存在',
                'errorCode'=>50001
            ]);
        }

        return $products->hidden(['summary']);
    }

    //删除商品 令牌需要权限管理 权限分组 鉴别用户身份
    public function delOne(){

    }




}