<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");
//chkLogin();
$action = be("get","action");
headAdminCollect ("视频自定义采集");

$p_ids = be("all","p_id");
//if (isN($p_id)){
//	$p_ids = be("all","p_id");
//}
$num = be("get","num");
$listnum = be("get","listnum");
$viewnum = be("get","viewnum");
$sb = be("get","sb");
$cg = be("get","cg");

$reCollExistMovie = true;

$collExiM= be("all","ignoreExistM");

if(!isN($collExiM)){
	$reCollExistMovie=false;
}

writetofile("crawel_info.log", $p_id.'{=====}'.$listnum ."{=====}Project===start");

if (isn($num)){ $num =0;} else {$num = intval($num);}
if (isN($listnum)) { $listnum=0;} else {$listnum = intval($listnum);}
if (isN($viewnum)) { $viewnum=0;} else {$viewnum = intval($viewnum);}


if ((isN($action) && strpos(";".$p_ids,",")) || $action=="pl"){
	$action = "pl";
	$arrid = explode(",",$p_ids);
	$arrcount = count($arrid) + 1;
	if ($num >= $arrcount) {
		dBreakpoint ("../../upload/vodbreakpoint");
		showmsg ("<font color='red'><b>批量采集完成</b></font>","collect_vod_manage.php");
	}
	$p_id = $arrid[$num];
}
else{
	$p_id = $p_ids;
}

if (isN($p_id)) { errmsg ("采集提示","采集项目ID不能为空!"); }

if ($sb=="" && $cg==""){
	$db->query ("update {pre}cj_vod_projects set p_time='".date('Y-m-d H:i:s',time())."' where p_id=".$p_id);
	$sb=0;
	$cg=0;
}

$sql = "select * from {pre}cj_vod_projects where p_id=".$p_id;
$row= $db->getRow($sql);

$p_id = $row["p_id"];
$p_name = $row["p_name"];
$p_coding = $row["p_coding"];
$p_playtype = $row["p_playtype"];
$p_pagetype = $row["p_pagetype"];
$p_url = $row["p_url"];
$p_pagebatchurl = $row["p_pagebatchurl"];
$p_manualurl = $row["p_manualurl"];
$p_pagebatchid1 = $row["p_pagebatchid1"];  $p_pagebatchid1 = intval($p_pagebatchid1);
$p_pagebatchid2 = $row["p_pagebatchid2"];  $p_pagebatchid2 = intval($p_pagebatchid2);
$p_script = $row["p_script"];
$p_showtype = $row["p_showtype"];
$p_collecorder = $row["p_collecorder"];
$p_savefiles = $row["p_savefiles"];
$p_ontime = $row["p_ontime"];
$p_listcodestart = $row["p_listcodestart"];
$p_listcodeend = $row["p_listcodeend"];
$p_classtype = $row["p_classtype"];
$p_collect_type = $row["p_collect_type"];
$p_time = $row["p_time"];
$p_listlinkstart = $row["p_listlinkstart"];
$p_listlinkend = $row["p_listlinkend"];
$p_starringtype = $row["p_starringtype"];
$p_starringstart = $row["p_starringstart"];
$p_starringend = $row["p_starringend"];
$p_titletype = $row["p_titletype"];
$p_titlestart = $row["p_titlestart"];
$p_titleend = $row["p_titleend"];
$p_pictype = $row["p_pictype"];
$p_picstart = $row["p_picstart"];
$p_picend = $row["p_picend"];
$p_timestart = $row["p_timestart"];
$p_timeend = $row["p_timeend"];
$p_areastart = $row["p_areastart"];
$p_areaend = $row["p_areaend"];
$p_typestart = $row["p_typestart"];
$p_typeend = $row["p_typeend"];
$p_contentstart = $row["p_contentstart"];
$p_contentend = $row["p_contentend"];
$p_playcodetype = $row["p_playcodetype"];
$p_playcodestart = $row["p_playcodestart"];
$p_playcodeend = $row["p_playcodeend"];
$p_playurlstart = $row["p_playurlstart"];
$p_playurlend = $row["p_playurlend"];
$p_playlinktype = $row["p_playlinktype"];
$p_playlinkstart = $row["p_playlinkstart"];
$p_playlinkend = $row["p_playlinkend"];
$p_playspecialtype = $row["p_playspecialtype"];
$p_playspecialrrul = $row["p_playspecialrrul"];
$p_playspecialrerul = $row["p_playspecialrerul"];
$p_server = $row["p_server"];
$p_hitsstart = $row["p_hitsstart"];
$p_hitsend = $row["p_hitsend"];
$p_lzstart = $row["p_lzstart"];
$p_lzend = $row["p_lzend"];
$p_colleclinkorder = $row["p_colleclinkorder"];
$p_lzcodetype = $row["p_lzcodetype"];
$p_lzcodestart = $row["p_lzcodestart"];
$p_lzcodeend = $row["p_lzcodeend"];
$p_languagestart = $row["p_languagestart"];
$p_languageend = $row["p_languageend"];
$p_remarksstart = $row["p_remarksstart"];
$p_remarksend = $row["p_remarksend"];
$p_directedstart = $row["p_directedstart"];
$p_directedend = $row["p_directedend"];
$p_setnametype = $row["p_setnametype"];
$p_setnamestart = $row["p_setnamestart"];
$p_setnameend = $row["p_setnameend"];

