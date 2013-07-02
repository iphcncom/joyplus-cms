<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "save" : save();break;	
	case "info" :headAdmin ("节目类别管理"); info();break;
	default : headAdmin ("节目类别管理");main();break;
}
dispseObj();

function save(){
	$keyword = be("all","keyword");
	$program_type = be("all","program_type");
	
	$keyword = replaceStr($keyword,chr(10),"");
		$keywords = explode(chr(13),$keyword);
//	var_dump($program_type);
//	var_dump($keywords);
	global $db;
	if(!isN($program_type)){
		$db->query('delete from mac_tv_program_type_item where program_type=\''.$program_type.'\'');
		foreach ($keywords as $keyword){
			if(!isN($keyword)){
			  $db->query("insert into mac_tv_program_type_item(program_type,program_name) values('".$program_type."','".$keyword."')");
			}
		}
	}
    echo "修改完毕";

	
}

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$tv_playfrom = be("post","tv_playfrom" .$id);
		$tv_playurl= be("post","tv_playurl" .$id);//var_dump($t_type);
		$status= be("post","status" .$id);
		if (isN($tv_playurl)) { echo "信息填写不完整!";exit;}
	    if (isN($tv_playfrom)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}tv_play",array("tv_playfrom", "tv_playurl","status"),array($tv_playfrom,$tv_playurl,$status),"id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main(){
	global $db,$cache;
	
	 $program_type = be("all", "program_type"); 
    
	 $where =' 1=1 ';
    
    if(!isN($program_type)){
    	$where .= " and program_type ='".$program_type."'";
    }else {
    	$where .= " and program_type =''";
    }
    
	
	$sql = "SELECT * FROM mac_tv_program_type_item  where ".$where;
	$rs = $db->query($sql);
//	var_dump($sql);
	$content='';
	 while ($row1 = $db ->fetch_array($rs))
		  	{
		  		$content=$content.chr(10).$row1["program_name"];
     }
     unset($rs);
	
?>
<script language="javascript">
function filter(){
	var program_type=$("#program_type").val();
	var url = "admin_program_type_items.php?program_type="+program_type;
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
	过滤条件：节目类别 <select id="program_type" name="program_type" >
	<option value=''>   </option>
	<?php echo makeSelectTV_live("prod_type", $program_type)?>
	</select>
	
	
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	| <a href="admin_program.php">返回电视直播</a>
	</td> 
	</tr>
	
	</table>
	</td>
	</tr>
</table>

<form name="form1" id="form1" method="post" action="?action=save">
<table class="tb">	
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">	
	<input id="backurl" name="program_type" type="hidden" value="<?php echo $program_type?>">	
	
	<tr> 
    <td><font color='red'><?php echo $program_type?> </font> 类别对应的关键词，每一行对应一个关键词</td>
    </tr>
    <tr> 
    <td>&nbsp;
    <textarea id="keyword" name="keyword" style="width:500px;height:400px;"><?php echo $content?></textarea>
  
    </td>
	</tr>
	<tr align="center">
	<td><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
 </table>
 </form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}tv_program_type_item&program_type=<?php echo $program_type ;?>" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	
	
	<tr>
	<td>直播地址来源：</td>
	<td><select id="tv_playfrom" name="tv_playfrom">
	<?php echo makeSelectTV_live("tv_playfrom", '')?>
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
    
	<tr>
     <td>直播视频地址：</td>
      <td>
      <TEXTAREA id="tv_playurl" NAME="tv_playurl" ROWS="2" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
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