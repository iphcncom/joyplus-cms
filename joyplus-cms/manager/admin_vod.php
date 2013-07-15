<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");

require_once ("./score/DouBanParseScore.php");
require_once ("./parse/NotificationsManager.php");





chkLogin();

$action = be("all","action");
$_SESSION["upfolder"] = "../upload/vod";

switch($action)
{
	case "add":
	case "edit" : headAdmin ("视频管理" ); info();break;
	case "save" : save();break;
	case "notifyMsg" : notifyMsg();break;
	case "douban" : douban();break;
	case "doubanPic" : doubanPic();break;
	case "doubans" : doubans();break;
	case "doubanThumb" :doubanThumb();break;
	case "doubanThumbs" :doubanThumbs();break;
	case "doubanComment" : doubanComment();break;
	case "doubansComment" : doubansComment();break;
	case "del" : del();break;
	case "chkpic" : chkpic();break;
	case "pse" : headAdmin ("视频管理"); pse();break;
	case "pserep" : headAdmin ("视频管理"); pserep();break;
	case "psesave" : psesave();break;
	default : headAdmin ("视频管理"); main();break;
}
dispseObj();

function chkpic(){
	global $db;
	$flag = "#err". date('Y-m-d',time());
	$num = $db->getOne("SELECT COUNT(*) FROM {pre}vod WHERE d_pic LIKE 'http://%' and instr(d_pic,'".$flag."')=0 ");
	echo $num;exit;
}

function save()
{
	//    $username= getCookie("adminname");
	//	writetofile("opearte.log", "operator:".$username.'{;Array===Array;}'.json_encode($_POST,JSON_UNESCAPED_UNICODE));
	//
	global $db;
	$backurl = be("post", "backurl");
	$flag = be("post", "flag");

	$uptime = be("post", "uptime");
	$oldtime = be("post", "oldtime");

	$d_id = be("post", "d_id"); $d_name = be("post", "d_name");
	$d_subname = be("post", "d_subname"); $d_enname = be("post", "d_enname");
	$d_type = be("post", "d_type"); $d_state = be("post", "d_state");
	$d_color = be("post", "d_color"); $d_pic = be("post", "pic");
	$d_starring = be("post", "d_starring");$d_directed = be("post", "d_directed");
	$d_area = be("post", "d_area"); $d_language = be("post", "d_language");
	$d_level = be("post", "d_level"); $d_stint = be("post", "d_stint");
	$d_hits = be("post", "d_hits"); $d_dayhits = be("post", "d_dayhits");
	$d_weekhits = be("post", "d_weekhits"); $d_monthhits = be("post", "d_monthhits");
	$d_topic = be("post", "d_topic"); $d_content = be("post", "d_content");
	$d_remarks = be("post", "d_remarks"); $d_hide = be("post", "d_hide");
	$d_good = be("post", "d_good"); $d_bad = be("post", "d_bad");
	$d_usergroup = be("post", "d_usergroup"); $d_year = be("post", "d_year");
	$d_addtime = be("post", "d_addtime"); $d_time = be("post", "d_time");
	$d_score = be("post", "d_score"); $d_playurl = be("post", "d_playurl");
	$d_downurl = be("post", "d_downurl"); $d_scorecount = be("post", "d_scorecount");
	$d_letter = be("post", "d_letter");
	$d_type_name= be("arr", "d_type_name");
	$d_pic_ipad= be("post", "d_pic_ipad");
	$can_play_device= be("post", "can_play_device");
	$d_capital_name= be("post", "d_capital_name");
	$can_search_device=be("arr","can_search_device");

	$d_status= be("post", "d_status");
	$duraning= be("post", "duraning");
	$d_video_desc_url= be("post", "d_video_desc_url");

	$d_play_check= be("post", "d_play_check");


	$urlid = be("arr", "urlid"); $url = be("arr", "url"); $urlfrom = be("arr", "urlfrom"); $urlserver = be("arr", "urlserver");
	if(strpos($urlfrom, "no")  !==false){
		echo 'no';
		return;
	}
	$urlidsarr = explode(",",$urlid); $urlarr = explode(",",$url); $urlfromarr = explode(",",$urlfrom); $urlserverarr = explode(",",$urlserver);
	$urlarr = $_POST["url"];
	$d_douban_id= $_POST["d_douban_id"];

	$downurlid = be("arr", "downurlid"); $downurl = be("arr", "downurl"); $downurlfrom = be("arr", "downurlfrom");
	if(strpos($downurlfrom, "no") !==false){
		echo 'no';
		return;
	}
	$downurlidsarr = explode(",",$downurlid); $downurlarr = explode(",",$downurl); $downurlfromarr = explode(",",$downurlfrom);
	$downurlarr = $_POST["downurl"];

	$rc = false;
	for ($i=0;$i<count($urlidsarr);$i++){
		if (count($urlarr) >= $i && count($urlfromarr) >= $i && count($urlserverarr) >= $i){
			if ($rc){ $d_playurl .= "$$$"; $d_playfrom .= "$$$"; $d_playserver .= "$$$"; }
			$d_playfrom .= trim($urlfromarr[$i]);
			$d_playserver .=  trim($urlserverarr[$i]);
			$d_playurl .= replaceStr(replaceStr(trim($urlarr[$i]), Chr(10), ""), Chr(13), "{Array}");
			// writetofile("ddd.txt", replaceStr(replaceStr(trim($urlarr[$i]), Chr(10), ""), Chr(13), "{Array}"));
			$rc =true;
		}
	}

	$rc = false;
	for($i=0;$i<count($downurlidsarr);$i++){
		if (count($downurlarr) >= $i && count($downurlfromarr)>=$i ){
			if ($rc) { $d_downurl .= "$$$"; }
			if (!isN($downurlfromarr[$i]) && !isN($downurlarr[$i])){
				$d_downurl .= trim($downurlfromarr[$i]) . "$$" . replaceStr(replaceStr(trim($downurlarr[$i]), Chr(10), ""), Chr(13), "{Array}");
				$rc = true;
			}
		}
	}

	$d_addtime = date('Y-m-d H:i:s',time());
	$d_time = date('Y-m-d H:i:s',time());
	if($uptime==""){
		$d_time = $oldtime;
	}

	if (isN($d_name)) { echo "名称不能为空";exit;}
	if (isN($d_type)) { echo "分类不能为空";exit;}
	if(!isNum($d_hide)) { $d_hide = 0;}
	if(!isNum($d_level)) { $d_level = 0;}
	if(!isNum($d_hits)) { $d_hits = 0;}
	if(!isNum($d_dayhits)) { $d_dayhits = 0;}
	if(!isNum($d_weekhits)) { $d_weekhits = 0;}
	if(!isNum($d_monthhits)) { $d_monthhits = 0;}
	if(!isNum($d_topic)) { $d_topic = 0;}
	if(!isNum($d_stint)) { $d_stint = 0;}
	if(!isNum($d_state)) { $d_state = 0;}
	if(!isNum($d_score)) { $d_score = 0;}
	if(!isNum($d_scorecount)) { $d_scorecount = 0;}
	if(!isNum($d_good)) { $d_good = 0;}
	if(!isNum($d_bad)) { $d_bad = 0;}
	if(!isNum($d_usergroup)) { $d_usergroup = 0;}
	if (isN($d_enname)) { $d_enname = Hanzi2PinYin($d_name); }
	if (isN($d_capital_name)) { $d_capital_name = Hanzi2PinYin_Captial($d_name); }
	//var_dump($d_capital_name);
	if (isN($d_letter)) { $d_letter = strtoupper(substring($d_enname,1)); }
	//$d_play_check
	if (strpos($d_enname, "*")>0 || strpos($d_enname, ":")>0 || strpos($d_enname, "?")>0 || strpos($d_enname, "\"")>0 || strpos($d_enname, "<")>0 || strpos($d_enname, ">")>0 || strpos($d_enname, "|")>0 || strpos($d_enname, "\\")>0){
		echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号";exit;
	}
	if(strpos($d_downurl, "http") ===false){
		//iPad,iphone,apad,aphone,web
		$can_search_device="iPad,iphone,apad,aphone,web";
	}
	if ($flag == "edit") {
		$db->Update ("{pre}vod", array("d_capital_name","duraning","d_status","can_search_device","d_douban_id","can_play_device","d_video_desc_url","d_play_check","d_pic_ipad","d_type_name","d_name", "d_subname", "d_enname", "d_type","d_letter", "d_state", "d_color", "d_pic", "d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint", "d_hits","d_dayhits","d_weekhits","d_monthhits", "d_topic", "d_content", "d_remarks","d_good","d_bad", "d_usergroup", "d_score", "d_scorecount", "d_hide", "d_time", "webUrls", "d_downurl", "d_playfrom", "d_playserver"), array($d_capital_name,$duraning,$d_status,$can_search_device,$d_douban_id,$can_play_device,$d_video_desc_url,$d_play_check,$d_pic_ipad,$d_type_name,$d_name, $d_subname, $d_enname, $d_type, $d_letter, $d_state, $d_color, $d_pic, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint, $d_hits, $d_dayhits, $d_weekhits, $d_monthhits ,$d_topic, $d_content, $d_remarks, $d_good, $d_bad, $d_usergroup, $d_score, $d_scorecount, $d_hide, $d_time, $d_playurl, $d_downurl, $d_playfrom, $d_playserver), "d_id=" . $d_id);
	}
	else{
		$backurl = "admin_vod.php?action=add";
		$db->Add ("{pre}vod", array("d_capital_name","duraning","d_status","can_search_device","d_douban_id","can_play_device","d_video_desc_url","d_pic_ipad","d_type_name","d_name", "d_subname", "d_enname", "d_type", "d_letter","d_state", "d_color", "d_pic", "d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint", "d_hits","d_dayhits","d_weekhits","d_monthhits", "d_topic", "d_content", "d_remarks", "d_good","d_bad", "d_usergroup", "d_score", "d_scorecount", "d_addtime", "d_time", "webUrls", "d_downurl", "d_playfrom", "d_playserver"), array($d_capital_name,$duraning,$d_status,$can_search_device,$d_douban_id,$can_play_device,$d_video_desc_url,$d_pic_ipad,$d_type_name,$d_name, $d_subname, $d_enname, $d_type,$d_letter,  $d_state, $d_color, $d_pic, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint, $d_hits, $d_dayhits, $d_weekhits, $d_monthhits , $d_topic, $d_content, $d_remarks, $d_good, $d_bad, $d_usergroup, $d_score, $d_scorecount, $d_addtime, $d_time, $d_playurl, $d_downurl, $d_playfrom, $d_playserver));
	}

	echo "保存完毕";
}

