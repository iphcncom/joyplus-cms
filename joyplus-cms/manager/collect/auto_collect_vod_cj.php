<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("BaiduParse.php");
require_once ("tools/ContentManager.php");

	$p_id = be("all","p_id"); 
	/**
	 * p_id=1_1-3 pid is 1 and page from 1 to 3
	 * p_id=1_1,3,5 pid is 1 and page is 1,3,5
	 * http://cmsdev.joyplus.tv/manager/collect/auto_collect_vod_cj.php?p_id=1_1-3
	 */ 
	
	writetofile("crawel_auto_info.log", $p_id.'{=====}Project===start');
	
	if (isN($p_id)) { writetofile("crawel_auto_info.log", $p_id.'采集提示","采集项目ID不能为空!'); break; }
	$tem=explode("_", $p_id);
	if(count($tem)==2){
		$p_id=$tem[0];
		$pagenums=$tem[1];
	}else {
		$pagenums="1";
	}
	writetofile("crawel_auto_info.log", $p_id.'{=====}'.$p_id.'===pagenums==='.$pagenums);
	$db->query ("update {pre}cj_vod_projects set p_time='".date('Y-m-d H:i:s',time())."' where p_id=".$p_id);
	
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

	$playcodeApiUrl =$row["p_playcodeApiUrl"] ; $playcodeApiUrltype= $row["p_playcodeApiUrltype"] ;
	$p_playcodeApiUrlParamend = $row["p_playcodeApiUrlParamend"] ; $playcodeApiUrlParamstart=  $row["p_playcodeApiUrlParamstart"] ;
	if (isN($playcodeApiUrltype)) { $playcodeApiUrltype = 0;}
    if (isN($p_videocodeType)) { $p_videocodeType = 0;}
	
	
	  
	$starringarr=array();
	$titlearr=array();
	$picarr=array();
	$strdstate = "";
	$flag=false;
	
	$reCollExistMovie = falses;

	$action = be("all","action");
	
	if(isN($action)){
	
	    $collExiM= be("all","ignoreExistM");
	
		if(!isN($collExiM)){
			$reCollExistMovie=true;
		}
		if(strpos($pagenums, "-") !==false){
			$nums= explode("-", $pagenums);
			writetofile("crawel_auto_info.log", $nums[1].'{=====}'.$nums[0]);
			for($i=$nums[0];$i<=$nums[1];$i++){
				writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i);
				$strListUrl= replaceStr($p_pagebatchurl,"{ID}",$i);
				writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
				clearSession();
				cjList();
				writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");
				writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i."{=====}end");
			}		
		}else {
			$nums= explode(",", $pagenums);
			writetofile("crawel_auto_info.log", 'Pagenums{=====}'.$pagenums);
			for($j=0;$j<count($nums);$j++){
				writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i);
				$strListUrl= replaceStr($p_pagebatchurl,"{ID}",$nums[$j]);
				writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}start");
				clearSession();
				cjList();
				writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}end");	
				writetofile("crawel_auto_info.log", $p_id.'===Current Number{=====}'.$i."{=====}end");		
			}
		}
	}else if($action ==='updateLZ'){
		
		if($p_collect_type ==='2') {
			$sql= "	SELECT vod.m_urltest as m_urltest, vod.m_typeid as m_typeid ,vod.m_remarks as m_remarks
					FROM `mac_cj_vod` AS vod
					WHERE vod.m_remarks != (
					SELECT count( * )
					FROM mac_cj_vod_url AS vodurl
					WHERE vod.m_id = vodurl.u_movieid )
					AND vod.m_typeid =2
					AND vod.m_name IS NOT NULL
					AND vod.m_name != ''
					AND vod.m_pid =".$p_id ;;
		} else if($p_collect_type ==='2') {
			$sql="SELECT m_urltest, m_typeid, (CASE WHEN (m_remarks IS NOT NULL AND m_remarks != '' ) THEN m_remarks ELSE m_state END) AS m_remarks
			FROM `mac_cj_vod` WHERE m_typeid =3 AND ( (m_remarks IS NOT NULL AND m_remarks != '') OR 
			( m_state IS NOT NULL AND m_state != '0' ) ) and  m_pid =".$p_id ;
		}
//		$sql = "SELECT m_urltest 
//    	        FROM   {pre}cj_vod where m_zt !=1 and m_pid=".$p_id ;
//		
		$rs = $db->query($sql);
	    $rscount = $db -> num_rows($rs);
//	    var_dump($rscount);
	    if($rscount==0){		
			errmsg ("没有可用的数据");
	    }else {
	    	while ($row = $db ->fetch_array($rs))	{
	    		$m_urltest=$row["m_urltest"];
	    		if(!isN($m_urltest)){
	    		  cjView(getHrefFromLink($m_urltest),1);
	    		} 		
	    	}
	    }
	    unset($rs);
	}
   
