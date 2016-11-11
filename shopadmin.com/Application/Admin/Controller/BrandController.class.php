<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/5
 * Time: 19:57
 */

namespace Admin\Controller;


use Think\Controller;

class BrandController extends Controller
{
    /**
     *
     * 获取商品列表
     */
    public function index()
    {
        //获取输入框传递过来的值构造搜索条件进行模糊搜索
        $keyword = trim(I('get.name')) ? trim(I('get.name')) : '';
        $cond = [];
        //判断获取的关键字是否存在
        //存在则作为查询条件进行数据库查询操作

        if ($keyword) {
            $cond['name'] = ['like', '%' . $keyword . '%'];
        }
        //实例化model对象
        $brand_model = D('Brand');
//        dump($brand_model);exit;没出现问题能找到数据
        //调用model获取数据
        $data  = $brand_model->getPageResult($cond);
//     dump($data);
        //将数据分配大页面
        $this->assign($data);
        $this->display();

    }

    public function add()
    {
        //收集数据
        //判断数据提交方式
        if (IS_POST) {
            $brand_model = D('Brand');
//            dump($brand_model->create());exit;
            if ($brand_model->create() == false) {
                //在没有收集到数据的时候则掉哦用方法内存在的错误提示
                $this->error($brand_model->getError());

            }
            //添加数据
            if ($brand_model->add() === false) {
                $this->error($brand_model->getError());
            }
            //跳转
            $this->success('添加成功', U('index'));
        } else {
            $this->display();
        }
    }

    public function edit($id)
    {
        $brand_model = D('Brand');
        //收集数据
        if (IS_POST) {
            if ($brand_model->create() == false) {
                $this->error($brand_model -> getError());

            }
            //保存修改之后的数据
            if ($brand_model->save() == false) {
                $this->error($brand_model -> getError());

            }
            //跳转到index页面
            $this->success('修改成功', U('index'));

        } else {
            //获取数据表中的数据
            $row = $brand_model->find($id);
            //分配数据
            $this->assign('row', $row);
            $this->display('add');

        }

    }

    /**
     *
     * 此处的删除商品只是逻辑意义上的删除商品
     * 在数据库也会有商品信息只是不让商品上架
     *
     */
    public function remove($id){
        $brand_model = D('Brand');
        /**
         * $User = M("User");
         * 实例化User对象更改用户的name值$User-> where('id=5')->setField('name','ThinkPHP');
         * setField方法支持同时更新多个字段，只需要传入数组即可，
         */
        if(!$brand_model->where(['id'=>$id])->setField('status', -1)){
            $this->error($brand_model->getError());
        }else{
            $this->success('删除成功',U('index'));
        }



    }
}