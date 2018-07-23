<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/9
 * Time: 下午6:55
 */

namespace app\lib\enum;


/**
 * 订单状态枚举
 */
class OrderStatusEnum
{


    // 待支付
    const UNPAID = 1;

    // 已支付
    const PAID = 2;

    // 已发货
    const DELIVERED = 3;

    // 已支付，但库存不足
    const PAID_BUT_OUT_OF = 4;

    // 已处理
    const HANDLED_OUT_OF = 5;


}