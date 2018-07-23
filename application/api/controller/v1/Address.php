<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/10
 * Time: 下午12:11
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\validate\AddressNew;
use app\api\service\Token as TokenService;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\lib\exception\AddressException;
use app\api\model\UserAddress as UserAddressModel;
use think\Exception;

class Address extends BaseController
{


    protected $beforeActionList = [

        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']  //坑：前置操作only=>函数 不支持驼峰命名(需要修改基类Controller)

    ];


    /*
     * url :/address ?  header:'token'
     *  创建或更新用户地址
     *  传入地址写入数据库需要用户user_id,  user_id不通过post传入
     * 1.根据Token,获取uid
     * 2.根据uid查找用户数据，判断用户是否存在，不存在 抛出异常
     * 3.用户存在 查找用户地址( 有：修改地址，无：添加地址 ) 添加地址:[从客户端传来数据，需要过滤]
     */
    public function createOrUpdateAddress()
    {

        $validate = new AddressNew();
        $validate->goCheck();
        $uid = TokenService::getCurrentUID();
        $user = UserModel::get($uid);
        if (!$user) {
            throw new UserException();
        }
        $dataArr = $validate->getDataByRule(input('post.'));   // 接受客户端传入地址 需要过滤
        //$dataArr=input('post.');
        $userAddress = $user->address;     // 用户存在情况 查找地址
        if (!$userAddress) {
            $user->address()
                ->save($dataArr);       //关联模型 添加地址
        } else {
            $user->address
                ->save($dataArr);        //修改地址
        }
        return json(new SuccessMessage(), 201);

    }


    /**
     * 获取用户地址
     */

    public function getUserAddress(){
        $uid=TokenService::getCurrentUID();
        $userAddress=UserAddressModel::where('user_id','=',$uid)
            ->find();
        if(!$userAddress){
            throw  new AddressException([
                'msg'=>'用户地址不存在'
            ]);
        }
        return $userAddress;

    }


}