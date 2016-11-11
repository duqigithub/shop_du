<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/9
 * Time: 16:06
 */

namespace Admin\Model;


use Think\Model;

class GoodsModel extends Model
{
    //开启批量验证模式
    protected $patchValidate = true;
    protected $_validate = [
        ['name', 'require', '商品名称不能为空'],
        ['sn', '', '货号已存在', self::VALUE_VALIDATE, 'unique'],
        ['goods_category_id', 'require', '商品分类不能为空'],
        ['brand_id', 'require', '品牌不能为空'],
//        ['supplier_id', 'require', '供货商不能为空'],
        ['market_price', 'require', '市场价不能为空'],
        ['market_price', 'currency', '市场价不合法'],
        ['shop_price', 'require', '售价不能为空'],
        ['shop_price', 'currency', '售价不合法'],
        ['stock', 'require', '库存不能为空'],
    ];
    //自动完成
    protected $_auto = [
        //新增数据的时候处理（默认)
        //self::MODEL_UPDATE或者2 更新数据的时候处理
        //self::MODEL_BOTH或者3 所有情况都进行处理
        //array(完成字段1,完成规则,[完成条件,附加规则])这是完成的规则
        ['sn', 'createSn', self::MODEL_INSERT, 'callback'],
        ['inputtime', NOW_TIME, self::MODEL_INSERT],
        ['goods_status', 'calcGoodsStatus', self::MODEL_BOTH, 'callback'],
    ];

    /**求和,求出商品推荐类型的位运算值.
     * @param $goods_status
     * @return int|number
     *
     *
     */
    protected function calcGoodsStatus($goods_status)
    {
        if (isset($goods_status)) {
            // 将数组中的所有值的和以整数或浮点数的结果返回。
            return array_sum($goods_status);
        } else {
            return 0;
        }
    }

    /**
     * 判断是否提交了货号,如果没有,就生成一个.
     * @param string $sn
     */
    protected function createSn($sn) {
        $this->startTrans();
        //如果已经提交了,就什么都不做
        if ($sn) {
            return $sn;
        }
        //生成规则:SN年月日编号:SN2016062800001
        //1.获取今天已经常见了多少个商品
        $date = date('Ymd');
        $goods_num_model = M('GoodsNum');
        //`保存到数据表中
        if ($num = $goods_num_model->getFieldByDate($date, 'num')) {
            ++$num;
            $data = ['date' => $date, 'num' => $num];
            $flag = $goods_num_model->save($data);
        } else {
            $num  = 1;
            $data = ['date' => $date, 'num' => $num];
            $flag = $goods_num_model->add($data);
        }
        if ($flag === false) {
            $this->rollback();
        }
        //2.计算SN
        $sn = 'SN' . $date . str_pad($num, 5, '0', STR_PAD_LEFT);
        return $sn;
    }

    /**
     * @return
     * 获取商品的详细信息
     */
    public function getGoodsInfo($id)
    {
        //取出一条商品的信息
        $row = $this->find($id);
        //特别注意的是状态值得确定商品的状态值回显需要json对象返回
        $row['goods_status'];
        $dq = [];
        //设置一个临时变量来装状态值
        if ($row['goods_status'] & 1) {
            $dq[] = 1;
        }
        if ($row['goods_status'] & 2) {
            $dq[] = 2;
        }
        if ($row['goods_status'] & 4) {
            $dq[] = 4;
        }
        //将取得的值变为json放入到状态值中保存
        $row['goods_status'] = json_encode($dq);
        //分类的信息几张表联合查询此处为详细信息展示
        $goods_intro_model = M('GoodsIntro');
        //动态查询
       //针对某个字段查询并返回某个字段的值，例如
    //$userId = $User->getFieldByName('liu21st','id');表示根据用户的name获取用户的id值。
        $row['content']=$goods_intro_model->getFieldByGoodsId($id,'content');
        //根据传过来的id在相册中查找相关信息
//        M('GoodsIntro')
//        $row['galleries']=$goods_intro_model->getFieldByGoodsId($id,'id,path');
        return $row;

    }

    /**
     * @return
     * 添加商品到数据库
     */
    public function addGoods()
    {
        $this->startTrans();
        //1.保存基本信息
        if (($goods_id = $this->add()) === false) {
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        $data = [
            'goods_id' => $goods_id,
            'content' => I('post.content', '', false),
            //false表示过滤方法为不过滤
            //在测试时create方法默认是将content过滤
        ];
        $goods_intro_model = M('GoodsIntro');
        if ($goods_intro_model->add($data) === false) {
            $this->rollback();
            return false;
        }
        //3.保存相册
        $goods_gallery_model = M('GoodsGallery');
        $pathes = I('post.path');
        $data = [];
        foreach ($pathes as $path) {
            $data[] = [
                'goods_id' => $goods_id,
                'path' => $path,
            ];
        }
        //如果上传了相册,并且相册保存失败,就回滚
        if ($data && ($goods_gallery_model->addAll($data) === false)) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    /**
     *
     * 保存编辑后的商品信息
     * 1.包含基本信息
     * 2.商品具体信息
     * 3.商品还有相册信息
     *
     */
    public function  saveGoods(){

        //保存商品的基本信息
        //此时在Mata中存放了由create收集的信息
        //baocun daointro
          dump(I('post.content'));

        $Model=M('GoodsIntro');








    }

    /**
     * 获取分页数据
     * @param array $cond 查询条件.
     * @return type
     */
    public function getPageResult(array $cond = [])
    {
        $cond = array_merge(['status' => 1], $cond);
        //1.获取总条数
        $count = $this->where($cond)->count();
        //2.获取分页代码
        $page_setting = C('PAGE');
        $page = new \Think\Page($count, $page_setting['SIZE']);
        $page->setConfig('theme', $page_setting['THEME']);
        $page_html = $page->show();
        //3.获取分页数据
        $rows = $this->where($cond)->where(['is_on_sale'=>['gt',0]])->page(I('get.p', 1), $page_setting['SIZE'])->select();
        //由于列表页要展示是否是新品精品热销,但是这些信息放在一个字段中,所以为了简化视图代码,我们在模型中处理好后再返回
        foreach ($rows as $key => $value) {
            $value['is_best'] = $value['goods_status'] & 1 ? true : false;
            $value['is_new'] = $value['goods_status'] & 2 ? true : false;
            $value['is_hot'] = $value['goods_status'] & 4 ? true : false;
            $rows[$key] = $value;
        }
        return compact('rows', 'page_html');
    }
}