exit();



function cjList()
{   
	global $db,$flag,$listnum,$strListUrl,$p_pagetype,$p_collecorder,$p_listcodestart,$p_listcodeend,$p_listlinkstart,$p_listlinkend,$p_starringstart,$p_starringend,$p_titlestart,$p_titleend,$p_picstart,$p_picend,$p_starringtype,$p_titletype,$p_pictype,$p_coding,$p_showtype,$viewnum,$p_ids,$sb,$cg,$p_savefiles,$p_pagebatchid2,$p_pagebatchid1;
	global $p_playtype, $reCollExistMovie, $p_playspecialtype,$starringarr,$titlearr,$picarr,$strdstate,$action,$p_pagebatchurl,$p_colleclinkorder,$p_id;
	
	$strListCode = getPage($strListUrl,$p_coding);
	writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strListUrl ."{=====}List===start");
	$listnum =$listnum+1; $tempStep = 1;	
				
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
				writetofile("crawel_auto_error.log", $p_id.'{=====}'.$strListUrl);
				return;
			}
			
		
			
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
				if($p_colleclinkorder==1){
					for ($i=$viewcount ;$i>=0;$i--){						
					    $urlMo=getHrefFromLink($linkarr[$i]);
						if($reCollExistMovie){
							if($p_playtype ==='baidu'){
								cjBaiduView($urlMo,$i);
							}else {
							  cjView($urlMo,$i);
							}
						}else {
							$sql="select m_id from {pre}cj_vod where m_urltest='".$urlMo."' order by m_id desc";
		                    $rowvod=$db->getRow($sql);		
	                        if (!$rowvod) {
		                        if($p_playtype ==='baidu'){
									cjBaiduView($urlMo,$i);
								}else {
								  cjView($urlMo,$i);
								}                   	
	                        }	                       
						}
					}
				}
				else{
					for ($i=0 ;$i<count($linkarr);$i++){						
//						cjView(getHrefFromLink($linkarr[$i]),$i);
					    $urlMo=getHrefFromLink($linkarr[$i]);
						if($reCollExistMovie){
							if($p_playtype ==='baidu'){
								cjBaiduView($urlMo,$i);
							}else {
							  cjView($urlMo,$i);
							}
						}else {
							$sql="select m_id from {pre}cj_vod where m_urltest='".$urlMo."' order by m_id desc";
		                    $rowvod=$db->getRow($sql);		
	                        if (!$rowvod) {
		                        if($p_playtype ==='baidu'){
									cjBaiduView($urlMo,$i);
								}else {
								  cjView($urlMo,$i);
								}                    	
	                        }	                       
						}
					}
				}
				clearSession();
	
}


