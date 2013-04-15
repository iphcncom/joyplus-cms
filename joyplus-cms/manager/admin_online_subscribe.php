<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");

require_once ("./score/DouBanParseScore.php");
require_once ("./parse/NotificationsManager.php");





chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/vod";

switch($action)
{  case "notifyMsg" : notifyMsg();break;
	default : headAdmin ("实时推送管理"); main();break;
}
dispseObj();


function main()
{
	global $db,$template,$cache;
	$loginname=getCookie("adminname");
    $keyword = be("all", "keyword"); $stype = be("all", "stype");
    $area = be("all", "area");   $topic = be("all", "topic");
    $level = be("all", "level");     $from = be("all", "from");
    $sserver = be("all", "sserver");  $sstate = be("all", "sstate");
    $repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
    $order = be("all", "order");     $pagenum = be("all", "page");
     $sort = be("all", "sort");
    $spic = be("all", "spic");    $hide = be("all", "hide"); $d_status = be("all", "d_status");
    $douban_score = be("all", "douban_score");
    $ipadpic = be("all", "ipadpic");
    $d_douban_id = be("all", "d_douban_id");
     $can_search_device = be("all", "can_search_device");
    if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
    if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
    if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
    if(!isNum($area)) { $area = 0;} else { $area = intval($area);}
    if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
    if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
    if(!isNum($ipadpic)) { $ipadpic = 0;} else { $ipadpic = intval($ipadpic);}
    if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
    if(!isNum($douban_score)) { $douban_score=0;} else { $douban_score = intval($douban_score);}
    if(!isNum($repeatlen)) { $repeatlen = 0;}
    if(!isNum($d_status)) { $d_status = -1;}else { $d_status = intval($d_status);}
    if(isNum($d_douban_id)) {  $d_douban_id = intval($d_douban_id);}
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
  
    $where = " d_type in (1,2,3,131) ";
    $keyword_col = be("all", "keyword_col");
    if (!isN($keyword)) {
    	$keyword=trim($keyword); 
    	if(isN($keyword_col)){
    	   $where .= " AND ( d_directed like '%".$keyword."%' or d_starring like '%".$keyword."%' or d_name like '%".$keyword."%' or d_enname like '%".$keyword."%'   ) ";
    	}else {
    		$where .= " AND ".$keyword_col ." like '%".$keyword."%' ";
    	}
    }
    if ($stype > 0) { 
    	$typearr = getValueByArray($cache[0], "t_id" ,$stype );
		if(is_array($typearr)){
			$where = $where . " and d_type in (" . $typearr["childids"] . ")";
		}
		else{
    		$where .= " AND d_type=" . $stype . " ";
    	}
    }
    if ($stype ==-1) { $where .= " AND d_type=0 ";}
    
    if ($area > 0) { $where .= " AND d_area = " . $area . " ";}
    if ($topic > 0) { $where .= " AND d_topic = " . $topic . " ";}
    if ($level > 0) { $where .= " AND d_level = " . $level . " ";}
    if ($sstate ==1){ 
    	$where .= " AND d_state>0 "; 
    }
    else if ($sstate==2){ 
    	$where .= " AND d_state=0 ";
    }
    
    if($hide!=-1){
    	$where .= " AND d_hide=".$hide ." ";
    }
    
    if($d_douban_id ==-1){
    	$where .= " AND d_douban_id=".$d_douban_id ." ";
    }else if($d_douban_id ==1){
    	$where .= " AND d_douban_id >0 ";
    }else if($d_douban_id ==2){
    	$where .= " AND d_douban_id =0 ";
    }
    
    if($d_status!=-1){
    	$where .= " AND d_status=".$d_status ." ";
    }
    
    if($douban_score==1){
    	$where .= " AND d_score >0 ";
    }
    
     if($douban_score==2){
    	$where .= " AND d_score <=0 ";
    }
    if($stype ==1 || $stype==2){
    	$douban_scoreT="block";
    }else {
    	$douban_scoreT="none";
    }
    
   if(!isN($can_search_device)){
//    	if($can_search_device ==='TV'){
//    		$where .= " AND can_search_device like '%TV%' ";
//    	}else {
//    		$where .= " AND (can_search_device like '".$can_search_device."' or can_search_device is null or can_search_device ='' ) ";
//    	}
    	$where .= " AND (can_search_device like '".$can_search_device."' or can_search_device is null or can_search_device ='' ) ";
    	
    }
    
    if ($repeat == "ok"){
        $repeatSearch = " d_name ";
        if($repeatlen>0){
			$repeatSearch = " substring(d_name,1,".$repeatlen.") ";
		}
        $repeatsql = " , (SELECT ". $repeatSearch ." as d_name1 FROM {pre}vod GROUP BY d_name1 HAVING COUNT(*)>1) as `t2` ";
        $where .= " AND `{pre}vod`.`d_name`=`t2`.`d_name1` ";
        if(isN($order)){ $order= "d_name,d_addtime"; }
    }
    
    
    
 $douban_comment = be("all", "douban_comment");
    if(!isNum($douban_comment)) { $douban_comment=0;} else { $douban_comment = intval($douban_comment  );}
    
     if($douban_comment==1){
     	$where.=" and d_id in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL) ";
     }
     
	if($douban_comment==2){
     	$where.=" and d_id not in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL) ";
     }
    
    if (isN($order)) { $orders = "d_time desc ";} else {
    	if(!isN($sort)){
    		$orders=$order. ' '.$sort;
    	}
    }
    
    if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
    if(!isN($from)) { $where .= " and d_playfrom like  '%" . $from . "%' ";}
    if($spic==1){
    	$where .= " AND d_pic = '' ";
    }
    else if($spic==2){
    	$where .= " and  d_pic not like '%joyplus%' and d_pic!=''  ";
    }
    if($ipadpic==1){
    	$where .= " AND (d_pic_ipad = ''  or d_pic_ipad is null )";
    }
    else if($ipadpic==2){
    	$where .= " AND d_pic_ipad not like '%joyplus%' and d_pic_ipad != '' ";
    }
    
    $select_weburl=be("all", "select_weburl");
    $select_videourl=be("all", "select_videourl");
    $select_videourl_play=be("all", "select_videourl_play");
    if(!isNum($select_videourl_play)) {
    	$select_videourl_play = -1;
    } else {
    	$select_videourl_play = intval($select_videourl_play);
    }
    
    if($select_videourl_play ==0) {
    	$where .= " AND d_play_check = 0 ";
    }
    
    if($select_videourl_play ==2) {
    	$where .= " AND d_play_check = 2 ";
    }
    
     if($select_videourl_play ==1) {
    	$where .= " AND d_play_check = 1 ";
    }
	if($select_weburl==1){
    	$where .= " AND webUrls is not null and webUrls !='' ";
    }
    
     if($select_weburl==2){
    	$where .= " AND (webUrls is null or  webUrls ='') ";
    }
    
	if($select_videourl==1){
    	$where .= " AND d_downurl is not null and d_downurl !='' ";
    }
    
     if($select_videourl==2){
    	$where .= " AND (d_downurl is null or d_downurl ='') ";
    }
    
    $sql = "SELECT count(*) FROM {pre}vod ".$repeatsql." where ".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	//$sql = "SELECT d_year,d_id, d_name, d_enname, d_play_num,d_type,d_state,d_topic, d_level, d_hits, d_time,d_remarks,d_playfrom,d_hide,p.id as popular_id FROM {pre}vod ".$repeatsql." left join {pre}vod_popular as p on p.vod_id=d_id  WHERE" . $where . " ORDER BY " . $orders . "  limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
  
    $sql = "SELECT d_year,d_id, d_name, d_enname, d_play_num,d_type,d_state,d_topic, d_level, d_hits, d_time,d_remarks,d_playfrom,d_hide FROM {pre}vod ".$repeatsql."  WHERE" . $where . " ORDER BY " . $orders . "  limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
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
	var stype=$("#stype").val();
	var order=$("#order").val();
	var from=$("#from").val();
	var sort=$("#sort").val();
	var keyword=$("#keyword").val();
	var keyword_col=$("#keyword_col").val();
	var can_search_device=$("#can_search_device").val();
	var url = "admin_online_subscribe.php?can_search_device="+can_search_device+"&keyword_col="+keyword_col+"&sort="+sort+"&keyword="+encodeURI(keyword)+"&stype="+stype+"&order="+order+"&from="+from; //ipadpic
	window.location.href=url;
}


