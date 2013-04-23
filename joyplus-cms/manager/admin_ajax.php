<?php
require_once ("admin_conn.php");
require_once ("genTopRecommendItems.php");
chkLogin();

$action = be("all","action");
$flag = be("all","flag");
$show = be("all","show");
$id = be("all","id");
$name = be("all","name");
$dbtype=be("all","dbtype");
$ajaxcontent = be("all","ajaxcontent");
//var_dump($action .'==='.$id);
$ajaxcontent=trim($ajaxcontent);
if ($flag=="vod"){
    $tid ="d_id";
    $ttype="d_type";
    $thit="d_hits";
    $ttopic="d_topic";
    $thide="d_hide";
}
else if ($flag=="art"){
	$tid ="a_id";
	$ttype="a_type";
	$thit="a_hits";
	$ttopic="a_topic";
	$thide="a_hide";
}

switch($action)
{
	case "getinfo" : getinfo();break;
	case "getinfoxml" : getinfoxml();break;
	case "save" : save();break;
	case "savexml" : savexml();break;
	case "del" : del();break;
	case "lunbo" : lunbo();break;
	case "delxml" : delxml();break;
	case "tj" : mtuijian();break;
	case "lz" : mstatus();break;
	case "zt" : mtopic();break;
	case "lunboForTopic" : lunboForTopic();break;
	 case "plluobo" : plluobo();break;
	case "bd" : bdtopic();break;
	case "subscribe_msg_list" : subscribe_msg_list();break;
	case "pltj" : mpltuijian();break;
	case "plfl" : mplfenlei();break;
	case "plzt" : mplzhuanti();break;
	case "plbd" : mplbangdan();break;
	case "plrq" : mplrenqi();break;
	case "plyc" : mplyinccang();break;
	case "ckname" : mname();break;
	default: redirect( getReferer() );break;
}
dispseObj();

function getinfoxml()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$val = be("all","val");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$arr = array();
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			if ($tab=="vodplay"){
				$path="player";
			}
			else if ($tab=="voddown"){
				$path="down";
			}
			else if ($tab=="vodserver"){
				$path="server";
			}
			$doc -> load("../inc/". $tab.".xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			foreach($nodes as $node){
				if ($val == $node->attributes->item(2)->nodeValue){
					$status = $node->attributes->item(0)->nodeValue;
					$sort = $node->attributes->item(1)->nodeValue;
					$show = $node->attributes->item(3)->nodeValue;
					$des = $node->attributes->item(4)->nodeValue;
					$tip = $node->getElementsByTagName("tip")->item(0)->nodeValue;
					break;
				}
			}
			$arr = array("from"=>"$val",
			"status"=>"$status",
			"sort"=>"$sort",
			"des"=>"$des",
			"tip"=>"$tip",
			"show"=>"$show"
			);
			unset($nodes);
			unset($xmlnode);
			break;
		case "timming":
			$doc -> load("../inc/timmingset.xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			foreach($nodes as $node){
				if ($val == $node->getElementsByTagName("name")->item(0)->nodeValue){
					$des = $node->getElementsByTagName("des")->item(0)->nodeValue;
					$status = $node->getElementsByTagName("status")->item(0)->nodeValue;
					$file = $node->getElementsByTagName("file")->item(0)->nodeValue;
					$paramets = $node->getElementsByTagName("paramets")->item(0)->nodeValue;
					$weeks = $node->getElementsByTagName("weeks")->item(0)->nodeValue;
					$hours = $node->getElementsByTagName("hours")->item(0)->nodeValue;
					break;
				}
			}
			$arr = array("name"=>"$val",
			"des"=>"$des",
			"status"=>"$status",
			"file"=>"$file",
			"paramets"=>"$paramets",
			"weeks"=>"$weeks",
			"hours"=>"$hours"
			);
			unset($nodes);
			unset($xmlnode);
			break;
		default:
			break;
	}
	unset($doc);
	echo json_encode($arr);
}

function delxml()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$val = be("all","val");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			if ($tab=="vodplay"){
				$path="player";
			}
			else if ($tab=="voddown"){
				$path="down";
			}
			else if ($tab=="vodserver"){
				$path="server";
			}
			$doc -> load("../inc/". $tab.".xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			foreach($nodes as $node){
				if ($val == $node->attributes->item(2)->nodeValue){
					$xmlnode->removeChild($node);
					break;
				}
			}
			$doc -> save("../inc/". $tab.".xml");
			unset($nodes);
			unset($xmlnode);
			break;
		case "timming":
			$doc -> load("../inc/timmingset.xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			foreach($nodes as $node){
				if ($val == $node->getElementsByTagName("name")->item(0)->nodeValue){
					$xmlnode->removeChild($node);
					break;
				}
			}
			$doc -> save("../inc/timmingset.xml");
			unset($nodes);
			unset($xmlnode);
			break;
		default:
			break;
	}
	unset($doc);
	redirect( getReferer() );
}

