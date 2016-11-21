<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/18
 * Time: 17:16
 */

namespace Home\Controller;


use Think\Controller;

class MemberController extends Controller
{
    private $_model;

    protected function _initialize()
    {
        $this->_model = D('Member');
    }

    /**
     * 用户注册
     */
    public function reg()
    {
        if (IS_POST) {
            if ($this->_model->create('', 'reg') === false) {
                $this->error(get_error($this->_model));
            }
            if ($this->_model->addMember() === false) {
                $this->error(get_error($this->_model));
            }
            $this->success('注册成功,请查收邮件激活账号', U('Index/index'));
        } else {
            //用户在没有提交注册信息时显示正常的注册页面
            $this->assign('title', '用户注册');
            $this->display();
        }
    }

    /**
     * 用户登陆
     *
     */
    public function login() {
        if(IS_POST){
            if($this->_model->create() === false){
                $this->error(get_error($this->_model));
            }
            if($this->_model->login() === false){
                $this->error(get_error($this->_model));
            }
            //登录成功跳转到首页
            $this->success('登陆成功',U('Index/index'));

        }else{
            $this->display();
        }
    }

    public function active($email,$token) {
        //修改数据库中对应的账户
        if($this->_model->where(['email'=>$email,'active_token'=>$token,'status'=>0])->setField('status',1)){
            $this->success('激活成功',U('Index/index'));
        }else{
            $this->error('激活失败',U('Index/index'));
        }
    }

    /**
     * 验证是否已被注册.
     */
    public function checkByParam() {
        $cond = I('get.');
        if($this->_model->where($cond)->count()){
            $this->ajaxReturn(false);
        }else{
            $this->ajaxReturn(true);
        }
    }

    /**
     * 发送验证码,ajax调用
     * @param type $tel
     */

    public function sms($tel) {
        //测试使用
        ////////////////////
//        vendor('Alidayu.TopSdk');
//        $c            = new \TopClient;
//        $c ->appkey = '23533642' ;
//        $c ->secretKey = '4a7a5d7c52ecc79737615c1c5f54882b' ;
//        $req = new AlibabaAliqinFcSmsNumSendRequest;
//        $req = new \AlibabaAliqinFcSmsNumSendRequest;
//        $req ->setExtend( "" );
//        $req ->setSmsType( "normal" );
//        $req ->setSmsFreeSignName( "杜琪测试" );
//        $req ->setSmsParam( "{product:'杜琪网站',code:'66633',name:'杜先生'}" );
//        $req ->setRecNum( "15680834168" );
//        $req ->setSmsTemplateCode( "SMS_25855320" );
//        $resp = $c ->execute( $req );
//        var_dump($resp);
//        exit;




        //////////////////////////////

        if (IS_AJAX) {
            vendor('Alidayu.TopSdk');
            date_default_timezone_set('Asia/Shanghai');
            $c            = new \TopClient;
            $c->appkey    = '23533642';
            $c->secretKey = '4a7a5d7c52ecc79737615c1c5f54882b';
            $req          = new \AlibabaAliqinFcSmsNumSendRequest;
            $req->setExtend("");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("杜琪测试");


            $data         = [
                'product' => '杜琪网站',
               'code'    => \Org\Util\String::randNumber(1000, 9999),
                'name'=>'杜先生'
            ];


            //将验证码存放到session中
            $code         = [
                'tel'  => $tel,
               'code' => $data['code'],
                'name'=>'杜先生'
            ];
            session('TEL_CODE', $code);
            $data         = json_encode($data);
            $req->setSmsParam($data);
            $req->setRecNum($tel);
            $req->setSmsTemplateCode("SMS_25855320");
            $resp         = $c->execute($req);

//            dump($resp);exit;
            if (isset($resp->result->success)) {
                //发送成功了
                $this->ajaxReturn(true);
            }
        }

        //代表发送失败,可能是接口速度限制,缺钱,或者是非ajax调用
        $this->ajaxReturn(false);
    }

    /**
     * @param $address
     * @throws \Exception
     * @throws \phpmailerException
     * 发送邮件接口 在函数中已经有封装
     */
    public function mail($address) {
        vendor('PhpMailer.PHPMailerAutoload');
        $mail = new \PHPMailer;
        $mail->isSMTP();
        $mail->Host       = 'smtp.163.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '15680834168@163.com';
        $mail->Password   = 'dq923186';
        //在phpini中开启拓展
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        $mail->setFrom('15680834168@163.com', '小杜');
        $mail->addAddress($address);     // Add a recipient
        $mail->isHTML(true);

        $mail->Subject = '非常感谢你注册我们的网站';
        $mail->Body    = '你一定会在我们的网站玩的很痛快!';
        $mail->CharSet = 'UTF-8';

        if (!$mail->send()) {
            echo '发送邮件出错了!';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo '邮件已经发送';
        }
    }
    //用户注销登录退出后台
    public function logout(){
        //注销则要清楚session值
        session(null);
        cookie(null);
        //退出后则跳转到首页页面
        $this->success('退出成功',U('Index/index'));
    }


}