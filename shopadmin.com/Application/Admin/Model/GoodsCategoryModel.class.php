<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/8
 * Time: 23:44
 */

namespace Admin\Model;

use Admin\Logic\MySQLORM;
use Admin\Logic\NestedSets;
use Think\Model;

class GoodsCategoryModel extends \Think\Model
{
    /**
     * @return mixed
     * 获取数据库数据作为展示页面使用
     *
     */
    public function getList()
    {
        return $this->order('lft')->limit(10)->select();
    }

    public function addCategory()
    {
        //这是一个类实现了接口的所有方法
        $orm = new MySQLORM();
        //这是一个核心的类里面是各种算法
        $NestedSets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');

        return $NestedSets->insert($this->data['parent_id'], $this->data, 'bottom');
    }

    public function saveCategory()
    {  //判断是否修改了父级分类
        //获取原来的父级分类
        $old_parent_id = $this->where(['id' => $this->data['id']])->getField('parent_id');
        if ($old_parent_id != $this->data['parent_id']) {
            //需要计算左右节点和层级，那么我们还是要使用nestedsets
            $orm = new MySQLORM();
            $NestedSets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($NestedSets->moveUnder($this->data['parent_id'], $this->data, 'bottom' === false)) {
                $this->error = '不能将分类移动到后代分类中';
                return false;
            }
        }
        return $this->save();
    }

    /**
     * 删除分类及其后代
     *
     */
    public function deleteCategory($id) {
        //需要计算左右节点和层级，那么我们还是要使用nestedsets
        $orm        = new MySQLORM();
        $nestedSets = new NestedSets($orm, $this->getTableName(), 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nestedSets->delete($id) === false) {
            $this->error = '删除失败';
            return false;
        }
        return true;
    }

}