function savexml()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$flag = be("all","flag");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			$from = be("post","from");
			$status = be("post","status");
			$des = be("post","des");
			$tip = be("post","tip");
			$sort = be("post","sort");
			$show = be("post","show");
			if ($tab=="vodplay"){
				$path="player";
			}
			else if ($tab=="voddown"){
				$path="down";
			}
			else if ($tab=="vodserver"){
				$path="server";
			}
			
			$doc -> load("../inc/". $tab.".xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			
			if ($flag=="edit"){
				foreach($nodes as $node){
					if ($from == $node->attributes->item(2)->nodeValue){
						$node->attributes->item(0)->nodeValue = $status;
						$node->attributes->item(1)->nodeValue = $sort;
						$node->attributes->item(3)->nodeValue = $show;
						$node->attributes->item(4)->nodeValue = $des;
						$node->getElementsByTagName("tip")->item(0)->nodeValue = "";
						$node->getElementsByTagName("tip")->item(0)->appendChild($doc->createCDATASection($tip));
						break;
					}
				}
				$doc -> save("../inc/". $tab.".xml");
				unset($nodes);
				unset($xmlnode);
			}
			else{
				$nodenew = $doc -> createElement($path);
				
				$nodestatus1 =  $doc -> createAttribute("status");
				$nodestatus2 =  $doc -> createTextNode($status);
				$nodestatus1 -> appendChild($nodestatus2);
				
				$nodesort1 =  $doc -> createAttribute("sort");
				$nodesort2 =  $doc -> createTextNode($sort);
				$nodesort1 -> appendChild($nodesort2);
				
				$nodefrom1 =  $doc -> createAttribute("from");
				$nodefrom2 =  $doc -> createTextNode($from);
				$nodefrom1 -> appendChild($nodefrom2);
				
				$nodeshow1 =  $doc -> createAttribute("show");
				$nodeshow2 =  $doc -> createTextNode($show);
				$nodeshow1 -> appendChild($nodeshow2);
 				
				$nodedes1 =  $doc -> createAttribute("des");
				$nodedes2 =  $doc -> createTextNode($des);
				$nodedes1 -> appendChild($nodedes2);
				
				$nodetip1 = $doc -> createElement("tip");
				$nodetip2 = $doc -> createCDATASection($tip);
				$nodetip1 -> appendChild($nodetip2);
				
				$nodenew -> appendChild($nodestatus1);
				$nodenew -> appendChild($nodesort1);
				$nodenew -> appendChild($nodefrom1);
				$nodenew -> appendChild($nodeshow1);
				$nodenew -> appendChild($nodedes1);
				$nodenew -> appendChild($nodetip1);
				
				$doc->getElementsByTagName($path."s")-> item(0)  -> appendChild($nodenew);
				$doc -> save("../inc/". $tab.".xml");
				unset($nodenew);
			}
		case "timming":
			$name = be("post","name");
			$des = be("post","des");
			$status = be("post","status");
			$file = be("post","file");
			$paramets = be("post","paramets"); $paramets = replaceStr($paramets,"&","&amp;");
			$weeks = be("arr","weeks");
			$hours = be("arr","hours");
			
			$doc -> load("../inc/timmingset.xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			
			if ($flag=="edit"){
				foreach($nodes as $node){
					if ($name == $node->getElementsByTagName("name")->item(0)->nodeValue){
						$node->getElementsByTagName("des")->item(0)->nodeValue = $des;
						$node->getElementsByTagName("status")->item(0)->nodeValue = $status;
						$node->getElementsByTagName("file")->item(0)->nodeValue = $file;
						$node->getElementsByTagName("paramets")->item(0)->nodeValue = $paramets;
						$node->getElementsByTagName("weeks")->item(0)->nodeValue = $weeks;
						$node->getElementsByTagName("hours")->item(0)->nodeValue = $hours;
						
						if( $node->getElementsByTagName("runtime")->length==0){
							$noderuntime1 = $doc -> createElement("runtime");
							$noderuntime2 = $doc -> createTextNode("");
							$noderuntime1 -> appendChild($noderuntime2);
							$node -> appendChild($noderuntime1);
						}
					}
				}
				$doc -> save("../inc/timmingset.xml");
			}
			else{
				
				$nodenew = $doc -> createElement('timming');
				
				$nodename1 =  $doc -> createElement("name");
				$nodename2 =  $doc -> createTextNode($name);
				$nodename1 -> appendChild($nodename2);
				
				$nodedes1 = $doc -> createElement("des");
				$nodedes2 = $doc -> createTextNode($des);
				$nodedes1 -> appendChild($nodedes2);
				
				$nodestatus1 = $doc -> createElement("status");
				$nodestatus2 = $doc -> createTextNode($status);
				$nodestatus1 -> appendChild($nodestatus2);
				
				$nodefile1 = $doc -> createElement("file");
				$nodefile2 = $doc -> createTextNode($file);
				$nodefile1 -> appendChild($nodefile2);
				
				$nodeparamets1 = $doc -> createElement("paramets");
				$nodeparamets2 = $doc -> createTextNode($paramets);
				$nodeparamets1 -> appendChild($nodeparamets2);
				
				$nodeweeks1 = $doc -> createElement("weeks");
				$nodeweeks2 = $doc -> createTextNode($weeks);
				$nodeweeks1 -> appendChild($nodeweeks2);
				
				$nodehours1 = $doc -> createElement("hours");
				$nodehours2 = $doc -> createTextNode($hours);
				$nodehours1 -> appendChild($nodehours2);
				
				$noderuntime1 = $doc -> createElement("runtime");
				$noderuntime2 = $doc -> createTextNode("");
				$noderuntime1 -> appendChild($noderuntime2);
				
				
				$nodenew -> appendChild($nodename1);
				$nodenew -> appendChild($nodedes1);
				$nodenew -> appendChild($nodestatus1);
				$nodenew -> appendChild($nodefile1);
				$nodenew -> appendChild($nodeparamets1);
				$nodenew -> appendChild($nodeweeks1);
				$nodenew -> appendChild($nodehours1);
				$nodenew -> appendChild($noderuntime1);
				
				$doc->getElementsByTagName("timmings")-> item(0) -> appendChild($nodenew);
				$doc -> save("../inc/timmingset.xml");
			}
			break;
		default:
			break;
	}
	unset($doc);
	echo "保存完毕";
}