function prepareWeiboText(type,id,name){
	   document.getElementById( "weiboText").value= name; 
	   document.getElementById( "notify_msg_prod_id").value= id; 
	   document.getElementById( "notify_msg_prod_type").value= type; 
	   $('#SendWeiboMsg').empty();
}


function sendWeiboText(){

	var channels= document.getElementById( "channel[]");
	alert(channels);
	var weibotxt= document.getElementById( "weiboText").value;
	var notify_msg_prod_id= document.getElementById( "notify_msg_prod_id").value;
	var notify_msg_prod_type= document.getElementById( "notify_msg_prod_type").value;
	var urlT='admin_vod.php?action=notifyMsg&prod_type='+notify_msg_prod_type+'&prod_id='+notify_msg_prod_id+'&content=' +encodeURIComponent(weibotxt) ;
	
	for(var i = 0; i < channels.length; i++){
		 if (channels[i].checked == true) {
			  urlT = urlT +'&channels='+channels[i].value;				 		 
		  }
	}
		//alert(urlT);
		 $.post(urlT, {Action:"post"}, function (data, textStatus){     
			  if(textStatus == "success"){   
	          //alert(data);
				  $('#SendWeiboMsg').empty().append(data);
	           }else{
	        	   $('#SendWeiboMsg').empty().append('发送失败。');
	           }
	       });
	
    // alert(urlT);
	 
}

