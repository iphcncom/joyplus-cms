<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "setLatestItem" : setLatestItem();break;
	case "info" :headAdmin ("视频榜单管理"); info();break;
	default : headAdmin ("视频榜单管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
	
		$status= be("post","status" .$id);
		$disp_order = be("post","disp_order" .$id);
		if (isN($disp_order)) { $t_sort= $db->getOne("SELECT MAX(disp_order) FROM apk_master_items ")+1; }
		if (!isNum($disp_order)) { echo "信息填写不完整!";exit;}
		$db->Update ("apk_master_items",array("status","disp_order"),
		array($status,$disp_order),"id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function setLatestItem()
{
	global $db;
	$t_id = be("all","id");
	$apk_id = be("all","apk_id");
	$db->Update ("apk_master_base",array('latest_item_id'),array($t_id),"id=".$apk_id);
	echo "修改完毕";
}

function main()
{global $db;$loginname=getCookie("adminname");
	
	 $apk_id = be("all", "apk_id"); 	 
	
  
	$sql = "SELECT * FROM apk_master_items  where apk_id=".$apk_id." ORDER BY version_code desc,version_name desc, upload_count desc,id ASC ";
	$rs = $db->query($sql);
	
	$rowapk= $db->getRow("select * from apk_master_base where id=".$apk_id);
	
	
?>
<script language="javascript">
function filter(){
	var category_id=$("#category_id").val();
	var status=$("#status").val();
	var keyword=$("#keyword").val();
	 
	var can_search_device=$("#can_search_device").val();
	var url = "admin_apk.php?category_id="+category_id+"&keyword="+encodeURI(keyword)+"&status="+status;
	window.location.href=url;
}

$(document).ready(function(){
	$("#form2").validate({
		rules:{
			t_name:{
				required:true,
//				stringCheck:true,
				maxlength:64
			},
//			t_enname:{
//				required:true,
////				stringCheck:true,
//				maxlength:128
//			},
//			t_template:{
//				required:true,
//				maxlength:128
//			},
			t_pic:{
				maxlength:254
			},
			t_sort:{
				number:true
			},
			t_des:{
				required:true,
				maxlength:254
			}
		}
	});
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
	$('#form3').form({
		onSubmit:function(){
			if(!$("#form3").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab=apk_master_items");
				$("#form1").submit();
			}
			else{return false}
	});

	

	
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
//	$("#btnAdd").click(function(){
//		$('#form2').form('clear');
//		$("#flag").val("add");
//		$('#win1').window('open');
//		
//	});

	$("#btnAdd").click(function(){
		$('#form3').form('clear');
		$("#flag2").val("add");
		$('#win3').window('open');
		
	});
	
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
	
	$("#btnCancel2").click(function(){
		location.href= location.href;
	});
	
});
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab=apk_master_base&col=id&val='+id);
}
function setLatestItem(id,apk_id)
{
	 $.ajax({
         type: "GET",
         url: 'admin_apk_items.php?action=setLatestItem&id='+id+"&apk_id="+apk_id,
         beforeSend: function(){},
         complete: function(){},
         success:function(resp){
             alert(resp);
             location.href= location.href;
         }
     });
}


function edit2(id)
{
	$('#form3').form('clear');
	$("#flag2").val("edit");
	$('#win3').window('open');
	$('#form3').form('load','admin_ajax.php?action=getinfo&tab=apk_master_items&col=id&val='+id);
}


</script>

<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr><td colspan="4" align="center">应用基本信息   <a href="javascript:void(0)" onclick="edit('<?php echo $apk_id?>');return false;"><font color="red">修改基本信息</font></a></td></tr>
	
	<tr>
	<td width="10%">应用名称：</td>
	<td> <?php echo $rowapk['app_name']?>
	</td>
	
	<td width="10%">应用包名：</td>
	<td> <?php echo $rowapk['package_name']?>
	</td>
	</tr>
	
	<tr>
	<td width="10%">应用类别：</td>
	<td> <select id="category_id" name="category_id" disabled>
	<?php echo makeSelectAll("apk_category","id","name","parent_id","disp_order",0,"","&nbsp;|&nbsp;&nbsp;",$rowapk['category_id'])?>
	</select>
	</td>
	
	<td width="10%">应用是否正常：</td>
	<td> <select id="status" name="status" disabled>
	<option value="1" <?php if ($rowapk['status']==1){ echo "selected";} ?>>正常</option>
	<option value="-1" <?php if ($rowapk['status']==-1){ echo "selected";} ?>>不正常</option>
	</select>
	</td>
	</tr>
	
	<tr>
	<td width="10%">渠道：</td>
	<td> 
	<select id="company_id" name="company_id" disabled>

	<?php echo makeSelect("apk_company","id","name","id","","&nbsp;|&nbsp;&nbsp;",$rowapk['company_id'])?>
	</select>
	</td>
		
	<td width="10%">下载次数：</td>
	<td> <?php echo $rowapk['upload_count']?>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 排序：<?php echo $rowapk['disp_order']?>
	</td>
	</tr>
	
	<tr>
	<td width="10%">标签：</td>
	<td> <?php echo $rowapk['apk_tag']?>
	</td>
	
	<td width="10%">图标：</td>
	<td> <?php echo $rowapk['apk_icon']?>
	</td>
	</tr>
	
	<tr>
	<td width="10%">简介：</td>
	<td colspan="3"> <?php echo $rowapk['description']?>
	</td>
	
	
	</tr>
	
	</table>
	</td>
	</tr>
</table>

<table class="tb">
 <tr>
	<td  colspan="9"> 应用版本列表
	</td>
</tr>
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="5%">编号</td>
	<td>版本号</td>
	<td>版本名</td>
	<td width="10%">文件大小</td>
	<td width="15%">md5</td>
	<td width="8%">下载次数</td>	
	<td width="8%">是否正常</td>
	<td width="5%">排序</td>
	<td width="20%">操作</td>
	</tr>
	<?php
		if(false){
	?>
  
    <?php
		}
		else{
			$latest_apk_item_id=$rowapk['latest_item_id'];
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$t_id=$row["id"];
		  		if($latest_apk_item_id ===$t_id) {
		  			
		  		}
	?>
    <tr <?php if($latest_apk_item_id ===$t_id) { echo 'style="background: #DE11FA"';}?>>
	  <td>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
      <td >
     <?php echo $row["version_code"]?></td>
       <td>
      <?php echo $row["version_name"]?></td>
       <td>
      <?php echo $row["file_size"]?></td>
        <td>
      <?php echo $row["md5"]?></td>
        <td align="center">
      <?php echo $row["upload_count"]?></td>
      <td>
      <select id="status<?php echo $t_id?>" name="status<?php echo $t_id?>">
	<option value="0">版本是否正常</option>
	<option value="1" <?php echo $row["status"]; if ($row["status"]==1){ echo "selected";} ?>>正常</option>
	<option value="-1" <?php if ($row["status"]==-1){ echo "selected";} ?>>不正常</option>
	</select></td>
	 
       <td>
	
	  <input name="disp_order<?php echo $t_id?>" type="text" value="<?php echo $row["disp_order"]?>"  size="5"/></td>
	  
	  
	  
	  
      <td>
	  <a href="javascript:void(0)" onclick="edit2('<?php echo $t_id?>');return false;">修改</a> | 
	  <?php if($latest_apk_item_id ===$t_id) {?>
	      
	  <?php }else {?>
	    <a href="javascript:void(0)" onclick="setLatestItem('<?php echo $t_id?>','<?php echo $apk_id;?>');return false;">设置为最新版</a> | 
	    <?php }?>
	 <?php if($loginname ==='dale'){?> <a href="admin_ajax.php?action=del&tab=apk_master_items&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> |<?php }?>
	  <a href="<?php echo $row["file_url"]?>" onClick="return confirm('确定要下载吗?');">下载apk</a>
<!--	  <a href="admin_ajax.php?action=lunboForTopic&tab={pre}vod_popular&t_id=<?php echo $t_id?>" onClick="return confirm('确定要添加到轮播图吗?');">添加到轮播图</a>-->
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="9">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
	<?php if($loginname ==='dale'){?><input type="button" value="批量删除" id="btnDel" class="input"  /><?php }?>
	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
<!--	&nbsp;<input type="button" value="添加到轮播图" id="btnAddLunBo" class="input"  />-->
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_apk.php?page={p}&status=" . $status . "&keyword=" . urlencode($keyword) . "&category_id=" . $category_id)?>
	</td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab=apk_master_base" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="30%">应用名称：</td>
	<td><input id="app_name" size=40 value="" name="app_name">
	</td>
	</tr>
	
	<tr>
	<td width="30%">应用包名：</td>
	<td><input id="package_name" size=40 value="" name="package_name">
	</td>
	</tr>
	<tr>
	<td width="30%">图标：</td>
	<td><input id="apk_icon" size=40 value="" name="apk_icon">
	</td>
	</tr>
	<tr>
	<td>应用是否正常：</td>
	<td>  <select id="status" name="status">
	
	<option value="1" >正常</option>
	<option value="-1" >不正常</option>
	</select>
	</td>
    </tr>
	<tr>
	<td>应用类别：</td>
	<td><select id="category_id" name="category_id">

	<?php echo makeSelectAll("apk_category","id","name","parent_id","disp_order",0,"","&nbsp;|&nbsp;&nbsp;",'')?>
	</select>
	</td>
	</tr>
	
	<tr>
	<td>应用渠道：</td>
	<td><select id="company_id" name="company_id">

	<?php echo makeSelect("apk_company","id","name","id","","&nbsp;|&nbsp;&nbsp;",'')?>
	</select>
	</td>
	</tr>
	
	<tr>
	<td width="30%">下载次数：</td>
	<td><input id="upload_count" size=40 value="" name="upload_count">
	</td>
	</tr>
	
	<tr>
     <td>排序：</td>
      <td><input id="disp_order" size=40 value="" name="disp_order" >
	  </td>
    </tr>
    
    <tr>
     <td>标签：</td>
      <td><input id="apk_tag" size=40 value="" name="apk_tag" >
	  </td>
    </tr>
    
   
    
    
	<tr>
     <td>描述信息：</td>
      <td>
      <TEXTAREA id="description" NAME="description" ROWS="2" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
	  </td>
    </tr>
    <tr align="center" >
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"></td>
    </tr>
</table>
</form>
</div>




<div id="win3" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab=apk_master_items&apk_id=<?php echo $apk_id;?>&package_name=<?php echo $rowapk['package_name']?>" method="post" name="form3" id="form3">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag2" name="flag2" type="hidden" value="">
	<tr>
	<td width="30%">版本号：</td>
	<td><input id="version_code" size=40 value="" name="version_code">
	</td>
	</tr>
	
	<tr>
	<td width="30%">版本名：</td>
	<td><input id="version_name" size=40 value="" name="version_name">
	</td>
	</tr>
	
	<tr>
	<td>版本是否正常：</td>
	<td>  <select id="status" name="status">	
	<option value="1" >正常</option>
	<option value="-1" >不正常</option>
	</select>
	</td>
    </tr>
	
	<tr>
	<td width="30%">下载次数：</td>
	<td><input id="upload_count" size=40 value="" name="upload_count" disabled>
	</td>
	</tr>
	
	<tr>
     <td>排序：</td>
      <td><input id="disp_order" size=40 value="" name="disp_order" >
	  </td>
    </tr>
    
    
    <tr>
     <td>下载地址：</td>
      <td><input id="file_url" size=40 value="" name="file_url" >
	  </td>
    </tr>
    
    <tr>
     <td>文件大小：</td>
      <td><input id="file_size" size=40 value="" name="file_size" >
	  </td>
    </tr>
   
    <tr>
     <td>md5：</td>
      <td><input id="md5" size=40 value="" name="md5" >
	  </td>
    </tr>
    
    <tr>
     <td>文件名：</td>
      <td><input id="file_name" size=40 value="" name="file_name" >
	  </td>
    </tr>
    
    <tr>
     <td>文件类别：</td>
      <td><input id="file_type" size=40 value="" name="file_type" >
	  </td>
    </tr>
    
     <tr>
     <td>七牛文件key：</td>
      <td><input id="qiniu_file_key" size=40 value="" name="qiniu_file_key" >
	  </td>
    </tr>
   
    
    
	<tr>
     <td>描述信息：</td>
      <td>
      <TEXTAREA id="description" NAME="description" ROWS="2" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
	  </td>
    </tr>
    <tr align="center" >
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel2"></td>
    </tr>
</table>
</form>
</div>

</body>
</html>
<?php
unset($rs);
}



function info()
{
	global $db,$cache;
	
	 $prod_id = be("all", "id");
	$sql = "SELECT * FROM {pre}vod_topic ,{pre}vod_topic_items as items where t_id = topic_id and t_userid=0 and t_id>4 and  vod_id=".$prod_id." ORDER BY t_sort,t_id ASC ";
	//var_dump($sql)
	$rs = $db->query($sql);
	$nums=1;
?>


<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="10%">编号</td>
	<td>名称</td>
	<td>关注度</td>
	<td width="15%">类别</td>
	<td width="15%">视频栏目</td>
	<td width="10%">显示到App</td>
	<td width="20%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$t_id=$row["t_id"];
	?>
    <tr>
	  <td>
	 </td>
      <td><?php echo $t_id?></td>
      <td>
     <?php echo $row["t_name"]?></td>
	  <td>
	   <select disabled id="t_toptype<?php echo $t_id?>" name="t_toptype<?php echo $t_id?>">
	<option value="-1">关注度</option>
	<option value="1" <?php if ($row["t_toptype"]==1){ echo "selected";} ?>>热门</option>
	<option value="0" <?php if ($row["t_toptype"]==0){ echo "selected";} ?>>非热门</option>
	</select>
	  </td>
	  <td>
	 
	  <select disabled id="t_type<?php echo $t_id?>" name="t_type<?php echo $t_id?>">
	<option value="0" <?php if($row["t_type"]==0){ echo "selected";} ?>>没有栏目</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$row["t_type"])?>
	</select>
	  </td>
       <td>
	 <select disabled id="t_bdtype<?php echo $t_id?>" name="t_bdtype<?php echo $t_id?>">
	<option value="-1">榜单类型</option>
	<option value="1" <?php if ($row["t_bdtype"]==1){ echo "selected";} ?>>悦单</option>
	<option value="2" <?php if ($row["t_bdtype"]==2){ echo "selected";} ?>>悦榜</option>
	</select></td>
      
	  <td>
	 
	  <select disabled id="t_flag<?php echo $t_id?>" name="t_flag<?php echo $t_id?>">
	<option value="0" <?php if($row["t_flag"]==0){ echo "selected";} ?>>不显示</option>
	<option value="1" <?php if($row["t_flag"]==1){ echo "selected";} ?>>显示</option>
	</select>
	  </td>
	  
	  
	  
      <td>
	  <a href="admin_vod_topic_items.php?topic_id=<?php echo $t_id?>">显示视频列表</a> |
<!--	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a>-->
	  </td>
    </tr>
	<?php
			}
		}
	?>
	   
</table>
</form>

</body>
</html>
<?php
unset($rs);
}
?>