function getinfo()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$col = be("all","col");
	$val = be("all","val");
	if($tab ==='{pre}vod_popular'){
		$type = be("all","type");
		if($type ==='0'){
		  $row = $db->queryArray("SELECT a.id as id,a.iphone_pic_url as iphone_pic_url,a.vod_id as vod_id,a.ipad_pic_url as ipad_pic_url,a.disp_order as disp_order,a.status as status,a.info_desc ,b.d_name as vod_name,a.type as type from ".$tab." a ,{pre}vod as b  WHERE a.vod_id=b.d_id and ".$col."=".$val,false);
		}else if($type ==='1'){
			 $row = $db->queryArray("SELECT a.id as id,a.iphone_pic_url as iphone_pic_url,a.vod_id as vod_id,a.ipad_pic_url as ipad_pic_url,a.disp_order as disp_order,a.status as status,a.info_desc ,b.t_name as vod_name ,a.type as type from ".$tab." a ,{pre}vod_topic as b  WHERE a.vod_id=b.t_id and ".$col."=".$val,false);
		
		}else{
			 $row = $db->queryArray("SELECT a.id as id,a.iphone_pic_url as iphone_pic_url,a.vod_id as vod_id,a.ipad_pic_url as ipad_pic_url,a.disp_order as disp_order,a.status as status,a.info_desc ,'' as vod_name ,a.type as type from ".$tab." a where a.type=".$type." and ".$col."=".$val,false);
		
		}
	}else {
		$row = $db->queryArray("SELECT * from ".$tab." WHERE ".$col."=".$val,false);
	}
//	var_dump("SELECT a.id as id,a.iphone_pic_url as iphone_pic_url,a.vod_id as vod_id,a.ipad_pic_url as ipad_pic_url,a.disp_order as disp_order,a.status as status,a.info_desc ,b.d_name as vod_name from ".$tab." a ,{pre}vod as b  WHERE a.id=b.d_id and".$col."=".$val);
//	$row = $db->queryArray("SELECT * from ".$tab." WHERE ".$col."=".$val,false);
	if($tab ==='{pre}vod_topic'){
		$rows= array();
		$rows['t_name']=$row[0]['t_name'];
		$rows['t_id']=$row[0]['t_id'];
		$rows['t_toptype']=$row[0]['t_toptype'];
		$rows['t_type']=$row[0]['t_type'];
		$rows['t_pic']=$row[0]['t_pic'];
		$rows['t_flag']=$row[0]['t_flag'];
		$rows['t_bdtype']=$row[0]['t_bdtype'];
		$rows['t_tag_name']=$row[0]['t_tag_name'];
		$rows['t_sort']=$row[0]['t_sort'];
		$rows['t_des']=$row[0]['t_des'];
		$rows['can_search_device[]']=$row[0]['can_search_device'];		
	    if(isN($rows['can_search_device[]'])){ 
        	$rows['can_search_device[]']=array("iPad","iphone","web","TV","aphone","apad");
        }else {
        	$rows['can_search_device[]']=split(",", $rows['can_search_device[]']);
        }
        $rowst= array();
        $rowst[]=$rows;
        $str = json_encode($rowst);
	  echo substr($str,1,strlen($str)-2);	
	}else {
	  $str = json_encode($row);
	  echo substr($str,1,strlen($str)-2);	
	}
	unset($row);
}

