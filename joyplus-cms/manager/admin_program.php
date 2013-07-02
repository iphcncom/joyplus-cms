<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "exportExcel" : exportExcel();break;
	case "info" :headAdmin ("电视直播管理"); info();break;
	default : headAdmin ("电视直播管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$tv_name = be("post","tv_name" .$id);
		$tv_code= be("post","tv_code" .$id);//var_dump($t_type);
		$tv_type= be("post","tv_type" .$id);
		$tv_playurl = be("post","tv_playurl" .$id);
		$status = be("post","status" .$id);
		$country = be("post","country" .$id);
		$area = be("post","area" .$id);
		if (isN($tv_name)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}tv",array("tv_name", "tv_code","tv_type","tv_playurl","create_date","status","country","area"),array($tv_name,$tv_code,$tv_type,$tv_playurl,date('Y-m-d H:i:s',time()),$status,$country,$area),"id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}
function exportExcel(){
	global $db,$cache;
	$sql = "SELECT * FROM {pre}tv order by country desc , area desc ";
	header("Content-Type: application/vnd.ms-execl");  
	header("Content-Disposition: attachment; filename=tv.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
	echo  mb_convert_encoding("国家","gb2312","utf-8")."\t"; 
	echo  mb_convert_encoding("地区","gb2312","utf-8")."\t";
	echo  mb_convert_encoding("频道id","gb2312","utf-8")."\t";  
echo mb_convert_encoding("频道名称","gb2312","utf-8")."\t";  
echo "\t\n";  
  
/*start of second line*/  

	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		  		$t_id=$row["id"];
		  		 
		  		echo mb_convert_encoding( $row["country"],"gb2312","utf-8")."\t";  
		  		echo mb_convert_encoding( $row["area"],"gb2312","utf-8")."\t"; 
		  		echo $t_id."\t";  
echo mb_convert_encoding( $row["tv_name"],"gb2312","utf-8")."\t";  
echo "\t\n";   
    }
    
    unset($rs);
}
function main(){
	global $db,$cache;
	
	 $keyword = be("all", "keyword"); $tv_type = be("all", "tv_type");
	 $country = be("all", "country");
	 $area = be("all", "area");
    
    if(!isNum($tv_type)) { $tv_type = -1; } else { $tv_type = intval($tv_type);}
   
    $where = "1=1 ";
    
    if (!isN($keyword)) {
    	$where .= " AND tv_name LIKE '%" . $keyword . "%' ";
    }
    
    if (!isN($area)) {
    	$where .= " AND area = '" . $area . "' ";
    }
    
    if (!isN($country)) {
    	$where .= " AND country = '" . $country . "' ";
    }
    
    if($tv_type !=='-1' && $tv_type !=-1){
    	$where .= " AND tv_type =" . $tv_type ;
    }
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}tv"." where ".$where;
	$nums = $db->getOne($sql); 
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM {pre}tv  where ".$where." ORDER BY tv_type desc,id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
function filter(){
	var keyword=$("#keyword").val();
	var tv_type=$("#tv_type").val();
	var country=$("#country").val();
	var area=$("#area").val();		 
	var url = "admin_program.php?tv_type="+tv_type+"&keyword="+encodeURI(keyword)+"&area="+encodeURI(area)+"&country="+encodeURI(country);
	window.location.href=url;
}

function exportExcel(){	
	var url = "admin_program.php?action=exportExcel";	
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_topic");
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
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}tv&col=id&val='+id);
}
</script>

<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：
	<select id="tv_type" name="tv_type">
	<option value="-1">类型</option>
	<option value="1" <?php if ($tv_type==1){ echo "selected";} ?>>央视</option>
	<option value="2" <?php if ($tv_type==2){ echo "selected";} ?>>卫视</option>
	<option value="3" <?php if ($tv_type==3){ echo "selected";} ?>>城市</option>
	<option value="4" <?php if ($tv_type==4){ echo "selected";} ?>>CETV</option>
	<option value="5" <?php if ($tv_type==5){ echo "selected";} ?>>数字</option>
	</select>
	
	 <select id="country" name="country">
    <option value="">请选择国家</option>
    <?php echo makeSelectTV_live("country", $country)?>
	</select>
	
	<select id="area" name="area">
    <option value="">请选择地区</option>
    <?php echo makeSelectTV_live("area", $area)?>
	</select>
	</td>
	</tr>
	<tr>
	<td colspan="6">
	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	<a href="admin_program.php?action=exportExcel"><font color="red"><b>导出频道</b></font></a>	
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
	<td>国家</td>
	<td>地区</td>