$p_videocodeApiUrl= $row["p_videocodeApiUrl"];
	$p_videocodeApiUrlParamstart= $row["p_videocodeApiUrlParamstart"];
	$p_videocodeApiUrlParamend= $row["p_videocodeApiUrlParamend"];
	$p_videourlstart= $row["p_videourlstart"];
	$p_videourlend= $row["p_videourlend"];
$p_videocodeType= $row["p_videocodeType"];
//api start
	$playcodeApiUrl =$row["p_playcodeApiUrl"] ; $playcodeApiUrltype= $row["p_playcodeApiUrltype"] ;
	$p_playcodeApiUrlParamend = $row["p_playcodeApiUrlParamend"] ; $playcodeApiUrlParamstart=  $row["p_playcodeApiUrlParamstart"] ;
	 if (isN($playcodeApiUrltype)) { $playcodeApiUrltype = 0;}
	  if (isN($p_videocodeType)) { $p_videocodeType = 0;}
	//api end 
	
	  
	  $starringarr=array();
$titlearr=array();
$picarr=array();
$strdstate = "";
$flag=true;
if(isset($action) && $action ==='collectSimpl'){
//	echo $action;
	$webUrl = be("all","site_url");
	$name = be("all","name");
	$actor = be("all","actor");
	$poster = be("all","poster");
//	$strListUrl=$webUrl;
	if( isset($name) && !is_null($name)){
		$titlearr[]=$name;
	}
	if( isset($actor) && !is_null($actor)){
		$starringarr[]=$actor;
	}
	if( isset($poster) && !is_null($poster)){
		$picarr[]=$poster;
	}
	
//	echo $name;
	cjView(getHrefFromLink($webUrl),0);
//	break;
}else if(isset($action) && $action ==='collectListUrl'){
	$flag=false;
//	echo $action;
	$webUrl = be("all","site_urls");
    $strListUrl=$webUrl;
	writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
	cjList();
	writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
    break;
}else if(isset($action) && $action ==='collect'){
	$flag=false;
//	echo $action;  
//  var_dump($action);
	$pagenum = be("all","pagenum");
    $strListUrl= replaceStr($p_pagebatchurl,"{ID}",$pagenum);
//    var_dump($pagenum);
//     var_dump($strListUrl);
	writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
	cjList();
	writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
    break;
}else if(isset($action) && $action ==='collectAll'){
	$flag=false;
	for($pagenum=$p_pagebatchid2;$pagenum>=$p_pagebatchid1;$pagenum--){
	    $strListUrl= replaceStr($p_pagebatchurl,"{ID}",$pagenum);
		writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
		cjList();
		writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
	}
    break;
}else{
	writetofile("crawel_info.log", $p_id.'{=====}'.$listnum ."{=====}Project===start===".$p_pagetype);
	switch($p_pagetype)
	{
		case 0 :
			if ($listnum < 1) { $strListUrl = $p_url;} else {$ListEnd = 1;}
			break;
		case 1:
		case 3:
			if ($p_collecorder ==1 ){
				if (($p_pagebatchid2-$listnum)< $p_pagebatchid1 || ($p_pagebatchid2-($listnum+1)) < 0){
					$ListEnd=1;
				}
				else{
					$strListUrl= replaceStr($p_pagebatchurl,"{ID}",($p_pagebatchid2-$listnum));
				}
			}
			else{
				if (($p_pagebatchid1+$listnum)> $p_pagebatchid2){
					$ListEnd=1;
				}
				else{
					$strListUrl=replaceStr($p_pagebatchurl,"{ID}",($p_pagebatchid1+$listnum));
				}
			}
			break;
		case 2:
			$ListArray=explode("|",$p_manualurl);
			if (($listnum)>count($ListArray)) {
				$ListEnd=1;
			}
			else{
				$strListUrl = $ListArray[$listnum];
			}
			break;
	}
	
	echo "采集中...  第" .($listnum+1). "页， 成功". $cg."条，失败". $sb."条 </br>";
	
	switch($ListEnd)
	{
		case 1:
			if ($action != "pl"){
				dBreakpoint ("../../upload/vodbreakpoint");
				showmsg ("采集完成","collect_vod_manage.php");
			}
			else{
				if ($num >= $arrcount){
					dBreakpoint ("../../upload/vodbreakpoint");
					showmsg ("批量采集完成","collect_vod_manage.php");
				}
				else{
				  echo "此数据采集完毕 ---   暂停3秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=0&num=".($num+1)."&action=".$action."';}</script>";
				}
			}
			break;
		default:
			writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
			cjList();
			writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
			break;
	}
	
	

}