function save()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$flag = be("all","flag");
	$upcache=false;
	switch($tab)
	{
		case "{pre}link" :
			$l_id = be("all","l_id");
			$l_name = be("post","l_name");
			$l_type = be("post","l_type");
			$l_url = be("post","l_url");
			$l_sort = be("post","l_sort");
			$l_logo = be("post","l_logo");
			if (!isNum($l_sort)) { $l_sort= $db->getOne("SELECT MAX(l_sort) FROM {pre}link")+1; }
			$colarr = array("l_name","l_type","l_url","l_sort","l_logo");
			$valarr = array($l_name,$l_type,$l_url,$l_sort,$l_logo);
			$where = "l_id=".$l_id;
			break;
		case "{pre}vod_type" :
			$t_id = be("all","t_id");
			$t_name = be("post","t_name");
			$t_enname = be("post","t_enname");
			$t_pid = be("post","t_pid");
			$t_sort = be("post","t_sort");
			$t_key = be("post","t_key");
			$t_des = be("post","t_des");
			$t_template = be("post","t_template");
			$t_vodtemplate= be("post","t_vodtemplate");
			$t_playtemplate = be("post","t_playtemplate");
			if (!isNum($t_sort)) { $t_sort= $db->getOne("SELECT MAX(t_sort) FROM {pre}vod_type")+1; }
			$colarr = array("t_name","t_enname","t_sort","t_pid","t_template","t_vodtemplate","t_playtemplate","t_key","t_des");
			$valarr = array($t_name,$t_enname,$t_sort,$t_pid,$t_template,$t_vodtemplate,$t_playtemplate,$t_key,$t_des);
			$where = "t_id=".$t_id;
			$upcache=true;
			break;
		case "{pre}vod_topic" :
			$t_id = be("all","t_id");
			$t_name = be("post","t_name");
			$t_toptype = be("post","t_toptype");
			$t_sort = be("post","t_sort");
			$t_pic = be("post","t_pic");
			$t_bdtype=be("post","t_bdtype");
			$t_template = be("post","t_template");
			$t_des = be("post","t_des");
			$t_tag_name= be("post","t_tag_name");
			$can_search_device= $_POST["can_search_device"];
			if(is_array($can_search_device)){
				$can_search_device=implode(',',$can_search_device);
			}
			
			$t_flag = be("post","t_flag" .$id);$t_type= be("post","t_type" .$id);
			if (!isNum($t_sort)) { $t_sort= 0; }
			$colarr = array("can_search_device","t_tag_name","t_name","t_toptype","t_sort","t_pic","t_template","t_des","t_flag","t_type","t_bdtype");
			$valarr = array($can_search_device,$t_tag_name,$t_name,$t_toptype,$t_sort,$t_pic,$t_template,$t_des,$t_flag,$t_type,$t_bdtype);
			$where = "t_id=".$t_id;
			$upcache=true;
			break;
		case "{pre}vod_popular" :
			$t_id = be("all","id");
			$iphone_pic_url = be("post","iphone_pic_url");
			$ipad_pic_url = be("post","ipad_pic_url");
			$status = be("post","status");
			$type = be("post","type");
			$disp_order = be("post","disp_order");
			$info_desc=be("post","info_desc");
//			if (!isNum($disp_order)) { $disp_order= $db->getOne("select max(disp_order) from {pre}vod_popular")+1; }
			$colarr = array("iphone_pic_url","ipad_pic_url","info_desc","disp_order","type");
			$valarr = array($iphone_pic_url,$ipad_pic_url,$info_desc,$disp_order,$type);
			$where = "id=".$t_id;
//			var
			$upcache=true;
			break;	
			
		case "{pre}art_type" :
			$t_id = be("all","t_id");
			$t_name = be("post","t_name");
			$t_enname = be("post","t_enname");
			$t_pid = be("post","t_pid");
			$t_sort = be("post","t_sort");
			$t_key = be("post","t_key");
			$t_des = be("post","t_des");
			$t_template = be("post","t_template");
			$t_arttemplate= be("post","t_arttemplate");
			if (!isNum($t_sort)) { $t_sort= $db->getOne("SELECT MAX(t_sort) FROM {pre}art_type")+1; }
			$colarr = array("t_name","t_enname","t_sort","t_pid","t_template","t_arttemplate","t_key","t_des");
			$valarr = array($t_name,$t_enname,$t_sort,$t_pid,$t_template,$t_arttemplate,$t_key,$t_des);
			$where = "t_id=".$t_id;
			$upcache=true;
			break;
		case "{pre}art_topic" :
			$t_id = be("all","t_id");
			$t_name = be("post","t_name");
			$t_enname = be("post","t_enname");
			$t_sort = be("post","t_sort");
			$t_pic = be("post","t_pic");
			$t_template = be("post","t_template");
			$t_des = be("post","t_des");
			if (!isNum($t_sort)) { $t_sort= $db->getOne("select max(t_sort) from {pre}art_topic")+1;}
			$colarr = array("t_name","t_enname","t_sort","t_pic","t_template","t_des");
			$valarr = array($t_name,$t_enname,$t_sort,$t_pic,$t_template,$t_des);
			$where = "t_id=".$t_id;
			$upcache=true;
			break;
		case "{pre}tv" :
			$id = be("all","id");
			$tv_name = be("post","tv_name");
			$status = be("post","status");
			$tv_code = be("post","tv_code");
			$tv_type = be("post","tv_type");
			$country = be("post","country");
			$area = be("post","area");
			$tv_type = be("post","tv_type");
			$tv_playurl = be("post","tv_playurl");
			$colarr = array("tv_name","tv_code","tv_type","tv_playurl","create_date",'status',"country",'area');
			$valarr = array($tv_name,$tv_code,$tv_type,$tv_playurl,date('Y-m-d H:i:s',time()),$status,$country,$area);
			$where = "id=".$id;
			$upcache=true;
			break;
			
		case "{pre}tv_program_item" :
			$id = be("all","id");
			$play_time = be("post","play_time");
			$video_name = be("post","video_name");
			$tv_id = be("all","tv_id");
			$day = be("all","day");
			$program_type = be("all","program_type");
			$colarr = array("play_time","video_name","tv_id","day","program_type");
			$valarr = array($play_time,$video_name,$tv_id,$day,$program_type);
			$where = "id=".$id;
		     //update config tabel
			if(!isN($program_type)){
				$row = $db->getRow("select * from  mac_tv_program_type_item where program_type ='".$program_type."' and program_name='".$video_name."'");
				// var_dump("select * from  mac_tv_program_type_item where program_type ='".$program_type."' and program_name='".$video_name."'");
				// var_dump($row);
				if(!$row){
					 $db->query("insert into mac_tv_program_type_item(program_type,program_name) values('".$program_type."','".$video_name."')");
					 var_dump("insert into mac_tv_program_type_item(program_type,program_name) values('".$program_type."','".$video_name."')");
				}			
				$db->query("update mac_tv_program_item set program_type='".$program_type ."' where video_name='".$video_name."'");
				//var_dump("update mac_tv_program_item set program_type='".$program_type ."' where video_name='".$video_name."'");
			}
			$upcache=true;
			break;
	    case "{pre}tv_play" :
			$id = be("all","id");
			$tv_playfrom = be("post","tv_playfrom");
			$status = be("post","status");
			$tv_id = be("all","tv_id");
			$tv_playurl = be("all","tv_playurl");
			$tv_definition = be("all","tv_definition");
			$colarr = array("tv_playfrom","status","tv_id","tv_playurl","tv_definition");
			$valarr = array($tv_playfrom,$status,$tv_id,$tv_playurl,$tv_definition);
			$where = "id=".$id;
			$upcache=true;
			break;
			
			
		case "{pre}gbook":
			$g_id = be("all","g_id");
			$g_reply = be("all","g_reply");
			$g_replytime= date('Y-m-d',time());
			$colarr = array("g_reply","g_replytime");
			$valarr = array($g_reply,$g_replytime);
			$where = "g_id=".$g_id;
			break;
		case "{pre}manager":
			$m_id = be("all","m_id");
			$m_name = be("all","m_name");
			$m_password = be("all","m_password");
			$m_levels = be("arr","m_levels");
			$m_status = be("all","m_status");
			if( $m_password !=""){
				$colarr = array("m_name","m_password","m_levels","m_status");
				$valarr = array($m_name,md5($m_password),$m_levels,$m_status);
			}
			else{
				$colarr = array("m_name","m_levels","m_status");
				$valarr = array($m_name,$m_levels,$m_status);
			}
			$where = "m_id=".$m_id;
			break;
		case "{pre}user_group":
			$ug_id = be("all","ugid");
			$ug_name = be("all","ug_name");
			$ug_type = be("arr","ug_type");
			$ug_popedom = be("arr","ug_popedom");
			$ug_upgrade = be("all","ug_upgrade");
			$ug_popvalue = be("all","ug_popvalue");
			$str=$ug_type;
			$arr = explode(",",$str);
			$ug_type=",";
			for ($i=0;$i<count($arr);$i++){
				if(trim($arr[$i]) !=""){
					$ug_type = $ug_type. trim($arr[$i]) . ",";
				}
			}
			$ug_type = replaceStr($ug_type,",,",",");
			if($ug_type==","){ $ug_type="";}
			$str=$ug_popedom;
			$arr = explode(",",$str);
			$ug_popedom=",";
			for ($i=0;$i<count($arr);$i++){
				if(trim($arr[$i]) !=""){
					$ug_popedom = $ug_popedom . trim($arr[$i]) . ",";
				}
			}
			$ug_popedom = replaceStr($ug_popedom,",,",",");
			if($ug_popedom==","){ $ug_popedom="";}
			$colarr = array("ug_name","ug_type","ug_popedom","ug_upgrade","ug_popvalue");
			$valarr = array($ug_name,$ug_type,$ug_popedom,$ug_upgrade,$ug_popvalue);
			$where = "ug_id=".$ug_id;
			$upcache=true;
			break;
		case "{pre}user":
			$u_id = be("all","uid");
			$u_name = be("all","u_name");
			$u_group = be("all","u_group");
			$u_password = be("all","u_password");
			if(!isN($u_password)){
				$u_password=md5($u_password) ;
			}
			$u_qq = be("all","u_qq");
			$u_email = be("all","u_email");
			$u_status = be("all","u_status");
			$u_points = be("all","u_points");
			$u_flag=be("all","u_flag");
			$u_phone = be("all","u_phone");
			$u_question = be("all","u_question");
			$u_answer = be("all","u_answer");
			if ($u_flag ==1) {
				$u_start=be("all","u_starttime");
			    $u_end = be("all","u_endtime");
			}
			else if ($u_flag==2){
				$u_start=be("all","u_startip");
				$u_end = be("all","u_endip");
			}
			
			if ($flag=="add" || $u_password!=""){
				$colarr = Array("u_name","u_group","u_password","u_email","u_qq","u_phone","u_question","u_answer","u_status","u_points","u_start","u_end","u_flag");
				$valarr = array($u_name,$u_group,$u_password,$u_email,$u_qq,$u_phone,$u_question,$u_answer,$u_status,$u_points,$u_start,$u_end,$u_flag);
			}
			else{
				$colarr = Array("u_name","u_group","u_email","u_qq","u_phone","u_question","u_answer","u_status","u_points","u_start","u_end","u_flag");
				$valarr = array($u_name,$u_group,$u_email,$u_qq,$u_phone,$u_question,$u_answer,$u_status,$u_points,$u_start,$u_end,$u_flag);
			}
			$where = "u_id=".$u_id;
			break;
	}
	if ($flag=="add"){
		 $db->Add($tab,$colarr,$valarr);
	}
	else if ($flag=="edit"){
		
		$db->Update($tab,$colarr,$valarr,$where);
	}
