<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/12
 * Time: 下午3:33
 */

namespace app\api\controller\v1;

use app\api\service\WxNotify;
use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pays as PaysService;

class Pays extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    /*
     * @params $id 为客户端传入订单号
     * 微信支付接口  需要携带token令牌  就行权限认证
     */
    public function getPreOrder($id = '')
    {
        //1.接受客户端  order_id
        //1.必须是正整数且不为空  2.订单存在,  3.订单与用户匹配 4.订单库存有 通过库存验证
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PaysService($id);
        $payParams = $pay->pay();
        return $payParams;

    }

    /*
     * 微信回调接口
     * 客户端调用微信支付后，微信主动调用该接口，并且携带 支付数据
     */
    public function receiveNotify()
    {
        $notify=new WxNotify();
        $notify->handle();

    }


}