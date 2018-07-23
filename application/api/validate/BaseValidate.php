<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午6:27
 */

namespace app\api\validate;

use app\lib\exception\ParameterException;
use think\Exception;
use think\Request;
use think\Validate;

/*
 * class BaseValidate
 * 验证类基类
 */

class BaseValidate extends Validate
{

    /*
     * 封装check()方法
     * 检测所有客户端发来的参数是否符合验证类规则
     * 基类定义了很多自定义验证方法
     * 这些自定义验证方法其实，也可以直接调用
     * @throws ParameterException
     * @return true
     */
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();
        $result = $this->batch()
            ->check($params);
        if (!$result) {
            //$e=new ParameterException();
            //$e->msg=$this->getError();       // $this->getError(); 获取具体错误信息
            $e = new ParameterException(
                [
                    'msg' => $this->getError(),
                ]);
            throw $e;
        }

        return true;

    }


    /**
     * 必须是正整数
     * @param $value 验证的值
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    public function isPositiveInteger($value, $rule = '', $data = '', $field = '')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        //return $field . '必须是正整数';
        return false;
    }


    /*
     * 规则对应值不能为空
     */
    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        if (empty($value)) {
            return $field . '不允许为空';
            //return false;
        } else {
            return true;
        }
    }

    /*
     * 过滤非法参数
     * @param $dataArr 客户端传入数据  input('post.') 为数组
     * @return array|bool 按照规则key过滤后的变量数组
     */
    public function getDataByRule($dataArr)
    {

        if( array_key_exists('uid',$dataArr) || array_key_exists('user_id',$dataArr) )
        {
            throw new   ParameterException([
                'msg'=>'传入参数包含非法参数uid或者user_id'
            ]);
        }

        $newArray=[];
        foreach ($this->rule as $key => $value){
            $newArray[$key]=$dataArr[$key];

        }
        return $newArray;

    }

    //没有使用TP的正则验证，集中在一处方便以后修改
    //不推荐使用正则，因为复用性太差
    //手机号的验证规则
    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }









}