</script>
<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	过滤条件：<select id="stype" name="stype" onchange="javascript:{var typeid= this.options[this.selectedIndex].value; if(typeid=='1' ||  typeid=='2'){document.getElementById('btnsearchs').style.display='block';  document.getElementById('btnsearchsThumbs').style.display='block';document.getElementById('btnsearchsComment').style.display='block';}else {document.getElementById('btnsearchs').style.display='none'; document.getElementById('btnsearchsThumbs').style.display='none';document.getElementById('btnsearchsComment').style.display='none';}}">
	<option value="0">视频栏目</option>
	<option value="-1" <?php if($stype==-1){ echo "selected";} ?>>没有栏目</option>
	<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$stype)?>
	</select>
	&nbsp;
	
	<select id="order" name="order">
	<option value="d_time">视频排序</option>
	<option value="d_id" <?php if($order=="d_id"){ echo "selected";} ?>>视频编号</option>
	<option value="d_name" <?php if($order=="d_name"){ echo "selected";} ?>>视频名称</option>
	<option value="d_play_num" <?php if($order=="d_play_num"){ echo "selected";} ?>>播放次数</option>
	<option value="d_year" <?php if($order=="d_year"){ echo "selected";} ?>>上映日期</option>
	</select>
	&nbsp;<select id="sort" name="sort">
	<option value="desc" <?php if($sort=="desc"){ echo "selected";} ?>>视频排序 降序序</option>
	<option value="asc" <?php if($sort=="asc"){ echo "selected";} ?>>视频排序  升序</option>
	</select>
	
	
	&nbsp;
	<select id="from" name="from">
	<option value="">视频播放器</option>
	<?php echo makeSelectPlayer($from)?>
	</select>
	
	 <select   id="can_search_device" name="can_search_device">
	    <option value="" >投放设备</option>
		<option value="TV" <?php if ($can_search_device==='TV'){ echo "selected";} ?>>TV版</option>
		<option value="iPad" <?php if ($can_search_device==='iPad'){ echo "selected";} ?>>iPad版</option>
		<option value="iphone" <?php if ($can_search_device==='iphone'){ echo "selected";} ?>>iphone版</option>
		<option value="apad" <?php if ($can_search_device==='apad'){ echo "selected";} ?>>Android-Pad版</option>
		<option value="aphone" <?php if ($can_search_device==='aphone'){ echo "selected";} ?>>Android-Phone版</option>
		<option value="web" <?php if ($can_search_device==='web'){ echo "selected";} ?>>网站版</option>
	</select> 
	
	</td>
	</tr>
	<tr>
	<td colspan="4">
	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">&nbsp;
	<select id="keyword_col" name="keyword_col">
	<option value="">关键字的匹配列</option>
	<option value="d_name" <?php if($keyword_col=="d_name"){ echo "selected";} ?>>视频名称</option>
	<option value="d_starring" <?php if($keyword_col=="d_starring"){ echo "selected";} ?>>演员</option>
	<option value="d_directed" <?php if($keyword_col=="d_directed"){ echo "selected";} ?>>导演</option>
	</select>
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
	<td width="6%">上映日期</td>
	<td width="5%">时间</td>
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
		  		$d_id=$row["d_id"];
		  		$tname= "未知";
				$tenname="";
		  		$typearr = getValueByArray($cache[0], "t_id" ,$row["d_type"] );
				if(is_array($typearr)){
					$tname= $typearr["t_name"];
					$tenname= $typearr["t_enname"];
				}
	?>
    <tr>
	<td><?php echo $d_id?></td>
	<td><?php echo substring($row["d_name"],20)?>
	<?php if($row["d_state"] > 0) {?><?php echo "<font color=\"red\">[" .$row["d_state"] . "]</font>"; }?>
	<?php if(!isN($row["d_remarks"])) {?><?php echo "<font color=\"red\">[" .$row["d_remarks"] . "]</font>"; }?>
	<?php if($row["d_hide"]==1){echo "<font color=\"red\">[隐藏]</font>";} ?>
	</td>
	<td><?php echo $row["d_play_num"]?></td>
	<td><?php echo $row["d_year"]?></td>
	<td><?php echo $tname?></td>
	<td><?php echo isToday($row["d_time"])?></td>
	<td><a href="admin_vod_topic.php?action=info&id=<?php echo $d_id?>">所在榜单</a> |
	<a class="thickbox" href="#TB_inline?height=400&width=600&inlineId=myOnPageContent" onclick="javascript:{prepareWeiboText('<?php echo $row["d_type"]?>','<?php echo $d_id?>','<?php echo substring($row["d_name"],20)?>');}" > 消息推送</a>	 </td>
    </tr>
	<?php
			}
		}
	?>
