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
<script language="javascript" src="<?php echo ADMIN_THEME?>js/jquery.min.js"></script>
<script language="javascript" src="<?php echo ADMIN_THEME?>js/dialog.js"></script>
<title>后台管理</title>
</head>
<body style="font-weight: normal;">
<div class="subnav">

<div class="content-menu ib-a blue line-x">

<a href='<?php echo purl("admin/alist", array('aid'=>$aid))?>' class="on"><em>首页分类管理</em></a><span>|</span>
    <a href='<?php echo purl("admin/adda", array('aid'=>$aid))?>'><em>新增</em></a><span>|</span>
<a href='<?php echo purl("admin/cache")?>'><em>更新缓存</em></a>

</div>
<div class="table-list">
<form action="" method="post">
<input name="form" id="list_form" type="hidden" value="del">
<table width="100%">
	<thead>
	<tr>
	  <th width="50" align="right">选择&nbsp;<input name="deletec" id="deletec" type="checkbox" onClick="setC()">&nbsp;</th>
		<th width="40" align="left">排序</th>
        <th width="250" align="left">绑定栏目</th>
        <th width="150" align="left">标题</th>
        <th width="150" align="left">小标题</th>
        <th width="50" align="left">默认图片</th>
        <th width="50" align="left">高亮图片</th>

		<th width="200"  align="left">管理操作</th>
	</tr>
    </thead>
    <tbody>
    <?php foreach ($list as $t) { ?>
	<tr height="25">
	  <td align="right"><input name="data[]" value="<?php echo $t['id'];?>" type="checkbox" class="deletec">&nbsp;</td>
	  <td align="left"><input type="text" class="input-text" value="<?php echo $t['listorder']?>" name="order[<?php echo $t['id'];?>]" style="width:25px;"></td>

	  <td align="left"><?php echo $t['catname']?catpos($t['catid'],'-'):'无';?></td>
      <td align="left"><?php echo $t['name'];?></td>
      <td align="left"><?php echo $t['sub_name'];?></td>
      <td align="left"><img style="height: 100px;" src="<?php echo $t['default_pic'];?>" alt=""></td>
      <td align="left"><img style="height: 100px;" src="<?php echo $t['hover_pic'];?>" alt=""></td>
     

	  <td align="left">
      <a href="<?php echo purl('admin/edita/',array('id'=>$t[id]));?>">修改</a>
      </td>
	  </tr>
      <?php } ?>


	<tr height="25">
	  <td colspan="9" align="left">
      <input type="submit" class="button" value="删  除" name="submit" onClick="$('#list_form').val('del')">
      <input type="submit" class="button" value="更  新" name="submit" onClick="$('#list_form').val('order')">
      </td>
	  </tr>
	<tr>
	  <td>      
	  </tbody>
</table>
<?php echo $pagelist?>

</form>
</div>
</div>
<script language="javascript">
function setC() {
	if($("#deletec").attr('checked')) {
		$(".deletec").attr("checked",true);
	} else {
		$(".deletec").attr("checked",false);
	}
}
function getViewData(id) {
	var url = '<?php echo purl("admin/ajaxview/", array("id"=>""))?>'+id;
	window.top.art.dialog(
	    {id:"ajaxview",iframe:url, title:'模板数据调用', width:'260', height:'80', lock:true,
		button: [
            {
				name: '复制',
				callback: function () {
					 var d = window.top.art.dialog({id:"ajaxview"}).data.iframe;
			         var c = d.document.getElementById('ads_'+id).value;
					 copyToClipboard(c);
					 return false;
				},
				focus: true
            }, {
                name: '关闭'
            }
        ]
		
		}
	);
}
function copyToClipboard(meintext) {
    if (window.clipboardData){
        window.clipboardData.setData("Text", meintext);
    } else if (window.netscape){
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将 'signed.applets.codebase_principal_support'设置为'true'"); 
		} 
        var clip = Components.classes['@mozilla.org/widget/clipboard;1'].
        createInstance(Components.interfaces.nsIClipboard);
        if (!clip) return;
        var trans = Components.classes['@mozilla.org/widget/transferable;1'].
        createInstance(Components.interfaces.nsITransferable);
        if (!trans) return;
        trans.addDataFlavor('text/unicode');
        var len = new Object();
        var str = Components.classes["@mozilla.org/supports-string;1"].
        createInstance(Components.interfaces.nsISupportsString);
        var copytext=meintext;
        str.data=copytext;
        trans.setTransferData("text/unicode",str,copytext.length*2);
        var clipid=Components.interfaces.nsIClipboard;
        if (!clip) return false;
        clip.setData(trans,null,clipid.kGlobalClipboard);
    }
    alert("复制成功，您可以粘贴到您模板中了");
    return false;
}
</script>
</body>
</html>