<!--	<td>编码</td>-->
	<td>类型</td>
	<td>显示到app</td>
<!--	<td width="45%">直播地址</td>-->
	<td width="40%">操作</td>
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
      <input type="text" name="tv_name<?php echo $t_id?>" value="<?php echo $row["tv_name"]?>" size="40"/></td>
      
	<td>
	 <select id="country<?php echo $t_id?>" name="country<?php echo $t_id?>">
    <option value="">请选择国家</option>
    <?php echo makeSelectTV_live("country", $row["country"])?>
	</select>
	</td>
	<td><select id="area<?php echo $t_id?>" name="area<?php echo $t_id?>">
    <option value="">请选择地区</option>
    <?php echo makeSelectTV_live("area", $row["area"])?>
	</select></td>
<!--      <td>-->
<!--      <input type="text" name="tv_code<?php echo $t_id?>" value="<?php echo $row["tv_code"]?>" size="20"/></td>-->
	  <td>
		  <select id="tv_type" name="tv_type<?php echo $t_id?>">
			<option value="-1">类型</option>
			<option value="1" <?php if ($row["tv_type"]==1){ echo "selected";} ?>>央视</option>
			<option value="2" <?php if ($row["tv_type"]==2){ echo "selected";} ?>>卫视</option>
			<option value="3" <?php if ($row["tv_type"]==3){ echo "selected";} ?>>城市</option>
			<option value="4" <?php if ($row["tv_type"]==4){ echo "selected";} ?>>CETV</option>
			<option value="5" <?php if ($row["tv_type"]==5){ echo "selected";} ?>>数字</option>
		  </select>
	  </td>
	  
	  <td>
	 
	  <select id="status<?php echo $t_id?>" name="status<?php echo $t_id?>">
	<option value="0" <?php if($row["status"]==0){ echo "selected";} ?>>不显示</option>
	<option value="1" <?php if($row["status"]==1){ echo "selected";} ?>>显示</option>
	</select>
	  </td>
	  
<!--      <td>-->
<!--      <input type="text" name="tv_playurl<?php echo $t_id?>" value="<?php echo $row["tv_playurl"]?>" size="70"/></td>-->
<!--	  -->
      <td>
     <a href="admin_program_items.php?tv_id=<?php echo $t_id?>">显示节目单</a>  |
     <a href="admin_program_play.php?tv_id=<?php echo $t_id?>">显示直播源</a>  |
	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> 
<!--	  | <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> |-->
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="7">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
<!--	<input type="button" value="批量删除" id="btnDel" class="input"  />-->
	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_program.php?page={p}&tv_type=" . $tv_type . "&keyword=" . urlencode($keyword) )?>
	</td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}tv" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="30%">电视名称：</td>
	<td><input id="tv_name" size=40 value="" name="tv_name">
	</td>
	</tr>
<!--	<tr>-->
<!--	<td>电视编码：</td>-->
<!--	<td>  <input id="tv_code" size=40 value="" name="tv_code">-->
<!--	</td>-->
<!--    </tr>-->
	<tr>
	<td>电视节目类别：</td>
	<td><select id="tv_type" name="tv_type">
	<option value="-1">类型</option>
	<option value="1" >央视</option>
	<option value="2">卫视</option>
	<option value="3" >城市</option>
	<option value="4">CETV</option>
	<option value="5">数字</option>
	</select>
	</td>
	</tr>
	
	<tr>
	<td>国家：</td>
	<td><select id="country" name="country">
	<?php echo makeSelectTV_live("country", '')?>
	</select>
	</td>
	</tr>
	
	<tr>
	<td>地区：</td>
	<td><select id="area" name="area">
	<?php echo makeSelectTV_live("area", '')?>
	</select>
	</td>
	</tr>
	
	 <tr>
     <td>显示到App：</td>
      <td><select id="status" name="status">
	   <option value="0" selected>不显示</option>
	   <option value="1" >显示</option>
	</select>
	  </td>
    </tr>
<!--	<tr>-->
<!--     <td>直播视频地址：</td>-->
<!--      <td>-->
<!--      <TEXTAREA id="tv_playurl" NAME="tv_playurl" ROWS="2" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>-->
<!--	  </td>-->
<!--    </tr>-->
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