<!--	<tr>-->
<!--	<td colspan="12">-->
<!--	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'d_id[]');"/>&nbsp;-->
<!--    批量操作：<input type="button" id="btnDel" value="删除" class="input">-->
<!--	<input type="button" id="pltj" value="推荐" onClick="plset('pltj','vod')" class="input">-->
<!--	<input type="button" id="plfl" value="分类" onClick="plset('plfl','vod')" class="input">-->
<!--	<input type="button" id="plzt" value="专题" onClick="plset('plzt','vod')" class="input">-->
<!--	<input type="button" id="plluobo" value="轮播图" onClick="plsetLuobo()" class="input">-->
<!--	<input type="button" id="plbd" value="视频悦单" onClick="plsetBD('plbd','vod','1')" class="input">-->
<!--	<input type="button" id="plbd" value="视频悦榜" onClick="plsetBD('plbd','vod','2')" class="input">-->
<!--	<input type="button" id="plrq" value="人气" onClick="plset('plrq','vod')" class="input">-->
<!--	<input type="button" id="plsc" value="生成" class="input">-->
<!--	<input type="button" id="plyc" value="显隐" onClick="plset('plyc','vod')" class="input">-->
<!--	<span id="plmsg" name="plmsg"></span>-->
<!--	</td>-->
<!--	</tr>-->
	<tr>
	<td align="center" colspan="12">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_online_subscribe.php?page={p}&can_search_device=" . $can_search_device . "&keyword=" . urlencode($keyword) . "&keyword_col=".$keyword_col."&sort=" . $sort . "&order=".$order ."&stype=" . $stype ."&from=".$from)?>   //
	</td>
	</tr>
