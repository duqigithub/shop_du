<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/19
 * Time: 20:50
 */

namespace Home\Model;


use Think\Model;

class GoodsCategoryModel extends Model
{
    public function getList($field='*') {
        return $this->field($field)->where(['status'=>1])->select();
    }

}