function del()
{
	global $db;
	$d_id = be("get","d_id");
	if(isN($d_id)){
		$d_id = be("arr","d_id");
	}
	$arr = explode(",",$d_id);
	foreach($arr as $v){
		$row = $db->getRow("SELECT d_pic FROM {pre}vod WHERE d_id=" .$v);
		if($row){
			$d_pic = $row["d_pic"];
			$db->Delete ("{pre}vod","d_id=". $v);
			if (strpos(",".$d_pic,"http://") <=0){
				if ( file_exists("../".$d_pic) && $d_pic!="" ){ unlink( "../".$d_pic) ; }
			}
			if ( file_exists("../upload/playdata/".$v."/".$v.".js") ){ unlink( "../upload/playdata/".$v."/".$v.".js" ) ; }
			if ( is_dir("../upload/playdata/".$v."/") ){ rmdir( "../upload/playdata/".$v."/" ) ; }
		}
		unset($row);
	}
	redirect ( getReferer() );
}

function doubans(){
	global $db,$action,$template,$cache;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }
	if ($action=="doubans"){
		$keyword = be("all", "keyword"); $stype = be("all", "stype");
		$area = be("all", "area");   $topic = be("all", "topic");
		$level = be("all", "level");     $from = be("all", "from");
		$sserver = be("all", "sserver");  $sstate = be("all", "sstate");
		$repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
		$order = be("all", "order");     $pagenum = be("all", "page");
		$spic = be("all", "spic");    $hide = be("all", "hide");
		$douban_score = be("all", "douban_score");
		if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
		if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
		if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
		if(!isNum($area)) { $area = 0;} else { $area = intval($area);}
		if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
		if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
		if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
		if(!isNum($douban_score)) { $douban_score=0;} else { $douban_score = intval($douban_score);}
		if(!isNum($repeatlen)) { $repeatlen = 0;}
		if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1; }

		$where = " (d_type=1 or d_type=2)  ";

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

		if($douban_score==1){
			$where .= " AND d_score >0 ";
		}

		if($douban_score==2){
			$where .= " AND d_score <=0 ";
		}


		$select_weburl=be("all", "select_weburl");
		$select_videourl=be("all", "select_videourl");

		if($select_weburl==1){
			$where .= " AND webUrls is not null and webUrls !=''";
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

		if (isN($order)) { $order = "d_time";}
		if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
		if(!isN($from)) { $where .= " and d_playfrom like  '" . $from . "%' ";}
		if($spic==1){
			$where .= " AND d_pic = '' ";
		}
		else if($spic==2){
			$where .= " AND d_pic like 'http://%' ";
		}

		$sql = "SELECT * FROM {pre}vod ".$repeatsql." where ".$where;
		//        echo $sql;
		$rs = $db->query($sql);

		$rscount = $db -> num_rows($rs);

		if($rscount==0){
			errmsg ("没有可用的数据");
		}else {
			while ($row = $db ->fetch_array($rs))	{
				$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
				$d_id=$row["d_id"];
				$scoreDouban = new DouBanParseScore();
				$score= $scoreDouban->getScore($name, $year, $area);
					
				if($score>0){
					$db->Update ("{pre}vod", array("d_score"), array($score), "d_id=" . $d_id);
					//showMsg();
					echo "<tr><td colspan=\"2\"></TD>获得评分成功：".$name." 的豆瓣评分为'.$score.'</TR></br>";
				}else {
					echo "<tr><td colspan=\"2\"></TD>找不到资源 ：".$name."</TR></br>";
				}
			}
		}
		unset($rs);
	}

	echo '<script language="javascript">alert("采集完成");</br></script><a href="'.$backurl.'"><font color="red">返回</font></a>';
}


function doubanThumbs(){

	global $db,$action,$template,$cache;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }
	if ($action=="doubanThumbs"){
		$keyword = be("all", "keyword"); $stype = be("all", "stype");
		$area = be("all", "area");   $topic = be("all", "topic");
		$level = be("all", "level");     $from = be("all", "from");
		$sserver = be("all", "sserver");  $sstate = be("all", "sstate");
		$repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
		$order = be("all", "order");     $pagenum = be("all", "page");
		$spic = be("all", "spic");    $hide = be("all", "hide");
		$douban_score = be("all", "douban_score");
		if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
		if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
		if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
		if(!isNum($area)) { $area = 0;} else { $area = intval($area);}
		if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
		if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
		if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
		if(!isNum($douban_score)) { $douban_score=0;} else { $douban_score = intval($douban_score);}
		if(!isNum($repeatlen)) { $repeatlen = 0;}
		if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1; }

		$where = "  (d_type =1 or d_type=2) ";

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

		if($douban_score==1){
			$where .= " AND d_score >0 ";
		}

		if($douban_score==2){
			$where .= " AND d_score <=0 ";
		}
		 
	 $douban_comment = be("all", "douban_comment");
	 if(!isNum($douban_comment)) { $douban_comment=0;} else { $douban_comment = intval($douban_comment  );}

	 if($douban_comment==1){
	 	$where.=" and d_id in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL) ";
	 }
	  
	 if($douban_comment==2){
	 	$where.=" and d_id not in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL) ";
	 }

	 $select_weburl=be("all", "select_weburl");
	 $select_videourl=be("all", "select_videourl");

	 if($select_weburl==1){
	 	$where .= " AND webUrls is not null and webUrls !=''";
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

	 if ($repeat == "ok"){
	 	$repeatSearch = " d_name ";
	 	if($repeatlen>0){
	 		$repeatSearch = " substring(d_name,1,".$repeatlen.") ";
	 	}
	 	$repeatsql = " , (SELECT ". $repeatSearch ." as d_name1 FROM {pre}vod GROUP BY d_name1 HAVING COUNT(*)>1) as `t2` ";
	 	$where .= " AND `{pre}vod`.`d_name`=`t2`.`d_name1` ";
	 	if(isN($order)){ $order= "d_name,d_addtime"; }
	 }

	 if (isN($order)) { $order = "d_time";}
	 if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
	 if(!isN($from)) { $where .= " and d_playfrom like  '" . $from . "%' ";}
	 if($spic==1){
	 	$where .= " AND (d_pic IS NULL OR d_pic = '' ) ";
	 }
	 else if($spic==2){
	 	$where .= " AND d_pic like 'http://%' ";
	 }

	  

	 $sql = "SELECT * FROM {pre}vod ".$repeatsql." where ".$where;
	 //        echo $sql;
		$rs = $db->query($sql);

		$rscount = $db -> num_rows($rs);

		if($rscount==0){
			errmsg ("没有可用的数据","");
		}else {
			while ($row = $db ->fetch_array($rs))	{
				$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
				$d_id=$row["d_id"];$type=$row["d_type"];
				$scoreDouban = new DouBanParseScore();
				$doubanid=$row['d_douban_id'];
				$flag=true;
				if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
					$flag=false;
					$pic= $scoreDouban->getThumb($doubanid);
				}else {
					$pic= $scoreDouban->getDoubanThumb($name, $year, $area);
				}
				if($pic !==false) {
					$doubanid=$pic['id'];
					if(!isN($doubanid) && $flag){
						$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
					}
					$pic=$pic['pic'];
				}
				if($pic !==false && !isN($pic)){
					writetofile("updateVodPic.txt", 'd_pic{=}'.$pic );
					$db->Update ("{pre}vod", array("d_pic"), array($pic), "d_id=" . $d_id);
					echo '<script language="javascript">alert("采集成功");location.href ="'.$backurl.'";</script>';
				}else {
					echo '<script language="javascript">alert("在豆瓣上找不到资源 ");location.href ="'.$backurl.'";</script>';
				}
			}
			 
		}
		unset($rs);

	}

	echo '<script language="javascript">alert("采集完成");</br></script><a href="'.$backurl.'"><font color="red">返回</font></a>';
}

