<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        //获取商品分类信息
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList('id,name,parent_id');
        //将数据分配到页面
//        dump($goods_categories);exit;
        $this->assign('goods_categories', $goods_categories);
        $this->display();
    }
}