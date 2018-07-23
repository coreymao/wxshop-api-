<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:38
 */

namespace app\api\model;
use traits\model\SoftDelete;

class UserAddress extends BaseModel
{
    use SoftDelete;         //软删除
    protected static $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true; //自动写入create_time      update_time

    protected $hidden = ['create_time', 'update_time', 'delete_time'];




}