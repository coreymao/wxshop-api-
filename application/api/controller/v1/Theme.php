<?php
/**
 * Created by PhpStorm.
 * User: caomao
 * Date: 2018/6/7
 * Time: 下午3:50
 */

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;
use think\Controller;
use app\api\model\Theme as ThemeModel;


class Theme extends Controller
{


    /*
     * url=/theme?ids=id1,id2,id3...
     *return 一组theme 模型
     */
    public function getThemeList($ids = '')
    {
        (new IDCollection())->goCheck();
        $ids = explode(',', $ids);
        $result = ThemeModel::with(['topicImg', 'headImg'])->select($ids);
        if (!$result) {
            throw new ThemeException();
        }
        return $result;

    }


    /*
     *return 一个主题[包含所有产品]
     */
    public function getThemeOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);
        if (!$theme) {
            throw new ThemeException();
        }
        return $theme->hidden(['products.summary', 'products.img_id'])->toArray();
    }


}