<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/8
 * Time: 23:44
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsCategoryController extends Controller
{
    private $_model;

    public function _initialize()
    {
        $this->_model = D('GoodsCategory');

    }

    public function index()
    {
        //从数据库获取数据
        $rows = $this->_model->getList();
        //将数据分配到页面
        $this->assign('rows', $rows);
        //展示数据
        $this->display();

    }

    /**
     * 添加分类顶级信息在下拉框显示
     *
     *
     */
    public function add()
    {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addCategory() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功', U('index'));
        } else {
            //获取已有分类，以便选择父级
            $this->_before_view();
            $this->display();
        }
    }

    public function edit($id)
    {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //如果成功了则执行保存
            if ($this->_model->saveCategory() === false) {
                $this->error($this->_model->getError());

            }
            $this->success('修改保存成功了', U('index'));
        } else {
            //如果没有数据提交则数据回显到页面
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            //将分类的消息以json的形式输出到ztree显示到页面
            $this->_before_view();
            $this->display('add');
        }
    }

    /**
     * 删除数据
     *
     */
    public function remove($id)
    {
        if ($this->_model->deleteCategory($id) === false) {
            $this->error($this->_model->getError());
        }
        $this->success('删除成功', U('index'));
    }

    /**
     * 将分类的信息传递折腾人是以json形式返回数据
     *
     *
     */
    private function _before_view()
    {
        //获取已有分类，以便选择父级
        $rows = $this->_model->getList();
        array_unshift($rows, ['id' => 0, 'name' => '顶级分类']);
        $categories = json_encode($rows);
        $this->assign('categories', $categories);
    }
}