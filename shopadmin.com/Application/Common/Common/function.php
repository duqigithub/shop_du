<?php
/**
 * 处理错误信息为列表
 * @param $userModel
 * @return string
 */
function dealErrorStr($userModel)
{
    $errors = $userModel->getError();
    $errorStr = "<ul>";
    //判断是否批量验证的数组错误信息
    if (is_array($errors)) {
        foreach($errors as $error){
            $errorStr .= "<li>$error</li>";
        }
    }else{
        $errorStr .= "<li>$errors</li>";
    }
    $errorStr .= "</ul>";
    return $errorStr;
}
function get_error(\Think\Model $model) {
    $errors = $model->getError();
    if (!is_array($errors)) {
        $errors = [$errors];
    }

    $html = '<ol>';
    foreach ($errors as $error) {
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '</ol>';
    return $html;
}
function arr2select(array $data, $name_field = 'name', $value_field = 'id', $name = '',$default_value='') {
    $html = '<select name="' . $name . '" class="' . $name . '">';
    $html .= '<option value=""> 请选择 </option>';
    foreach ($data as $key => $value) {
        //由于get和post提交的数据都是字符串,所以可能存在数字0和空字符串相等的问题
        //我们将遍历的数据变成string,然后强制类型比较.
        if((string)$value[$value_field] === $default_value){
            $html .= '<option value="' . $value[$value_field] . '" selected="selected">' . $value[$name_field] . '</option>';
        }else{
            $html .= '<option value="' . $value[$value_field] . '">' . $value[$name_field] . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

