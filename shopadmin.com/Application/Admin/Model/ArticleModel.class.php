<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/6
 * Time: 16:12
 */

namespace Admin\Model;

use Think\Page;


use Think\Model;

class ArticleModel extends Model
{
    protected $patchValidate = true; //开启批量验证
    /**
     * 文章的名字不能为空
     * 文章状态必须为0或者1
     *
     */

    protected $_validate = [
        ['name', 'require', '文章名称不能为空'],
        ['article_category_id', 'require', '文章分类不合法'],
        ['status', '0,1', '文章状态不合法', self::EXISTS_VALIDATE, 'in'],
        ['sort', 'number', '排序必须为数字'],
    ];

    /**
     * 获取分页数据
     */
    public function getPageResult(array $cond = [])
    {
        $cond = array_merge(['status' => ['neq', -1]], $cond);
        //获取分页工具条
        $count = $this->where($cond)->count();
        $page = new Page($count, C('PAGE.SIZE'));
//        dump($page);exit;
        $page->setConfig('theme', C('PAGE.THEME'));
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p'), C('PAGE.SIZE'))->order('sort')->select();
        //返回数据
//        dump($rows);
//        dump($page_html);
        return [
            'page_html' => $page_html,
            'rows' => $rows,
        ];
    }

    public function addArticle()
    {
        //数据获取成功判断数据入库操作
//        dump(I('post.'));
//        exit;
        //调用add方法写入数据成功后返回的结果是
        //插入数据库后生成的id此表对应的是文章的id
        if (($article_id = $this->add()) === false) {

            return false;
        }
        //保存文章的内容根据文章保存时生成的id
        $data = [
            'article_id' => $article_id,
            'content' => I('post.content'),
        ];
        $articleContent = M('ArticleContent');
        if ($articleContent->add($data) === false) {
            $this->error = '保存文章内容出错了!!';
            return false;
        }
        return true;
    }

    /**获取完整的文章信息内容
     * @param $id
     * @return mixed
     *
     *
     */
    public function getArticleInfo($id){
       return $this->join('__ARTICLE_CONTENT__ as dq on dq.article_id=__ARTICLE__.id')->find($id);
    }

    /**
     * @return bool
     * 将更新后的数据进行保存
     */
    public function saveArticle()
    {
        $article_id = $this->data['id'];
//        dump($article_id);exit;
//        dump(I('post.'));exit;
        //保存文章基本信息
        if ($this->save() === false) {
            return false;
        }
        //保存文章内容
        $data = [
            'article_id' => $article_id,
            'content' => I('post.content'),
        ];
        if (M('ArticleContent')->save($data) === false) {
            //法的返回值是影响的记录数，如果返回false则表示更新出错，
            //因此一定要用恒等来判断是否更新失败。

            $this->error = '保存详细内容失败!!!';
            return false;
        }
        return true;
    }

    public function deleteArticle($id)
    {
        //删除第一张表的基本信息
        if ($this->delete($id) === false) {
            return false;
        }
        //删除第二张表的详细内容
        $articleContent = M('ArticleContent');
        if ($articleContent->delete($id) === false) {
            $this->error = '删除失败咯!';
            return false;
        }
        return true;
    }



}