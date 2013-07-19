<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
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
		$package_name = be("post","package_name" .$id);
		$category_id= be("post","category_id" .$id);//var_dump($t_type);
		$status= be("post","status" .$id);
		$apk_icon= be("post","apk_icon" .$id);
//		$upload_count = be("post","upload_count" .$id);
		
		$disp_order = be("post","disp_order" .$id);
		$app_name = be("post","app_name" .$id);
		if (isN($app_name)) { echo "信息填写不完整!";exit;}
		if (isN($disp_order)) { $disp_order= $db->getOne("SELECT MAX(disp_order) FROM apk_master_base ")+1; }
		if (!isNum($disp_order)) { echo "信息填写不完整!";exit;}
		$db->Update ("apk_master_base",array("package_name", "category_id","status","disp_order","app_name","apk_icon"),
		array($package_name,$category_id,$status,$disp_order,$app_name,$apk_icon),"id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main()
{global $db;
	$loginname=getCookie("adminname");
	 $keyword = be("all", "keyword"); 	 
	 
	
   $where = " 1=1 ";
    if(!isN($keyword)){
    	$keyword=trim($keyword); 
      $where .= " AND (package_name like '%".$keyword."%' or  app_name like '%".$keyword."%')";
    }
    
    $status = be("all", "status");
    if(!isNum($status)) {
    	$status = 0;
    } else {
    	$status = intval($status);
    }   

    if($status !=0){
    	$where .=" and status=".$status;
    }
   
    $category_id = be("all", "category_id");
    if(!isNum($category_id)) {
    	$category_id = 0;
    } else {
    	$category_id = intval($category_id);
    }   

    if($category_id !=0){
    	$where .=" and category_id=".$category_id;
    }
    
    $company_id = be("all", "company_id");
    
    if(isNum($company_id)) {    	
    	$company_id = intval($company_id);
    	$where .=" and company_id=".$company_id;
    }else {
    	$company_id=0;
    }   

    
    
    
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM apk_master_base "." where ".$where;
	
	$nums = $db->getOne($sql); 
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM apk_master_base  where ".$where." ORDER BY  upload_count desc,id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
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
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab=apk_master_base");
				$("#form1").submit();
			}
			else{return false}
	});

	

	
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$('#win1').window('open');
		
	});
	$("#btnCancel").click(function(){
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
</script>

<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：<select id="category_id" name="category_id">
	<option value="0">应用类别</option>
	<?php echo makeSelectAll("apk_category","id","name","parent_id","disp_order",0,"","&nbsp;|&nbsp;&nbsp;",$category_id)?>
	</select>
	

	
	&nbsp;
	<select id="status" name="status">
	<option value="0">应用是否正常</option>
	<option value="1" <?php if ($status==1){ echo "selected";} ?>>正常</option>
	<option value="-1" <?php if ($status==-1){ echo "selected";} ?>>不正常</option>
	</select>
	
	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">  
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	
	</td>
	<td width="150px">
		
	</td>
	</tr>
	</table>
	</td>
	</tr>
</table>

<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="5%">编号</td>
	<td>名称</td>
	<td>包名</td>
	<td>图标</td>
	<td width="3%">是否正常</td>
	<td width="15%">应用栏目</td>
	<td width="8%">下载次数</td>
	<td width="5%">排序</td>
	<td width="30%">操作</td>
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
		  		$t_id=$row["id"];
	?>
    <tr>
	  <td>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
      <td>
      <input type="text" name="app_name<?php echo $t_id?>" value="<?php echo $row["app_name"]?>" size="20"/></td>
       <td>
      <input type="text" name="package_name<?php echo $t_id?>" value="<?php echo $row["package_name"]?>" size="20"/></td>
       <td>
      <input type="text" name="apk_icon<?php echo $t_id?>" value="<?php echo $row["apk_icon"]?>" size="20"/></td>
      <td>
      <select id="status<?php echo $t_id?>" name="status<?php echo $t_id?>">
	<option value="0">应用是否正常</option>
	<option value="1" <?php if ($row["status"]==1){ echo "selected";} ?>>正常</option>
	<option value="-1" <?php if ($row["status"]==-1){ echo "selected";} ?>>不正常</option>
	</select></td>
	  <td>	   
	<select id="category_id<?php echo $t_id?>" name="category_id<?php echo $t_id?>">
	<option value="0">应用类别</option>
	<?php echo makeSelectAll("apk_category","id","name","parent_id","disp_order",0,"","&nbsp;|&nbsp;&nbsp;",$row["category_id"])?>
	</select>
	  </td>
	  <td><?php echo $row["upload_count"]?></td>
       <td>
	
	  <input name="disp_order<?php echo $t_id?>" type="text" value="<?php echo $row["disp_order"]?>"  size="5"/></td>
	  
	  
	  
	  
      <td>
	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> | 
	  <a href="admin_apk_items.php?apk_id=<?php echo $t_id?>">显示应用版本</a> |
	
	 <?php if($loginname ==='dale'){?> <a href="admin_ajax.php?action=del&tab=apk_master_base&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> |<?php }?>
<!--	  <a href="admin_ajax.php?action=lunboForTopic&tab={pre}vod_popular&t_id=<?php echo $t_id?>" onClick="return confirm('确定要添加到轮播图吗?');">添加到轮播图</a>-->
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="8">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
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
	  <a href="admin_vod_topic_items.php?topic_id=<?php echo $t_id?>">显示视频列表</a> <?php if($loginname ==='dale'){?>|
	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> <?php }?>
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