function doubansComment(){
	//	echo "ss";
	global $db,$action,$template,$cache;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }
	if ($action=="doubansComment"){
		$keyword = be("all", "keyword"); $stype = be("all", "stype");
		$area = be("all", "area");   $topic = be("all", "topic");
		$level = be("all", "level");     $from = be("all", "from");
		$sserver = be("all", "sserver");  $sstate = be("all", "sstate");
		$repeat = be("all", "repeat");   $repeatlen = be("all", "repeatlen");
		$order = be("all", "order");     $pagenum = be("all", "page");
		$spic = be("all", "spic");    $hide = be("all", "hide");
		$douban_score = be("all", "douban_score");
		if(!isNum($level)) { $level = 0;} else { $level = intval($level);}
		if(!isNum($sstate)) { $sstate = 0;} else { $sstate = intval($sstate);}
		if(!isNum($stype)) { $stype = 0;} else { $stype = intval($stype);}
		if(!isNum($area)) { $area = 0;} else { $area = intval($area);}
		if(!isNum($topic)) { $topic = 0;} else { $topic = intval($topic);}
		if(!isNum($spic)) { $spic = 0;} else { $spic = intval($spic);}
		if(!isNum($hide)) { $hide=-1;} else { $hide = intval($hide);}
		if(!isNum($douban_score)) { $douban_score=0;} else { $douban_score = intval($douban_score);}
		if(!isNum($repeatlen)) { $repeatlen = 0;}
		if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
		if ($pagenum < 1) { $pagenum = 1; }

		$where = " (d_type =1 or d_type=2) ";

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

		if($douban_score==1){
			$where .= " AND d_score >0 ";
		}

		if($douban_score==2){
			$where .= " AND d_score <=0 ";
		}
		 
	 $douban_comment = be("all", "douban_comment");
	 if(!isNum($douban_comment)) { $douban_comment=0;} else { $douban_comment = intval($douban_comment  );}

	 if($douban_comment==1){
	 	$where.=" and d_id in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL and comment_type=1 ) ";
	 }
	  
	 if($douban_comment==2){
	 	$where.=" and d_id not in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND thread_id IS NULL and comment_type=1) ";
	 }

	 $select_weburl=be("all", "select_weburl");
	 $select_videourl=be("all", "select_videourl");

	 if($select_weburl==1){
	 	$where .= " AND webUrls is not null and webUrls !=''";
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

	 if ($repeat == "ok"){
	 	$repeatSearch = " d_name ";
	 	if($repeatlen>0){
	 		$repeatSearch = " substring(d_name,1,".$repeatlen.") ";
	 	}
	 	$repeatsql = " , (SELECT ". $repeatSearch ." as d_name1 FROM {pre}vod GROUP BY d_name1 HAVING COUNT(*)>1) as `t2` ";
	 	$where .= " AND `{pre}vod`.`d_name`=`t2`.`d_name1` ";
	 	if(isN($order)){ $order= "d_name,d_addtime"; }
	 }

	 if (isN($order)) { $order = "d_time";}
	 if(!isN($sserver)) { $where .= " AND d_playserver like '%" . $sserver . "%' ";}
	 if(!isN($from)) { $where .= " and d_playfrom like  '" . $from . "%' ";}
	 if($spic==1){
	 	$where .= " AND d_pic = '' ";
	 }
	 else if($spic==2){
	 	$where .= " AND d_pic like 'http://%' ";
	 }

	 $sql = "SELECT * FROM {pre}vod ".$repeatsql." where ".$where;
	 //        echo $sql;
		$rs = $db->query($sql);

		$rscount = $db -> num_rows($rs);

		if($rscount==0){
			errmsg ("没有可用的数据");
		}else {
			while ($row = $db ->fetch_array($rs))	{
				$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
				$d_id=$row["d_id"];$type=$row["d_type"];
				$scoreDouban = new DouBanParseScore();
				$doubanid=$row['d_douban_id'];
				$flag=true;
				if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
					$flag=false;
					$comments= $scoreDouban->getReviewsById($doubanid);
				}else {
					$comments= $scoreDouban->getDoubanReviews($name, $year, $area);
				}
				if($comments !==false) {
					$doubanid=$comments['id'];
					if(!isN($doubanid) && $flag){
						$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
					}
				}
				//var_dump($comments);
			 $review_flag=false;
			 if(is_array($comments)&& !isN( $comments['comments'])){
			 	$commentsArray = $comments['comments'];
			 	$titlesArray =$comments['title'];
			 	$reviewidsArray= $comments['reviewid'];
			 	$total= count($commentsArray);
			 	if($total>0){
			 		$db->Delete("tbl_comments", "content_id=".$d_id ." and author_id is null and comment_type=1");
			 		//$db->Delete("mac_comment", "c_vid=".$d_id );
			 	}
			 		
			 	for ($i=0;$i<$total;$i++) {
			 		$com=$commentsArray[$i];

			 		$title=$titlesArray[$i];
			 		$reviewid=$reviewidsArray[$i];
			 		if(!isN($com)){
			 			$com=filterScript($com,8191);
			 			$db->Add("tbl_comments", array("status","content_type","content_name","content_id","create_date","comments","comment_type","douban_comment_id","comment_title"),
			 			array('1',$type,$name,$d_id,date("Y-m-d H:i:s"),$com,'1',$reviewid,$title));

			 			$db->Add("mac_comment", array("c_audit","c_type","c_vid","c_time","c_content","c_name"),
			 			array('1',$type,$d_id,date("Y-m-d H:i:s"),$com,$author));
			 			$review_flag=true;
			 		}
			 	}
			 	//showMsg();
			 	if($review_flag){
				 	echo "<tr><td colspan=\"2\"></TD>采集完'".$name. "'的评论</TR></br>";
			 	}else {
			 		echo "<tr><td colspan=\"2\"></TD>找不到资源 ：'".$name."'</TR></br>";
			 	}
			 }else {
			 	echo "<tr><td colspan=\"2\"></TD>找不到资源 ：'".$name."'</TR></br>";
			 }
			}
			//
		}
		unset($rs);
	}

	echo '<script language="javascript">alert("采集完成");</br></script><a href="'.$backurl.'"><font color="red">返回</font></a>';
}

function main()
{
	global $db,$template,$cache;
	$loginname=getCookie("adminname");
	$keyword = be("all", "keyword"); $stype = be("all", "stype");
	$area = be("all", "area");   $topic = be("all", "topic");
	$level = be("all", "level");     $from = be("all", "from");
	  $sstate = be("all", "sstate");
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
//	var_dump($sql);
	$rs = $db->query($sql);
	?>


<script
	type="text/javascript" src="./resource/thickbox-compressed.js"></script>
<script
	type="text/javascript" src="./resource/thickbox.js"></script>
<link
	href="./resource/thickbox.css" rel="stylesheet" type="text/css" />
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
	
});
function filter(){
	var stype=$("#stype").val();
	var state=$("#state").val();
	var order=$("#order").val();
	var level=$("#level").val();
	var topic=$("#topic").val();
	var sserver=$("#sserver").val();
	var from=$("#from").val();
	var spic=$("#spic").val();
	var hide=$("#hide").val();
	var ipadpic=$("#ipadpic").val();
	var sort=$("#sort").val();
	
	
	var select_weburl=$("#select_weburl").val();
	var select_videourl=$("#select_videourl").val();
	var douban_score =$("#douban_score").val();
	var douban_comment =$("#douban_comment").val();
	var select_videourl_play=$("#select_videourl_play").val();
	var keyword=$("#keyword").val();	
	var d_status=$("#d_status").val();	
	var d_douban_id=$("#d_douban_id").val();
	var keyword_col=$("#keyword_col").val();
	var can_search_device=$("#can_search_device").val();
	var url = "admin_vod.php?can_search_device="+can_search_device+"&d_douban_id="+d_douban_id+"&keyword_col="+keyword_col+"&d_status="+d_status+"&sort="+sort+"&select_videourl_play="+select_videourl_play+"&douban_comment="+douban_comment+"&keyword="+encodeURI(keyword)+"&stype="+stype+"&topic="+topic+"&select_weburl="+select_weburl+"&select_videourl="+select_videourl+"&level="+level+"&order="+order+"&sserver="+sserver+"&sstate="+state+"&from="+from+"&spic="+spic+"&hide="+hide+"&douban_score="+douban_score+"&ipadpic="+ipadpic; //ipadpic
	window.location.href=url;
}

function doubans(){
	var stype=$("#stype").val();
	var state=$("#state").val();
	var order=$("#order").val();
	var level=$("#level").val();
	var topic=$("#topic").val();
	var sserver=$("#sserver").val();
	var from=$("#from").val();
	var spic=$("#spic").val();
	var hide=$("#hide").val();
	var sort=$("#sort").val();
	var ipadpic=$("#ipadpic").val();
	var douban_score =$("#douban_score").val();
	var select_weburl=$("#select_weburl").val();
	var select_videourl=$("#select_videourl").val();
	var douban_comment =$("#douban_comment").val();
	var select_videourl_play=$("#select_videourl_play").val();
	var keyword=$("#keyword").val();	
	var d_status=$("#d_status").val();
	var keyword_col=$("#keyword_col").val();	
	var d_douban_id=$("#d_douban_id").val();
	var can_search_device=$("#can_search_device").val();
	var url = "admin_vod.php?can_search_device="+can_search_device+"&d_douban_id="+d_douban_id+"&keyword_col="+keyword_col+"&d_status="+d_status+"&sort="+sort+"&select_videourl_play="+select_videourl_play+"&douban_comment="+douban_comment+"&action=doubans&keyword="+encodeURI(keyword)+"&select_weburl="+select_weburl+"&select_videourl="+select_videourl+"&stype="+stype+"&topic="+topic+"&level="+level+"&order="+order+"&sserver="+sserver+"&sstate="+state+"&from="+from+"&spic="+spic+"&hide="+hide+"&douban_score="+douban_score+"&ipadpic="+ipadpic;
	window.location.href=url;
}

function doubansComment(){
	var stype=$("#stype").val();
	var state=$("#state").val();
	var order=$("#order").val();
	var level=$("#level").val();
	var topic=$("#topic").val();
	var sserver=$("#sserver").val();
	var from=$("#from").val();
	var spic=$("#spic").val();
	var hide=$("#hide").val();
	var sort=$("#sort").val();
	var ipadpic=$("#ipadpic").val();
	var douban_score =$("#douban_score").val();
	var select_weburl=$("#select_weburl").val();
	var select_videourl=$("#select_videourl").val();
	var select_videourl_play=$("#select_videourl_play").val();
	var keyword=$("#keyword").val();
	var douban_comment =$("#douban_comment").val();	
	var d_status=$("#d_status").val();
	var keyword_col=$("#keyword_col").val();	
	var d_douban_id=$("#d_douban_id").val();
	var can_search_device=$("#can_search_device").val();
	var url = "admin_vod.php?can_search_device="+can_search_device+"&d_douban_id="+d_douban_id+"&keyword_col="+keyword_col+"&d_status="+d_status+"&sort="+sort+"&select_videourl_play="+select_videourl_play+"&douban_comment="+douban_comment+"&action=doubansComment&keyword="+encodeURI(keyword)+"&select_weburl="+select_weburl+"&select_videourl="+select_videourl+"&stype="+stype+"&topic="+topic+"&level="+level+"&order="+order+"&sserver="+sserver+"&sstate="+state+"&from="+from+"&spic="+spic+"&hide="+hide+"&douban_score="+douban_score+"&ipadpic="+ipadpic;
	window.location.href=url;
}

function doubanThumbs(){
	var stype=$("#stype").val();
	var state=$("#state").val();
	var order=$("#order").val();
	var level=$("#level").val();
	var topic=$("#topic").val();
	var sserver=$("#sserver").val();
	var from=$("#from").val();
	var spic=$("#spic").val();
	var hide=$("#hide").val();
	var sort=$("#sort").val();
	var ipadpic=$("#ipadpic").val();
	var douban_score =$("#douban_score").val();
	var select_weburl=$("#select_weburl").val();
	var select_videourl=$("#select_videourl").val();
	var select_videourl_play=$("#select_videourl_play").val();
	var keyword=$("#keyword").val();
	var douban_comment =$("#douban_comment").val();	
	var d_status=$("#d_status").val();
	var keyword_col=$("#keyword_col").val();	
	var d_douban_id=$("#d_douban_id").val();
	var can_search_device=$("#can_search_device").val();
	var url = "admin_vod.php?can_search_device="+can_search_device+"&d_douban_id="+d_douban_id+"&keyword_col="+keyword_col+"&d_status="+d_status+"&sort="+sort+"&select_videourl_play="+select_videourl_play+"&douban_comment="+douban_comment+"&action=doubanThumbs&keyword="+encodeURI(keyword)+"&select_weburl="+select_weburl+"&select_videourl="+select_videourl+"&stype="+stype+"&topic="+topic+"&level="+level+"&order="+order+"&sserver="+sserver+"&sstate="+state+"&from="+from+"&spic="+spic+"&hide="+hide+"&douban_score="+douban_score+"&ipadpic="+ipadpic;
	window.location.href=url;
}



function showpic(){
	$.get("admin_vod.php?action=chkpic&rnd=" + Math.random(),function(obj){
		if(Number(obj)>0){
			$.messager.show({
			title:'系统提示',
			msg:'发现数据中调用远程图片<br>下载到本地可以提高网页载入速度<br>',
			timeout:5000,
			showType:'slide'
			});
		}
	});
}
function gosyncpic(){
	if(confirm('确定要同步下载远程图片吗?数据不可恢复，请做好备份')){
		location.href = 'admin_pic.php?action=syncpic';
	}
}

