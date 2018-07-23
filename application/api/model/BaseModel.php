<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午7:16
 */
namespace app\api\model;
use think\Config;
use think\Model;
use traits\model\SoftDelete;

class BaseModel extends Model{
    //使用软删除
    use SoftDelete;
    protected static $deleteTime='delete_time';
    //自动写入create_time update_time
    protected $autoWriteTimestamp = 'datetime';

    protected $hidden=['delete_time'];

    /**
     * 处理 Img url
     * @param $value  url字段对应的值
     * @param $data   数据库查询单条记录
     * @return string url全路径
     */
    protected function  prefixImgUrl($value,$data){
        $finUrl=$value;
        if($data['from']==1){
            $finUrl=config('setting.img_prefix').$value;
        }
        return $finUrl;
    }



}