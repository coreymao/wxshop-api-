<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/12
 * Time: 下午8:05
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Product as ProductModel;
use think\Exception;
use think\Loader;
use think\Log;
use think\Db;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');


class WxNotify extends \WxPayNotify
{

    //    protected $data = <<<EOD
//<xml><appid><![CDATA[wxaaf1c852597e365b]]></appid>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[N]]></is_subscribe>
//<mch_id><![CDATA[1392378802]]></mch_id>
//<nonce_str><![CDATA[k66j676kzd3tqq2sr3023ogeqrg4np9z]]></nonce_str>
//<openid><![CDATA[ojID50G-cjUsFMJ0PjgDXt9iqoOo]]></openid>
//<out_trade_no><![CDATA[A301089188132321]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[944E2F9AF80204201177B91CEADD5AEC]]></sign>
//<time_end><![CDATA[20170301030852]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4004312001201703011727741547]]></transaction_id>
//</xml>
//EOD;

    /*
     * 回调 处理业务 库存检测 修改数据库 订单状态 减去库存
     * 重新子类方法
     * @param array $data 微信服务器传来数据 该函数会把xml格式 数据转化为 array
     * @param string $msg
     * @return \true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     * 微信服务器每隔30s 50s 60s 访问API请求 如果30s内一次程序还没走完就会2次 reduceStock函数 操作数据库2次
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS')         //代表支付成功 文档里是字段 'trade_state'
        {
            $orderNo = $data['out_trade_no'];          //订单编号
            Db::startTrans();                          //事务 防止高并发 多次减去库存
            try {
                $order = OrderModel::where('order_no', '=', $orderNo)->find();
                if ($order->status == 1) {
                    //修改未支付订单 检测库存
                    $orderService = new OrderService();
                    $status = $orderService->checkOrderStock();
                    if ($status['pass']) {
                            $this->updateOrderStatus($order->id, true); //修改订单状态
                            $this->reduceStock($status);                    //更新库存
                        } else {
                            $this->updateOrderStatus($order->id, false);
                        }

                    }
                    Db::commit();

                    return true;
                }
                catch (Exception $ex)
                {
                    Db::rollback();
                    Log::error($ex);
                    return false;   // 出现异常，向微信返回false，请求重新发送通知
                }

        }
        else
        {
            //支付失败
            return true;  //支付失败也返回true 如果返回false 微信会不停调用该接口

        }


    }


    //修改订单状态
    private function updateOrderStatus($orderID, $falg)
    {
        //$status=$falg ? '2' : '4';
        $status = $falg ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }


    //减去库存
    private function reduceStock($status)
    {

        foreach ($status['pStatusArr'] as $key => $value) {
            ProductModel::where('id', '=', $value['id'])
                ->setDec('stock', $value['count']);

        }


    }



















}