//	var_dump($flag);
	if ($upcache){ updateCacheFile();}
    echo "保存完毕";
}

function del()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$tab = be("all","tab");
	$flag = be("all","flag");
	$upcache=false;
	switch($tab)
	{
		case "{pre}link" :
			$col="l_id";
			$ids = be("get","l_id");
			if(isN($ids)){
				$ids= be("arr","l_id");
			}
			break;
		case "{pre}vod_type":
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$upcache=true;
			break;
		case "{pre}vod_topic" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$upcache=true;
	        if (!isN($ids)) { $db->Delete("{pre}vod_topic_items", "topic_id"." in (".$ids.")"); }
			break;
			
		case "{pre}art":
			$col="a_id";
			$ids = be("get","a_id");
			if(isN($ids)){
				$ids= be("arr","a_id");
			}
			break;
		case "{pre}art_type" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$upcache=true;
			break;
		case "{pre}art_topic" :
			$col="t_id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			$upcache=true;
	        
			break;
		case "{pre}gbook":
			$col="g_id";
			$ids = be("get","g_id");
			if(isN($ids)){
				$ids= be("arr","g_id");
			}
			break;
		case "{pre}manager":
			$col="m_id";
			$ids = be("get","m_id");
			if(isN($ids)){
				$ids= be("arr","m_id");
			}
			break;
		case "{pre}user_group":
			$col="ug_id";
			$ids = be("get","ug_id");
			if(isN($ids)){
				$ids= be("arr","ug_id");
			}
			$upcache=true;
			break;
		case "{pre}user":
			$col="u_id";
			$ids = be("get","u_id");
			if(isN($ids)){
				$ids= be("arr","u_id");
			}
			break;
		case "{pre}user_card":
			$col="c_id";
			$ids = be("get","c_id");
			if(isN($ids)){
				$ids= be("arr","c_id");
			}
			break;
		case "{pre}comment":
			$col="c_id";
			$ids = be("get","c_id");
			if(isN($ids)){
				$ids= be("arr","c_id");
			}
			break;
		case "{pre}tv_play":
			$col="id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			break;
		case "{pre}vod_topic_items":
			$col="id";
			$ids = be("get","ids");
			if(isN($ids)){
				$ids= be("arr","ids");
			}
			break;
		case "{pre}vod_pasre_item":
			$col="id";
			$ids = be("get","ids");
			if(isN($ids)){
				$ids= be("arr","ids");
			}
			break; 
		case "{pre}vod_popular":
			$col="id";
			$ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}
			
			break;
	}
	if (!isN($ids)) { $db->Delete($tab, $col." in (".$ids.")"); 
	   if($tab ==='{pre}vod_topic_items'){
		   $topic_id= getBody(getReferer(), 'topic_id=', '&');
			if(isN($topic_id)){
				$topic_id= getBodys(getReferer(), 'topic_id=');
			}
			
			if(!isN($topic_id)){
			   replaceTopRecommend($topic_id);
			}
	   }
	}
	if ($upcache){ updateCacheFile();}
	if (isN($flag)){
		redirect ( getReferer() );
	}
	else{
	       
		echo "删除完毕";
	}
}