function prepareWeiboText(type,id,name){
	   document.getElementById( "weiboText").value= name; 
	   document.getElementById( "notify_msg_prod_id").value= id; 
	   document.getElementById( "notify_msg_prod_type").value= type; 
	   $('#SendWeiboMsg').empty();
}


function sendWeiboText(){

	var device_types= document.getElementById( "device_type");
	
	var weibotxt= document.getElementById( "weiboText").value;
	var notify_msg_prod_id= document.getElementById( "notify_msg_prod_id").value;
	var notify_msg_prod_type= document.getElementById( "notify_msg_prod_type").value;
	var urlT='admin_vod.php?action=notifyMsg&prod_type='+notify_msg_prod_type+'&prod_id='+notify_msg_prod_id+'&content=' +encodeURIComponent(weibotxt) ;
	
	for(var i = 0; i < device_types.options.length; i++){
		  if(device_types.options[i].selected){
			  urlT = urlT +'&device_type='+device_types.options[i].value;				 		 
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

function send(e){
	if(e.keyCode==13){
		  filter();
    }
}
 
</script>
<table class="admin_vod_1 tb">
	<tr>
		<td>
		<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
			<tr>
				<td colspan="2">过滤条件：<br /><select id="stype" name="stype"
					onchange="javascript:{var typeid= this.options[this.selectedIndex].value; if(typeid=='1' ||  typeid=='2' ||  typeid=='131'){document.getElementById('btnsearchs').style.display='block';  document.getElementById('btnsearchsThumbs').style.display='block';document.getElementById('btnsearchsComment').style.display='block';}else {document.getElementById('btnsearchs').style.display='none'; document.getElementById('btnsearchsThumbs').style.display='none';document.getElementById('btnsearchsComment').style.display='none';}}">
					<option value="0">视频栏目</option>
					<option value="-1" <?php if($stype==-1){ echo "selected";} ?>>没有栏目</option>
					<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$stype)?>
				</select><select id="state" name="state">
					<option value="0">视频连载</option>
					<option value="1" <?php if($sstate=="1"){ echo "selected";} ?>>连载中</option>
					<option value="2" <?php if($sstate=="2"){ echo "selected";} ?>>未连载</option>
				</select><select id="order" name="order">
					<option value="d_time">视频排序</option>
					<option value="d_id" <?php if($order=="d_id"){ echo "selected";} ?>>视频编号</option>
					<option value="d_name"
					<?php if($order=="d_name"){ echo "selected";} ?>>视频名称</option>
					<option value="d_play_num"
					<?php if($order=="d_play_num"){ echo "selected";} ?>>播放次数</option>
					<option value="d_year"
					<?php if($order=="d_year"){ echo "selected";} ?>>上映日期</option>
					<option value="d_level"
					<?php if($order=="d_level"){ echo "selected";} ?>>推荐度</option>
				</select><select id="sort" name="sort">
					<option value="desc" <?php if($sort=="desc"){ echo "selected";} ?>>视频排序
					降序序</option>
					<option value="asc" <?php if($sort=="asc"){ echo "selected";} ?>>视频排序
					升序</option>
				</select><select id="level" name="level">
					<option value="0">视频推荐</option>
					<option value="1" <?php if($level==1) { echo "selected";} ?>>推荐1</option>
					<option value="2" <?php if($level==2) { echo "selected";} ?>>推荐2</option>
					<option value="3" <?php if($level==3) { echo "selected";} ?>>推荐3</option>
					<option value="4" <?php if($level==4) { echo "selected";} ?>>推荐4</option>
					<option value="5" <?php if($level==5) { echo "selected";} ?>>推荐5</option>
					<option value="6" <?php if($level==6) { echo "selected";} ?>>推荐6</option>
					<option value="7" <?php if($level==7) { echo "selected";} ?>>推荐7</option>
					<option value="8" <?php if($level==8) { echo "selected";} ?>>推荐8</option>
				</select><select id="from" name="from">
					<option value="">视频播放器</option>
					<?php echo makeSelectPlayer($from)?>
				</select><select id="spic" name="spic">
					<option value="0">视频图片</option>
					<option value="1" <?php if ($spic==1){ echo "selected";} ?>>无图片</option>
					<option value="2" <?php if ($spic==2){ echo "selected";} ?>>远程图片</option>
				</select><select id="ipadpic" name="ipadpic">
					<option value="0">视频IPAD图片</option>
					<option value="1" <?php if ($ipadpic==1){ echo "selected";} ?>>无图片</option>
					<option value="2" <?php if ($ipadpic==2){ echo "selected";} ?>>远程图片</option>
				</select><select id="hide" name="hide">
					<option value="-1">视频显隐</option>
					<option value="0" <?php if ($hide==0){ echo "selected";} ?>>显示</option>
					<option value="1" <?php if ($hide==1){ echo "selected";} ?>>隐藏</option>
					<option value="-100" <?php if ($hide==-100){ echo "selected";} ?>>视频不能播放</option>
				</select><select id="douban_score" name="douban_score">
					<option value="0">豆瓣评分</option>
					<option value="1" <?php if ($douban_score==1){ echo "selected";} ?>>已采集</option>
					<option value="2" <?php if ($douban_score==2){ echo "selected";} ?>>未采集</option>
				</select><select id="douban_comment" name="douban_comment">
					<option value="0">豆瓣评论</option>
					<option value="1"
					<?php if ($douban_comment==1){ echo "selected";} ?>>已采集</option>
					<option value="2"
					<?php if ($douban_comment==2){ echo "selected";} ?>>未采集</option>
				</select><select id="select_weburl" name="select_weburl">
					<option value="0">网页地址</option>
					<option value="1"
					<?php if ($select_weburl==1){ echo "selected";} ?>>存在</option>
					<option value="2"
					<?php if ($select_weburl==2){ echo "selected";} ?>>不存在</option>
				</select><select id="select_videourl" name="select_videourl">
					<option value="0">视频地址</option>
					<option value="1"
					<?php if ($select_videourl==1){ echo "selected";} ?>>存在</option>
					<option value="2"
					<?php if ($select_videourl==2){ echo "selected";} ?>>不存在</option>
				</select><select id="d_status" name="d_status">
					<option value="-1">是否已经审核过</option>
					<option value="0" <?php if ($d_status==0){ echo "selected";} ?>>未审核</option>
					<option value="1" <?php if ($d_status==1){ echo "selected";} ?>>审核并锁定</option>
				</select><select id="d_douban_id" name="d_douban_id">
					<option value="0">能否在豆瓣上找到</option>
					<option value="-1"
					<?php if ($d_douban_id==-1){ echo "selected";} ?>>不能</option>
					<option value="1" <?php if ($d_douban_id==1){ echo "selected";} ?>>能找到</option>
					<option value="2" <?php if ($d_douban_id==2){ echo "selected";} ?>>不确定，未搜索</option>
				</select><select id="can_search_device" name="can_search_device">
					<option value="">投放设备</option>
					<option value="TV"
					<?php if ($can_search_device==='TV'){ echo "selected";} ?>>TV版</option>
					<option value="iPad"
					<?php if ($can_search_device==='iPad'){ echo "selected";} ?>>iPad版</option>
					<option value="iphone"
					<?php if ($can_search_device==='iphone'){ echo "selected";} ?>>iphone版</option>
					<option value="apad"
					<?php if ($can_search_device==='apad'){ echo "selected";} ?>>Android-Pad版</option>
					<option value="aphone"
					<?php if ($can_search_device==='aphone'){ echo "selected";} ?>>Android-Phone版</option>
					<option value="web"
					<?php if ($can_search_device==='web'){ echo "selected";} ?>>网站版</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="4">关键字：<input id="keyword" size="40" name="keyword"
					value="<?php echo $keyword?>" onkeypress="send(event);">&nbsp; <select
					id="keyword_col" name="keyword_col">
					<option value="">关键字的匹配列</option>
					<option value="d_name"
					<?php if($keyword_col=="d_name"){ echo "selected";} ?>>视频名称</option>
					<option value="d_starring"
					<?php if($keyword_col=="d_starring"){ echo "selected";} ?>>演员</option>
					<option value="d_directed"
					<?php if($keyword_col=="d_directed"){ echo "selected";} ?>>导演</option>
				</select> <input class="input" type="button" value="搜索"
					id="btnsearch" onClick="filter();"></td>
				<td align="left"><input style="display:<?php echo $douban_scoreT;?>" type="button" value="豆瓣评分" id="btnsearchs" onClick="doubans();">
				</td>
				<td align="left"><input style="display:<?php echo $douban_scoreT;?>" type="button" value="豆瓣影评" id="btnsearchsComment" onClick="doubansComment();">
				</td>
				<td align="left"><input style="display:<?php echo $douban_scoreT;?>" type="button" value="豆瓣缩略图 " id="btnsearchsThumbs" onClick="doubanThumbs();">
				</td>

			</tr>
		</table>
		</td>
	</tr>
</table>


<form id="form1" name="form1" method="post">
<table class="admin_vod tb">
	<tr>
		<td width="4%"></td>
		<td width="4%">编号</td>
		<td width="15%">名称</td>
		<td width="4%">播放次数</td>
		<td width="3%">上映日期</td>
		<td width="5%">分类</td>
		<td width="5%">来源</td>
		<!--	<td width="3%">人气</td>-->
		<td width="4%">推荐</td>
		<!--	<td width="5%">专题</td>-->
		<!--	<td width="5%">轮播图</td>-->
		<td width="13%" align="center">视频榜单</td>
		<!--	<td width="5%">浏览</td>-->
		<td width="8%">时间</td>
		<td width="25%">操作</td>
	</tr>
	<?php
	if($nums==0){
		?>
	<tr>
		<td align="center" colspan="12">没有任何记录!</td>
	</tr>
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
		<td><input name="d_id[]" type="checkbox" value="<?php echo $d_id?>"></td>
		<td><?php echo $d_id?></td>
		<td><?php echo substring($row["d_name"],20)?> <?php if($row["d_state"] > 0) {?><?php echo "<font color=\"red\">[" .$row["d_state"] . "]</font>"; }?>
		<?php if(!isN($row["d_remarks"])) {?><?php echo "<font color=\"red\">[" .$row["d_remarks"] . "]</font>"; }?>
		<?php if($row["d_hide"]==1){echo "<font color=\"red\">[隐藏]</font>";} ?>
		</td>
		<td><?php echo $row["d_play_num"]?></td>
		<td><?php echo $row["d_year"]?></td>
		<td><?php echo $tname?></td>
		<td title="<?php echo replaceStr($row["d_playfrom"],"$$$",",");?>"><?php echo substring(replaceStr($row["d_playfrom"],"$$$",","),15,0)?></td>
		<!--<td><?php echo $row["d_hits"]?></td>
	-->
		<td id="tj<?php echo $d_id?>"><?php echo "<img src=\"../images/icons/ico".$row["d_level"].".gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('tj','".$d_id."','vod')\"/>"?>
		</td>
		<!--<td id="zt<?php echo $d_id?>" >
	<?php if($row["d_topic"]==0) {?>
	<?php echo "<img src=\"../images/icons/icon_02.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"setday('zt','".$d_id."','vod')\"/>"?>
	<?php }else{?>
	<?php echo "<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxdivdel('".$d_id."','zt','vod')\"/>"?>
	<?php }?>
	</td>
	
	-->
		<!--<td id="popular<?php echo $d_id?>" >
	<?php if($row["popular_id"]>0) {?>
		<?php echo "<img src=\"../images/icons/icon_01.gif\" border=\"0\" style=\"cursor: pointer;\" />"?>
	
	<?php }else{?>
	<?php echo "<img src=\"../images/icons/icon_02.gif\" border=\"0\" style=\"cursor: pointer;\" onClick=\"ajaxsubmit('".$d_id."','lunbo','vod');\"/>"?>
	<?php }?>
	</td>
	
	-->
		<td align="center"><span id="bd<?php echo $d_id?>"></span> <a href="#"
			onClick="setdayBD('bd','<?php echo $d_id?>','vod','1')" /> 添加悦单</a> |
		<a href="#" onClick="setdayBD('bd','<?php echo $d_id?>','vod','2')" />
		添加悦榜</a></td>
		<!--<td>
	<?php
	if (app_playtype ==0){
	 	if ($row["d_type"] == 0){
			$mlink = "#";
		}
		else{
	 		$mlink = "../".$template->getVodLink($d_id,$row["d_name"],$row["d_enname"],$row["d_type"],$tname,$tenname);
		}
		$mlink = replaceStr($mlink,"../".app_installdir,"../");
	 	$plink = $mlink;
	}
	 else{
	 	$mlink = "../".$template->getVodPlayUrl($d_id,$row["d_name"],$row["d_enname"],$row["d_type"],$tname,$tenname,1,1);
	 	$mlink = replaceStr($mlink,"../".app_installdir,"../");
	 	$plink= $mlink;
	 	$mlink = replaceStr($mlink,"javascript:OpenWindow1('","");
	 	$mlink = replaceStr($mlink,"',popenW,popenH);","");
	 }
	 
	 if (substring($mlink,1,strlen($mlink)-1)=="/") { $mlink = $mlink ."index.". app_vodsuffix;}
	 if (app_vodcontentviewtype == 2) {
		 if (file_exists($mlink)){
		 	?>
		 	<a target="_blank" href="<?php echo $plink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
		 	<?php
		 }
		 else{
		 	?>
		 	<a  href="admin_makehtml.php?action=viewpl&flag=vod&d_id=<?php echo $d_id?>"><Img src="../images/icons/html_no.gif" border="0" alt='生成' /></a>
		 	<?php
		 }
	}
	 else{
	 ?>
	 	<a target="_blank" href="<?php echo $plink?>"><Img src="../images/icons/html_ok.gif" border="0" alt='浏览' /></a>
	 <?php
	 }
	?>
	 </td>
	-->
		<td><?php echo isToday($row["d_time"])?></td>
		<td><a href="admin_vod_topic.php?action=info&id=<?php echo $d_id?>">所在榜单</a>
		|<a href="admin_vod.php?action=edit&id=<?php echo $d_id?>">修改</a> | <?php if($row["d_type"] ==1 || $row["d_type"] ==2 || $row["d_type"] ==131){ ?><a
			href="admin_vod.php?action=douban&id=<?php echo $d_id?>">豆瓣评分</a> | <a
			href="admin_vod.php?action=doubanComment&id=<?php echo $d_id?>">豆瓣影评</a>
		| <?php }?> <a
			href="admin_vod.php?action=doubanPic&id=<?php echo $d_id?>">豆瓣图片</a>
		| <a href="admin_vod.php?action=doubanThumb&id=<?php echo $d_id?>">豆瓣缩略图</a>
		|<a href="#"
			onClick="if(confirm('确定要添加到追剧列表吗')){ajaxsubmit('<?php echo $d_id;?>','subscribe_msg_list','vod');}">添加到追剧列表</a>
			<?php if($loginname ==='dale' || $loginname ==='admin'  || $loginname ==='lulu' || $loginname ==='scottliyq' || $loginname ==='zhouyinlong'  ){?>
		<!--	|--> <!--	 <a class="thickbox" href="#TB_inline?height=200&width=400&inlineId=myOnPageContent" onclick="javascript:{prepareWeiboText('<?php echo $row["d_type"]?>','<?php echo $d_id?>','<?php echo substring($row["d_name"],20)?>');}" > 消息推送</a>	  -->
			<?php }?> | <A
			href="admin_vod.php?action=del&d_id=<?php echo $d_id?>"
			onClick="return confirm('确定要删除吗?');">删除</a></td>
	</tr>
	<?php
		}
	}
	?>
	<tr class="formlast">
		<td colspan="11"><input name="chkall" type="checkbox" id="chkall"
			value="1" onClick="checkAll(this.checked,'d_id[]');" /> 全选 &nbsp;批量操作：&nbsp;<input
			type="button" id="btnDel" value="删除" class="input"> <input
			type="button" id="pltj" value="推荐" onClick="plset('pltj','vod')"
			class="input"> <input type="button" id="plfl" value="分类"
			onClick="plset('plfl','vod')" class="input"> <input type="button"
			id="plluobo" value="轮播图" onClick="plsetLuobo()" class="input"> <input
			type="button" id="plbd" value="视频悦单"
			onClick="plsetBD('plbd','vod','1')" class="input"> <input
			type="button" id="plbd" value="视频悦榜"
			onClick="plsetBD('plbd','vod','2')" class="input"> <input
			type="button" id="plyc" value="显隐" onClick="plset('plyc','vod')"
			class="input"> <span id="plmsg" name="plmsg"></span></td>
	</tr>
	<tr class="formlast">
		<td align="center" colspan="12"><?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod.php?page={p}&can_search_device=" . $can_search_device . "&d_douban_id=" . $d_douban_id . "&keyword=" . urlencode($keyword) . "&d_status=".$d_status."&keyword_col=".$keyword_col."&sort=" . $sort . "&select_videourl_play=" . $select_videourl_play . "&topic=" . $topic . "&level=".$level."&order=".$order ."&stype=" . $stype ."&sserver=" . $sserver ."&sstate=".$sstate."&repeat=".$repeat."&repeatlen=".$repeatlen."&from=".$from."&spic=".$spic."&hide=".$hide."&douban_comment=".$douban_comment."&douban_score=".$douban_score."&select_weburl=".$select_weburl."&select_videourl=".$select_videourl."&ipadpic=".$ipadpic)?>
		</td>
	</tr>
</table>

<div id="myOnPageContent" style="display: none">

<table class="table" cellpadding="0" cellspacing="0" width="100%"
	border="0">

	<thead class="tb-tit-bg">
		<tr>
			<td>
			<h3 class="title">发送设备:<select name="device_type" id="device_type">
				<option value="">所有设备</option>
				<option value="ios">IOS</option>
				<option value="android">Android</option>
			</select></h3>
			</td>

		</tr>
		<tr>
			<td><span><font color="blue">消息内容 </font></span></td>

		</tr>
		<input type="hidden" name="notify_msg_prod_id" id="notify_msg_prod_id"
			value="">
		<input type="hidden" name="notify_msg_prod_type"
			id="notify_msg_prod_type" value="">

		<tr>
			<td align="center"><textarea name="wbText" id="weiboText" rows="9"
				cols="60" style="border: 1; border-color: blue;"></textarea></td>
		</tr>
		<tr>
			<td align="right"><a href="#" onclick="javascript:sendWeiboText();">发送</a></td>
		</tr>
		<tr>
			<td align="center"><font color=red><span id="SendWeiboMsg"></span></font></td>

		</tr>

	</thead>
</table>

</div>
</form>
	<?php
	if ($pagenum==1 && $where==" 1=1 ") { echo "<script>showpic();</script>";}
	unset($rs);
}


