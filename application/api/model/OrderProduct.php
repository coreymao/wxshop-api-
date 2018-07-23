<?php

/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/8
 * Time: 下午11:41
 */
namespace app\api\model;

use traits\model\SoftDelete;

class OrderProduct extends BaseModel
{


    use SoftDelete;
    protected static $deleteTime = 'delete_time';
    protected $autoWriteTimestamp=true;

}
