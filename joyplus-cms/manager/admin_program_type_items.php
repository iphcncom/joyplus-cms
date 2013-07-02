<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "info" :headAdmin ("节目类别管理"); info();break;
	default : headAdmin ("节目类别管理");main();break;
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

function main(){
	global $db,$cache;
	
	 $keyword = be("all", "keyword"); $tv_type = be("all", "tv_type");
     
   
    $where = " 1=1 ";
    
    if (!isN($keyword)) {
    	$where .= " AND video_name LIKE '%" . $keyword . "%' ";
    }
    
    
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count( * )
FROM (

SELECT count( video_name )
FROM `mac_tv_program_item`
WHERE (program_type IS NULL
OR program_type = '' ) and video_name is not null and video_name !='' and ".$where."
GROUP BY video_name
) AS c
	";
	$nums = $db->getOne($sql); 
//	var_dump($sql);
	$pagecount=ceil($nums/100);
	$sql = "SELECT  video_name FROM `mac_tv_program_item` WHERE (program_type IS NULL OR program_type = '' ) and video_name is not null and video_name !='' and ".$where." GROUP BY video_name   limit ".(100 * ($pagenum-1)) .",".'100';
	$rs = $db->query($sql);
?>
<script language="javascript">
function filter(){
	var keyword=$("#keyword").val(); 
	var url = "admin_program_type_items.php?keyword="+encodeURI(keyword);
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
	<td colspan="6">
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
	
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			
	?>
	<tr>
	<td>下面节目还没归类，请选择相应的节目类别<select id="program_type" name="program_type" >
	<?php echo makeSelectTV_live("prod_type",'')?>
	</select>  全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />

	&nbsp;<input type="button" value="批量归档" id="btnEdit" class="input" />
	 </td>
	</tr>
    <tr>
	  <td>
	   <?php 
	      $index=0;
	     while ($row = $db ->fetch_array($rs))
		  	{   $index++;
		  		$t_id=$row["video_name"];
	   ?>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" />
      <?php echo $t_id?> &nbsp; &nbsp;  <?php if($index%10 ===0){ echo '<br/>';}?>
      <?php }?>
     </td>
      
    </tr>
	<?php
			
		}
	?>
	
	
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_program_type_items.php?page={p}&keyword=" . urlencode($keyword) )?>
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