function douban(){
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }

	if ($action=="douban"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}else {
			$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
			$scoreDouban = new DouBanParseScore();
			$score= $scoreDouban->getScore($name, $year, $area);
				
			unset($row);
			if($score>0){
				$db->Update ("{pre}vod", array("d_score"), array($score), "d_id=" . $d_id);
				//			 	showMsg('采集成功',$backurl);
				echo '<script language="javascript">alert("采集成功,评分为'.$score.'");location.href ="'.$backurl.'";</script>';
			}else {
				echo '<script language="javascript">alert("在豆瓣上找不到资源 ");location.href ="'.$backurl.'";</script>';
			}
		}

	}
}
function doubanPic(){
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }

	if ($action=="doubanPic"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}else {
			$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
			$scoreDouban = new DouBanParseScore();
			$doubanid=$row['d_douban_id'];
			$flag=true;
			unset($row);
			if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
				$flag=false;
				$pic= $scoreDouban->getPicById($doubanid,7/5);
			}else {
				$pic= $scoreDouban->getDouBanPics($name, $year, $area,7/5);
			}
			if($pic !==false) {
				$doubanid=$pic['id'];
				if(!isN($doubanid) && $flag){
					$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
				}
				$pic=$pic['pic'];
			}
			if($pic !==false &&  !isN($pic)){
				$padPic= explode("{Array}", $pic);
				if(count($padPic)>0){
					$padPic=$padPic[0];
					writetofile("updateVodPic.txt", 'd_pic_ipad{=}'.$padPic .'{=}d_pic_ipad_tmp{=}'.$pic);
					$db->Update ("{pre}vod", array("d_pic_ipad","d_pic_ipad_tmp"), array($padPic,$pic), "d_id=" . $d_id);
					echo '<script language="javascript">alert("采集成功");location.href ="'.$backurl.'";</script>';
				}else {
					echo '<script language="javascript">alert("在豆瓣上找不到资源 ");location.href ="'.$backurl.'";</script>';
				}
			}else {
				echo '<script language="javascript">alert("在豆瓣上找不到资源 ");location.href ="'.$backurl.'";</script>';
			}
		  
		  
		}

	}
}