</table>

</form>
<div id="myOnPageContent" style="display:none">
<script language="javascript">

$('#form2').form({
	success:function(data){
		$('#SendWeiboMsg').empty().append(data);
		$("#btnEdit").attr("disabled",false); 		
    }
});

$("#btnEdit").click(function(){
	if(confirm('确定要推送消息吗')){
		$("#form2").attr("action","?action=notifyMsg");
		$("#form2").submit();
		$("#btnEdit").attr("disabled",true); 
	}
});
</script>
<form id="form2"  name="form1" method="post">
<table class="table" cellpadding="0" cellspacing="0" width="100%" border="0">

                <thead class="tb-tit-bg">
<!--                   <tr>-->
<!--                        <td > <h3 class="title">    发送设备:<select name="device_type" id="device_type" >                       -->
<!--                             <option value="" >所有设备</option>-->
<!--                             <option value="ios" >IOS</option>-->
<!--                             <option value="android" >Android</option>                          -->
<!--                        </select> -->
<!--                        </h3></td>    -->
<!--                      -->
<!--                    </tr>-->
                    <tr>
                        <td colspan="2"><span><font color="blue">发送信息 </font></span></td>    
                       
                    </tr>
                    <input type="hidden" name="notify_msg_prod_id" id="notify_msg_prod_id" value="">
                    <input type="hidden" name="notify_msg_prod_type" id="notify_msg_prod_type" value="">
                      
                      <tr>
                        <td colspan="2" align="center"><textarea name="wbText" id="weiboText" rows="10" cols="90" style="border:1;border-color:blue;" ></textarea></td>
                    </tr>
                     <tr>
                        <td align="left"> <br/>
	                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id='channel[]' name="channel[]" value="CHANNEL_ANDROID" checked  />悦视频 Android版<br/>
						    &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id='channel[]' name="channel[]" value="CHANNEL_TV" checked />悦视频 TV版
                        </td>
                        <td align="left"> 
						    <input type="checkbox" id='channel[]' name="channel[]" value="CHANNEL_IOS" checked />悦视频 IOS版<br/>
                        </td>
                    </tr>
                      <tr>
                         <td align="center" colspan="2"><input type="button" value="发送消息" id="btnEdit"  class="input"  /></td>
                      </tr>
                        <tr>
                        <td align="center" colspan="2">  <font color=red><span id="SendWeiboMsg"></span></font></td>    
                       
                    </tr> 
                    
                </thead>
            </table>
</form>
</div>
<?php
if ($pagenum==1 && $where==" 1=1 ") { echo "<script>showpic();</script>";}
unset($rs);
}



function notifyMsg(){
	
	$d_id = be("all","notify_msg_prod_id");$prod_type = be("all","notify_msg_prod_type");
	
	$can_search_device=be("arr","channel");
	if(!isN($can_search_device)){
		$content = be("all","wbText");
		$msg = new Notification();
		$msg->alert=$content;
		$msg->prod_id=$d_id;
		$msg->prod_type=$prod_type;
		$msg->push_type='1';
		$msg->channels=explode(",", $can_search_device);
		$isoFlag=false;
		$androidFlag=false;
		
		if(strpos($can_search_device, 'CHANNEL_ANDROID') !==false || strpos($can_search_device, 'CHANNEL_TV') !==false){
			$androidFlag=true;
		}
	    if(strpos($can_search_device, 'CHANNEL_IOS') !==false ){
			$isoFlag=true;
		}
	    if($isoFlag && !$androidFlag){
			$msg->type=NotificationsManager::DEVICE_ISO;	
		}
		if($androidFlag && !$isoFlag){
			$msg->type=NotificationsManager::DEVICE_ANDROID;	
		}
		
		$result= NotificationsManager::push($msg);
		if($result['code'].'' == '200'){
			echo "消息推送成功";
		}else {
			echo "消息推送失败:".$result['response'];
		};
	}else {
		echo "你必须要选择一个频道发送";
	}
}



?>
</body>
</html>