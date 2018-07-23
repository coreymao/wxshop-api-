<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午2:56
 */

namespace app\api\service;

use app\lib\exception\OrderException;

class DeliveryMessage extends WxMessage
{
    const DELIVERY_MSG_ID = 'teSmJjkT0BGiGlvVNZJvq7bgUIRPXWBMk9eTdUfYM9g';  // 小程序模板消息ID号

    //发送模板消失
    public function sendDeliveryMessage($order, $jumpPage = '')
    {
        if (!$order) {
            throw new OrderException();
        }
        $this->tplID = self::DELIVERY_MSG_ID;
        $this->formID = $order->prepay_id;
        $this->page = $jumpPage;
        $this->data = $this->prepareMessageData($order);
        $this->emphasisKeyword = 'keyword2.DATA';
        return parent::sendMessage();

    }

    public function prepareMessageData($order)
    {
        $dt = new \DateTime();
        $data = [
            'keyword1' => [
                'value' => $order->order_no,
            ],
            'keyword2' => [
                'value' => $order->snap_name,
                'color' => '#27408B'
            ],
            'keyword3' => [
                'value' =>  $dt->format("Y-m-d H:i")
            ],
            'keyword4' => [
                'value' => '4008-419-417'
            ]
        ];
        return $data;


    }



}