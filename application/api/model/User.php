<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午6:38
 */

namespace app\api\model;

use traits\model\SoftDelete;


class User extends BaseModel
{

    use SoftDelete;         //软删除
    protected static $deleteTime = 'delete_time';
    protected $autoWriteTimestamp = true; //自动写入create_time      update_time


    //关联 table user_address
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }


    //根据openid查找用户
    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)
            ->find();

        return $user;


    }

    //新增用户
    public static function newUser($openid)
    {
        $userAdd = self::create([
            'openid' => $openid
        ]);
        return $userAdd->id;
    }


}