function cjBaiduView($strlink,$num){
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
	writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strlink ."{=====}View===start");
	echo "<tr><td colspan=\"2\">开始采集：".$strlink." / '.$strListUrl.'</br> </TD></TR>";
	$strViewCode = getPage($strlink,$p_coding);
	
	if ($strViewCode ==false) {
		$strdstate = "true";
		echo "<tr><td colspan=\"2\">在获取内容页时出错：".$strlink." / '.$strListUrl.' </br></TD></TR>";
		writetofile("crawel_auto_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
		$sb=$sb+1;
		return;
	}
	else{
		$info= BaiduParse::parseMovieInfoByContent($strViewCode, $p_coding, $p_collect_type);
//		var_dump($info)
		echo "<tr><td colspan=\"2\">在获取内容页时success ：".$strlink." / '.$strListUrl.'</br> </TD></TR>";
		//节目名称，来自列表或者来自内容页 
		if ($p_titletype ==1) {
			$titlecode = $titlearr[$num];
			
		}
		else{
			$titlecode = getBody($strViewCode,$p_titlestart,$p_titleend);
			if(isN($titlecode)){
			  $titlecode = $info->title;
			}
		}
		
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,0);
		$titlecode = replaceStr(replaceStr(replaceStr($titlecode,","," "),"'",""),"\"\"","");
		$titlecode = trim($titlecode);
		
	 
		$lzcode =$info->curr_episode;
		$lzcode = replaceStr($lzcode,"false","0");
		$lzcode = trim($lzcode);
		try{
		  $lzcode = intval($lzcode);
		}catch(Exception $e){
			$lzcode=0;
		}
		


		
		$p_hitsstart = 0 ;
		$p_hitsend = 0 ;
		$m_hits=0;
		
		if ($p_starringtype ==1) {
			$starringcode = $starringarr[$num];
		}
		else{
			$starringcode = $info->actor;
		}
		//演员
		$starringcode = filterScriptStar($starringcode,$p_script);
		$starringcode = replaceStr(replaceStr(replaceStr($starringcode,","," "),"'",""),"\"\"","");
		$starringcode = trim($starringcode);
		
		if ($p_pictype ==1) {
			$piccode = $picarr[$num];
		}
		else{
		 	$piccode = $info->big_poster;
		}
		//图片
		$piccode = trim($piccode);
		$piccode = getHrefFromImg(definiteUrl($piccode,$strListUrl));
		
		$m_typeid = $p_collect_type;
		$typecode=$info->type;
		$typecode = filterScript($typecode,$p_script);
		
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
		$weburl=$info->sites;
		if ($weburl ==false) {
				echo "<tr><td colspan=\"2\">在获取播放列表链接时出错 ".$strlink." / '.$strListUrl.'</TD></TR>";			
			    writetofile("crawel_auto_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
				$sb=$sb+1;
				return;
		}else{
		    $directedcode = $info->director;
			//备注
			$remarkscode = $info->max_episode;
			//语音
			$languagecode = !isN($info->language)?$info->language:"未知";
			$languagecode = trim($languagecode);
			//时间
			$timecode =  !isN($info->pubdate)?$info->pubdate:"未知";
			$timecode = trim($timecode);
			//地区
			$areacode = !isN($info->area)?$info->area:"未知";
			$areacode = trim($areacode);
			//内容
			$contentcode = !isN($info->brief)?$info->brief:"未知";
			$contentcode = filterScript(replaceFilters($contentcode,$p_id,2,0),$p_script);
			$contentcode = replaceStr(replaceStr(replaceStr($contentcode,","," "),"'",""),"\"\"","");
			$contentcode = trim($contentcode);
			//备注
			$duration = !isN($info->duration)?$info->duration:"";	
			$m_area = $areacode;
			$m_languageid = $languagecode;
			$piccode="";
			foreach ($weburl as $weburlitem){
				$p_playtypebaiduweb = $weburlitem['site_name'];
				$baiduwebUrls=$weburlitem['episodes'];
//				var_dump($p_playtypebaiduweb);
//				var_dump('----------------');
//				var_dump($weburlitem);
				$movieid = updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directedcode,$timecode,$p_playtypebaiduweb,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode);
			}
		
	   }
	}
}

function updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directedcode,$timecode,$p_playtype,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode){
	global $db,$cg;
       $sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$p_id."' and m_name='".$titlecode."'  and m_playfrom='".$p_playtype."'  order by m_id desc";
			
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
				$sql = "update {pre}cj_vod  set duraning='".$duration."' , m_pic='".$piccode."', m_type='".$typecode."',m_area='".$areacode."',m_urltest='".$strlink."',m_name='".$titlecode."',m_starring='".$starringcode."',m_directed='".$directedcode."',m_year='".$timecode."',m_playfrom='".$p_playtype."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."',m_playserver='',m_state='".$lzcode."',m_language='".$languagecode."',m_remarks='".$remarkscode."' where m_id=".$rowvod["m_id"];
				writetofile("sql.txt", $sql);
				$db->query($sql);
			}
			else{
				$cg=$cg+1;
				$sql="insert {pre}cj_vod (duraning,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values('".$duration."', '".$titlecode."','".$typecode."','".$areacode."','".$p_playtype."','".$starringcode."','".$directedcode."','".$piccode."','".$contentcode."','".$timecode."','".$strlink."','0','".$p_id."','".$m_typeid."','0','','".$lzcode."','".date('Y-m-d H:i:s',time())."','".$languagecode."','".$remarkscode."')";
				writetofile("sql.txt", $sql);
	 			$db->query($sql);
				$movieid= $db->insert_id();
			}
