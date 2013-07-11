<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");

require_once ("./score/DouBanParseScore.php");
require_once ("./parse/NotificationsManager.php");





chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/vod";

switch($action)
{  case "view" :headAdmin ("用户视频反馈");  view();break;
  case "updateStatus" : updateStatus();break;
  case "deleteStatus" : deleteStatus();break;
	default : headAdmin ("用户视频反馈"); main();break;
}
dispseObj();


function main()
{$backurl = getReferer();
	global $db,$template,$cache;
    $status = be("all", "status");
     $client = be("all", "client");
    $feedback_type = be("all", "feedback_type");
     $pagenum = be("all", "page");
    if(!isNum($feedback_type)) { $feedback_type = 9;} else { $feedback_type = intval($feedback_type);}
    
    if(!isNum($status)) { $status = -1;} else { $status = intval($status);}
   
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
  
    $where = " 1=1 ";
     
    if($client ==='1'){
    	$where .= " AND client !='android' ";
    }else if($client ==='2'){
    	$where .= " AND client ='android' ";
    }
    
    if ($feedback_type > 0) { $where .= " AND feedback_type like '%" . $feedback_type . "%' ";}
    if ($status > 0) { $where .= " AND status =" . $status . " ";}
  
    
    $sql = "SELECT COUNT( * ) from (
SELECT vod.d_name
FROM mac_vod AS vod, (

SELECT prod_id, feedback_type, COUNT( * ) AS feedback_count
FROM tbl_video_feedback where 
".$where." 
GROUP BY prod_id
) AS feed
WHERE vod.d_id = feed.prod_id
) AS feeds";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	//$sql = "SELECT d_year,d_id, d_name, d_enname, d_play_num,d_type,d_state,d_topic, d_level, d_hits, d_time,d_remarks,d_playfrom,d_hide,p.id as popular_id FROM {pre}vod ".$repeatsql." left join {pre}vod_popular as p on p.vod_id=d_id  WHERE" . $where . " ORDER BY " . $orders . "  limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
//  var_dump($sql);
    $sql = "SELECT vod.d_name, vod.d_play_num, feed. * 
FROM mac_vod AS vod, (

SELECT prod_id, feedback_type, COUNT( * ) AS feedback_count,status
FROM tbl_video_feedback where
".$where." 
GROUP BY prod_id
) AS feed
WHERE vod.d_id = feed.prod_id
ORDER BY feedback_count DESC , d_play_num DESC   limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
// var_dump($sql);
	$rs = $db->query($sql);
?>


<script type="text/javascript" src="./resource/thickbox-compressed.js"></script>
<script type="text/javascript" src="./resource/thickbox.js"></script>
<link href="./resource/thickbox.css" rel="stylesheet" type="text/css" />
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			repeatlen:{
				number:true,
				max:10
			}
		}
	});
	
	$("#btnrepeat").click(function(){
		var repeatlen = $("#repeatlen").val();
		var reg = /^\d+$/;
		var re = repeatlen.match(reg);
		if (!re){ repeatlen=0; }
		if (repeatlen >20){ alert("长度最大20");$("#repeatlen").focus();return;}
		var url = "admin_vod.php?repeat=ok&repeatlen=" + repeatlen;
		window.location.href=url;
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_vod.php?action=del");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#plsc").click(function(){
		var ids="",rc=false;
		$("input[name='d_id']").each(function() {
			if(this.checked){
				if(rc)ids+=",";
				ids =  ids + this.value;
				rc=true;
			}
        });
		$("#form1").attr("action","admin_makehtml.php?acton=viewpl&flag=vod&d_id="+ids);
		$("#form1").submit();
	});
});
function filter(){
	var feedback_type=$("#feedback_type").val();	
	var status=$("#status").val();
	var client=$("#client").val();
	var url = "admin_vod_feedback.php?feedback_type="+feedback_type+"&status="+status+"&client="+client;
	window.location.href=url;
}

function updateStatus(id){
	$.get("admin_vod_feedback.php","id="+id+"&action=updateStatus", function(obj) {
		alert(obj);
		window.location.href="<?php echo "admin_vod_feedback.php?page=".$pagenum."&feedback_type=" . $feedback_type.'&status=' . $status ;?>";
	});
}
function deleteStatus(id){
	$.get("admin_vod_feedback.php","id="+id+"&action=deleteStatus", function(obj) {
		alert(obj);
		window.location.href="<?php echo "admin_vod_feedback.php?page=".$pagenum."&feedback_type=" . $feedback_type.'&status=' . $status ;?>";
	});
}


</script>
<table class="admin_vod_feedback_1 tb">
	<tr>
	<td>
	<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">过滤条件：反馈意见类别:
	<select id="feedback_type" name="feedback_type">
	<option value="9" <?php if($feedback_type=="9"){ echo "selected";} ?>>程序反馈影片无法播放</option>
	<option value="1" <?php if($feedback_type=="1"){ echo "selected";} ?>>用户反馈影片无法播放</option>
	<option value="2" <?php if($feedback_type=="2"){ echo "selected";} ?>>用户反馈影片播放不流畅</option>
	<option value="3" <?php if($feedback_type=="3"){ echo "selected";} ?>>用户反馈影片加载比较慢</option>
	<option value="4" <?php if($feedback_type=="4"){ echo "selected";} ?>>用户反馈影片不能下载</option>
	<option value="5" <?php if($feedback_type=="5"){ echo "selected";} ?>>用户反馈观看影片时出现闪退</option>
	<option value="6" <?php if($feedback_type=="6"){ echo "selected";} ?>>用户反馈画质不清晰</option>
	<option value="7" <?php if($feedback_type=="7"){ echo "selected";} ?>>用户反馈音画不同步</option>
	<option value="8" <?php if($feedback_type=="8"){ echo "selected";} ?>>用户反馈其它（用户自己填写，可不填）</option>
	</select>
	
	<select id="client" name="client">
	<option value="0" >来源</option>
	<option value="1" <?php if($client=="1"){ echo "selected";} ?>>ios</option>
	<option value="2" <?php if($client=="2"){ echo "selected";} ?>>android</option>
	</select>
	
	<select id="status" name="status">
	<option value="-1">是否修正</option>
	<option value="1" <?php if($status=="1"){ echo "selected";} ?>>未修改</option>
	<option value="2" <?php if($status=="2"){ echo "selected";} ?>>已修改</option>
	
	</select>
	
