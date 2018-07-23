<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 上午11:18
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use think\Controller;
use app\api\model\Category as CategoryModel;
use app\api\model\Product as ProductModel;


class  Category extends Controller
{


    /**
     * 获取所有的分类,不包含分类下商品
     * url:  /category/all
     */
    public function getAllCategories()
    {
        $categories = CategoryModel::all([], 'img');
        if ($categories->isEmpty()) {
            throw new MissException([
                'msg' => '分类不存在',
                'errorCode' => 50000
            ]);
        }
        return $categories;

    }




}