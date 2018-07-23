<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//banner

Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');


//theme
Route::get('api/:version/theme/:id','api/:version.Theme/getThemeOne');
Route::get('api/:version/theme','api/:version.Theme/getThemeList');


//product

Route::get('api/:version/product/recent','api/:version.Product/getRecent');
Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id','api/:version.Product/getOneProduct');


//category
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');


//token
Route::post('api/:version/token/user','api/:version.Token/getToken');
Route::post('api/:version/token/verify','api/:version.Token/verifyToken');
Route::post('api/:version/token/app','api/:version.Token/getAppToken'); //第三方获取令牌


//address

Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress'); //创建更新地址
Route::get('api/:version/address', 'api/:version.Address/getUserAddress'); //获取用户地址

//order
Route::post('api/:version/order', 'api/:version.Order/placeOrder');                            //下订单接口 数据传递 data
Route::post('api/:version/order/:id', 'api/:version.Order/getOrderDetail',[],['id'=>'\d+']);  //订单详情
Route::post('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');            //用户历史订单列表

Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');             //获取全部订单
Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');               //发货模版消息


//pays

//Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');                //pay模块下面下订单接口 拷贝
Route::post('api/:version/pays/pre_order', 'api/:version.Pays/getPreOrder');    //pays模块下订单接口 自己写的

Route::post('api/:version/pays/notify', 'api/:version.Pays/receiveNotify');     //微信支付结果通知 异步回调接口