function doubanThumb(){
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }

	if ($action=="doubanThumb"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}else {
			$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
			$scoreDouban = new DouBanParseScore();
			$doubanid=$row['d_douban_id'];
			unset($row);
			$flag=true;
			if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
				$flag=false;
				$pic= $scoreDouban->getThumb($doubanid);
			}else {
				$pic= $scoreDouban->getDoubanThumb($name, $year, $area);
			}
			if($pic !==false) {
				$doubanid=$pic['id'];
				if(!isN($doubanid) && $flag){
					$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
				}
				$pic=$pic['pic'];
			}
		  
			if($pic !==false && !isN($pic)){
				writetofile("updateVodPic.txt", 'd_pic{=}'.$pic );
				$db->Update ("{pre}vod", array("d_pic"), array($pic), "d_id=" . $d_id);
				echo '<script language="javascript">alert("采集成功");location.href ="'.$backurl.'";</script>';
			}else {
				echo '<script language="javascript">alert("在豆瓣上找不到资源 ");location.href ="'.$backurl.'";</script>';
			}
		}

	}
}

function doubanComment(){
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }
	//	echo 'dd';
	if ($action=="doubanComment"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}else {
			$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
			$type=$row["d_type"];
			$scoreDouban = new DouBanParseScore();
			$doubanid=$row['d_douban_id'];
			unset($row);
			$flag=true;
			if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
				$flag=false;
				$comments= $scoreDouban->getReviewsById($doubanid);
			}else {
				$comments= $scoreDouban->getDoubanReviews($name, $year, $area);
			}
			if($comments !==false) {
				$doubanid=$comments['id'];
				if(!isN($doubanid) && $flag){
					$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
				}
			}
		  
			$review_flag=false;
			//var_dump($comments);
			if(is_array($comments)&& !isN( $comments['comments'])){
				$commentsArray = $comments['comments'];
				$titlesArray =$comments['title'];
				$reviewidsArray= $comments['reviewid'];
				$total= count($commentsArray);
				if($total>0){
					$db->Delete("tbl_comments", "content_id=".$d_id ." and author_id is null and comment_type=1");
					//$db->Delete("mac_comment", "c_vid=".$d_id );
				}
					
				for ($i=0;$i<$total;$i++) {
					$com=$commentsArray[$i];
					$title=$titlesArray[$i];
					$reviewid=$reviewidsArray[$i];
					if(!isN($com)){
						$com=filterScript($com,8191);
						$title=replaceStr($title, "'", "\"");
						$com=replaceStr($com, "'", "\"");
						$db->Add("tbl_comments", array("status","content_type","content_name","content_id","create_date","comments","comment_type","douban_comment_id","comment_title"),
						array('1',$type,$name,$d_id,date("Y-m-d H:i:s"),$com,'1',$reviewid,$title));

						$db->Add("mac_comment", array("c_audit","c_type","c_vid","c_time","c_content","c_name"),
						array('1',$type,$d_id,date("Y-m-d H:i:s"),$com,$author));
						$review_flag=true;
					}
				}
				//			 	$db->Updatiss ("{pre}vod", array("d_score"), array($score), "d_id=" . $d_id);
				//			 	showMsg('采集成功',$backurl);
				if($review_flag){
					echo '<script language="javascript">alert("采集评论成功");location.href ="'.$backurl.'";</script>';
				}else {
					echo '<script language="javascript">alert("在豆瓣上找不到评论 ");location.href ="'.$backurl.'";</script>';
				}
			}else {
				echo '<script language="javascript">alert("在豆瓣上找不到评论 ");location.href ="'.$backurl.'";</script>';
			}
		}

	}
}

function updateCommentCount(){
	global $db,$action; //update mac_vod set total_comment_number=0
	$sql='update mac_vod set total_comment_number=0 ';
	$db->query($sql);


	$sql='update mac_vod as vod , (select count(content_id) as count, content_id
from tbl_comments group by content_id  ) as comment set vod.total_comment_number=comment.count where vod.d_id=comment.content_id ';
	$db->query($sql);


}

function notifyMsg(){

	$d_id = be("get","prod_id");$prod_type = be("get","prod_type");

	$device_type = be("get","device_type");
	$content = be("get","content");
	$msg = new Notification();
	$msg->alert=$content;
	$msg->prod_id=$d_id;
	$msg->prod_type=$prod_type;
	if(isset($device_type) && !is_null($device_type)){
		$msg->type=$device_type;
	}
	//    $msg->action='com.joyplus.UPDATE_PROGRAM';
	//$manager = new NotificationsManager();
	$result= NotificationsManager::push($msg);
	//   var_dump($result['response']);
	if($result['code'].'' == '200'){
		echo "消息推送成功";
	}else {
		echo "消息推送失败:".$result['response'];
	};
}


function info()
{
	global $db,$action;
	$backurl = getReferer();
	if (strpos($backurl,"admin_vod.php") <=0){ $backurl="admin_vod.php"; }

	if ($action=="edit"){
		$d_id = be("get","id");
		$row = $db->getRow("SELECT * FROM {pre}vod WHERE d_id=".$d_id);
		if (!$row){
			errmsg ("系统信息","错误没有找到该数据");
		}
		else{
			$d_name=$row["d_name"]; $d_enname=$row["d_enname"]; $d_state=$row["d_state"]; $d_type=$row["d_type"];
			$d_color=$row["d_color"]; $d_pic=$row["d_pic"]; $d_starring=$row["d_starring"]; $d_directed=$row["d_directed"];
			$d_area=$row["d_area"]; $d_year=$row["d_year"]; $d_language=$row["d_language"]; $d_level=$row["d_level"];
			$d_stint=$row["d_stint"]; $d_hits=$row["d_hits"]; $d_dayhits=$row["d_dayhits"]; $d_weekhits=$row["d_weekhits"];
			$d_monthhits=$row["d_monthhits"]; $d_topic=$row["d_topic"]; $d_content=$row["d_content"]; $d_remarks=$row["d_remarks"];
			$d_hide=$row["d_hide"]; $d_good=$row["d_good"]; $d_bad=$row["d_bad"]; $d_usergroup=$row["d_usergroup"];
			$d_score=$row["d_score"]; $d_scorecount=$row["d_scorecount"]; $d_addtime=$row["d_addtime"]; $d_time=$row["d_time"];
			$d_hitstime=$row["d_hitstime"]; $d_subname=$row["d_subname"]; $d_playurl=$row["d_playurl"]; $d_downurl=$row["d_downurl"];
			$d_playfrom=$row["d_playfrom"]; $d_playserver=$row["d_playserver"]; $d_letter=$row["d_letter"];$d_type_name=$row["d_type_name"];
			$d_pic_ipad=$row["d_pic_ipad"];
			$d_video_desc_url=$row["d_video_desc_url"];
			$duraning=$row["duraning"];
			$d_play_check=$row["d_play_check"];
			$can_play_device=$row["can_play_device"];
			$d_status=$row["d_status"];
			$can_search_device=$row["can_search_device"];
			$d_douban_id=$row["d_douban_id"];
			$d_capital_name=$row["d_capital_name"];
			if (isN($d_playurl)) { $d_playurl = "";}
			if (isN($d_video_desc_url)) { $d_video_desc_url = "";}
			if (isN($d_downurl)) { $d_downurl = "";}
			//			var_dump($d_downurl);
			$d_weburl=$row["webUrls"];
			if (isN($d_weburl)) { $d_weburl = "";}
			unset($row);
		 if(!isNum($d_hide)) { $d_hide=-1;} else { $d_hide = intval($d_hide);}
		 if(!isNum($d_play_check)) { $d_play_check=0;} else { $d_play_check = intval($d_play_check);}

		}
	}
	?>


<div id="win1" class="easyui-window" title="窗口"
	style="padding: 5px; width: 650px;" closed="true" minimizable="false"
	maximizable="false">
<table class="admin_vod tb">

	<tr>
		<td>视频地址：</td>
		<td><TEXTAREA id="tip" NAME="tip" ROWS="10"
			style="width: 500px; table-layout: fixed; word-wrap: break-word;"></TEXTAREA>
		</td>
	</tr>

</table>
</div>

<script
	language="javascript" src="editor/xheditor-zh-cn.min.js"></script>
<script language="javascript">
var ac = "<?php echo $action?>";
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			d_type:{
				required:true
			},
			d_name:{
				required:true,
				maxlength:254,
			},
			d_subname:{
				maxlength:254
			},
			d_enname:{
				maxlength:254
			},
			d_letter:{
				maxlength:1
			},
			d_state:{
				number:true
			},
			d_pic:{
				maxlength:254
			},
			d_starring:{
				maxlength:254
			},
			d_directed:{
				maxlength:254
			},
			d_year:{
				maxlength:32
			},
			d_hits:{
				number:true
			},
			d_dayhits:{
				number:true
			},
			d_weekhits:{
				number:true
			},
			d_monthhits:{
				number:true
			},
			d_good:{
				number:true
			},
			d_bad:{
				number:true
			},
			d_score:{
				number:true
			},
			d_scorecount:{
				number:true
			},
			d_stint:{
				number:true
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        if (ac=="add"){
		        $.messager.defaults.ok = "确定";
				$.messager.defaults.cancel = "返回";
				$.messager.confirm('系统提示', '是否继续添加数据?', function(r){
					if(r==true){
						location.href = "admin_vod.php?action=add";
					}
					else{
		        		location.href = $("#backurl").val();
		        	}
		        });
	        }
	        else{
	           if(data.indexOf('no') >-1){ 
	           alert("存在播放器为no，不能保存。");
			   }else {
				   location.href = $("#backurl").val();
			   }
	        	//
	        }
	    }
	});
	$("#btnCancel").click(function(){
		location.href = $("#backurl").val();
	});
});

	function collect(weburls,playerfrom){
		var urls=$("#"+weburls).val();
		var playerfrom=$("#"+playerfrom).val();
	//	alert(urls);
		$.post("admin_vod_getVideoUrls.php",{"weburls":urls,"playerfrom":playerfrom}, function(obj) {
			//oncomplete(obj);
			$("#tip").val(obj);
			$('#win1').window('open');
		});
		
	};

	function changeArea(prod_type,area,typename){
//	  alert(prod_type);alert(area);
//	  var areaArray=
//	  var content='<option value="0">请选择地区</option>';
      var content='';
	  if(prod_type =='1'){
		  content='内地,香港,台湾,美国,日本,韩国,欧洲,东南亚,其他';
	  }
	  if(prod_type =='2'){
		  content='内地,香港,台湾,韩国,美国,日本,其他';
	  }
	  if(prod_type =='3'){
		  content='港台,内地,日韩,欧美,其他';
	  }
	  if(prod_type =='131'){
		  content='日本,欧美,国产,其他';
	  }
	  var obj=$("#d_area_span");

	  obj[0].innerHTML=content;
//	  //alert(obj[0].innerHTML);
//	  for(var i=0;i<areaArray.length;i++){
//          if(area==areaArray[i]){
//        	  content=content+'<option value="'+area+'" selected="">'+area+'</option>';
//          }else {
//        	  content=content+'<option value="'+areaArray[i]+'" >'+areaArray[i]+'</option>';
//          }
//       }
//	  obj[0].innerHTML=content;

	//<input type="checkbox" name="can_search_device[]" value="TV" "checked" />TV版
	     var typenameContent='';
	     if(prod_type =='1'){
	    	 typeNameArray='恐怖,惊悚,悬疑,伦理,爱情,剧情,喜剧,科幻,动作,战争,冒险,音乐,动画,运动,奇幻,传记,古装,犯罪,武侠,其他'.split(',');
		  }
		  if(prod_type =='2'){
			  typeNameArray='剧情,情感,青春偶像,家庭伦理,喜剧,犯罪,战争,古装,动作,奇幻,经典,乡村,商战,历史,情景,TVB,其他'.split(',');
		  }
		  if(prod_type =='3'){
			  typeNameArray='综艺,选秀,情感,访谈,播报,旅游,音乐,美食,纪实,曲艺,生活,游戏互动,财经,求职,其他'.split(',');
		  }
		  if(prod_type =='131'){
			  typeNameArray='情感,科幻,热血,推理,搞笑,冒险,萝莉,校园,动作,机战,运动,耽美,战争,少年,少女,社会,原创,亲子,益智,励志,百合,其他'.split(',');
		  }
		  var typeNameobj=$("#d_type_name_span");
		  
		  for(var i=0;i<typeNameArray.length;i++){			 
			  var index =typename.indexOf(typeNameArray[i]);
			//  alert(typename + '==='+typeNameArray[i] +' ==' +index);
	          if( index >= 0 ){
		        //  alert('hear');
	        	  typenameContent=typenameContent+'<input type="checkbox" name="d_type_name[]" value="'+typeNameArray[i]+'" checked />'+typeNameArray[i];
	          }else {
	        	  typenameContent=typenameContent+'<input type="checkbox" name="d_type_name[]" value="'+typeNameArray[i]+'"  />'+typeNameArray[i];
	          }
	       }
		  typeNameobj[0].innerHTML=typenameContent;
	}

	
     
