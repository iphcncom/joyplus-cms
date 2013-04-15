<?php
require_once ("admin_conn.php");
require_once ("genTopRecommendItems.php");
require_once ("./parse/NotificationsManager.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	default : headAdmin ("追剧推送管理");main();break;
}
dispseObj();

	function editall()
	{
	
	global $db;
	$t_id = be("arr","ids");	
	if(!isN($t_id)){	
		$sql = "SELECT vod.d_remarks ,vod.d_state, a.id as id, vod.d_type as d_type, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_pasre_item a,{pre}vod vod where  a.prod_id=vod.d_id  AND  id in (".$t_id.")";
		$rs = $db->query($sql);	
		$list=array();
	    while ($row = $db ->fetch_array($rs)){
	    	$msg = new Notification();
	    	$id=$row["id"];
	    	$vod_id=$row["vod_id"];
	    	$d_type=$row["d_type"];
	    	if(!isN($row["d_remarks"])) {
	    		$d_remarks=$row["d_remarks"];
	    	}
	        if(!isN($row["d_state"])) {
	    		$d_state=$row["d_state"];
	    	}
	    	if(!isN($d_state) && $d_state !== $d_remarks){
	    	  $content='亲，你关注的《'.$row["vod_name"].'》更新到'.$d_state.'集了，快来收看吧~';
	    	}else {
	    	 $content='亲，你关注的《'.$row["vod_name"].'》更新全，快来收看吧~';	
	    	}
		    $msg->alert=$content;
		    $msg->prod_id=$vod_id;
		    $msg->prod_type=$d_type;
		    $msg->push_type='2';
		    $msg->channels=array('CHANNEL_PROD_'.$vod_id);
		    
		   $result= NotificationsManager::push($msg);
		  // $result=array('code'=>'','response'=>'');
		   if($result['code'].'' == '200'){
		   	// echo "消息推送成功";
		   	 $list[]=$id;
	         writetofile("parsemsg.log", $content."====消息推送成功 ");
		   }else {
	//	    	echo "消息推送失败:".$result['response'];
	         writetofile("parsemsg.log", $content."====消息推送失败:".$result['response']);
		   };
	    }
	    unset($rs);
	     
	    if(is_array($list) && count($list)>0){
	    	$ids = implode(",",$list);
	    	$db->query('delete from {pre}vod_pasre_item where id in ('.$ids.')');
	    }
		
		echo "推送完毕";
	}else {
		echo "你至少需要选择一个视频";
	}
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
	$sql = "SELECT count(*) FROM {pre}vod_pasre_item as a ";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT vod.d_remarks ,vod.d_state,a.d_status as status,a.id as id, a.create_date as create_date, vod.favority_user_count as favority_user_count, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_pasre_item a,{pre}vod vod where  a.prod_id=vod.d_id order by a.d_status asc, a.create_date desc limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
//var_dump($sql);
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_pasre_item");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnEdit").click(function(){
		if(confirm('确定要推送消息吗')){
		$("#form1").attr("action","?action=editall");
		$("#btnEdit").attr("disabled",true); ;
		$("#form1").submit();
		}
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



<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="10%">视频ID</td>
	<td>视频名称</td>
	<td width="15%">更新时间</td>
	<td width="15%">追剧人数</td>
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
      <td><?php echo $row["vod_id"]?></td>
      <td><a href="admin_vod.php?action=edit&id=<?php echo $row["vod_id"];?>">
      <?php echo $row["vod_name"]?></a>
      <?php if($row["d_state"] > 0) {?><?php echo "<font color=\"red\">[" .$row["d_state"] . "]</font>"; }?>
	<?php if(!isN($row["d_remarks"])) {?><?php echo "<font color=\"red\">[" .$row["d_remarks"] . "]</font>"; }?>
      </td>
	  
	 
	  <td>
	   <?php echo isToday($row["create_date"])?></td>
	  <td>
	 
	  <?php echo $row["favority_user_count"]?>
	  </td>
      <td>	
	 
	  <a href="admin_ajax.php?action=del&tab={pre}vod_pasre_item&ids=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="8">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'ids[]')" />
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	&nbsp;<input type="button" value="发送消息" id="btnEdit" class="input" />
	
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_subscribe.php?page={p}&topic_id=".$topic_id ."&flag=".$flag)?>
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