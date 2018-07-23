<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午3:50
 */

namespace app\api\controller\v1;

use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\BannerMissException;
use think\Controller;
use think\Exception;

class Banner extends Controller
{

    /*
     * @params $id  int 正整数
     * @return banner信息
     */
    public function getBanner($id)
    {

        (new  IDMustBePositiveInt())->goCheck();
        $banner = BannerModel::getBannerById($id);
        if(!$banner){
            throw new BannerMissException(); //抛给客户端异常 不需要记录日志
        }
        return $banner;

    }



}