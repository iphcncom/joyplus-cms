<?php
require_once ("admin_conn.php");
require_once ("genTopRecommendItems.php");
require_once ("./parse/NotificationsManager.php");
$parse_appid_restkey =require(dirname(__FILE__).'/parse/app_config.php');
chkLogin();

$action = be("all","action");
switch($action)
{   case "editall" : headAdmin ("视频采集入库管理"); editall();break; 
	case "parse" : headAdmin ("视频采集入库管理"); parse();break;
	default : headAdmin ("追剧推送管理");main();break;
}
dispseObj();
function editall()
	{
		$t_id = be("all","ids");
	
	$t_id=explode(",", $t_id);
	$tids= array();
	foreach ($t_id as $tid){
		if(!isN($tid)){
			$tids[]=$tid;
		}
	}
	
	
	$t_id=implode(",", $tids);
	?>
<table class=tb>
    <tr>
		<td  colspan="2" align="center"><span id="storagetext">正 在 推 送 信 息...</span></td>
  	</tr>
	<tr>
		<td  colspan="2" align="center">推送信息状态 
		
		 <div id="refreshlentext" align="left"></div>
		</td>
	</tr>
	
  	
</table>
<script language="javascript">
location.href='<?php echo "admin_subscribe.php?action=parse&ids=".$t_id;?>';	
</script>
<?php
	 
	
}
	function parse()
	{
	?>
<table class=tb>
    <tr>
		<td  colspan="2" align="center"><span id="storagetext">正 在 推 送 信 息...</span></td>
  	</tr>
	<tr>
		<td  colspan="2" align="center">推送信息状态 
		
		 <div id="refreshlentext" align="left"></div>
		</td>
	</tr>
	
  	
</table>
<?php
	global $db,$parse_appid_restkey;
	$t_id = be("all","ids");
	if(!isN($t_id)){	
		$sql = "SELECT a.channels as channels, vod.d_remarks ,vod.d_state, a.id as id, vod.webUrls as webUrls, vod.d_type as d_type, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_pasre_item a,{pre}vod vod where  a.prod_id=vod.d_id  AND  id in (".$t_id.")";
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
	    	if($d_type ==='1'){
	    		$content='亲，您想看的《'.$row["vod_name"].'》已经上线啦，快来看看哦~';
	    	}else if($d_type ==='3'){
	    		$content='亲，你关注的《'.$row["vod_name"].'》有更新啦,';
	    		if(!isN($d_state) && !isN($row["webUrls"])){
	    			$itemArray=explode("{Array}", $row["webUrls"]);	 
	    			$flag=true; 			
	    			foreach ($itemArray as $itemName){	    				
	    				$nameUrls=explode("$", $itemName);	    				
	    				if(strpos($nameUrls[0], $d_state) !==false ){
	    					$names=trim(replaceStr($nameUrls[0], $d_state, ''));
		    				if($names){
		    					$flag=false;
		    				 	$content .=$names;
		    				 	break;
		    				 }
	    				}	    				
	    			}
	    			if($flag){
	    				$content='亲，你关注的《'.$row["vod_name"].'》更新到了'.$d_state.'期，快来收看吧~';
	    			}
	    		}
	    	}else{
		    	if(!isN($d_state) && $d_state !== $d_remarks){
		    	  $content='亲，你关注的《'.$row["vod_name"].'》更新到了第'.$d_state.'集，快来收看吧~';
		    	}else {
		    	 $content='亲，你关注的《'.$row["vod_name"].'》已更新完结，快来收看吧~';	
		    	}
	    	}
		    $msg->alert=$content;
		    $msg->prod_id=$vod_id;
		    $msg->prod_type=$d_type;
		    $msg->push_type='2';
		    $msg->channels=array('CHANNEL_PROD_'.$vod_id);
		    $channels = $row["channels"];
		    if(isN($channels) ){
		      $appKeys= array_keys($parse_appid_restkey);
		      $channels=implode(",", $appKeys);
		    }else {
		      $appKeys=explode(",", $channels);
		    }
		    $pushFlag=true;
		    foreach ($appKeys as $appkey){
		       if($appkey==null || trim($appkey)=='' ){
		        
		       }else {
			       $msg->appid=$parse_appid_restkey[$appkey]['appid'];		
			       $msg->restkey=$parse_appid_restkey[$appkey]['restkey'];
		           $result= NotificationsManager::push($msg);
				   if($result['code'].'' == '200'){		
				   	 $channels=replaceStr($channels,$appkey.',', '');			   	
				   	 $channels=replaceStr($channels,$appkey, '');	   	 
				   	 appendMsg($content."====消息推送 到 [".$parse_appid_restkey[$appkey]['appname']."] 成功 ");
			         writetofile("parsemsg.log", $content."====消息推送 到 [".$parse_appid_restkey[$appkey]['appname']."] 成功 ");
				   }else {
				   	 $pushFlag=false;
				   	 appendMsg($content."====消息推送 到 [".$parse_appid_restkey[$appkey]['appname']."] 失败:".$result['response']);
			         writetofile("parsemsg.log", $content."====消息推送 到 [".$parse_appid_restkey[$appkey]['appname']."] 失败:".$result['response']);
				   };
		       }
		    }
		    
		    if($pushFlag){
		    	$list[]=$id;
		    }else {
		    	$db->query("update {pre}vod_pasre_item set channels='".$channels."' where id in (".$id.")");
		    }
		    
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

function appendMsg($content){
	 echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "$(\"#refreshlentext\").append(\"".$content."\").append(\"<br/>\");";			
			echo "</script>";
}

function main()
{
	global $db,$cache, $parse_appid_restkey;
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
	$sql = "SELECT count(a.prod_id) FROM {pre}vod_pasre_item a,{pre}vod vod where  a.prod_id=vod.d_id and vod.favority_user_count >0 ";
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT a.channels as channels, vod.d_remarks ,vod.d_state,a.d_status as status,a.id as id, a.create_date as create_date, vod.favority_user_count as favority_user_count, vod.d_name as vod_name,vod.d_id as vod_id FROM {pre}vod_pasre_item a,{pre}vod vod where  a.prod_id=vod.d_id and vod.favority_user_count >0 order by vod.favority_user_count desc, a.d_status asc, a.create_date desc limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
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
			var url='admin_subscribe.php?action=editall&ids=';
			var channels=document.getElementsByName("ids[]");
			var channelFlag=true;
			var ids='';
			for(var i = 0; i < channels.length; i++){
				 if (channels[i].checked == true) {
					  channelFlag=false;
					  ids=channels[i].value+","+ids;
			     }
		    }
			if(channelFlag){
				alert("你至少需要选择一个视频");
		    }else {
		    	location.href=url+ids;		    	
		    }
		    
//			location.href
			
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



<table class="admin_subscribe tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="10%">视频ID</td>
	<td>视频名称</td>
	<td>推送应用</td>
	<td width="15%">更新时间</td>
	<td width="5%">追剧人数</td>
	<td width="5%">操作</td>
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
      <td><?php echo $row["vod_id"]?> </td>     
      <td><a href="admin_vod.php?action=edit&id=<?php echo $row["vod_id"];?>">
      <?php echo $row["vod_name"]?></a>
      
      <?php if($row["d_state"] > 0) {?><?php echo "<font color=\"red\">[" .$row["d_state"] . "]</font>"; }?>
	<?php if(!isN($row["d_remarks"])) {?><?php echo "<font color=\"red\">[" .$row["d_remarks"] . "]</font>"; }?>
      </td>
	   <td> <?php 
	     $tempChannels = $row["channels"]; //$parse_appid_restkey
	     if(isN($tempChannels)){echo '所有应用';} else {
      	   $tempChannels = explode(",", $tempChannels);
      	   foreach ($tempChannels as $tChannel){
      	   	 echo '['.$parse_appid_restkey[$tChannel]['appname'].'] ';
      	   }
      	
      }?></td>
	 
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
	<tr class="formlast">
	<td  colspan="8">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'ids[]')" />
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	&nbsp;<input type="button" value="发送消息" id="btnEdit" class="input" />
	
	</td></tr>
    <tr align="center" class="formlast">
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