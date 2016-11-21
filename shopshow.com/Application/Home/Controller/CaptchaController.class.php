<?php
namespace Home\Controller;

use Think\Controller;
use Think\Verify;

/**
 * Description of CaptchaController
 *
 * create by D
 */
class CaptchaController extends Controller
{
    public function show()
    {
        //展示验证码
        $options = [
            'length' => 4,
            'useNoise' => false,
        ];
        //验证成功
        $verify = new Verify($options);
        $verify->entry();
    }
}
