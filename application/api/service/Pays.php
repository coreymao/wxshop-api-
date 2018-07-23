<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/12
 * Time: 下午3:51
 */

namespace app\api\service;


use app\lib\exception\OrderException;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;

//Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pays
{
    protected $orderID;         //订单号
    protected $uid;             //用户id
    protected $orderNo;         //订单编号

    public function __construct($id)
    {
        if (!$id) {
            throw new ParameterException([
                'msg' => '订单号不能为空'
            ]);
        }
        $this->orderID = $id;
        $uid = TokenService::getCurrentUID();
        $this->uid = $uid;          //token令牌获取用户身份
    }


    public function Pay()
    {
        $this->prePayValidate();                                    //订单业务验证
        $order = new OrderService();                                //检测库存量
        $status = $order->checkOrderStock($this->orderID);          //返回订单总状态
        if (!$status['pass']) {
            throw new OrderException([
                'msg' => '订单库存量检测不通过,商品缺货了'
            ]);
        }
        //发起支付,调用统一订单接口(获取客户端需要参数)
        $payParams = $this->makeWxPreOrder($status['orderPrice']);
        return $payParams;


    }

    /* 访问WX统一下订单接口 接受最终需要数据
     * @params 订单具体参数
     * return 客户端最终需要参数
     */
    private function makeWxPreOrder($totalPrice)
    {
        $openid = TokenService::getTokenValues('openid');

        if (!$openid) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);              //订单号
        $wxOrderData->SetTrade_type('JSAPI');                //交易类型
        $wxOrderData->SetTotal_fee($totalPrice * 100);      //交易总额单位：分
        $wxOrderData->SetBody('刘家商城');                   //
        $wxOrderData->SetOpenid($openid);                         //身份标识
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));                   //支付成功 微信服务器携带 支付结果 访问这接口[回调]
        //  获取参数返客户端
        return $this->getPaySignature($wxOrderData);

    }



    /*
     * 访问统一下订单接口
     */
    private function getPaySignature($wxOrderData)
    {

        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);   //发送http请求->微信服务器
        //dump($wxOrder) ;
        if ($wxOrder['return_code'] !== 'SUCCESS' || $wxOrder['result_code'] !== 'SUCCESS')
        {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
            throw new Exception('访问统一下订单接口失败');
        }

        $this->recordPreOrder($wxOrder);                            //prepay_id记录数据库
        $signature = $this->sign($wxOrder);                        //返回客户端需要数据与签名
        return $signature;


    }
    /*
     * prepay_id 保存数据库
     */
    private function recordPreOrder($wxOrder)
    {

        // 必须是update，每次用户取消支付后再次对同一订单支付，prepay_id是不同的
        OrderModel::where('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    /*
     *生成客户端需要参数
     */
    private function sign($wxOrder)
    {
        $jsApiPayData=new \WxPayJsApiPay();                                     //调用sdk 生成客户端需要数据
        $jsApiPayData->SetAppid(config('wx.app_id'));                     //1.小程序ID
        $jsApiPayData->SetTimeStamp((string)time());                            //2.时间戳
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);                                      //3.随机串
        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);    //4.数据包
        $jsApiPayData->SetSignType('md5');                                //5.签名方式
        $sign=$jsApiPayData->MakeSign();                                        //生成签名
        $rawValues=$jsApiPayData->GetValues();                                  //对象转化为数组
        $rawValues['paySign']=$sign;                                            //添加签名
        unset($rawValues['appId']);                                             //删除appId
        return $rawValues;
    }


    /*
     * 验证订单业务合法性
     * 1.存在？2.订单与用户匹配？  3.订单支付状态？
     */
    private function prePayValidate()
    {
        //订单存在,  3.
        $order = OrderModel::where('id', '=', $this->orderID)
            ->find();
        if (!$order) {
            throw new OrderException([
                'msg' => '传入订单号有问题,订单不存在'
            ]);

        }
        $uID = $this->uid;
        if ($order->user_id != $uID) {
            throw new OrderException([
                'msg' => '订单与用户不匹配，这不是您的订单'
            ]);
        }

        if ($order->status != 1) {
            throw new OrderException([
                'msg' => '订单异常,该订单已经支付了'
            ]);

        }
        $this->orderNo = $order->order_no;  //获取订单编号id
        return true;

    }


}