<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        //��ȡ��Ʒ������Ϣ
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList('id,name,parent_id');
        //�����ݷ��䵽ҳ��
//        dump($goods_categories);exit;
        $this->assign('goods_categories', $goods_categories);
        $this->display();
    }
}