</script>

<form name="form1" id="form1" method="post" action="?action=save">
<table class="admin_vod_2 tb">
	<input name="flag" type="hidden" value="<?php echo $action?>">
	<input name="d_id" type="hidden" value="<?php echo $d_id?>">
	<input id="backurl" name="backurl" type="hidden"
		value="<?php echo $backurl?>">
	<tr>
		<td width="10%">参数：</td>
		<td><select id="d_type" name="d_type"
			onchange="changeArea(this.options[this.selectedIndex].value,'<?php echo $d_area;?>','<?php echo $d_type_name?>');">
			<option value="">请选择栏目</option>
			<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$d_type)?>
		</select><select id="d_level" name="d_level">
			<option value="">选择推荐值</option>
			<option value="1" <?php if($d_level == 1) { echo "selected";} ?>>推荐1</option>
			<option value="2" <?php if($d_level == 2) { echo "selected";} ?>>推荐2</option>
			<option value="3" <?php if($d_level == 3) { echo "selected";} ?>>推荐3</option>
			<option value="4" <?php if($d_level == 4) { echo "selected";} ?>>推荐4</option>
			<option value="5" <?php if($d_level == 5) { echo "selected";} ?>>推荐5</option>
			<option value="6" <?php if($d_level == 6) { echo "selected";} ?>>推荐6</option>
			<option value="7" <?php if($d_level == 7) { echo "selected";} ?>>推荐7</option>
			<option value="8" <?php if($d_level == 8) { echo "selected";} ?>>推荐8</option>
		</select> &nbsp; <select id="d_language" name="d_language">
			<option value="0">请选择语言</option>
			<?php echo makeSelectAreaLang("lang",$d_language)?>
		</select><select id="d_hide" name="d_hide">
			<option value="0" <?php if($d_hide==0) { echo "selected";} ?>>显示</option>
			<option value="1" <?php if($d_hide==1) { echo "selected";} ?>>隐藏</option>
			<option value="-100" <?php  if ($d_hide==-100){ echo "selected";} ?>>视频不能播放</option>
		</select> &nbsp;&nbsp;&nbsp;<a
			href="admin_vod_topic.php?action=info&id=<?php echo $d_id?>"><font
			color="red">所在榜单</font></a> &nbsp;&nbsp;&nbsp;视频ID： <?php echo $d_id?>&nbsp;&nbsp;&nbsp;<input
			type="checkbox" name="uptime" value="1" checked>更新时间 <input
			type="hidden" name="oldtime" value="<?php echo $d_time?>"></td>
	</tr>
	<tr>
		<td>名称：</td>
		<td><input id="d_name" name="d_name" type="text" size="40"
			value="<?php echo $d_name?>"
			onBlur="if(this.value){ajaxckname(this.value);}"><span id="d_name_ok"></span>
		&nbsp;名称首字母：<input id="d_capital_name" name="d_capital_name"
			type="text" size="40" value="<?php echo $d_capital_name?>">



		</td>
	</tr>
	<tr>
		<td>拼音：</td>
		<td><input id="d_enname" name="d_enname" type="text" size="40"
			value="<?php echo $d_enname?>"> &nbsp;备注：<input id="d_remarks"
			name="d_remarks" type="text" size="40"
			value="<?php echo $d_remarks?>"> &nbsp;连载信息：<input id="d_state"
			name="d_state" type="text" size="10" value="<?php echo $d_state?>"> <!--	&nbsp;&nbsp;&nbsp;播放设备:-->
		<!--	<select id="can_play_device" name="can_play_device">--> <!--	<option value="0" <?php if ($can_play_device==0){ echo "selected";} ?>>所有设备</option>-->
		<!--	<option value="1" <?php if ($can_play_device==1){ echo "selected";} ?>>只能ipad/iphone播放</option>-->
		<!--	<option value="2" <?php if ($can_play_device==2){ echo "selected";} ?>>只能Android播放</option>-->
		<!--	</select>--></td>
	</tr>
	<tr>
		<td>演员：</td>
		<td><input id="d_starring" name="d_starring" type="text"
			size="40" value="<?php echo $d_starring?>"> &nbsp;导演：<input
			id="d_directed" name="d_directed" type="text" size="40"
			value="<?php echo $d_directed?>"> &nbsp;上映日期：<input id="d_year"
			name="d_year" type="text" value="<?php echo $d_year?>" size="10">
		&nbsp;豆瓣ID：<input id="d_douban_id" name="d_douban_id" type="text"
			value="<?php echo $d_douban_id?>" size="10">
	
	</tr>

	<tr>
		<td><font color="red"><strong>是否已经审核过：</strong></font></td>
		<td><select id="d_status" name="d_status">
			<option value="0" <?php if ($d_status==0){ echo "selected";} ?>>未审核</option>
			<option value="1" <?php if ($d_status==1){ echo "selected";} ?>>审核并锁定</option>
		</select> &nbsp;<font color="red"><strong>投放的设备：</strong></font> <?php

		if(isN($can_search_device)){
			$can_search_device="iPad,iphone,web,apad,TV,aphone";
		}

		?> <input type="checkbox" name="can_search_device[]" value="TV"
		<?php if(strpos($can_search_device, "TV") !==false){echo "checked";}?> />TV版
		<input type="checkbox" name="can_search_device[]" value="iPad"
		<?php if(strpos($can_search_device, "iPad") !==false){echo "checked";}?> />iPad版
		<input type="checkbox" name="can_search_device[]" value="iphone"
		<?php if(strpos($can_search_device, "iphone") !==false){echo "checked";}?> />iphone版
		<input type="checkbox" name="can_search_device[]" value="apad"
		<?php if(strpos($can_search_device, "apad") !==false){echo "checked";}?> />Android-Pad版
		<input type="checkbox" name="can_search_device[]" value="aphone"
		<?php if(strpos($can_search_device, "aphone") !==false){echo "checked";}?> />Android-phone版
		<input type="checkbox" name="can_search_device[]" value="web"
		<?php if(strpos($can_search_device, "web") !==false){echo "checked";}?> />网站版

		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="red"><strong>播放时长：</strong></font>
		<input id="duraning" name="duraning" type="text"
			value="<?php echo $duraning?>" size="10"> <font color="red"><strong>（格式为：分钟：秒，例如：122:00）</strong></font>
		</td>
	</tr>


	<tr>
		<td>类别：</td>
		<td><span id="d_type_name_span"><?php echo $d_type_name?></span>
		</td>
	</tr>
	<tr>
		<td>地区：</td>
		<td><input id="d_area" name="d_area" type="text" size="60"
			value="<?php echo $d_area?>">可以多个，以空格分开 <br />
		<span id='d_area_span'></span></td>
	</tr>

	<tr>
		<td>图片：</td>
		<td><input id="pic" name="pic" type="text" size="60"
			value="<?php echo $d_pic?>"><font color="red">（可以直接填写图片url地址）</font><iframe
			src="editor/uploadshow.php?action=vod" scrolling="no" topmargin="0"
			width="320" height="24" marginwidth="0" marginheight="0"
			frameborder="0" align="middle"></iframe></td>
	</tr>




	<tr>
		<td>图片 For IPad：</td>
		<td><input id="d_pic_ipad" name="d_pic_ipad" type="text"
			size="60" value="<?php echo $d_pic_ipad?>"><font color="red">（可以直接填写图片url地址）</font><iframe
			src="editor/uploadshow.php?action=vod" scrolling="no" topmargin="0"
			width="320" height="24" marginwidth="0" marginheight="0"
			frameborder="0" align="middle"></iframe> <br />
		<font color="red">备注：对于综艺悦榜里的视频，需要两种大图片，他们以逗号分开，格式为：综艺悦榜列表图片地址,综艺详细页面图片地址</font>
		</td>
	</tr>

	<tr>
		<td>短视频简介Url地址：</td>
		<td>&nbsp; <textarea id="d_video_desc_url" name="d_video_desc_url"
			style="width: 700px; height: 50px;"><?php echo $d_video_desc_url?></textarea>
		<br />
		只用于微信 ,如果存在两个地址，按{Array}隔开。IOS/Android,其中android版的支持flash.</td>
	</tr>

	<tr>
		<td>其他：</td>
		<td>豆瓣评分：<input id="d_score" name="d_score" type="text" size="8"
			value="<?php echo $d_score?>"></td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 0">
		<div id="urlarr"><?php
		$playnum=0;
		if ($action=="edit"){
			if (isN($d_weburl)) { $d_weburl="";}
			if (isN($d_playfrom)) { $d_playfrom="";}
			$playurlarr1 = explode("$$$",$d_weburl);
			$playfromarr = explode("$$$",$d_playfrom);
			$playserverarr = explode("$$$",$d_playserver);

			for ($i=0;$i<count($playurlarr1);$i++){
				if(!isN($playurlarr1[$i]) || $playfromarr[$i] ==='yuanxian' ){
					$playnum = $i + 1;
					$playurl = replaceStr($playurlarr1[$i], "{Array}", Chr(13));
					$playfrom = $playfromarr[$i];
					$playserver = $playserverarr[$i];
					?>
		<div id="playurldiv<?php echo $playnum?>" class="playurldiv">
		<table width="100%" class='tb2'>
			<tr>
				<td width='22%'>网页播放器<?php echo $playnum?>：</td>
				<td><input id="urlid<?php echo $playnum?>" name="urlid[]"
					type="hidden" value="<?php echo $playnum?>" /> &nbsp;播放器： <select
					id="urlfrom<?php echo $playnum?>" name="urlfrom[]">
					<option value="no">暂无数据</option>
					<?php echo makeSelectPlayer($playfrom)?>
				</select> &nbsp;&nbsp;<a href="javascript:void(0)"
					onclick="removeplay('<?php echo $playnum?>')">删除</a> &nbsp;&nbsp;<a
					href="javascript:void(0)" onclick="moveUp('<?php echo $playnum?>')">上移</a>
				&nbsp;&nbsp;<a href="javascript:void(0)"
					onclick="moveDown('<?php echo $playnum?>')">下移</a>
				说明:每行一个地址，不能有空行(如果是电视剧，剧集数$网页播放地址)。 <input type="button"
					value="采集视频地址" class="input"
					onclick="collect('url<?php echo $playnum?>','urlfrom<?php echo $playnum?>');return false;" />
				</td>
			</tr>
			<tr>
				<td>网页播放地址<?php echo $playnum?>:</td>
				<td><textarea id="url<?php echo $playnum?>" name="url[]"
					style="width: 700px; height: 150px;"><?php echo $playurl?></textarea></td>
			</tr>
		</table>
		</div>
		<?php
				}
			}
		}
		?></div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><img
			onClick="appendplay(<?php echo $playnum+1?>,escape('<?php echo replaceStr(makeSelectPlayer(""),"'","\'")?>'),escape('<?php echo replaceStr(makeSelectServer(""),"'","\'")?>'))"
			src="../images/icons/edit_add.png" style="cursor: pointer" />&nbsp;&nbsp;单击按钮添加一组播放地址
		</td>
	</tr>

	<!--  play weburl address -->






	<!--  play weburl address -->


	<tr>

		<td colspan="2" style="padding: 0">
		<div id="downurlarr"><?php
		if ($action=="edit"){
			$downurlarr1 = explode("$$$",$d_downurl);
			$downurlarrcount = count($downurlarr1);
			for ($j=0;$j<$downurlarrcount;$j++){
				if(!isN($downurlarr1[$j])){
					$downurlarr2 = explode( "$$",$downurlarr1[$j] ); $downnum=$j+1;
					$downfrom = $downurlarr2[0];
					$downurl= replaceStr($downurlarr2[1],"{Array}",chr(13));
					//					$playfrom = $playfromarr[$j];
					//
					//					$playnum = $i + 1;
					//	                $playurl = replaceStr($playurlarr1[$i], "{Array}", Chr(13));
					//	                $playfrom = $playfromarr[$i];
					//	                $playserver = $playserverarr[$i];
					?>
		<div id="downurldiv<?php echo $downnum?>" class="downurldiv">
		<table width="100%" class='tb2'>
			<tr>
				<td width='22%'>视频下载选择<?php echo $downnum?>：</td>
				<td><input id="downurlid<?php echo $downnum?>" name="downurlid[]"
					type="hidden" value="<?php echo $downnum?>" /> &nbsp;类型： <select
					id="downurlfrom<?php echo $downnum?>" name="downurlfrom[]">
					<option value="no">暂无数据</option>
					<?php echo makeSelectPlayer($downfrom)?>
				</select> &nbsp;&nbsp;<a href="javascript:void(0)"
					onclick="removedown('<?php echo $downnum?>')">删除</a> &nbsp;&nbsp;<a
					href="javascript:void(0)"
					onclick="moveUps('down','<?php echo $downnum?>')">上移</a>
				&nbsp;&nbsp;<a href="javascript:void(0)"
					onclick="moveDowns('down','<?php echo $downnum?>')">下移</a>
				说明:每行一个地址，不能有空行。（如果是电视剧，剧集数$视频地址)</td>
			</tr>
			<tr>
				<td>视频下载地址<?php echo $downnum?>:</td>
				<td><textarea id="downurl<?php echo $downnum?>" name="downurl[]"
					style="width: 700px; height: 150px;"><?php echo $downurl?></textarea></td>
			</tr>
		</table>
		</div>
		<?php
				}
			}
		}
		?></div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><img
			onClick="appenddown(<?php echo $downnum+1?>,escape('<?php echo replaceStr(makeSelectPlayer(""),"'","\'")?>'))"
			src="../images/icons/edit_add.png" style="cursor: pointer" />&nbsp;&nbsp;单击按钮添加一组下载地址
		</td>
	</tr>

	<tr>
		<td>相关介绍：</td>
		<td><textarea name="d_content" id="d_content"
			class="xheditor {tools:'BtnBr,Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink,Img,Flash,Media,Table,Source,Fullscreen',width:'700',height:'200',upBtnText:'上传',html5Upload:false,upMultiple:1,upLinkUrl:'{editorRoot}upload.php?action=xht',upImgUrl:'{editorRoot}upload.php?action=xht'}"><?php echo $d_content?></textarea>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2"><input class="input" type="submit" value="保存"
			id="btnSave"> <input class="input" type="button" value="返回"
			id="btnCancel"></td>
	</tr>
