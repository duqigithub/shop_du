<?php
/**
 * Created by PhpStorm.
 * User: D
 * Date: 2016/11/18
 * Time: 17:19
 */

namespace Home\Model;


use Think\Model;
use Think\Verify;

class MemberModel extends Model
{
    protected $patchValidate = true;

    /**
     * 1.验证规则用户名不能为空
     * 2当用户名存在的时候进行验证
     * 3.密码必须存在而且被两次密码必须相同用confirm判断
     * 4.邮箱必须正确需要发送激活邮件才能激活
     * 5.邮箱唯一 手机号码必须符合十一位并且符合国内手机号码的验证原则
     * 6.验证码存在并且输入验证码要相符合
     */
    protected $_validate = [
//        ['username', 'require', '用户名不能为空'],
//        ['username', '', '用户名已存在', self::EXISTS_VALIDATE, 'unique', 'reg'],
//        ['password', 'require', '密码不能为空'],
//        ['repassword', 'require', '重复密码不能为空'],
//        ['repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'],
//        ['email', 'require', '邮箱不能为空'],
//        ['email', 'email', '邮箱不合法'],
//        ['email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique'],
//        ['tel', 'require', '手机号码不能为空'],
//        ['tel', '/^1[34578]\d{9}$/', '手机号码不合法', self::EXISTS_VALIDATE, 'regex'],
//        ['tel', '', '手机号码已存在', self::EXISTS_VALIDATE, 'unique'],
//        ['checkcode', 'require', '验证码不能为空'],
      ['checkcode', 'checkCheckcode', '验证码不正确', self::EXISTS_VALIDATE, 'callback'],
//       ['captcha', 'checkTelcode', '手机验证码不合法', self::MUST_VALIDATE, 'callback', 'reg'],
    ];
    protected $_auto = [
        ['add_time', NOW_TIME, 'reg'],
        //生成随机盐在用户注册时为用户的密码进行加盐加密处理
        ['salt', '\Org\Util\String::randString', 'reg', 'function']
    ];

    /**
     * 检查收集验证码是否匹配
     *条件只是在注册时候检验验证码是否正确
     */
    protected function checkTelcode($code)
    {
        //获取session
        $sess_code = session('TEL_CODE');
        if (empty($sess_code)) {
            return false;
        }
        //如果成功将用户信息写入session
        session('TEL_CODE', null);
        return $code == $sess_code['code'] && I('post.tel') == $sess_code['tel'];
    }

    /**
     * @param $code
     * @return bool
     * 验证验证码
     */

    protected function checkCheckcode($code)
    {
        $verify = new Verify();
//      dump($code);
//      dump($verify->check($code));exit;
        return $verify->check($code);
    }
    /**
     * 生成令牌，保存到cookie和db中
     * @param type $admin_info
     */
    private function _saveToken($userinfo,$is_remember=false) {
        //如果勾选了记住密码，就生成token
        if($is_remember){
            //生成随机字符串，我们习惯上称之为令牌token
            $token = \Org\Util\String::randString(32);
            //存储到cookie一份
            $data  = [
                'id'    => $userinfo['id'],
                'token' => $token,
            ];
            cookie('AUTO_LOGIN_TOKEN', $data, 604800);
            //存储到数据库一份
            $this->save($data);
        }
    }
    /**
     * 完成用户自动登录
     * @return type
     */
    public function autoLogin() {
        //获取cookie数据
        $cookie = cookie('AUTO_LOGIN_TOKEN');
        //如果没有cookie，就返回空
        if(empty($cookie)){
            return [];
        }
        //检查数据库中是否有匹配的记录
        //其实可以
        if($userinfo = $this->where($cookie)->where(['token'=>['neq','']])->find()){
            //更新令牌
            $this->_saveToken($userinfo,true);
            //保存管理员信息到session中
            session('USER_INFO', $userinfo);
            return $userinfo;
        }else{
            return [];
        }
    }

    /**
     * 用户注册,加盐加密.
     * @return type
     */
    public function addMember()
    {
        $this->data['password'] = salt_mcrypt($this->data['password'], $this->data['salt']);

        //发送邮件
        //邮件中带有一个激活链接,点击就验证参数是否正确(通过一个随机字符串)
        $address = $this->data['email'];
        $subject = '欢迎注册小小杜的网站';
        $token = \Org\Util\String::randString(32);
        $url = U('Member/active', ['token' => $token, 'email' => $address], '', true);
        $content = '<h2>欢迎注册</h2><p>感谢您注册小小杜的网站,账号需要激活才能使用,请点击<a href="' . $url . '">激活链接</a></p><p>如果无法点击,请复制下面的地址在浏览器中粘贴打开' . $url . '</p>';
        if (!$rst = send_mail($address, $subject, $content)) {
            //如果发送没有成功则返回false打印出错误信息
            dump($rst);
            exit;
        }
        $this->data['active_token'] = $token;
        // 发送成功
        return $this->add();
    }

    /**
     * 用户登录账户
     *1.需要检测用户名是否存在数据库
     * 2.对用户的密码进行加盐加密然后进行比较
     *
     */
    public function login()
    {
        //检查是否有这个用户
        $username = $this->data['username'];
        $password = $this->data['password'];
        if ($userinfo = $this->where(['status'=>0])->getByUsername($username)) {
            $this->error='或许你还没有激活账号!';
            return false;
        }
        if (!$userinfo = $this->getByUsername($username)) {
           $this->error = '用户名或密码错误';
            return false;
        }
        //检验密码是否正确
        //调用加盐加密函数进行处理用户输入的密码
        $new_password = salt_mcrypt($password, $userinfo['salt']);
        if ($new_password != $userinfo['password']) {
            $this->error = '用户名或密码有误';
            return false;
        }

        //记录用户的登陆时间
        $data = [
            'id' => $userinfo['id'],
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        ];
        //如果只是更新个别字段的值，可以使用setField方法
        $this->setField($data);
        //保存用户信息到session中
        session('USER_INFO',  $userinfo);

        //保存cookie信息
        $this->_saveToken($userinfo,I('post.remember'));
        return true;
    }


}