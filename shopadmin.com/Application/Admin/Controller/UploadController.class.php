<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/5
 * Time: 22:51
 */

namespace Admin\Controller;


use Think\Controller;
use Think\Upload;

class UploadController extends Controller
{
    public function upload()
    {
        //收集数据
        $config = [
            'mimes' => array('image/jpeg', 'image/png', 'image/gif'), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'jpeg', 'jpe','gif'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './', //保存根路径
            'savePath' => 'Uploads/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => false, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => 'Qiniu', // 文件上传驱动
            'driverConfig' => array(
                'secretKey' => 'sgJ7ncBhRRCUi5LCeOY7GyOfuL5yNg2geOYuy819', //七牛服务器
                'accessKey' => 'G6leBoK2EJYTy4DNSHyEuP86DxJd5Q-Dkr2koP_A', //七牛用户
                'domain' => 'og6cgf3n3.bkt.clouddn.com', //域名
                'bucket' => 'duqi', //空间名称
                'timeout' => 30, //超时时间
            ), // 上传驱动配置
        ];
        $upload = new Upload($config);
//        dump($upload);有值存在
        //保存文件
        $fileinfo = $upload->upload();
//        dump($fileinfo);
        //出错???????
        //array_pop — 将数组最后一个单元弹出（出栈）
        $fileinfo = array_pop($fileinfo);
//        dump($fileinfo);
        $data = [];
        if (!$fileinfo) {
            $data = [
                'status' => false,
                'msg' => $upload->getError(),
                'url' => '',
            ];
        } else {
            if ($upload->driver == 'Qiniu') {
                $url = $fileinfo['url'];
            } else {
                $url = C('BASE_URL') . $upload->rootPath . $fileinfo['savepath'] . $fileinfo['savename'];
            }
            $data = [
                'status' => true,
                'msg' => '上传成功',
                'url' => $url,
            ];
        }
        //返回结果
        $this->ajaxReturn($data);

    }
}