</table>
</form>


<script language="javascript">
 changeArea('<?php echo $d_type;?>','<?php echo $d_area;?>','<?php echo $d_type_name?>');
</script>
		<?php
		if($playnum==0){
			?>
<script>
	appendplay(1,escape("<?php echo makeSelectPlayer("")?>"),escape("<?php echo makeSelectServer("")?>"));
</script>
			<?php
		}
		unset($rs);
}

function pserep()
{
	global $db;
	$pagenum = be("all","page");
	$startid = be("all","startid");
	$endid = be("all","endid");

	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	if(!isN($startid)){ $where .= " and d_id >=" . $startid; }
	if(!isN($endid)){ $where .= " and d_id <=" . $endid; }
	$sql = "SELECT count(*) FROM {pre}vod where 1=1".$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/500);
	if (($pagecount-$pagenum) <0){
		echo "恭喜，所有数据已经替换";exit;
	}

	$sql = "SELECT d_id,d_name,d_content FROM {pre}vod where 1=1 ".$where . " limit ".(500 * ($pagenum-1)) .",500" ;
	$rs = $db->query($sql);

	if($rs){
		$psecontent  = file_get_contents("../inc/dim_pse1.txt");
		if (isN($psecontent)) { $psecontent = "";}
		$psecontent = replaceStr($psecontent,chr(10),"");
		$psearr1 = explode(chr(13),$psecontent);
		while ($row = $db ->fetch_array($rs))
		{
			$d_content=$row["d_content"];
			$d_id=$row["d_id"];
			for($j=0;$j<count($psearr1);$j++){
				$k = strpos($psearr1[$j],"=");
				if ($k > 0) { $m=explode("=",$psearr1[$j]); $d_content = replaceStr($d_content,$m[0],$m[1]);}
			}
			if ($d_content != $row["d_content"]){
				$db->Update ( "{pre}vod",array("d_content"),array($d_content),"d_id=".$d_id);
				echo "<font color=green>成功替换 ID:". $d_id."	".$row["d_name"]."</font><br>";
			}
			else{
				echo "<font color=red>跳过替换 ID:". $d_id."	".$row["d_name"]."</font><br>";
			}
		}
		echo "<br>暂停3秒后继续替换</div><script language=\"javascript\">setTimeout(function (){location.href='?action=pserep&page=".($pagenum+1)."&startid=".$startid."&endid=".$endid."';},3000);</script>";
	}
	unset($rs);
}

function psesave()
{
	$pse1 = stripslashes(be("post","pse1"));
	$pse2 = stripslashes(be("post","pse2"));
	fwrite(fopen("../inc/dim_pse1.txt","wb"),$pse1);
	fwrite(fopen("../inc/dim_pse2.txt","wb"),$pse2);
	echo "修改完毕";
}

function pse()
{
	$fc1 = file_get_contents("../inc/dim_pse1.txt");
	$fc2 = file_get_contents("../inc/dim_pse2.txt");
	?>
<script language="javascript">
$(document).ready(function(){
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnPse").click(function(){
		var startid = $("#startid").val();
		var endid = $("#endid").val();
		$('#pseinfo').window('open'); 
		$("#pseiframe").attr("src","admin_vod.php?action=pserep&startid="+startid+"&endid="+endid);
	});
});
</script>
<form action="?action=psesave" method="post" id="form1" name="form1">
<table class="admin_vod tb">
	<tr class="thead">
		<th width="30%" align=left>同义词批量替换(改动数据库) <br>
		1.每个一行; 2.不要有空行;3.格式：<font color="red">要替换=替换后</font></th>
		<th align=left>随机添加内容 (不改动数据库)<br>
		每段内容随机插入到简介的中，也不会每次都随机改变</th>
	</tr>
	<tr>
		<td valign="top"><textarea id="pse1" name="pse1"
			style="width: 100%; font-family: Arial, Helvetica, sans-serif; font-size: 14px;"
			rows="25"><?php echo $fc1?></textarea> 起始ID：<input id="startid"
			type="text" size=10 /> 结束ID：<input id="endid" type="text" size=10 />
		<input type="button" id="btnPse" name="btnPse" value="替换内容"
			class="input" /><br>
		<font color=red>（不填写ID则替换所有数据）</font></td>
		<td valign="top"><textarea id="pse2" name="pse2"
			style="width: 100%; font-family: Arial, Helvetica, sans-serif; font-size: 14px;"
			rows="27"><?php echo $fc2?></textarea></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" id="btnSave"
			name="btnSave" value="保存内容" class="input" /></td>
	</tr>
</table>
</form>
<div id="pseinfo" class="easyui-window" title="同义词批量替换"
	style="OVERFLOW: HIDDEN" closed="true" minimizable="false"
	maximizable="false"><iframe id="pseiframe" name='pseiframe' src=''
	width="400" height="400" MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0"
	VSPACE="0" FRAMEBORDER="0" SCROLLING="yes"></iframe></div>
	<?php
}
?>
</body>
</html>