function cjList()
{
	global $db,$reCollExistMovie, $flag,$listnum,$strListUrl,$p_pagetype,$p_collecorder,$p_listcodestart,$p_listcodeend,$p_listlinkstart,$p_listlinkend,$p_starringstart,$p_starringend,$p_titlestart,$p_titleend,$p_picstart,$p_picend,$p_starringtype,$p_titletype,$p_pictype,$p_coding,$p_showtype,$viewnum,$p_ids,$sb,$cg,$p_savefiles,$p_pagebatchid2,$p_pagebatchid1;
	global  $p_playspecialtype,$starringarr,$titlearr,$picarr,$strdstate,$action,$p_pagebatchurl,$p_colleclinkorder,$p_id;
	
	if (isN($_SESSION["strListCode"])) {
		$strListCode = getPage($strListUrl,$p_coding);
		$_SESSION["strListCode"] = $strListCode;
	}
	else{
		$strListCode = $_SESSION["strListCode"];
	}
	
	if ($strListCode == false) {
		echo "<tr><td colspan=\"2\">在获取:".$strListUrl."网页源码时发生错误！</TD></TR>";
		writetofile("crawel_error.log", $p_id.'{=====}'.$strListUrl);
		exit;
	}
	writetofile("crawel_info.log", $p_id.'{=====}'.$strListUrl ."{=====}List===start");
	$listnum =$listnum+1; $tempStep = 1;
	
	
	switch($p_pagetype)
	{
		case 3:
			$strViewCode = $strListCode;
			$j = 1;
			if ($p_collecorder == 1) {
				$startnum = $p_pagebatchid2 ; $endnum = $p_pagebatchid1;
			}
			else{
				$startnum = $p_pagebatchid1 ; $endnum = $p_pagebatchid2;
			}
			if (!strpos($p_pagebatchurl,"{ID}")){
				$startnum=0; $endnum=0;
			}
			wtablehead();
			
			for ($i=$startnum ;$i<= $endnum;$i++)
			{
				$UrlTest = replaceStr($p_pagebatchurl,"{ID}",$i);
				echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$UrlTest."的数据 </TR>";				
				cjView($UrlTest,$i);
				$j = $j + 1;
			}
			wtablefoot();
			if($flag){
			  echo "<br> 此分页数据采集完毕 --- <script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_manage.php';}</script>";
			}
			break;
		default:			
			if( isN($_SESSION["strListCodeCut"] )){
				$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
				$_SESSION["strListCodeCut"] = $strListCodeCut;
			}
			else{
				$strListCodeCut = $_SESSION["strListCodeCut"];
			}
			if( isN($_SESSION["linkarrcode"] )){
				$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
				$_SESSION["linkarrcode"] = $linkarrcode;
			}
			else{
				$linkarrcode = $_SESSION["linkarrcode"];
			}
			
			if ($p_starringtype ==1) {
				$starringarrcode = getArray($strListCodeCut,$p_starringstart,$p_starringend);
			}
			if ($p_titletype ==1) {
				$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
			}
			if ($p_pictype ==1) {
				$picarrcode = getArray($strListCodeCut,$p_picstart,$p_picend);
			}
			
			if ($linkarrcode ==false) {
				echo "<tr><td colspan=\"2\"></TD>在获取链接列表时出错！'.$strListUrl.’</TR>";
				$sb = $sb+1;
				writetofile("crawel_error.log", $p_id.'{=====}'.$strListUrl);
				return;
			}
			
			wBreakpoint ("../../upload/vodbreakpoint",getUrl());
			
			$linkarr = explode("{Array}",$linkarrcode);
			if ($p_starringtype ==1) {
				$starringarr = explode("{Array}",$starringarrcode);
			}
			if ($p_titletype ==1) {
				$titlearr = explode("{Array}",$titlearrcode);
			}
			if ($p_pictype ==1) {
				$picarr = explode("{Array}",$picarrcode);
			}
			$viewcount = count($linkarr);
			if ($p_showtype==1) {
				if ($viewnum >= $viewcount){
					clearSession();
			        if($flag){
					 echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
			       }
				}
				else{
					if ($p_savefiles==1){ $strdstate = "false"; }else{ $strdstate = "true"; }
					wtablehead();
					cjView(getHrefFromLink($linkarr[$viewnum]),$viewnum);
					wtablefoot();
					if($flag){
					  echo "数据采集完毕 --- 稍后继续采集<script language=\"javascript\">var dstate=".$strdstate.";setInterval(\"makeNextPage();\",500);function makeNextPage(){if(dstate){dstate=false;location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".($listnum-1)."&sb=".$sb."&cg=".$cg."&num=".$num."&viewnum=".($viewnum+1)."&action=".$action."';}}</script>";
					}exit;
				}
			}
			else{
				if($p_colleclinkorder==1){
					for ($i=$viewcount ;$i>=0;$i--){
						wtablehead();
						if ($i==$viewcount){
							echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$strListUrl."的数据 </TR>";
						}
						$urlMo=getHrefFromLink($linkarr[$i]);
						if($reCollExistMovie){
							cjView($urlMo,$i);
						}else {
							$sql="select m_id from {pre}cj_vod where m_urltest='".$urlMo."' order by m_id desc";
		                    $rowvod=$db->getRow($sql);		
	                        if (!$rowvod) {
	                           cjView($urlMo,$i);                     	
	                        }else {	                        	
	                           writetofile("crawel_info.log", $p_id.'{=====}'.$urlMo ."{=====}View===is collected.");
	                        }	                       
						}
						wtablefoot();
					}
				}
				else{
					for ($i=0 ;$i<count($linkarr);$i++){
						wtablehead();
						if ($i==0){
							echo "<tr><td colspan=\"2\"></TD>正在采集列表：".$strListUrl."的数据 </TR>";
						}
					    $urlMo=getHrefFromLink($linkarr[$i]);
						if($reCollExistMovie){
							cjView($urlMo,$i);
						}else {
							$sql="select m_id from {pre}cj_vod where m_urltest='".$urlMo."' order by m_id desc";
		                    $rowvod=$db->getRow($sql);		
	                        if (!$rowvod) {
	                           cjView($urlMo,$i);                     	
	                        }else {	                        	
	                           writetofile("crawel_info.log", $p_id.'{=====}'.$urlMo ."{=====}View===is collected.");
	                        }	                       
						}
						wtablefoot();
					}
				}
				clearSession();
				if($flag){
				  echo "<br> 此分页数据采集完毕 ---   暂停2秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",2000);function makeNextPage(){location.href='collect_vod_cj.php?p_id=".$p_ids."&listnum=".$listnum."&sb=".$sb."&cg=".$cg."&num=".$num."&action=".$action."';}</script>";
				}
			}
	}
}