//			var_dump($baiduwebUrls);
			foreach ($baiduwebUrls as $baiduweburl){
				if(array_key_exists('url', $baiduweburl)){
				   $WebTestx = $baiduweburl['url'];	
				}else {
					continue;
				}
				
			    if(array_key_exists('pic', $baiduweburl)){
				 $picurl = $baiduweburl['pic'];	
				}else {
					$picurl='';
				}
						
					
				writetofile("crawel_auto_info.log", $p_id.'{=====}'.$WebTestx ."{=====}ViewList===start");
				
			    $contentObject =ContentProviderFactory::getContentProvider($p_playtype);
			    $androidUrl = $contentObject->parseAndroidVideoUrl($WebTestx, $p_coding, $p_script);
				$videoAddressUrl = $contentObject->parseIOSVideoUrl($WebTestx, $p_coding, $p_script);
				writetofile("android_log.txt", $WebTestx.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );
			    
//			    $url = getBody($playCode,$p_playurlstart,$p_playurlend);
			    $url = "";
			    if(array_key_exists('episode', $baiduweburl)){
				   $setname= $baiduweburl['episode'];
				}else {
					$setname='';
				}
				
				if($m_typeid ==3){
					if(array_key_exists('name', $baiduweburl)){
					   $setname=$setname. ' '.$baiduweburl['name'];
					}
					
				}
				$setname=trim($setname);
				
			   $sql="SELECT {pre}cj_vod_url.u_url FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.u_weburl='" . $WebTestx . "' and {pre}cj_vod.m_pid=" . $p_id . " and {pre}cj_vod.m_id=" . $movieid;
			   
     		   $rowurl = $db->getRow($sql);
     		   
			   if (!$rowurl) {
					 writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
					  $db->query("insert into {pre}cj_vod_url(pic,u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$picurl."','".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
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
	writetofile("crawel_auto_info.log", $p_id.'{=====}'.$strlink ."{=====}View===start");
	
	$strViewCode = getPage($strlink,$p_coding);
	
	if ($strViewCode ==false) {
		$strdstate = "true";	
		writetofile("crawel_auto_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
		$sb=$sb+1;
		return;
	}
	else{
		
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
			}
			else{
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
			
			//echo "<tr><td width=\"20%\" >自动下载图片：</td><td><iframe border=\"0\" valign=\"bottom\" vspace=\"0\" hspace=\"0\" marginwidth=\"0\" marginheight=\"0\" framespacing=\"0\" frameborder=\"0\" scrolling=\"no\" width=\"400\" height=\"15\" src=\"../admin_pic.php?action=downpic&wjs=1&path=../".$picpath."&file=".$picfile."&url=".$piccode."\"></iframe></td></tr>";
			$piccode = $picpath . $picfile;
		}
	}
	else{
//		echo "<tr><td colspan=\"2\" align=\"center\">第".($num+1)."条数据采集结果</td></tr><tr><td width=\"20%\" >来源：</td><td >".$strlink."</td></tr><tr><td width=\"20%\" >名称：</td><td >".$titlecode." 连载:".$lzcode." 备注:".$remarkscode."</td></tr>";
	}
	
	if ($weburl ==false) {
//			echo "<tr><td colspan=\"2\">在获取播放列表链接时出错 ".$strlink." / '.$strListUrl.'</TD></TR>";			
		    writetofile("crawel_auto_error.log", $p_id.'{=====}'.$strlink.'{=====}'.$strListUrl);
//			$sb=$sb+1;
			return;
	}
	else{
		
		$sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where  m_pid='".$p_id."' and m_name='".$titlecode."'  order by m_id desc";
		
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
				
			writetofile("crawel_auto_info.log", $p_id.'{=====}'.$WebTestx ."{=====}ViewList===start");
			
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
//     		   writetofile("d:\\sql.txt",$rowurl);
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


?>