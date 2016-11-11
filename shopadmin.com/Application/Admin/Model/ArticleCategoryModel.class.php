<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/6
 * Time: 11:16
 */

namespace Admin\Model;


use Think\Model;
use Think\Page;

class ArticleCategoryModel extends Model
{
    //自动验证规则
    protected $_validate = [
        ['name','require','品牌名称不能为空'],
    ];
    /**
     * 获取分页数据
     */
    public function getPageResult(array $cond = []) {
        $cond = array_merge(['status'=>['neq',-1]],$cond);
        //获取分页工具条
        $count = $this->where($cond)->count();
        $page = new Page($count, C('PAGE.SIZE'));

        $page->setConfig('theme', C('PAGE.THEME'));
        $page_html = $page->show();
        //获取分页数据
        $rows = $this->where($cond)->page(I('get.p'),C('PAGE.SIZE'))->order('sort')->select();
        //返回数据
//        dump($rows);
//        dump($page_html);
        return [
            'page_html'=>$page_html,
            'rows'=>$rows,
        ];
    }
    /**
     * 获取所有的文章分类。
     * @return array
     */
    public function getList() {
        return $this->getField('id,name');
    }


}