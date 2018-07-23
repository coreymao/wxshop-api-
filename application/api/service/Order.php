<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/12
 * Time: 下午3:51
 */

namespace app\api\service;

use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\Order as OrderModel;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\enum\ScopeEnum;
use app\lib\exception\OrderException;
use app\lib\exception\ProductException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

/**
 * 订单类
 * 订单做了以下简化：
 * 创建订单时会检测库存量，但并不会预扣除库存量，因为这需要队列支持
 * 未支付的订单再次支付时可能会出现库存不足的情况
 * 所以，3次检测库存
 * 1. 创建订单时检测库存
 * 2. 支付前检测库存
 * 3. 支付成功后检测库存
 *
 * 下订单接口
 * 思路:  1.oproducts 客户端传来数据 ['product_id'=>1,'count'=>3] 找出数据库对应商品 products 对比
 *       2.products[0]['stock']--count>0 检测库存 通过--对应用户地址uid 写入数据库--1个 订单下包含多个商品
 *       3.库存检测不通过 向客户端抛出异常,哪个商品没通过检测？拿到每个商品状态以及包含('havestock','count','totalprice')
 *      4.商品状态影响订单状态 订单总数量、订单总价格 (通过每个商品状态获得) 、+ 订单编号 + 每个商品信息 [二维关联数组] 一起写入数据库
 *
 *
 */
class Order
{

    protected $oProducts;   //客户端传入商品
    protected $products;    //从数据库读取对应商品
    protected $uid;         //订单对应用户

    /*
     * @param int $uid 用户id
     * @param array $oProducts 订单商品列表
     * @return array  订单商品状态
     * @throws Exception
     */
    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;                        // 客户端传入商品
        $this->products = $this->getProducts($oProducts);     //对应数据库商品
        $this->uid = $uid;
        $status = $this->getOrderStatus();                    // 获取订单状态
        if (!$status['pass']) {
            /*  throw new OrderException([
                  'msg' => '订单异常，库存检测不通过'
              ]);*/
            $status['order_id'] = -1;
            return $status;

        }
        $orderData = $this->snapOrder($status);          //订单快照数据
        $order = $this->createOrder($orderData);         //写入数据库  return havastock= true, order_no , order_create,order_id
        $order['pass'] = true;
        return $order;

    }

    //客户端商品product_id，找出数据库对应商品
    protected function getProducts($oProducts)
    {
        $oPIDArr = [];
        foreach ($oProducts as $oPValue) {
            array_push($oPIDArr, $oPValue['product_id']);
        }
        $products = Product::all($oPIDArr)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;       //               是一组商品 需要排除找出来结果 结果不一定齐全 过滤
    }


    //获取订单状态--取决于每个商品状态
    protected function getOrderStatus()
    {
        $status = [
            'pass' => true,     //是否通过库存量检测
            'orderPrice' => 0,    //订单总价格
            'totalCount' => 0,     //订单数量
            'pStatusArr' => []           //每个商品状态
        ];

        foreach ($this->oProducts as $oProduct) {

            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalprice'];
            $status['totalCount'] += $pStatus['counts'];
            array_push($status['pStatusArr'], $pStatus);
        }

        return $status;
    }

    //获取每个商品状态
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'counts' => $oCount,
            'name' => '',
            'totalprice' => 0,
            'price' => 0,
            'main_img_url' => null,

        ];
        $pIndex = -1;
        for ($i = 0; $i < count($products); $i++) {
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1) {
            throw new ProductException([
                'msg' => 'id为' . $oPID . '商品不存在,订单创建失败'
            ]);
        }
        $product = $products[$pIndex];    // 找到对应商品
        $pStatus['id'] = $oPID;
        $pStatus['name'] = $product['name'];
        $pStatus['price'] = $product['price'];
        $pStatus['main_img_url'] = $product['main_img_url'];
        $pStatus['totalprice'] = $product['price'] * $oCount;
        if ($product['stock'] - $oCount > 0) {
            $pStatus['haveStock'] = true;
        }
        return $pStatus;

    }

    //创建订单 分表添加
    private function createOrder($orderData)
    {
        Db::startTrans();       //开始事务 相当于数据库锁机制
        try {
            $orderNo = self::makeOrderNo();   //订单编号
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $orderData['orderPrice'];
            $order->total_count = $orderData['totalCount'];
            $order->snap_img = $orderData['snapImg'];
            $order->snap_name = $orderData['snapName'];
            $order->snap_address = $orderData['snapAddress'];
            $order->snap_items = json_encode($orderData['pStatus']);
            $order->save();

            $orderID = $order->id;                          // 新创建订单ID
            $orderCreateTime = $order->create_time;         // 订单创建时间

            foreach ($this->oProducts as &$oProduct) {
                $oProduct['order_id'] = $orderID;             // oProducts 每个数组子元素添加属性
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();                                     // 提交事务
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'order_time' => $orderCreateTime

            ];
        } catch (Exception $ex) {
            Db::rollback();           //发生异常
            throw $ex;
        }


    }

    //写入数据库之前准备订单快照数据
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => $status['orderPrice'],
            'totalCount' => $status['totalCount'],
            'pStatus' => $status['pStatusArr'],
            'snapAddress' => json_encode($this->getUserAddress()),
            'snapName' => $this->products[0]['name'],
            'snapImg' => $this->products[0]['main_img_url']
        ];

        if (count($this->products) > 1) {
            $snap['snapName'] .= '等多个';
        }

        return $snap;

    }


    //获取用户收货地址
    private function getUserAddress()
    {
        $uid = $this->uid;
        $userAddress = UserAddress::where('user_id', '=', $uid)
            ->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户收货地址不存在，下订单失败',
                'errorCode' => 60001
            ]);
        }

        return $userAddress->toArray();

    }

    //订单编号，随机数
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }


    /*订单是否通过库存检测
     * @param string $orderNo 订单号
     * @return array 订单商品状态
     * @throws Exception
     */
    public function checkOrderStock($orderID)
    {

        $oProducts = OrderProduct::where('order_id', '=', $orderID)
            ->select()->toArray();       //这里$oProducts 是从订单号 查询 订单表来的 ，与place函数不一样 (place函数是从客户端传来的)
        $this->products = $this->getProducts($oProducts);
        $this->oProducts = $oProducts;
        $status = $this->getOrderStatus();
        return $status;

    }

    /**
     * 订单发货
     */
    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)->find();
        if (!$order) {
            throw  new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '您还没付款，无法发货',
                'errorCode' => 8002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;   //已经发货
        $order->save();     //更新数据库

        $msg = new DeliveryMessage();
        return $msg->sendDeliveryMessage($order, $jumpPage);

    }


}
