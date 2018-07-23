<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/20
 * Time: 下午3:03
 */

namespace app\api\service;
use think\Exception;

class WxMessage
{

    private $sendUrl='https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s';
    private $color='red';
    private  $touser;
    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyword;


    function __construct()
    {
        $accessToken=new AccessToken();
        $token=$accessToken->get();
        $this->sendUrl=sprintf($this->sendUrl,$token);

    }


    /**
     * 发送模板消息
     * 需要在真机上拉起支付
     * @param $openID
     * @return bool
     * @throws Exception
     */
    public function sendMessage($openID){
        $data=[
            'touser'=>$openID,
            'template_id'=>$this->tplID,
            'page'=>$this->page,
            'form_id'=>$this->formID,
            'data'=>$this->data,
            'emphasis_keyword'=>$this->emphasisKeyword

        ];
        $res=curl_post($this->sendUrl,$data);
        $res=json_decode($res,true);
        if($res['errcode']==0){
            return true;
        }else{
            throw new Exception('模板消息发送失败'.$res['errmsg']);
        }



    }


}