function lunboForTopic(){
	global $db;
	$tab = be("all","tab");
	$flag = be("all","flag");
	$upcache=false;
	switch($tab)
	{
		case "{pre}vod_popular" :
	       $ids = be("get","t_id");
			if(isN($ids)){
				$ids= be("arr","t_id");
			}			
			$colarr = array("vod_id","type","status");
			$ids=explode(",", $ids);
			foreach ($ids as $t_id){
					if(isNum($t_id)){
				       $valarr=array($t_id,'1','1');
				     $db->Add($tab,$colarr,$valarr);
					}
		    }
			$upcache=true;
			break;
	}
	
	if ($upcache){ updateCacheFile();}
    if (isN($flag)){
		redirect ( 'admin_vod_popular.php' );
	}
    echo "保存完毕";
}

function mname()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_name='". $name ."'");
	if (!$row){
		$str="d_name_ok$<img src=\"../images/icons/html_ok.gif\" border=\"0\">";
	}
	else{
		$str="d_name_ok$<img src=\"../images/icons/html_no.gif\" border=\"0\">";
	}
	unset($row);
	echo $str;
}

function mtuijian()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"\">请选择推荐</option><option value=\"1\">推荐1</option><option value=\"2\">推荐2</option><option value=\"3\">推荐3</option><option value=\"4\">推荐4</option><option value=\"5\">推荐5</option><option value=\"6\">推荐6</option><option value=\"7\">推荐7</option><option value=\"8\">推荐8</option><option value=\"0\">取消推荐</option></select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut><input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else{
		switch ($ajaxcontent){
			case "1" : $tjText ="1";break;
			case "2" : $tjText ="2";break;
			case "3" : $tjText ="3";break;
			case "4" : $tjText ="4";break;
			case "5" : $tjText ="5";break;
			case "6" : $tjText ="6";break;
			case "7" : $tjText ="7";break;
			case "8" : $tjText ="8";break;
			case "0" : $tjText ="0";break;
		}
		$db->Update ("{pre}vod" ,array("d_level"),array($ajaxcontent) ,"d_id = ".$id );
		echo "tj".$id."$<img src=\"../images/icons/ico".$tjText.".gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('tj','".$id."')\"/> ";
	}
}

