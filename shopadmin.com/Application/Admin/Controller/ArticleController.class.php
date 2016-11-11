<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/6
 * Time: 11:14
 */

namespace Admin\Controller;


use Think\Controller;

class ArticleController extends Controller
{

    public function index()
    {
        //1.展示文章从数据库获取数据展示
        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        $article = D('Article');
        $rows = $article->getPageResult($cond);
//        dump($res);exit;
        $this->assign($article->getPageResult($cond));


        //实例化model对象
        $articleCategory = D('ArticleCategory');

        //调用categories的model获取数据文章需要的分类数据

        //    dump($rows);
        $categories = $articleCategory->getList();
        $this->assign('categories', $categories);
//        dump($categories);exit;
        $this->display();


        //2.也要在从文章分类的列表中跳转到文章列表
        //3.但是是跳转到相对应的文章列表
        //4.文章列表也可以进行分页和查询


    }

//添加文章数据

    public function add()
    {
        if (IS_POST) {
            $article = D('Article');
            //收集数据
            if ($article->create() === false) {
                $this->error(get_error($article));
            }
            if ($article->addArticle() === false) {
                $this->error(get_error($article));
            }
            $this->success('添加成功', U('index'));
        } else {
            //获取分类列表
            //实例化categories  model对象获取数据
            $articleCategory = D('ArticleCategory');
            $categories = $articleCategory->getList();
            $this->assign('categories', $categories);
            $this->display();

        }
    }

    public function edit($id)
    {
        if (IS_POST) {
            //收集数据
            $article = D('Article');
            if ($article->create() === false) {
                $this->error(get_error($article));
            }
            if ($article->saveArticle() === false) {
                $this->error(get_error($article));
            }
            $this->success('小小杜你修改成功', U('index'));
        } else {
            $article = D('Article');
            //展示数据
            $row = $article->getArticleInfo($id);
            $this->assign('row', $row);

            //获取分类列表
            $articleCategory = D('ArticleCategory');
            $categories = $articleCategory->getList();
            $this->assign('categories', $categories);
            $this->display();

        }
    }
    public function remove($id) {
        $article = D('Article');
        if ($article->deleteArticle($id) === false) {
            $this->error(get_error($article));
        } else {
            $this->success('小小杜你删除成功', U('index'));
        }
    }
}