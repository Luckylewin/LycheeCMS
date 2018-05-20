<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="<?php echo ADMIN_THEME?>images/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/system.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/dialog.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/main.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/switchbox.css" rel="stylesheet" type="text/css" />
<link href="<?php echo ADMIN_THEME?>images/table_form.css" rel="stylesheet" type="text/css" />
<script src="<?php echo ADMIN_THEME?>js/jquery.min.js"></script>
<script src="<?php echo COMMON_THEME?>js/layer.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_THEME?>js/dialog.js"></script>
<script type="text/javascript">var sitepath = "<?php echo SITE_PATH;?>";</script>
<script type="text/javascript" src="<?php echo LANG_PATH?>lang.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_THEME?>js/core.js"></script>
<title>后台管理</title>
</head>
<body style="font-weight: normal;">
<div class="subnav">
<div class="content-menu ib-a blue line-x">

<a href='<?php echo purl("admin/alist", array('aid'=>$aid))?>'><em>首页分类管理</em></a><span>|</span>
<a href='<?php echo purl("admin/cache")?>'><em>更新缓存</em></a>

</div>
<div class="bk10"></div>
<div class="table-list">
<form action="" method="post">
<input name="data[id]" type="hidden" id="id" value="<?php echo $data['id']?>">
<table width="100%" class="table_form ">
	<tr>
        <th width="200">标题：</th>
        <td><input type="text"  class="input-text" style="width:200px;" value="<?php echo $data['name']?>" name="data[name]"></td>
    </tr>

    <tr>
        <th width="200">小标题：</th>
        <td><input type="text" class="input-text" style="width:200px;" value="<?php echo $data['name']?>" name="data[sub_name]"></td>
    </tr>

    <tr>
        <th width="200">序号：</th>
        <td><input type="text" class="input-text" style="width:50px;" value="<?php echo $data['name']?>" name="data[listorder]"></td>
    </tr>
    
    <tr>
        <th width="200">栏目绑定</th>
        <td id="bindcat">

            <select class="cat" name="data[catids][]" id="bindcat" <?php if($action=='edit') echo "disabled='disabled'"; ?> style="<?php if($action=='edit') echo 'color:grey;'?>" >
                <?php 
                    echo  '<option value="-1" selected="selected">不绑定</option>';
                    foreach ($cat as $key => $c) {
                        $child = $c['child'] ? "child='1'" : "child='0'";
                        $select = $cat['catid']==$data['catid']? 'selected':'';
                        echo '<option value="'.$c['catid'].'" '.$child.'">'.$c['catname'].'</option>';
                    }
                ?>
            </select>
        </td>
    </tr>


    <tr id="fine_setting_thumb">
        <th>上传默认图片：</th><td><input type="text" class="input-text" size="50" value="" name="data[default_pic]" id="fc_setting_thumb"  />
            <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="preview('fc_setting_thumb')" value="预览" />
            <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadImage('fc_setting_thumb','','','300')" value="上传" />
            <span id="ck_setting_thumb"></span></td>
    </tr>

    <tr id="fine_setting_thumb">
        <th>上传高亮图片：</th><td><input type="text" class="input-text" size="50" value="" name="data[hover_pic]" id="fc_setting_hover"  />
            <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="preview('fc_setting_hover')" value="预览" />
            <input type="button" style="width:66px;cursor:pointer;" class="button" onClick="uploadImage('fc_setting_thumb','','','300')" value="上传" /><span id="ck_setting_thumb"></span></td></tr><tr id="fine_setting_url">

    </tr>
	<tr>
        <th></th>
        <td><input type="submit" class="button" value="提交" name="submit"></td>
    </tr>
</table>
</form>
</div>
</div>
<input type="hidden" value="<?php echo purl("admin/ajaxgetcat");?>" id="url">
<script type="text/javascript">

function post(catid)
{
    var url  =  $('#url').val()+'&catid='+catid;
        $.get(url,'',function(data)
        {   
            var obj =   eval('('+data+')');
            var select = $('<select style="margin-left:3px;" class="newcat" name="data[catids][]"><option value="0">所有子栏目适用</option></select>');
            $.each(obj.root,function(name,value) {

                    var ob = value;
                    if(!ob){return;}
                    var option = '<option value="'+ob.catid+'" child="'+ob.child+'">'+ob.catname+'</option>';
                    select.append(option);

                });
             console.log(obj.root);
             $('#bindcat').append(select);
            
        })
}
function change()
{
     $('.cat').change(function()
    {   
        var catid  =  $(this).val();
        $('.newcat').remove();
        post(catid);

    }) ;


}
function removeOpt()
{

}
function addnew(obj)
{
     post(catid);
}
$(function()
{
     change();
    //  $('.newcat').on('click','option',function(){ 
    //        alert($(this));
    // });
})

</script>
</body>
</html>