function cjView($strlink,$num)
{
	global $starringarr,$titlearr,$picarr,$strListUrl,$p_playspecialtype,$p_playtype, $p_videocodeType,$p_videocodeApiUrl,$p_id,$p_videocodeApiUrlParamstart,$p_videocodeApiUrlParamend,$p_videourlstart,$p_videourlend, $playcodeApiUrl,$playcodeApiUrlParamstart,$p_playcodeApiUrlParamend,$playcodeApiUrltype,$db,$strListUrl,$p_titletype,$starringarr,$titlearr,$picarr,$p_id,$p_titlestart,$p_titleend,$p_lzstart,$p_lzend,$p_hitsstart,$p_hitsend,$p_starringtype,$p_starringstart,$p_starringend,$p_picstart,$p_picend,$p_typestart,$p_typeend,$p_pictype,$p_classtype,$p_collect_type,$p_timestart,$p_timeend,$p_areastart,$p_areaend,$p_contentstart,$p_contentend,$p_playcodestart,$p_playcodeend,$p_playlinkstart,$p_playlinkend,$p_playurlstart,$p_playurlend,$p_playcodetype,$p_playlinktype,$p_playtype,$p_coding,$p_lzstart,$p_lzend,$p_lzcodetype,$p_lzcodestart,$p_lzcodeend,$p_languagestart,$p_languageend,$p_remarksstart,$p_remarksend,$p_script,$p_showtype,$p_savefiles,$strdstate,$p_server,$p_setnametype,$p_setnamestart,$p_setnameend,$p_directedstart,$p_directedend,$cache;
	$androidUrl="";
	//var_dump($strlink);var_dump($strListUrl);
    try {
	  $pos = strpos($strlink, "href=\"");
	  if ($pos !== false) {
		$strlink=substr($strlink, $pos+6);
	  }
	  $pos = strpos($strlink, "\"");
	  if ($pos !== false) {
		$strlink=substr($strlink, 0,$pos);
	  }	
		
	} catch (Exception $e) {
	}
	$strlink = definiteUrl($strlink,$strListUrl);
	writetofile("crawel_info.log", $p_id.'{=====}'.$strlink ."{=====}View===start");
	echo "<tr><td colspan=\"2\">开始采集：".$strlink." / '.$strListUrl.'</br> </TD></TR>";
	$strViewCode = getPage($strlink,$p_coding);
	
	if ($strViewCode ==false) {
		$strdstate = "true";
		echo "<tr><td colspan=\"2\">在获取内容页时出错：".$strlink." / '.$strListUrl.' </br></TD></TR>";
		writetofile("crawel_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
		$sb=$sb+1;
		return;
	}
	else{
		echo "<tr><td colspan=\"2\">在获取内容页时success ：".$strlink." / '.$strListUrl.'</br> </TD></TR>";
		//节目名称，来自列表或者来自内容页 
		if ($p_titletype ==1) {
			$titlecode = $titlearr[$num];
			
		}
		else{
			$titlecode = getBody($strViewCode,$p_titlestart,$p_titleend);
		}
//		var_dump($titlearr[$num]);
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,0);
		$titlecode = replaceStr(replaceStr(replaceStr($titlecode,","," "),"'",""),"\"\"","");
		$titlecode = trim($titlecode);
		
//		$sql="select count(*) as cc from {pre}cj_vod where m_name='".$titlecode."' and m_playfrom='".$p_playtype."'";
//		$row=$db->getOne($sql);
//		//var_dump($row);var_dump($titlecode);
//		$rowcount = $row;
		
		//先缩小范围
		if ($p_lzcodetype ==1){
			//连载范围
			$lzfwcode = getBody($strViewCode,$p_lzcodestart,$p_lzcodeend);
			//连载编码
			$lzcode = getBody($lzfwcode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"false","0");
			$lzcode = trim($lzcode);
			$lzcode = intval($lzcode);
		}
		else{
			$lzcode = getBody($strViewCode,$p_lzstart,$p_lzend);
			$lzcode = replaceStr($lzcode,"false","0");
			$lzcode = trim($lzcode);
			$lzcode = intval($lzcode);
		}
		
//		if ($p_playcodetype !=2 &&($lzcode == 0) && ($rowcount>0)) {
//			$strdstate = "true";
//			echo "<tr><td colspan=\"2\">遇到重复电影数据跳过采集!</TD></TR>";
//			return;
//		}

		
		if (isN($p_hitsstart) || !isnum($p_hitsstart) ){ $p_hitsstart = 0 ;}
		if (isN($p_hitsend)  || !isnum($p_hitsend)) { $p_hitsend = 0 ;}
		if ($p_hitsstart ==0 && $p_hitsend ==0 ){ $m_hits = 0;} else {$m_hits = rand($p_hitsend,$p_hitsstart);}
		
		if ($p_starringtype ==1) {
			$starringcode = $starringarr[$num];
		}
		else{
			$starringcode = getBody($strViewCode,$p_starringstart,$p_starringend);
		}
		//演员
		$starringcode = filterScriptStar($starringcode,$p_script);
		$starringcode = replaceStr(replaceStr(replaceStr($starringcode,","," "),"'",""),"\"\"","");
		$starringcode = trim($starringcode);
		
		if ($p_pictype ==1) {
			$piccode = $picarr[$num];
		}
		else{
		 	$piccode = getBody($strViewCode,$p_picstart,$p_picend);
		}
		//图片
		$piccode = trim($piccode);
		$piccode = getHrefFromImg(definiteUrl($piccode,$strListUrl));
		//栏目设置
		if ($p_classtype ==1) {
			$typecode = filterScript(getBody($strViewCode,$p_typestart,$p_typeend),$p_script);
			$typecode = trim($typecode);
			$m_typeid = changeId($typecode,$p_id,0,0);
		}
		else{
			$typecode = $p_collect_type;
			$typecode = trim($typecode);
			$m_typeid = $p_collect_type;
			$typearr = getValueByArray($cache[0], "t_id" ,$typecode );
			$typecode = $typearr["t_name"];
		}
		if($m_typeid==0){
			$m_typeid = $p_collect_type;
		}
		$typecode = filterScript($typecode,$p_script);
		
		//导演
		$directedcode = filterScriptStar(getBody($strViewCode,$p_directedstart,$p_directedend),$p_script);
		$directedcode = replaceStr($directedcode,"false","");
		$directedcode = replaceStr($directedcode,"'","");
		$directedcode = trim($directedcode);
		//备注
		$remarkscode = filterScript(getBody($strViewCode,$p_remarksstart,$p_remarksend),$p_script);
		$remarkscode = replaceStr($remarkscode,"false","");
		$remarkscode = trim($remarkscode);
		//语音
		$languagecode = filterScript(getBody($strViewCode,$p_languagestart,$p_languageend),$p_script);
		$languagecode = replaceStr($languagecode,"false","未知");
		$languagecode = trim($languagecode);
		//时间
		$timecode = filterScript(getBody($strViewCode,$p_timestart,$p_timeend),$p_script);
		if ($timecode ==false){ $timecode == "未知" ;}
		$timecode = trim($timecode);
		//地区
		$areacode = filterScript(getBody($strViewCode,$p_areastart,$p_areaend),$p_script);
		if ($areacode ==false){ $areacode = "未知" ;}
		$areacode = trim($areacode);
		//内容
		$contentcode = filterScript(getBody($strViewCode,$p_contentstart,$p_contentend),$p_script);
		if ($contentcode ==false){ $contentcode = "未知" ;}
		$contentcode = filterScript(replaceFilters($contentcode,$p_id,2,0),$p_script);
		$contentcode = replaceStr(replaceStr(replaceStr($contentcode,","," "),"'",""),"\"\"","");
		$contentcode = trim($contentcode);
			
		$m_area = $areacode;
		$m_languageid = $languagecode;
	    //播放列表，缩小
		if ($p_playcodetype ==1) {
			$playcode = getBody($strViewCode,$p_playcodestart,$p_playcodeend);
			//获取地址设置
			if ($p_playlinktype >0) {//播放链接
				$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
			}
			else{//内容页直接获取地址， 地址开始
				$weburl = getArray($playcode,$p_playurlstart,$p_playurlend);
			}
			
			if ($p_setnametype == 3) {
				$setnames = getArray($playcode,$p_setnamestart,$p_setnameend);
			}
		}else if ($p_playcodetype ==2) { //from api
//		writetofile("d:\\s.txt",$linkcode) ;
//		echo $p_playcodeApiUrlParamend .'=='.$playcodeApiUrlParamstart;
   
//		echo $playcodeApiUrlParamstart .'\n' .$p_playcodeApiUrlParamend .'  = '.$playcodeApiUrltype;
		if($playcodeApiUrltype ==0){
		  $paracode = getBody($strViewCode,$playcodeApiUrlParamstart,$p_playcodeApiUrlParamend);
		}else {
			 $paracode = getBody($UrlTestMoive,$playcodeApiUrlParamstart,$p_playcodeApiUrlParamend);
		}
//		echo $paracode;
        
		$p_apibatchurl = replaceStr($playcodeApiUrl,"{PROD_ID}",$paracode);
		$p_apibatchurls = replaceStr($p_apibatchurl,"{PAGE_NO}",1);
		$playcode=getFormatPage($p_apibatchurls,$p_coding);		
		
		$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
		$page_num=2;
//		echo "page 1 :".$weburl .'\n';
		$flag=true;
	
		while ($flag && $page_num<15 && strpos($playcodeApiUrl, "{PAGE_NO}") !==false){
			$p_apibatchurls = replaceStr($p_apibatchurl,"{PAGE_NO}",$page_num);
//			echo $p_apibatchurls .'\n';
		    $playcode=getFormatPage($p_apibatchurls,$p_coding);		
		    $weburls = getArray($playcode,$p_playlinkstart,$p_playlinkend);
//		    echo "page ".$page_num." :".$weburls .'\n';
		    if($weburls){
		    	$weburl=$weburl."{Array}".$weburls;		    	
		        $page_num=$page_num+1;
		    }else {
		    	$flag=false;
		    }
		    
		}
		
//		var_dump($weburl);
//		if ($p_playlinktype >0) {
//			$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
//		}
//		else{
//			$weburl = getArray($playcode,$p_playurlstart,$p_playurlend);
//		//	var_dump($playcode);
//		}
//		if ($p_setnametype == 3) {
//			$setnames = getArray($playcode,$p_setnamestart,$p_setnameend);
//		}
	}
		else{
			
			if ($p_playlinktype >0) {
				$weburl = getArray($strViewCode,$p_playlinkstart,$p_playlinkend);
//				var_dump($weburl);
			}else{ 
				$weburl = getArray($strViewCode,$p_playurlstart,$p_playurlend);
				$androidUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseAndroidVideoUrlByContent($strViewCode, $p_coding, $p_script);
				$videoAddressUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseIOSVideoUrlByContent($strViewCode, $p_coding, $p_script);
				writetofile("android_log.txt", $strlink.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );

			}
			if ($p_setnametype == 3) {
				$setnames = getArray($strViewCode,$p_setnamestart,$p_setnameend);
			}
		}
	
	if ($p_showtype==1) {
		echo "<tr><td  colspan=\"2\" align=\"center\">此列表中第".($num+1)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</td></tr><tr><td >演员：</td><td >".$starringcode."</td></tr><tr><td >导演：</td><td >".$directedcode."</td></tr><tr><td >时间：</td><td >".$timecode."</td></tr><tr><td >分类：</td><td >".$typecode."</td></tr><tr><td >地区：</td><td >".$areacode."</td></tr><tr><td >语言：</td><td >".$languagecode."</td></tr><tr><td  >图片：</td><td >".$piccode."</td></tr><tr><td >介绍：</td><td >".substring($contentcode,50).".....</td></tr>";
	
		if ($p_savefiles ==1) {
			$filename = time() . $num;
			if (strpos($piccode,".jpg") || strpos($piccode,".bmp") || strpos($piccode,".png") || strpos($piccode,".gif")){
				$extName= substring($piccode,4,strlen($piccode)-4);
			}
			else{
				$extName=".jpg";
			}
			$picpath = "upload/vod/". getSavePicPath() . "/" ;
			$picfile = $filename . $extName;
			
			echo "<tr><td width=\"20%\" >自动下载图片：</td><td><iframe border=\"0\" valign=\"bottom\" vspace=\"0\" hspace=\"0\" marginwidth=\"0\" marginheight=\"0\" framespacing=\"0\" frameborder=\"0\" scrolling=\"no\" width=\"400\" height=\"15\" src=\"../admin_pic.php?action=downpic&wjs=1&path=../".$picpath."&file=".$picfile."&url=".$piccode."\"></iframe></td></tr>";
			$piccode = $picpath . $picfile;
		}
	}
	else{
		echo "<tr><td colspan=\"2\" align=\"center\">第".($num+1)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</td></tr>";
	}
	
	if ($weburl ==false) {
			echo "<tr><td colspan=\"2\">在获取播放列表链接时出错 ".$strlink." / '.$strListUrl.'</TD></TR>";			
		    writetofile("crawel_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
			$sb=$sb+1;
			return;
	}
	else{
		$sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$p_id."' and m_name='".$titlecode."' order by m_id desc";
		
		$rowvod=$db->getRow($sql);
		
	    if ($rowvod) {
			$cg=$cg+1;
			$movieid=$rowvod["m_id"];
			if(isN($titlecode)){
				$titlecode = $rowvod["m_name"];
			}
	    
			if(isN($starringcode)){
				$starringcode = $rowvod["m_starring"];
			}
	    
			if(isN($piccode)){
				$piccode = $rowvod["m_pic"];
			}
			$sql = "update {pre}cj_vod set m_pic='".$piccode."', m_type='".$typecode."',m_area='".$areacode."',m_urltest='".$strlink."',m_name='".$titlecode."',m_starring='".$starringcode."',m_directed='".$directedcode."',m_year='".$timecode."',m_playfrom='".$p_playtype."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."',m_playserver='".$p_server."',m_state='".$lzcode."',m_language='".$languagecode."',m_remarks='".$remarkscode."' where m_id=".$rowvod["m_id"];
			writetofile("sql.txt", $sql);
			$db->query($sql);
//			$sql="delete from {pre}cj_vod_url where u_movieid=".$rowvod["m_id"];
//			writetofile("sql.txt", $sql);
//			$db->query($sql);
		}
		else{
			
			$cg=$cg+1;
			$sql="insert {pre}cj_vod (m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values('".$titlecode."','".$typecode."','".$areacode."','".$p_playtype."','".$starringcode."','".$directedcode."','".$piccode."','".$contentcode."','".$timecode."','".$strlink."','0','".$p_id."','".$m_typeid."','".$m_hits."','".$p_server."','".$lzcode."','".date('Y-m-d H:i:s',time())."','".$languagecode."','".$remarkscode."')";
			writetofile("sql.txt", $sql);
 			$db->query($sql);
			$movieid= $db->insert_id();
		}
		
		$webArray=explode("{Array}",$weburl);
		$setnamesArray=explode("{Array}",$setnames);
 	    $webArraTemp=array();
	    $index=0;
	    $webUrls='';
	    for ($i=0 ;$i<count($webArray);$i++){
	 	  $UrlTemp = $webArray[$i];
	   	  if(strpos($webUrls, $UrlTemp.'<array>') === false){
	 		$webArraTemp[$index]=$UrlTemp;
	 		$webUrls=$webUrls.$UrlTemp.'<array>';
	 		$index++;
	 	  }
	    }
//	    writetofile("d:\\ssssss.txt","p_videocodeType:".$p_videocodeType);
        $webArray=$webArraTemp;
        	
		//http://www.youku.com/show_episode/id_zc16d0492e81411e196ac.html?dt=json&divid=reload_1&__rt=1&__ro=reload_1
		for ($i=0 ;$i< count($webArray);$i++){
			$WebTestx = $webArray[$i];
			if ($p_playspecialtype ==1 && strpos(",".$p_playspecialrrul,"[变量]")) {
					$Keyurl = explode("[变量]",$p_playspecialrrul);
					$urli = getBody ($UrlTest,$Keyurl[0],$Keyurl[1]);
				    if ($urli==false) { break; }
					$WebTestx = replaceStr($p_playspecialrerul,"[变量]",$urli);
			}
			
						
			  if ($p_playspecialtype ==2 ) {
					$urArray = explode("/", $strlink);
					
					$ur="";
					for($k=0;$k<count($urArray)-1;$k++){
						$ur=$ur.$urArray[$k]."/";
					}
					$WebTestx=$ur.$WebTestx.".html";
				}
				
			writetofile("crawel_info.log", $p_id.'{=====}'.$WebTestx ."{=====}ViewList===start");
			
			if ($p_playlinktype == 1){ //播放页获取地址
				$WebTestx=getHrefFromLink($WebTestx);
			    $WebTestx = definiteUrl($WebTestx,$strListUrl);
			    $playCode = getPage($WebTestx,$p_coding);
			    
			    $androidUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseAndroidVideoUrlByContent($playCode, $p_coding, $p_script);
				$videoAddressUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseIOSVideoUrlByContent($playCode, $p_coding, $p_script);
				writetofile("android_log.txt", $strlink.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );
			    
			    $url = getBody($playCode,$p_playurlstart,$p_playurlend);
			    $url = replaceLine($url);
			    
			        
			}
			else if($p_playlinktype == 2){ //播放链接中获取地址
				$WebTestx=getHrefFromLink($WebTestx);
				if (isN($p_playurlend)){
					$tmpA = strpos($WebTestx, $p_playurlstart);
                	$url = substr($WebTestx,strlen($WebTestx)-$tmpA-strlen($p_playurlstart)+1);
				}
				else{
					$url = getBody($WebTestx,$p_playurlstart,$p_playurlend);
				}
				
			}
			else if($p_playlinktype == 3){ //单播放页获取所有播放地址
				$WebTestx=getHrefFromLink($WebTestx);
				$playCode = getPage($WebTestx,$p_coding);
				$tmpB = getArray($webCode,$p_playurlstart,$p_playurlend);
				$tmpC = explode("$Array$",$tmpB);
				foreach($tmpC as $tmpD){
					$sql="SELECT {pre}vod_url.u_url FROM ({pre}vod_url INNER JOIN {pre}vod ON {pre}vod_url.u_movieid = {pre}vod.m_id)  where {pre}vod_url.u_url='" . $tmpD . "' and {pre}vod.m_pid=" . $p_id;
     		   		$row = $db->getRow($sql);
			   		if(!$row){
			   			$strTempUrl = $strTempUrl . $tmpD . "<br>";
					  	$db->query( "insert into {pre}vod_url(u_url,u_movieid) values('".$tmpD."','".$movieid."')");
			   		}
				}
				break;
			}
			else{
				$url= $WebTestx;
				$url = replaceLine($url);
				
			}
				
			   $url = replaceFilters($url,$p_id,3,0);
			   if ($p_setnametype == 1){
					$setname = getBody($url,$p_setnamestart,$p_setnameend);
//					$url = $setname . "$" . $url;
			   }
			   else if($p_setnametype == 2 && $p_playlinktype ==1) {
					$setname = getBody($playCode,$p_setnamestart,$p_setnameend);
//					$url = $setname ."$" .$url;
				}
				else if($p_setnametype == 3){
					$setname= $setnamesArray[$i];
				}
			   $sql="SELECT {pre}cj_vod_url.u_url FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.u_url='" . $url . "' and {pre}cj_vod.m_pid=" . $p_id . " and {pre}cj_vod.m_id=" . $movieid;
			   
     		   $rowurl = $db->getRow($sql);
     		//   writetofile("d:\\sql.txt",$sql);
//     		   var_dump($sql);
//			    return ;
			   if (!$rowurl) {
				   if ($p_playlinktype ==1) {
					  $strTempUrl .=  $url . "<br>";
					  $url = replaceStr($url,"'","''");
					 writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$movieid."','".getHrefFromLink($WebTestx)."','".getHrefFromLink($videoAddressUrl)."','".filterScriptStar($setname,$p_script)."' ,'".$androidUrl."' )");
					  $db->query("insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$movieid."','".getHrefFromLink($WebTestx)."','".getHrefFromLink($videoAddressUrl)."','".filterScriptStar($setname,$p_script)."' ,'".$androidUrl."' )");
					}
				   else{
					  $strTempUrl .= $url . "<br>";
					  writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,iso_video_url,u_weburl,android_vedio_url) values('".$url."','".$movieid."','".getHrefFromLink($videoAddressUrl)."','".getHrefFromLink($strlink)."', '".$androidUrl."')");
					 
					  $db->query("insert into {pre}cj_vod_url(u_url,u_movieid,iso_video_url,u_weburl,android_vedio_url) values('".$url."','".$movieid."','".getHrefFromLink($videoAddressUrl)."','".getHrefFromLink($strlink)."', '".$androidUrl."')");
				   }
			   }
		}
	}
	 
	}  
	$cg=$cg+1;
}

function wtablehead()
{
?>
<TABLE width="96%" border=0 align=center cellpadding="4" cellSpacing=1 class=tb >
<TBODY>
<?php
}

function wtablefoot()
{
?>
</TBODY>
</TABLE>
<?php
}
?>