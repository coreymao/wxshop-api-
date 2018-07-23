<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午3:50
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PagingParameter;
use app\lib\enum\ScopeEnum;
use app\lib\exception\DeliverySuccess;
use app\lib\exception\ForbiddenException;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use think\Exception;


class Order extends BaseController
{
    // 下订单接口  支付  思路
    //1.用户下订单选择商品，向 Api 提交信息 （商品信息） 二维数组 多个商品 每个数组['id','count']
    //2.api 接受商品信息 对比数据库  进行库存量检测  不通过 抛出异常：商品缺货， 通过：把订单数据库入数据库 status=1（未付款）客户端发送msg可以付款
    //3.客户端调用 API 申请支付   服务器API(再次检查stock) 调用wx.server统一下单接口 (需要携带参数：订单编号 APPID appSecret 商户号 订单总额 URl.....)
    //4.WX服务器 返回结果  参数 prepary_id.... 服务器把这些参数打包返回客户端
    //5.客户端 wx.request.payment ---->wx.service(http请求)  进行付款
    //6.WX.server return msg(异步)=>success or fail  支付成功:(检测库存，减去数量 修改订单状态)

    protected $beforeActionList = [

        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getOrderDetail,getSummaryByUser']
    ];


    /*
     *
     * 下单接口 只有用户可以访问
     * url:  /order ?  post body{'token','data'}
     * @params $products 二维关联数组
     *
     */
    public function placeOrder()
    {
        (new OrderPlace())->goCheck();
        $produdcts = input('post.products/a');   //获取客户端传入数据 转化为数组
        $uid = TokenService::getCurrentUID();
        $order = new OrderService();
        $status = $order->place($uid, $produdcts);     //没通过库存量检测  返回具体商品状态  通过库存量检测 写入数据库 返回订单编号 创建时间
        return $status;

    }

    /*
     * url:...api/v1/order/by_user?page=1&size=15
     * 所有历史订单列表 接口 分页显示
     *
     */
    public function getSummaryByUser($page = 1, $size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUID();
        $pagingOrdersData = OrderModel::getSummaryOrderByUser($uid, $page, $size);
        if ($pagingOrdersData->isEmpty()) {
            //$data=[];
            return [
                'data' => [],
                'current_page' => $pagingOrdersData->currentPage()
            ];
        }
        $data = $pagingOrdersData->hidden(['snap_items', 'snap_address', 'prepay_id'])->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrdersData->currentPage()
        ];

    }


    /*
     * 订单详情
     */
    public function getOrderDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);      //数据需要处理 address需要转为 json 读取器
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail
            ->hidden(['prepay_id']);

    }

    /**
     * cms获取全部订单信息
     */

    public function getSummary($page = 1, $size = 20)
    {
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getAllOrderByPage($page, $size);
        if ($pagingOrders->isEmpty()) {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
//        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
//            ->toArray();
        $data = $pagingOrders->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    /**
     * 发货接口 发送模版消息给客户
     */
    public function delivery($id)
    {
        (new  IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if ($success) {
            return new DeliverySuccess();
        }


    }


}