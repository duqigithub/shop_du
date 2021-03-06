<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html>
<html>
    <head>
        <title>ECSHOP 管理中心 - 添加文章 </title>
        <meta name="robots" content="noindex, nofollow"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="/Public/css/general.css" rel="stylesheet" type="text/css" />
        <link href="/Public/css/main.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="/Public/ext/uploadify/common.css" />
    </head>
    <body>
        <h1>
            <span class="action-span"><a href="<?php echo U('index');?>">文章</a></span>
            <span class="action-span1"><a href="#">ECSHOP 管理中心</a></span>
            <span id="search_id" class="action-span1"> - 添加文章 </span>
        </h1>
        <div style="clear:both"></div>
        <div class="main-div">
            <form method="post" action="<?php echo U('add');?>" enctype="multipart/form-data" >
                <table cellspacing="1" cellpadding="3" width="100%">
                    <tr>
                        <td class="label">文章名称</td>
                        <td>
                            <input type="text" name="name" maxlength="60" value="<?php echo ($row["name"]); ?>" />
                            <span class="require-field">*</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">文章分类</td>
                        <td>
                            <select name="article_category_id" class="article_category_id">
                                <option value="">请选择</option>
                                <?php if(is_array($categories)): foreach($categories as $key=>$category): ?><option value="<?php echo ($key); ?>"><?php echo ($category); ?></option><?php endforeach; endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">文章简介</td>
                        <td>
                            <textarea  name="intro" cols="60" rows="4"  ><?php echo ($row["intro"]); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">文章内容</td>
                        <td>
                            <textarea  name="content" cols="60" rows="8"  ><?php echo ($row["content"]); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">排序</td>
                        <td>
                            <input type="text" name="sort" maxlength="40" size="15" value="<?php echo ((isset($row["sort"]) && ($row["sort"] !== ""))?($row["sort"]):20); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="label">是否显示</td>
                        <td>
                            <input type="radio" name="status" value="1" class='status' /> 是
                            <input type="radio" name="status" value="0" class='status' /> 否
                        </td>
                    </tr>
                    <tr>
                        <td class="label">录入时间</td>
                        <td>
                            <input type="date" name="user_date" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><br />
                            <input type="hidden" value="<?php echo ($row["id"]); ?>" name='id'/>
                            <input type="submit" class="button" value=" 确定 " />
                            <input type="reset" class="button" value=" 重置 " />
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div id="footer">
            共执行 1 个查询，用时 0.018952 秒，Gzip 已禁用，内存占用 2.197 MB<br />
            版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。
        </div>

        <script type="text/javascript" src="/Public/js/jquery.min.js"></script>
        <script type="text/javascript">
            $(function(){
                //回显文章状态
                $('.status').val([<?php echo ((isset($row["status"]) && ($row["status"] !== ""))?($row["status"]):1); ?>]);
                //回显分类
                <?php if(isset($row)): ?>$('.article_category_id').val([<?php echo ($row["article_category_id"]); ?>]);<?php endif; ?>
            });
        </script>
    </body>
</html>