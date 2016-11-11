<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/6
 * Time: 11:13
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleCategoryController extends Controller
{
    /**
     * 展示文章列表
     * 用户可以搜索文章并且可以进行分页展示文章列表
     *
     */
    public function index()
    {
        //获取搜索框的内容 进行模糊查询
        $keyword = trim(I('get.name')) ? trim(I('get.name')) : '';
        $cond = [];
        //判断获取的关键字是否存在
        if ($keyword) {
            $cond['name'] = ['like', '%' . $keyword . '%'];

        }
        //实例化model对象
        $articleCategory = D('ArticleCategory');

        //调用model获取数据
        $data = $articleCategory->getPageResult($cond);
//        dump($rows);
        $this->assign($data);
        $this->display();

    }

    public function add()
    {

        //判断数据是否已经提交
        if (IS_POST) {
            //实例化model
            $articleCategory = D('ArticleCategory');
            if ($articleCategory->create() === false) {
                //如果没有数据则提示错误
                $this->error($articleCategory->getError());

            }
            //如果没存在错误就将数据存入数据库
            if ($articleCategory->add() == false) {
                $this->error($articleCategory->getError());

            }
            //跳转到展示页面
            $this->success('添加文章分类成功了', U('index'));

        } else {
            $this->display();
        }

    }

    public function edit($id)
    {
        //实例化model
        $articleCategory = D('ArticleCategory');
        //1.收集数据进行回显
        if (IS_POST) {
            if ($articleCategory->create() == false) {
                $this->error($articleCategory->getError());


            }
            //2并且将修改的数据进行提交到数据库保存
            if ($articleCategory->save() === false) {
                $this->error($articleCategory->getError());

            }
            //跳转到index页面
            $this->success('文章类型修改成功', U('index'));

            //将数据回显

        }else{
            //如果接受到传过来的id则回显到页面显示效果
           $row= $articleCategory->find($id);
            $this->assign('row',$row);
            $this->display('add');
        }


    }
    public function remove($id){
        //实例化model
        $articleCategory = D('ArticleCategory');

        /**
         * $User = M("User");
         * 实例化User对象更改用户的name值$User-> where('id=5')->setField('name','ThinkPHP');
         * setField方法支持同时更新多个字段，只需要传入数组即可，
         */
        if($articleCategory ->where(['id'=>$id])->delete()===false){
            $this->error( $articleCategory ->getError());
        }else{
            $this->success('删除成功',U('index'));
        }


    }

}