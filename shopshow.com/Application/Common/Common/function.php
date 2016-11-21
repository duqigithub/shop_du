<?php

/**
 * @link http://blog.kunx.org/.
 * @copyright Copyright (c) 2016-11-10 
 * @license kunx-edu@qq.com.
 */

/**
 * 将模型错误信息变成一个有序列表字符串.
 *
 * @param \Think\Model $model 模型.
 *
 * @return string
 */
function get_error(\Think\Model $model) {
    $errors = $model->getError();
    if (!is_array($errors)) {
        $errors = [$errors];
    }
    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html.='</ol>';
    return $html;
}

/**
 * 将二维关联数组转换成下拉列表
 * @param array $data 二维数组.
 * @param string $value_field 值字段
 * @param string $name_field 文案提示字段
 * @param string $form_name 控件名字
 * @param string $select_value 默认选中的项
 * @return string 下拉列表的html代码.
 */
function arr2select(array $data, $value_field, $name_field, $form_name, $select_value) {
    $html = '<select name="' . $form_name . '" class="' . $form_name . '">';
    $html.='<option value="">--请选择--</option>';
    foreach ($data as $item) {
        if ($select_value == $item[$value_field]) {
            $html .= '<option value="' . $item[$value_field] . '" selected="selected">' . $item[$name_field] . '</option>';
        } else {
            $html .= '<option value="' . $item[$value_field] . '">' . $item[$name_field] . '</option>';
        }
    }

    $html .= '</select>';
    return $html;
}

/**
 * 加盐加密
 * @param string $password 原始密码.
 * @param string $salt     盐.
 * @return string 加盐加密后的结果.
 */
function salt_mcrypt($password, $salt) {
    return md5(md5($password) . $salt);
}

/**
 * @param $address
 * @param $subject
 * @param $content
 * @return array
 * @throws Exception
 * @throws phpmailerException
 *
 * 邮件发送接口配置
 */
function send_mail($address, $subject, $content) {
    vendor('PhpMailer.PHPMailerAutoload');
    $mail             = new \PHPMailer;
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host       = 'smtp.163.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                               // Enable SMTP authentication
    $mail->Username   = '15680834168@163.com';                 // SMTP username
    $mail->Password   = 'dq923186';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 465;                                    // TCP port to connect to

    $mail->setFrom('15680834168@163.com');
    $mail->addAddress($address);     // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->CharSet = 'UTF-8';

    if(!$mail->send()){
        //出错了
        $data = [
            'status'=>false,
            'msg'=>$mail->ErrorInfo,
        ];
    }else{
        $data = [
            'status'=>true,
            'msg'=>'发送成功',
        ];
    }
    return $data;
}
