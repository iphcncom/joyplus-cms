<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "info" :headAdmin ("云推推渠道管理"); info();break;
	default : headAdmin ("云推推渠道管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$tv_playfrom = be("post","tv_playfrom" .$id);
		$tv_definition = be("post","tv_definition" .$id);
		$tv_playurl= be("post","tv_playurl" .$id);//var_dump($t_type);
		$status= be("post","status" .$id);
		if (isN($tv_playurl)) { echo "信息填写不完整!";exit;}
	    if (isN($tv_playfrom)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}tv_play",array("tv_playfrom", "tv_playurl","status","tv_definition"),array($tv_playfrom,$tv_playurl,$status,$tv_definition),"id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main(){
	global $db,$cache;
	
	 $keyword = be("all", "keyword"); 	 
	 
	
   $where = " 1=1 ";
    if(!isN($keyword)){
    	$keyword=trim($keyword); 
      $where .= " AND name like '%".$keyword."%'";
    }
    
    

    
   
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM apk_company"." where ".$where;
	$nums = $db->getOne($sql); 
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM apk_company  where ".$where." ORDER BY id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
function filter(){
	var tv_id=$("#keyword").val();
	var url = "admin_apk_company.php?keyword="+tv_id;
	window.location.href=url;
}

$(document).ready(function(){
	$("#form2").validate({
		rules:{
			name:{
				required:true,
//				stringCheck:true,
				maxlength:64
			},
			zipcode:{
				isZipCode:true
			},
			email:{
				email:true
			}
//			t_enname:{
//				required:true,
////				stringCheck:true,
//				maxlength:128
//			},
//			t_template:{
//				required:true,
//				maxlength:128
//			},
			
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab=apk_company");
				$("#form1").submit();
			}
			else{return false}
	});

	$("#btnAddLunBo").click(function(){
		if(confirm('确定要添加到轮播图吗')){
			$("#form1").attr("action","admin_ajax.php?action=lunboForTopic&flag=batch&tab={pre}vod_popular");
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
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab=apk_company&col=id&val='+id);
}
</script>

<table class="admin_apk_company tb">
	<tr>
	<td colspan="2">
	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">&nbsp;
	
	
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	
	</td> 
	</tr>
</table>

<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="5%">编号</td>
	<td>名称</td>
	<td>电子邮件</td>
	<td>联系人</td>
	<td>邮编</td>
	<td width="20%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr class="formlast"><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$t_id=$row["id"];
	?>
    <tr class="formlast">
	  <td>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
 
	<td>
	<?php echo $row["name"];?>
	</td>
	
	<td>
	<?php echo $row["email"];?>
	</td>
	
	<td>
	<?php echo $row["contact"];?>
	</td>
	<td>
	<?php echo $row["zipcode"];?>
	</td>
	
	  
	    <td>
	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> |  
	   <a href="admin_apk.php?company_id=<?php echo $t_id?>">应用列表</a> | 
	   <a href="admin_ajax.php?action=del&tab=apk_company&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> 
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr class="formlast">
	<td  colspan="7"><input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" /> 全选
	<input type="button" value="批量删除" id="btnDel" class="input"  />
<!--	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />-->
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_apk_company.php?page={p}&keyword=" . urlencode($keyword) )?>
	</td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab=apk_company" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	
	
	<tr>
	<td>名称：</td>
	<td><input type="text" name="name" id="name" value="" size=25>
	</td>
	</tr>
	
	<tr>
	<td>电子邮件：</td>
	<td><input type="text" name="email" id="email" value="" size=25>
	</td>
	</tr>
	
	<tr>
	<td>联系人：</td>
	<td><input type="text" name="contact" id="contact" value="" size=25>
	</td>
	</tr>
	
	<tr>
	<td>邮编：</td>
	<td><input type="text" name="zipcode" id="zipcode" value="" size=25>
	</td>
	</tr>
	
	<tr>
	<td>地址：</td>
	<td><input type="text" name="adress" id="adress" value="" size=25>
	</td>
	</tr>
	
	
	
	 <tr>
     <td>是否隐藏：</td>
      <td><select id="status" name="status">
	   <option value="0" selected>隐藏</option>
	   <option value="1" >显示</option>
	</select>
	  </td>
    </tr>
    
	<tr>
     <td>简介：</td>
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