<!--	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">&nbsp;-->
	
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">
	
	</td>
	
	</tr>
	</table>
	</td>
	</tr>
</table>

<form id="form1" name="form1" method="post">
<table class="tb">
	<tr>
	<td width="5%">编号</td>
	<td width="15%">名称</td>
	<td width="6%">播放次数</td>
	<td width="6%">反馈次数</td>
<!--	<td width="5%">反馈类别</td>-->
	<td width="25%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
	<tr><td align="center" colspan="12">没有任何记录!</td></tr>
	<?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$d_id=$row["prod_id"];
		  		
	?>
    <tr>
	<td><?php echo $d_id?></td>
	<td><?php echo substring($row["d_name"],100)?>	
	</td>
	<td><?php echo $row["d_play_num"]?></td>
	<td><?php echo $row["feedback_count"]?></td>
	
	<td>
	<?php if($row['status'] ==='1'){?>
	<a href="#" onclick='updateStatus(<?php echo $d_id ?>);' id="updateStatus_<?php echo $d_id ?>">完成修改</a> |<?php }?><a href="admin_vod.php?action=edit&id=<?php echo $d_id?>">修改视频</a> |  <a href="admin_vod_feedback.php?action=view&id=<?php echo $d_id?>">视频反馈</a> | <a href="#" onclick='deleteStatus(<?php echo $d_id ?>);' id="deleteStatus_<?php echo $d_id ?>">删除</a>  
	 
    </tr>
	<?php
			}
		}
	?>

	<tr class="formlast">
	<td align="center" colspan="12">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod_feedback.php?page={p}&feedback_type=" . $feedback_type.'&status=' . $status.'&client=' . $client )?>
	</td>
	</tr>
</table>

</form>
<?php
if ($pagenum==1 && $where==" 1=1 ") { echo "<script>showpic();</script>";}
unset($rs);
echo '</body>
</html>';
}



function View(){
	
	$d_id = be("all","id");
	
	global $db;
?>

<table class="admin_vod_feedback tb" width="50%">
	<tr>
	<td  width="5%"></td>
	<td width="40%">反馈类别</td>
	<td>反馈次数</td>
	</tr>
	
<?php 
   for ($i=1;$i<10;$i++){
   	if($i ==8){
   		continue;
   	}
 ?>
 <tr><td></td>
	<td><select id="feedback_types" name="feedback_types" disabled>
	<option value="9" <?php if($i=="9"){ echo "selected";} ?>>程序反馈影片无法播放</option>
	<option value="1" <?php if($i=="1"){ echo "selected";} ?>>用户反馈影片无法播放</option>
	<option value="2" <?php if($i=="2"){ echo "selected";} ?>>用户反馈影片播放不流畅</option>
	<option value="3" <?php if($i=="3"){ echo "selected";} ?>>用户反馈影片加载比较慢</option>
	<option value="4" <?php if($i=="4"){ echo "selected";} ?>>用户反馈影片不能下载</option>
	<option value="5" <?php if($i=="5"){ echo "selected";} ?>>用户反馈观看影片时出现闪退</option>
	<option value="6" <?php if($i=="6"){ echo "selected";} ?>>用户反馈画质不清晰</option>
	<option value="7" <?php if($i=="7"){ echo "selected";} ?>>用户反馈音画不同步</option>
	<option value="8" <?php if($i=="8"){ echo "selected";} ?>>用户反馈其它（用户自己填写，可不填）</option>
	</select></td>
	<td><?php echo   $db->getOne("select count(*) as count from tbl_video_feedback where feedback_type like '%".$i."%' and prod_id=".$d_id);?></td>
	</tr>
 <?php 
   }
 
?>
</table>
<table class="admin_vod_feedback tb" width="50%">
	<tr>
	<td colspan="3">反馈类别:其它（用户自己填写，可不填）</td>
	</tr>
	<tr>
	<td  width="5%"></td>
	<td>反馈时间</td>
	<td>反馈内容</td>
	</tr>
	
	<?php 
	   $rs = $db->query("select * from tbl_video_feedback where feedback_type like '%8%' and prod_id=".$d_id);
	   while ($row = $db ->fetch_array($rs)){
    ?>
      <tr><td  width="5%"></td>
		<td width="40%"><?php echo isToday($row["create_date"]);?></td>
		<td><?php echo substring($row["feedback_memo"],20);?></td>
	 </tr>
    <?php 		
	   }
	   unset($rs);
	?>
	</table>
<?php 	
	
	
	echo '</body>
</html>';
}

?>


<?php 

function updateStatus(){
	$d_id = be("all","id");
	global $db;
	$db->query("update tbl_video_feedback set status=2 where prod_id=".$d_id);
	echo "更新成功";
}
function deleteStatus(){
	$d_id = be("all","id");
	global $db;
	$db->query("delete from tbl_video_feedback where prod_id=".$d_id);
	echo "删除成功";
}


?>