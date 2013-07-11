<?php
 require_once ("admin_conn.php");
 chkLogin();
$_SESSION["upfolder"] = "../upload/thirdpartlogo";
$action = be("all","action");
headAdmin ("API配置");main();
dispseObj();

function main()
{
	global $db,$menulist;
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}thirdpart_config";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT  id,device_name,api_url,logo_url,app_key FROM {pre}thirdpart_config ORDER BY id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript" src = "../js/md.js"></script>
<script language="javascript">
$(document).ready(function(){
	
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        location.href=location.href;
	        });
	    }
	});
	$('#form2').form({
		onSubmit:function(){
			if(!$("#form2").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}thirdpart_config");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$('#win1').window('open'); 
	});
	$("#btnCancel").click(function(){ 
		location.href= location.href;
	});
	$("#create_appkey").click(function(){

		var md5 = hex_md5(new Date().toString());

		$("#app_key").val(md5);
	});
});
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$.get('admin_ajax.php?action=getinfo&tab={pre}thirdpart_config&col=id&val='+id,function(obj){
		$("#win1 #id").val(obj.id);
		$("#device_name").val(obj.device_name);
		$("#api_url").val(obj.api_url);
		$("#logo_url").val(obj.logo_url);
		$("#app_key").val(obj.app_key);
	},"json");
}
</script>
<form action="" method="post" name="form1" id="form1">
<table class="tb">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="17%">设备名称</td>
	<td width="17%">API地址</td>
	<td width="15%">Logo URL</td>
	<td width="15%">APP KEY </td>
	<td width="15%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="6">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$id=$row["id"];
	?>
    <tr>
	  <td><input name="m_id[]" type="checkbox" value="<?php echo $id?>" /></td>
      <td><?php echo $row["device_name"]?></td>
      <td><?php echo $row["api_url"]?></td>
      <td><?php echo $row["logo_url"]?></td>
      <td><?php echo $row["app_key"]?></td>
      <td><a href="javascript:void(0)" onclick="edit('<?php echo $id?>');return false;">修改</a> |
	  <a href="admin_ajax.php?action=del&tab={pre}thirdpart_config&m_id=<?php echo $id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr  class="formlast">
	<td colspan="6">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'m_id[]')" />
	&nbsp;<input type="button" id="btnDel" value="批量删除" class="input" />
	&nbsp;<input type="button" id="btnAdd" value="添加"  class="input"/>
	</td></tr>
    <tr align="center"  class="formlast">
      <td colspan="6">
       <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"api_manager.php?page={p}")?>
      </td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:480px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}thirdpart_config" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="20%" >设备名称：</td>
	<td>
		<TEXTAREA id="device_name" NAME="device_name"  style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA><br>可写入多个设备名，以“,”分开
	<td/>
	</tr>
	<tr>
	<td width="20%" >API地址：</td>
	<td><INPUT id="api_url" type="text" style="width:300px" name="api_url">
	</td>
	</tr>
	<tr>
	<td width="20%" >Logo URL：</td>
	<td><INPUT id="logo_url" type="text" style="width:300px" name="logo_url">
	</td>
	</tr>
	<tr>
	<td width="20%" >APP KEY：</td>
	<td><INPUT id="app_key" type="text" style="width:300px" name="app_key" readonly="readonly"><br> <input class="input" type="button" value="生成APP KEY" id="create_appkey"> 
	</td>
	</tr>
	
	
	<tr align="center">
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php
unset($rs);
}
?>