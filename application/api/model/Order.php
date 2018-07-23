<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:23
 */

namespace app\api\model;

use think\Exception;
use traits\model\SoftDelete;

class Order extends BaseModel
{

    protected $hidden = ['user_id', 'update_time', 'delete_time'];

    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true;


    //读取器 deal with table:order  field:'snap_items' the value of type to 'json'
    public function getSnapItemsAttr($value)
    {
        if (!$value) {
            return null;    // 需要返给客户端 不抛出异常
        }
        return json_decode($value);

    }

    //读取器 deal with table:order  field:'snap_address'  the value of type to 'json'
    public function getSnapAddressAttr($value)
    {
        if (!$value) {
            return null;
        }
        return json_decode($value);

    }

    //获取用户历史订单 分页显示
    public static function getSummaryOrderByUser($uid, $page, $size)
    {
        $pagingData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);   //paginate 返回是对象同模型一样返回的是模型对象
        return $pagingData;


    }
    //获取全部订单
    public static function getAllOrderByPage($page,$size){
        $pagingData=self::order('create_time desc')
                    ->paginate($size,true,['page'=>$page]);
        return $pagingData;
    }








}