function mpltuijian()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"\">请选择推荐</option><option value=\"1\">推荐1</option><option value=\"2\">推荐2</option><option value=\"3\">推荐3</option><option value=\"4\">推荐4</option><option value=\"5\">推荐5</option><option value=\"6\">推荐6</option><option value=\"7\">推荐7</option><option value=\"8\">推荐8</option><option value=\"0\">取消推荐</option></select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if ($show == 2){
		switch($ajaxcontent){
			case "1" : $tjText ="1";break;
			case "2" : $tjText ="2";break;
			case "3" : $tjText ="3";break;
			case "4" : $tjText ="4";break;
			case "5" : $tjText ="5";break;
			case "6" : $tjText ="6";break;
			case "7" : $tjText ="7";break;
			case "8" : $tjText ="8";break;
		}
		$db->query("UPDATE {pre}vod set d_level=".$ajaxcontent." WHERE d_id IN(".$id.")");
		$idarr = explode(",",$id);
		
		if ($ajaxcontent =="0"){
			for ($i=0;$i<count($idarr);$i++){
				echo "tj".$idarr[$i]."$<img src=\"../images/icons/ico0.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('tj','".$idarr[$i]."')\"/>|||";
			}
		}
		else{
			for ($i=0;$i<count($idarr);$i++){
				echo "tj".$idarr[$i]."$<img src=\"../images/icons/ico".$tjText.".gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxdivdel('".$idarr[$i]."','".$action."')\"/> |||";
			} 
		}
	}
}

function mplfenlei()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show ==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"0\">请选择栏目</option>" . makeSelectAll("{pre}".$flag."_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;","") ."</select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if($show ==2){
		$db->query ("UPDATE {pre}".$flag . " set ". $ttype ."=".$ajaxcontent. " WHERE " . $tid ." IN(".$id.")" );
		echo "reload";
	}
}

function mplzhuanti()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show ==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"0\">请选择专题</option>" . makeSelectWhere("{pre}".$flag."_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;",""," where t_id <= 4") ."</select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if($show ==2){
		$db->query ("UPDATE {pre}".$flag . " set ". $ttopic ."=".$ajaxcontent. " WHERE " . $tid ." IN(".$id.")" );
		echo "reload";
	}
}

function mplbangdan()
{
global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic,$dbtype;
	$bd=" ";
	$name='';
	$keyword = be("all","keyword");
	if($dbtype==1){
		$bd=" and t_bdtype=1 ";
		$name='悦单';
		
	}
    if($dbtype==2){
		$bd=" and t_bdtype=2 ";
		$name='悦榜';
	}
	
	if(!isN($keyword)){
		$bd=$bd. " and t_name like '%".$keyword."%' ";
	}
	
	if ($show ==1){
		$str="<input type='text' name='bd_keyword' id='bd_keyword' value='".$keyword."' size='30' ><input type='button' value='查询' onclick=\"plsetBDkewyowrd('plbd','vod','".$dbtype."',document.getElementById('bd_keyword').value);\"></br><select style='width:155px' id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"0\">请选择".$name."</option>" . makeSelectWhere("{pre}".$flag."_topic","t_id","t_name","t_name,t_sort","","&nbsp;|&nbsp;&nbsp;",""," where t_id > 4 ".$bd."  ") ."</select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if($show ==2){
//		var_dump($id);
		$ids=explode(",", $id);
		$tab="{pre}vod_topic_items";
		$colarr=array("topic_id","vod_id");
		foreach ($ids as $t_id){
			if(isNum($t_id) && isNum($ajaxcontent)){
		       $valarr=array($ajaxcontent,$t_id);
		     $db->Add($tab,$colarr,$valarr);
			}
		}
	    replaceTopRecommend($ajaxcontent);
//		$db->query ("UPDATE {pre}".$flag . " set ". $ttopic ."=".$ajaxcontent. " WHERE " . $tid ." IN(".$id.")" );
//		echo "reload";
        echo "bd".$id."$<script language=\"javascript\">alert('添加成功');</script>";
	}
}



function mplrenqi()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show ==1){
		$str="<input id=\"num1\" name=\"num1\" type=\"text\"  size=\"5\">到<input id=\"num2\" name=\"num2\" type=\"text\"  size=\"5\">之间<input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if ($show ==2){
		$num1 = be("all","num1");
		$num2 = be("all","num2");
		if (!isNum($num1)){ $num1=1;}
		if (!isNum($num2)){ $num1=1000;}
		
		$rs = $db->query("select ".$tid." from {pre}".$flag." where ".$tid." in (" .$id . ")");
		while($row = $db->fetch_array($rs))
		{
			$num3 = rndNum($num1,$num2);
			$db->Update ("{pre}".$flag ,array($thit),array($num3) ,$tid."=".$row[$tid]);
		}
		unset($rs);
		echo "reload";
	}
}

