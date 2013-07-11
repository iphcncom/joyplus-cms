<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "info" :headAdmin ("电视播放源管理"); info();break;
	default : headAdmin ("电视播放源管理");main();break;
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
	
	 $tv_id = be("all", "tv_id"); 	 
	 $day = be("all", "day"); 
	
    if(!isNum($tv_id)) { $tv_id = 0; } else { $tv_id = intval($tv_id);}
    if(isN($day)){
      $day=date('Y-m-d',time());
    }
    
    $where = " 1=1 ";

    $where .= " AND tv_id =".$tv_id;
   
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}tv_play"." where ".$where;
	$nums = $db->getOne($sql); 
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM {pre}tv_play  where ".$where." ORDER BY id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
function filter(){
	var tv_id=$("#tv_id").val();
	var url = "admin_program_play.php?tv_id="+tv_id;
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

<table class="admin_program_play tb">
	<tr>
	<td>
	<table border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：频道<select id="tv_id" name="tv_id" >
	
	<?php echo makeSelectWhere("{pre}tv","id","tv_name","tv_type","","&nbsp;|&nbsp;&nbsp;",$tv_id," where status=1")?>
	</select>
	
	
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	| <a href="admin_program.php">返回电视直播</a>
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
	<td>播放来源</td>
	<td>显示到app</td>
	<td width="45%">直播地址</td>
	<td>清晰度</td>
	<td width="10%">操作</td>
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
	 <select id="tv_playfrom<?php echo $t_id?>" name="tv_playfrom<?php echo $t_id?>">
    <option value="">请选择播放来源</option>
    <?php echo makeSelectTV_live("tv_playfrom", $row["tv_playfrom"])?>
	</select>
	</td>
	
	  
	  <td>
	 
	  <select id="status<?php echo $t_id?>" name="status<?php echo $t_id?>">
	<option value="0" <?php if($row["status"]==0){ echo "selected";} ?>>不显示</option>
	<option value="1" <?php if($row["status"]==1){ echo "selected";} ?>>显示</option>
	</select>
	  </td>
	  
      <td>
      <input type="text" name="tv_playurl<?php echo $t_id?>" value="<?php echo $row["tv_playurl"]?>" size="100"/></td>
	  
      <td>
	  <select id="tv_definition<?php echo $t_id?>" name="tv_definition<?php echo $t_id?>">
	  <option value=""> </option>
	<option value="mp4" <?php if($row["tv_definition"]==='mp4'){ echo "selected";} ?>>高清</option>
	<option value="flv" <?php if($row["tv_definition"]==='flv'){ echo "selected";} ?>>流畅</option>
	</select>
	  </td>
	  
	    <td>
<!--	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> -->
	   <a href="admin_ajax.php?action=del&tab={pre}tv_play&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> 
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr class="formlast">
	<td  colspan="7"><input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" /> 全选
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
<form action="admin_ajax.php?action=save&tab={pre}tv_play&tv_id=<?php echo $tv_id ;?>" method="post" name="form2" id="form2">
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
     <tr>
     <td>直播来源清晰度：</td>
      <td>
        <select id="tv_definition" name="tv_definition">
	  <option value=""> </option>
	<option value="mp4" >高清</option>
	<option value="flv">流畅</option>
	</select>
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