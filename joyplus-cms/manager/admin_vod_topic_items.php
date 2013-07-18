<?php
require_once ("admin_conn.php");
require_once ("genTopRecommendItems.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	default : headAdmin ("视频榜单管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","ids");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$t_flag = be("post","t_flag" .$id);
		$t_sort = be("post","disp_order" .$id);
		
		if (isN($t_sort)) { $t_sort= $db->getOne("SELECT MAX(disp_order) FROM {pre}vod_topic_items")+1; }
		if (!isNum($t_sort)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}vod_topic_items",array("flag", "disp_order"),array($t_flag,$t_sort),"id=".$id);
	}
	updateCacheFile();
	$topic_id= getBody(getReferer(), 'topic_id=', '&');
	if(isN($topic_id)){
		$topic_id= getBodys(getReferer(), 'topic_id=');
	}
	
	if(!isN($topic_id)){
	   replaceTopRecommend($topic_id);
	}
	echo "修改完毕";
}

function main()
{
	global $db,$cache;
	
	 $topic_id = be("all", "topic_id"); 
	  $flag = be("all", "flag"); 
	
    if(!isNum($topic_id)) { $topic_id = 0; } else { $topic_id = intval($topic_id);}
    
if(!isNum($flag)) { $flag = -1; } else { $flag = intval($flag);}
   
    
$where = " 1=1 ";

    $where .= " AND a.topic_id =".$topic_id;
if($flag==1){
    	$where .= " AND a.flag =1 ";
    }
    
     if($flag==0){
    	$where .= " AND a.flag =0 ";
    }
    
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}vod_topic_items as a "." where ".$where;;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	//$sql = "SELECT a.id as id, a.flag as flag, a.disp_order as disp_order, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_topic_items a,{pre}vod vod where ".$where." and a.vod_id=vod.d_id ORDER BY a.disp_order,a.id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
//	var_dump($sql);
	  $sql = "SELECT a.id as id, a.flag as flag, a.disp_order as disp_order, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_topic_items AS a LEFT JOIN {pre}vod AS vod ON a.vod_id = vod.d_id WHERE ".$where." and a.topic_id =$topic_id LIMIT ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript">
function filter(){
	var topic_id=$("#topic_id").val();
	var st_flag  =$("#st_flag").val();
	var url = "admin_vod_topic_items.php?topic_id="+topic_id+"&flag="+st_flag;
	window.location.href=url;
}

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
	
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_topic_items");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
//	$("#btnAdd").click(function(){
//		window.location.href="admin_vod.php?topic_id=<?php echo $topic_id?>";
//	});
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
});
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}vod_topic&col=t_id&val='+id);
}
</script>

<table class="admin_vod_topic_items tb">
	<tr>
	<td>
	<table border="0" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：视频榜单 <select id="topic_id" name="topic_id" >
	
	<?php echo makeSelectWhere("{pre}vod_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",$topic_id," where t_id>4")?>
	</select>
	
	 <select id="st_flag" name="st_flag">
	 <option value="-1" <?php if($flag==-1){ echo "selected";} ?>>显示到榜单</option>
	<option value="0" <?php if($flag==0){ echo "selected";} ?>>不显示</option>
	<option value="1" <?php if($flag==1){ echo "selected";} ?>>显示</option>
	</select>
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	 | <a href="admin_vod_topic.php">返回视频榜单</a>
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
	<td width="10%">编号</td>
	<td>名称</td>
	<td width="5%">排序</td>
	<td width="10%">显示到榜单</td>
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
		  		$t_id=$row["id"];
	?>
    <tr>
	  <td>
	  <input name="ids[]" type="checkbox" id="ids" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
      <td><a href="admin_vod.php?action=edit&id=<?php echo $row["vod_id"];?>">
      <?php echo $row["vod_name"]?></a></td>
	  
	 
	  <td>
	  <input name="disp_order<?php echo $t_id?>" type="text" value="<?php echo $row["disp_order"]?>"  size="5"/></td>
	  <td>
	 
	  <select id="t_flag<?php  echo $t_id?>" name="t_flag<?php echo $t_id?>">
	   <option value="-1" <?php if($flag==-1){ echo "selected";} ?>>显示到榜单</option>
	<option value="0" <?php if($row["flag"]==0){ echo "selected";} ?>>不显示</option>
	<option value="1" <?php if($row["flag"]==1){ echo "selected";} ?>>显示</option>
	</select>
	  </td>
      <td>	
	 
	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic_items&ids=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr class="formlast">
	<td  colspan="8"><input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'ids[]')" /> 全选
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	&nbsp;<input id="addvod" type="button" value="添加视频" class="input" onclick="javascript:window.location.href='admin_vod.php?action=addTopicItems&topic_id=<?php echo $topic_id?>'" />
	</td></tr>
    <tr align="center" class="formlast">
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod_topic_items.php?page={p}&topic_id=".$topic_id ."&flag=".$flag)?>
	</td>
    </tr>
</table>
</form>

</body>
</html>
<?php
unset($rs);
}
?>