function mplyinccang()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thide,$thit,$ttopic;
	if ($show ==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"\">请选择...</option><option value=\"0\">显示</option><option value=\"1\">隐藏</option></select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if($show ==2){
		$db->query ("UPDATE {pre}".$flag . " set ". $thide ."=".$ajaxcontent. " WHERE " . $tid ." IN(".$id.")" );
		echo "reload";
	}
}

function mtopic()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic;
	if ($show ==1){
		$str="<select id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"0\">请选择专题</option>" . makeSelectWhere("{pre}".$flag."_topic","t_id","t_name","t_sort","","&nbsp;|&nbsp;&nbsp;","", " where t_id <= 4 ") ."</select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if ($show ==2){
		$db->Update ("{pre}".$flag ,array($ttopic),array($ajaxcontent),$tid."=".$id);
		echo "zt".$id."$<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxdivdel('".$id."','".$action."','".$flag."')\"/>";
	}
	else if ($show ==3){
		$db->Update ("{pre}".$flag ,array($ttopic),array(0),$tid."=".$id);
		echo "zt".$id."$<img src=\"../images/icons/icon_02.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('zt','".$id."','".$flag."')\"/>"; 
	}
}


function subscribe_msg_list(){
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic,$dbtype;
	$tab="{pre}mac_vod_pasre_item";
		if(isNum($id) ){
		  
		  $info=$db->getRow('select prod_id from mac_vod_pasre_item where prod_id='.$id);
		  if($info ===false ){
		  	$d_addtime= date('Y-m-d H:i:s',time());
		  	$db->query("INSERT INTO mac_vod_pasre_item (prod_id,create_date) VALUES('".$id."','".$d_addtime."')");
		  }
		 
		}
		echo "bd".$id."$<script language=\"javascript\">alert('添加成功');</script>";
}
function bdtopic()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic,$dbtype;
	$bd=" ";
	$name='';
	$keyword = be("all","keyword");
	if($dbtype==1){
		$bd=" and t_bdtype=1 ";
		$name='悦单';
		
	}
    if($dbtype==2){
		$bd=" and t_bdtype=2 ";
		$name='悦榜';
	}
	
	if(!isN($keyword)){
		$bd=$bd. " and t_name like '%".$keyword."%' ";
	}
	
	if ($show ==1){
		$str="<input type='text' name='bd_keyword' id='bd_keyword' value='".$keyword."' size='30' ><input type='button' value='查询' onclick=\"setdayBDkewyowrd('bd','".$id."','vod','".$dbtype."',document.getElementById('bd_keyword').value);\"></br><select style='width:155px' id=\"ajaxcontent\" name=\"ajaxcontent\"><option value=\"0\">请选择".$name."</option>" . makeSelectWhere("{pre}".$flag."_topic","t_id","t_name","t_name,t_sort","","&nbsp;|&nbsp;&nbsp;",""," where t_id > 4 ".$bd."  ") ."</select><input type=\"button\" value=\"确定\" onclick=\"ajaxsubmit('".$id."','".$action."','".$flag."');\" class=inputbut> <input type=\"button\" value=\"取消\" onclick=\"closew();\" class=inputbut>";
		echo $str;
	}
	else if ($show ==2){
//		$db->query ("{pre}".$flag ,array($ttopic),array($ajaxcontent),$tid."=".$id);
		$tab="{pre}vod_topic_items";
		$colarr=array("topic_id","vod_id");
		if(isNum($id) && isNum($ajaxcontent)){
		  $valarr=array($ajaxcontent,$id);
		  $db->Add($tab,$colarr,$valarr);
		}
		replaceTopRecommend($ajaxcontent);
		echo "bd".$id."$<script language=\"javascript\">alert('添加成功');</script>";
	}
	
}

function lunbo(){
	global $db,$action,$flag,$show,$id;
        $tab="{pre}vod_popular";
		$colarr=array("vod_id");
		if(isNum($id)){
		  $valarr=array($id);
		  $db->Add($tab,$colarr,$valarr);
		}   
		
		echo "popular".$id."$<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" />";
}

function plluobo()
{
	global $db,$action,$flag,$show,$id,$name,$ajaxcontent,$tid,$ttype,$thit,$ttopic,$dbtype;

//		var_dump($id);
		$ids=explode(",", $id);
	$tab="{pre}vod_popular";
		$colarr=array("vod_id");
		foreach ($ids as $t_id){
			if(isNum($t_id)){
		       $valarr=array($t_id);
		     $db->Add($tab,$colarr,$valarr);
			}
		}
//		$db->query ("UPDATE {pre}".$flag . " set ". $ttopic ."=".$ajaxcontent. " WHERE " . $tid ." IN(".$id.")" );
//		echo "reload";
        echo "popular".$id."$<script language=\"javascript\">alert('添加成功');</script>";
	
}

?>