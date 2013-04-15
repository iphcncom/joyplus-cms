<?php
require_once ("admin_conn.php");
require_once ("./collect/collect_vod_program_item.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "collecProgram" : collecProgram();break;
	default : headAdmin ("电视直播管理");main();break;
}
dispseObj();

function  collecProgram()
{
	global $db,$cache;
	
	 $tv_id = be("all", "tv_id"); 
     if(!isNum($tv_id)) { 
     	echo '参数非法。'; 
     } else { 
     	  $tv_id = intval($tv_id);
     	  $tv_code=$db->getOne("select tv_code from mac_tv where id=".$tv_id);
     	  if($tv_code !==false ){
			    $day = be("all", "day");
			    if(isN($day)){
			      parseVodPad(array(
			        'id'=>$tv_id,
			        'tv_code'=>$tv_code
			      ));
			    }else{
			    	parseVodPadSimple($tv_id,$tv_code,$day);
			    }
			    echo '采集完成。';
     	  }else {
     	  	echo '指定的电视台没有相应的采集编码。'; 
     	  }
     }
	 
	
}
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
	
	 $tv_id = be("all", "tv_id"); 	 
	 $day = be("all", "day"); 
	
    if(!isNum($tv_id)) { $tv_id = 0; } else { $tv_id = intval($tv_id);}
    if(isN($day)){
      $day=date('Y-m-d',time());
    }
    
    $where = " 1=1 ";

    $where .= " AND tv_id =".$tv_id;
    $where .= " AND day ='".$day."'";
    
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}tv_program_item as a "." where ".$where;;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql1 = "SELECT * FROM {pre}tv_program_item  where  ".$where." and play_time<='12:00'  order by play_time asc ";
	$sql2 = "SELECT * FROM {pre}tv_program_item  where  ".$where." and play_time>'12:00'  order by play_time asc ";
//	var_dump($sql);
	$rs1 = $db->query($sql1);
	$rs2 = $db->query($sql2);
?>
<script language="javascript">
function filter(){
	var tv_id=$("#tv_id").val();
	var day  =$("#date").val();
	var url = "admin_program_items.php?tv_id="+tv_id+"&day="+day;
	window.location.href=url;
}

function collecProgram(){
	var tv_id=$("#tv_id").val();
	var day  =$("#date").val();
	var url = "admin_program_items.php?action=collecProgram&tv_id="+tv_id+"&day="+day;
	$.get(url,"", function(obj) {
		alert(obj);
	});
	
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
<script type="text/javascript" src="/js/calendar.js"></script>
<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：电视台 <select id="tv_id" name="tv_id" >
	
	<?php echo makeSelectWhere("{pre}tv","id","tv_name","tv_type","","&nbsp;|&nbsp;&nbsp;",$tv_id," where status=1")?>
	</select>
	
	 <input id="date" name="date" type="text" onclick="new Calendar().show(this);" value="<?php echo $day; ?>" readonly="readonly"/>
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	 |  <input class="input" type="button" value="采集节目单" id="btnsearch1" onClick="collecProgram();">	 | <a href="admin_program.php">返回电视直播</a>
	</td> 
	</tr>
	
	</table>
	</td>
	</tr>
</table>

<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td align="left">AM 00:00-12:00节目单</td>
	<td align=""left"" width="50%">AM 12:00-24:00节目单</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="2">没有任何记录!</td></tr>
    <?php
		}
		else{
			
		  		
	?>
    <tr>
	  <td align="left">
	  <?php 
		while ($row1 = $db ->fetch_array($rs1))
		  	{
		  		echo $row1['play_time'].' '.$row1['video_name'].'<br/>';
		  	}
	  ?>
	  </td>
	<td align="left">
	    <?php 
		while ($row2 = $db ->fetch_array($rs2))
		  	{
		  		echo $row2['play_time'].' '.$row2['video_name'].'<br/>';
		  	}
	  ?>
	  </td>
	 
   
    </tr>
	<?php
			
		}
	?>
	
</table>
</form>

</body>
</html>
<?php
unset($rs1);
unset($rs2);
}
?>