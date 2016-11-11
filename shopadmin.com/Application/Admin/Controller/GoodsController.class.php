<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/9
 * Time: 16:04
 */

namespace Admin\Controller;


use Think\Controller;

class GoodsController extends Controller
{
    private $_model = null;

    protected function _initialize()
    {
        $this->_model = D('Goods');
    }

    //商品展示页面对数据进行输出
    public function index()
    {
        //将可以进行的关键字进行组合放在数组中
        //搜索的是关键字

        $name = I('get.name');
        $cond = [];
        if ($name) {
            $cond['name'] = ['like', '%' . $name . '%'];
        }
        //分类
        $goods_category_id = I('get.goods_category_id');
        if ($goods_category_id) {
            $cond['goods_category_id'] = $goods_category_id;
        }


        //品牌分类查询

        $brand_id = I('get.brand_id');
        if ($brand_id) {
            $cond['brand_id'] = $brand_id;
        }
        //促销状态
        $goods_status = I('get.goods_status');
        if ($goods_status) {
            $cond[] = 'goods_status & ' . $goods_status;
        }
        //查询是否是促销促销的默认值是1;
        $is_on_sale = I('get.is_on_sale');
        if (strlen($is_on_sale)) {
            $cond['is_on_sale'] = $is_on_sale;
        }
        //1.获取商品列表采用分页技术
        $this->assign($this->_model->getPageResult($cond));

        //取出商品分类
        //1.获取所有的商品分类,使用ztree展示,所以转换成json
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList();
        $this->assign('goods_categories', $goods_categories);
        //2.获取所有的品牌列表
        $brand_model = D('Brand');
        $brands = $brand_model->getList();
        //将数据分配到页面
        $this->assign('brands', $brands);
        //3.获取促销状态
        $goods_statuses = [
            ['id' => 1, 'name' => '精品',],
            ['id' => 2, 'name' => '新品',],
            ['id' => 4, 'name' => '热销',],
        ];
        //将数据分配到页面
        $this->assign('goods_statuses', $goods_statuses);
        //是否在上架状态
        $is_on_sales    = [
            ['id' => 1, 'name' => '上架',],
            ['id' => 0, 'name' => '下架',],
        ];
        $this->assign('is_on_sales', $is_on_sales);
        $this->display();

    }

    /**
     * 添加商品
     *
     */

    public function add() {
        if (IS_POST) {
            //收集数据
            if ($this->_model->create() === false) {
                $this->error(get_error($this->_model));
            }
            //添加商品
            if ($this->_model->addGoods() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }
    public function  edit($id){
        if(IS_POST){

            if($this->_model->create()===false){
                $this->error(get_error($this->_model));
            }
            //修改商品成功后保存
            if($this->_model->saveGoods()===false){
                $this->error(get_error($this->_model));
            }
            $this->success('修改成功了',U('index'));

        }else{
            //获取数据
            $row=$this->_model->getGoodsInfo($id);
            //分配数据
            $this->assign('row',$row);
            //将分类下拉框的数据拿到
            $this->_before_view();
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
        $brand_model = D('Goods');
        /**
         * $User = M("User");
         * 实例化User对象更改用户的name值$User-> where('id=5')->setField('name','ThinkPHP');
         * setField方法支持同时更新多个字段，只需要传入数组即可，
         */
        if(!$brand_model->where(['id'=>$id])->setField('is_on_sale',0)){
            $this->error($brand_model->getError());
        }else{
            $this->success('删除成功',U('index'));
        }



    }
    //记得将所有的下拉框内容封装成一个类展示的下拉框取名为_before_view
    private function _before_view(){
        //获取品牌的列表
        $brand_model = D('Brand');
        $brands     = $brand_model->getList();
        $this->assign('brands', $brands);
        //获取商品分类的信息
        $goods_category_model = D('GoodsCategory');
        $goods_categories = $goods_category_model->getList();
//        dump($goods_categories);exit;
        //商品分类信息要展示到ztree上
        //所以使用的json返回数据转换为json字符串
        $this->assign('goods_categories', json_encode($goods_categories));
        //获取所有供货商的信息绑定到下拉框

    }
}