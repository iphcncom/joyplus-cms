<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("conn.php");

if (app_timming==0){ echo "closed"; exit; }
$action = be("get", "action");
$action2 = be("get", "action2");
$flag = be("all", "flag");
$psize = be("all", "psize");
$stime = execTime();
$makeinterval=5;
$sql="";
$sql1="";

switch($action)
{
	case "index": makeindex();break;
	case "artindex": makeartindex();break;
	case "map": makemap();break;
	case "googlexml": makegoogle();break;
	case "baiduxml": makebaidu();break;
	case "rssxml": makerss();break;
	case "diypage": makediypage();break;
	case "diypageall": makediypageall();break;
        
	case "type": checkViewType(); maketype();break;
	case "typeall": checkViewType(); maketypeall();break;
	case "view": checkViewType(); makeview();break;
	case "viewall": checkViewType(); makeviewall();break;
	case "viewpart": checkViewType(); makeviewpart();break;
	case "viewpl": checkViewType(); makeviewpl();break;
	case "viewday": checkViewType(); makeviewday();break;
	case "topicindex": checkViewType(); maketopicindex();break;
	case "topic": checkViewType(); maketopic();break;
	case "topicall": checkViewType(); maketopicall();break;
	default:   main();break;
}

function checkViewType()
{
	global $flag,$sql,$sql1,$makeinterval;
    if ($flag == "art"){
    	$viewtype = app_artviewtype; $makeinterval = app_artmakeinterval;
    }
    else{ 
    	$viewtype = app_vodviewtype; $makeinterval = app_vodmakeinterval;
    }
    if ($viewtype != 2 ){  echo "网站运行为动态模式，不允许生成，请更换到静态模式下重试";exit; }
    if ($flag == "art"){
        $sql = "SELECT * FROM {pre}art WHERE a_hide=0 AND a_type >0 ";
        $sql1 = "SELECT count(a_id) FROM {pre}art WHERE a_hide=0 AND a_type >0 ";
    }
    else{
        $sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_type>0 ";
        $sql1 = "SELECT count(d_id) FROM {pre}vod WHERE d_hide=0 AND d_type>0 ";
    }
}

function main()
{
}

function makeindex()
{
	global $flag,$template;
	$fpath="";
	if ($flag=="art"){ $suffix= app_artsuffix;$fpath= $flag."index.html"; } else { $suffix=app_vodsuffix;$fpath= "index.html"; }
	if (suffix == "php"){ alertUrl ("后缀为php时不能生成首页","admin_makehtml.php") ;}
	$template->html = getFileByCache("template_index",root ."template/". app_templatedir ."/" .app_htmldir ."/". $fpath);
	$template->mark();
	$template->ifEx();
	$template->run("vod");
	$slink = "../". $fpath;
	fwrite(fopen($slink,"wb"),$template->html);
	echo  "首页生成完毕 <a target='_blank' href='". $slink."'><font color=red>浏览首页</font></a><br>";
}

function makemap()
{
	global $flag,$template;
	if ($flag=="art"){
		$suffix= app_artsuffix;
		$slink= "../artmap.".$suffix;
	}
	else{
		$suffix= app_vodsuffix;
		$slink="../map.".$suffix;
	}
	if ($suffix == "php"){ alertUrl ("后缀为php时不能生成地图","admin_makehtml.php");}
	$template->html = getFileByCache("template_".$flag."map", root."template/".app_templatedir."/".app_htmldir."/".$flag."map.html");
	$template->mark();
	$template->ifEx();
	$template->run ($flag);
	fwrite(fopen($slink,"wb"),$template->html);
	echo  "地图页生成完毕 <a target='_blank' href='".$slink."'><font color=red>浏览地图页</font></a><br>";
}

function makegoogle()
{
	global $db,$template,$cache;
	$allmakenum = be("all","gallmakenum");
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	$rs = $db->query($sql);
	$googleStr =  "<?xml version=\"1.0\" encoding=\"utf-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">"."\n";
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$googleStr .= "<url><loc>".$viewLink."</loc><lastmod>".getDatet("Y-m-d",$row["d_time"])."</lastmod><changefreq>hourly</changefreq><priority>1.0</priority></url>";
	}
	unset($rs);
	$googleStr .= "</urlset>";
	$slink = "../google.xml";
	fwrite(fopen($slink,"wb"),$googleStr);
	echo "生成完毕 <a target='_blank' href='../google.xml'><font color=red>浏览谷歌XML</font></a>  请通过<a href='http://www.google.com/webmasters/tools/' target='_blank'>http://www.google.com/webmasters/tools/</a>提交!<br>";
}

function makebaidu()
{
	global $db,$template,$cache;
	$allmakenum = be("all","ballmakenum");
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
	$rs = $db->query($sql);
	$baiduStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?><urlset>". "\n";
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$baiduStr .= "<url><loc>".$viewLink."</loc><lastmod>".getDatet("Y-m-d",$row["d_time"])."</lastmod>	<changefreq>always</changefreq><priority>1.0</priority></url>";
	}
	unset($rs);
	$baiduStr .= "</urlset>";
	$slink = "../baidu.xml";
	fwrite(fopen($slink,"wb"),$baiduStr);
	echo "生成完毕 <a target='_blank' href='../baidu.xml'><font color=red>浏览百度XML</font></a>  请通过<a href='http://news.baidu.com/newsop.html' target='_blank'>http://news.baidu.com/newsop.html</a>提交!<br>";
}

function makerss()
{
	global $db,$template,$cache;
	$allmakenum = be("all","rallmakenum");
	if (isN($allmakenum)){ $allmakenum=100;} else { $allmakenum = intval($allmakenum);}
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_time FROM {pre}vod WHERE d_type >0 ORDER BY d_time DESC limit 0,".$allmakenum;
	
	$rs = $db->query($sql);
	$rssStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?><rss version='2.0'><channel><title><![CDATA[".app_sitename."]]></title>	<description><![CDATA[".app_sitename."]]></description><link>http://".app_siteurl."</link><language>zh-cn</language><docs>".app_sitename."</docs><generator>Rss Powered By ".app_siteurl."</generator><image><url>http://".app_siteurl."/images/logo.gif</url></image>";
	
	while ($row = $db ->fetch_array($rs))
	{
		$typearr = getValueByArray($cache[0],"t_id" ,$row["d_type"]);
		$viewLink = "http://". app_siteurl . $template->getVodLink($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"]);
		$rssStr .= "<item><title><![CDATA[".$row["d_name"]."]]></title><link>".$viewLink."</link><author><![CDATA[".$row["d_starring"]."]]></author><pubDate>".$row["d_time"]."</pubDate><description><![CDATA[".strip_tags ( substring($row["d_content"], 150) )."]]></description></item>";
	}
	unset($rs);
	$rssStr .= "</channel></rss>";
	$slink = "../rss.xml";
	fwrite(fopen($slink,"wb"),$rssStr);
	echo "生成完毕<a target='_blank' href='../rss.xml'><font color=red>浏览RSS</font></a><br>"	;
}

function makediypage()
{
	$fname = be("all","fname");
	if (isN($fname)){ alertUrl ("请选择自定义页面","admin_makehtml.php"); }
	makediypagebyid ($fname);
}

function makediypageall()
{
	$filedir.= "../template/" . app_templatedir . "/ ". app_htmldir ."/" ;
    $fso=opendir($filedir);
	while ($file=readdir($fso)){
		$fullpath = "$filedir/$file";
		if(is_file($fullpath)){
			if (substring($file,6)== "label_"){
				makediypagebyid ($file);
			}
		}
	}
	closedir($fso);
	unset($fso);
	echo "生成全部自定义页面完毕";
}

function makediypagebyid($fname)
{
	global $template;
	$template->html = getFileByCache("label_".$fname,"../template/" . app_templatedir ."/" . app_htmldir . "/" .$fname);
	$template->mark();
	$template->ifEx();
	$template->run("other");
	$fname = replaceStr($fname,"label_","");
	$fname = replaceStr($fname,"$$","/");
	$slink = "../" . $fname;
	fwrite(fopen($slink,"wb"),$template->html);
	echo " 生成完毕 <a target='_blank' href='".$slink."'>".$fname."&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
}

function maketype()
{
	global $makeinterval,$psize,$flag;
	$typeids = be("arr","mtype");
	$num=be("get","num");
	if(isN($typeids)) { $typeids = be("get","mtype"); }
	if (isN($typeids) || $typeids=="0"){ alertUrl ("请选择分类...","admin_makehtml.php"); }
	$typearr = explode(",",$typeids);
	$typearrconunt = count($typearr);
	
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			alertUrl ("所选分类生成完毕","admin_makehtml.php");
		}
	}
	$typeid = trim($typearr[$num]);
	maketypebyid ($typeid);
	echo "<br>暂停". $makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=type&mtype=".$typeids."&flag=".$flag."&num=".($num + 1)."&psize=".$psize."';}</script>";
}

function maketypeall()
{
	global $flag,$makeinterval,$psize,$cache;
    $num=be("get","num");
	if ($flag=="art"){
		$typearr = $cache[1];
	}
	else{
		$typearr = $cache[0];
	}
	$typearrconunt = count($typearr);
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			alertUrl ("所有分类生成完毕","admin_makehtml.php");
		}
	}
	$typeid = trim( $typearr[$num]["t_id"] );
	maketypebyid ($typeid);
	echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=typeall&flag=".$flag."&num=".($num + 1)."&psize=".$psize."';}</script>";
}

function maketypebyid($typeid)
{
	global $flag,$stime,$psize,$makeinterval,$cache,$mac,$mac,$db,$template;
	
	if ($flag=="art"){
		$typearr = getValueByArray($cache[1],"t_id",$typeid);
		$mac["arttypeid"] = $typeid;
		$sql = "select count(a_id) from {pre}art where a_type IN (". $typearr["childids"].")";
	}
	else{
		$typearr = getValueByArray($cache[0],"t_id",$typeid);
		$mac["vodtypeid"] = $typeid;
		$sql = "select count(d_id) from {pre}vod where d_type IN (". $typearr["childids"] .") ";
	}
    $template->html = getFileByCache("template_" . $flag . "list_" . $typeid, root . "template/" . app_templatedir . "/" . app_htmldir . "/" . $typearr["t_template"]);
    if (isN($psize)){ $psize = $template->getPageListSizeByCache($flag . "page");}
    if (!isNum($psize)) { $psize = 10;}
    $tempLabelStr = $template->html;
    $nums = $db->getOne($sql);
    $pcount = ceil($nums/$psize);
    if ($nums == 0){ echo  $typeid . " 的分类没有数据</font><br>"; $pcount = 1; }
    
    for ($i=1;$i<=$pcount;$i++){
        $mac["page"] = $i;
        if ($flag=="art"){
        	$template->loadlist ("art", $typearr);
        }
        else{
        	$template->loadlist ("vod", $typearr);
        }
        $typeLink = $template->getPageLink($i);
        if (app_installdir != "/"){ $typeLink = replaceStr($typeLink, app_installdir, "../");} else { $typeLink = ".." . $typeLink;}
        $path = dirname($typeLink);
        if(!file_exists($path)){
			mkdir($path);
		}
        fwrite(fopen($typeLink,"wb"),$template->html);
    }
}


function makeview()
{
	$typeids = be("arr","mtype");
	$num=be("all","num");
	if(isN($typeids)) { $typeids = be("get","mtype"); }
	if (isN($typeids) || $typeids=="0"){ alertUrl ("请选择分类...","admin_makehtml.php");}
	$typearr = explode(",",$typeids);
	$typearrconunt = count($typearr);
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>=intval($typearrconunt)){
			alertUrl ("所选分类生成完毕","admin_makehtml.php");
		}
	}
	$typeid = trim($typearr[$num]);
	makeviewbytype ($typeid,$typeids,$typearrconunt);
}

function makeviewall()
{
	global $flag,$cache;
	$num = be("get","num");
	if ($flag=="art"){
		$typearr = $cache[1];
	}
	else{
		$typearr = $cache[0];
	}
	$typearrconunt = count($typearr);
	
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>= intval($typearrconunt)){
			alertUrl ("生成全部内容页完成","admin_makehtml.php"	);
		}
	}
	$typeid = $typearr[$num]["t_id"];
	makeviewbytype ($typeid,$ids,$typearrconunt);
}

function makeviewbytype($typeid,$ids,$allnum)
{
	global $action,$action2,$flag,$stime,$sql,$sql1,$makeinterval,$db,$cache;
	
	$macpage = be("all","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else {$num = intval($num);}
	if (isN($macpage)){ $macpage=1;} else {$macpage=intval($macpage);}
	
	if ($flag== "art"){
		$typearr = getValueByArray($cache[1],"t_id",$typeid);
		$sql = $sql ." and a_type=" .$typeid;
		$sql1 = $sql1 . " and a_type=" .$typeid;
	}
	else{
		$typearr = getValueByArray($cache[0],"t_id",$typeid);
		$sql = $sql ." and d_type=". $typeid;
		$sql1 = $sql1 . " and d_type=" .$typeid;
	}
	$sql .= " limit ".(100 *($macpage-1)).",100";
	
	$typename = $typearr["t_name"];
	$nums = $db->getOne($sql1);
	$pcount = ceil($nums/100);
	
	if ($nums==0){ 
		if (isN($action2) && ($num>$allnum)){
			echo "<font color='red'>ID为 ".$typeid." 的分类没有数据</font><br>";
		}
	    else{
			echo "恭喜<font color='red'>".$typename."</font>搞定<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$makeinterval."000);function makeNextPage(){location.href='?action=".$action."&mtype=".$ids."&flag=".$flag."&num=".($num + 1)."&page=".$macpage."&action2=".$action2."';}</script>";
			return;
		}
	}
	
	$rs = $db->query($sql);
	echo "正在开始生成栏目<font color='red'>".$typename."</font>的内容页,当前是第<font color='red'>".$macpage."</font>页,共<font color='red'>".$pcount."</font>页<br>";
	
	while ($row = $db ->fetch_array($rs)){
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	if ($macpage == $pcount || $nums<100){
		echo "<font color='red'>恭喜".$typename."搞定</font>";
	}
	
	echo  "页面生成时间: " . (execTime()-$stime)."秒 &nbsp;<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$makeinterval."000);function makeNextPage(){location.href='?action=".$action."&mtype=".$ids."&page=".($macpage + 1)."&action2=".$action2."&num=".$num."&flag=".$flag."';}</script>";
}

function makeviewpart()
{
	global $sql,$flag,$db,$cache;
	$startnum = be("all","startnum");
	$endnum = be("all","endnum");
	$psize=100;
	if (isN($startnum) && isN($endnum)){	showMsg ("至少需要输入第1个ID！"  ,"admin_makehtml.php") ;}
	if (!isNum($startnum) && !isNum($endnum)){ showMsg ("只能输入数字,请检查！"  ,"admin_makehtml.php") ;}
	if ($flag=="art"){
		$sql = $sql . " and a_id=" ;
	}
	else{
		$sql = $sql ." and d_id =";
	}
	
	
	$startnum = intval($startnum) ;
	if (isNum($endnum)){ $endnum = intval($endnum);} else { $endnum = $startnum ;$mflag=true;}
	
	if ($startnum > $endnum){
		if ($endnum+$psize <= $startnum ){ $tempnum1 = $endnum+$psize;$mflag=false;} else {$tempnum1 =$endnum;$mflag=true;}
		$tempnum2=$endnum;
		$endnum = $endnum + $psize;
	}
	else{
		if ($startnum+100 <= $endnum ){ $tempnum1 = $startnum+100 ;$mflag=false;} else {$tempnum1 =$startnum;$mflag=true;}
		$tempnum2=$startnum;
		$startnum = $startnum+$psize;
	}
	
	for($i=$tempnum2;$i<=$tempnum1;$i++){
		$tmpsql = $sql . $i;
		$row = $db->getRow($tmpsql);
		if($row){
			if($flag=="art"){ $typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);} else { $typearr = getValueByArray($cache[0],"t_id",$row["d_type"]) ;}
			makeviewbyrs ($row,$typearr);
		}
		unset($row);
	}
	
	if ($mflag){
		alertUrl ("生成内容页完成","admin_makehtml.php");
	}
	else{
		echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$makeinterval."000);function makeNextPage(){location.href='?action=viewpart&startnum=".$startnum."&endnum=".$endnum."&flag=".$flag."';}</script>";
	}
}

function makeviewpl()
{
	global $flag,$sql,$cache;
	$backurl = getReferer();
	$ids = be("arr","d_id");
	if (isN($ids)){ alertUrl ("您没有选择任何数据","");}
  	$idarr =  explode(",",$ids);
  	
  	if ($flag=="art"){
		$sql = $sql ." and a_id=" ;
	}
	else{
		$sql = $sql ." and d_id = ";
	}
	for ($i=0;$i<count($idarr);$i++){
		$tmpsql = $sql . $idarr[$i];
		$row =$db->getRow($tmpsql);
		if($row){
			if ($flag=="art"){
				$typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);
			}
			else{
				$typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);
			}
			makeviewbyrs ($row,$typearr);
		}
	}
	unset($rs);
	alertUrl ("生成内容页完成",$backurl);
}

function makeotherday()
{
	makegoogle();
	makebaidu();
	makerss();
	makeindex();
	makemap();
	echo "一键生成当天数据完毕!";
}

function maketypeday($sql)
{
	global $flag,$db;
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		$ids = $ids . $row["d_type"] . ",";
	}
	if (substring($ids, 1,strlen($ids)-1) == ","){  $ids = substring($ids, strlen($ids)-1 ,0);}
	
	echo "<br>准备生成当天数据的分类，请稍候...<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$makeinterval."000);function makeNextPage(){location.href='?action=type&flag=".$flag."&mtype=".$ids."';}</script>";
}

function makeviewday()
{
	global $action2,$flag,$sql,$stime,$makeinterval,$db,$cache;
	$macpage = be("get","page");
	$num = 	be("get","num");
	if (isN($num)){ $num = 0;} else { $num = intval($num);}
	
	if ($flag=="art"){
		$where = " and STR_TO_DATE(a_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	else{
		$where = " and STR_TO_DATE(d_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	$sql = $sql . $where;
	$sql1 = "SELECT DISTINCT d_type FROM {pre}vod WHERE 1=1 " . $where;
	$nums = $db->getOne($sql1. $where);
	$pcount=ceil($nums/100);
	
	if (isN($macpage)){ $macpage=1;} else { $macpage= intval($macpage);}
	
	if ($nums==0){ 
		echo "今天没有更新数据";
		exit;
	}
	//
	$rs = $db->query($sql);
	
	while($row = $db ->fetch_array($rs)){
		if ($flag=="art"){
			$id = $row["a_type"];
			$typearr = getValueByArray($cache[1],"t_id",$row["a_type"]);
		} 
		else {
			$id = $row["d_type"];
			$typearr = getValueByArray($cache[0],"t_id",$row["d_type"]);
		}
		
		if (strpos($ids,",".$id.",") <=0){
			$ids = $ids . $id . ",";
		}
		
		makeviewbyrs ($row,$typearr);
	}
	unset($rs);
	
	echo "ok恭喜今日数据搞定";
	
	$idsarr = explode(",",$ids);
	for ($i=0;$i<count($idsarr);$i++){
		if (!isN( $idsarr[$i] )){
			maketypebyid ( $idsarr[$i] );
		}
	}
	echo "ok恭喜今日分类搞定";
	
	makeotherday();
}

function makeviewbyrs($rs,$typearr)
{
	global $flag,$db,$template,$mac;
	$tname = $typearr["t_name"];
	$tpath = $typearr["t_enname"];
	
	if ($flag=="vod"){
		$mac["vodtypeid"] = $rs["d_type"];
		$viewId = $rs["d_id"];
		$strName= $rs["d_name"];
		if (app_playtype == 0){
			$viewLink = $template->getVodLink($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_type"],$tname,$tpath);
			
			if (app_installdir != "/"){ $viewLink = replaceStr($viewLink,app_installdir,"../");} else { $viewLink = ".." . $viewLink;}
			if (substring($viewLink,1,strlen(viewLink)) =="/"){ $viewLink = $viewLink . "index." . app_vodsuffix;}
			$template->loadvod ($rs,$typearr,"view");
			$template->run ("vod");
			$path = dirname($viewLink);
	        if(!file_exists($path)){
				mkdir($path);
			}
			fwrite(fopen($viewLink,"wb"),$template->html);
		}
		
		$template->html = "";
		if (app_makeplay ==1){
			$template->loadvod ($rs,$typearr,"play");
			$template->run ("vod");
			$playLink = $template->getVodPlayUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_type"],$tname,$tpath,1,1);
			$playLink = substring($playLink,strpos($playLink,"?")-1);
			if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." .  $playLink;}
			if (substring($playLink,1,strlen($playLink)-1)=="/"){ $playLink = $playLink . "index." . app_vodsuffix;}
			$path = dirname($playLink);
	        if(!file_exists($path)){
				mkdir($path);
			}
			fwrite(fopen($playLink,"wb"),$template->html);
		}
		elseif (app_makeplay ==2){
			$template->loadvod ($rs,$typearr,"play");
			$tmpHtml = $template->html;
			$playarr1 = explode("$$$",$rs["d_playurl"]);
			$playarr2 = explode("$$$",$rs["d_playfrom"]);
			$playarr3 = explode("$$$",$rs["d_playserver"]);
			
			for ($i=0;$i<count($playarr1);$i++){
				$sserver = $playarr3[$i]; $from = $playarr2[$i]; $url= $playarr1[$i]; 
				$urlarr = explode("#",$url);
				$template->html = $tmpHtml;
				
				for ($j=0;$j<count($urlarr);$j++){
					$urlone = explode("$",$urlarr[$j]);
					if (count($urlone)==2){
						$urlname = $urlone[0];
						$urlpath = $urlone[1];
					}
					else{
						$urlname = "第" . $j + 1 . "集";
						$urlpath = $urlone[0];
					}
					$playLink = $template->getVodPlayUrl($rs["d_id"],$rs["d_name"],$rs["d_enname"],$rs["d_type"],$tname,$tpath, ($i+1),$j+1);
					if (app_playtype == 1 && $i==0 && $j==0){ $viewLink = $playLink . "?" .$rs["d_id"].",1,0." . app_htmlSuffix;}
					if (app_installdir != "/"){ $playLink = replaceStr($playLink,app_installdir,"../");} else { $playLink = ".." .  $playLink;}
					if (substring($playLink,1,strlen($playLink)-1)=="/"){ $playLink = $playLink . "index." . app_vodsuffix;;}
					$template->html = $tmpHtml;
					$template->html = replaceStr($template->html,"[playinfo:num]",($j+1));
					$template->html = replaceStr($template->html, "[playinfo:name]", $urlname);
					$template->html = replaceStr($template->html, "[playinfo:urlpath]", $urlpath);
					$template->run ("vod");
					$path = dirname($playLink);
			        if(!file_exists($path)){
						mkdir($path);
					}
					fwrite(fopen($playLink,"wb"),$template->html);
				}
			}
		}
	}
	else{
		$mac["arttypeid"] = $rs["a_type"];
		$viewId = $rs["a_id"];
		$strName= $rs["a_title"];
		$viewLink = $template->getArtLink($rs["a_id"],$rs["a_title"],$rs["a_entitle"],$rs["a_type"],$tname,$tpath);
		if (app_installdir != "/"){ $viewLink = replaceStr($viewLink,app_installdir,"../");} else { $viewLink = ".." . $viewLink;}
		$template->loadart ($rs,$typearr);
		$template->run ("art");
		$path = dirname($viewLink);
	    if(!file_exists($path)){
			mkdir($path);
		}
		fwrite(fopen($viewLink,"wb"),$template->html);
	}
	
	//echo $strName . " <a target='_blank' href='".$viewLink."'>&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
}

function maketopicindex()
{
	global $flag,$db,$mac,$mac,$template;
	$t1 = root."template/".app_templatedir."/".app_htmldir."/".$flag."topic.html";
	$template->html =  getFileByCache("template_".$flag."topic",$t1);
	$tempLabelStr = $template->html;
	
	$psize = $template->getPageListSizeByCache($flag."topicpage");
	if (isN($psize)){ $psize = 10;}
	$sql = "select t_id from {pre}".$flag."_topic";
	$nums = $db->getOne( "select count(t_id) from {pre}".$flag."_topic" );
	$pcount=ceil($nums/$psize);
	if ($nums==0){ echo "<font color='red'>没有专题数据!</font><br>" ; $pcount =1; }
	echo "正在开始生成专题首页...";
	
	for ($i=1;$i<=$pcount;$i++){
		$mac["page"] = $i;
		$template->html = $tempLabelStr;
		$template->topicpagelist();
		$template->mark();
		$template->ifEx();
		$template->run ("other");
		$topicLink = $template->getPageLink($i);
		if (app_installdir != "/"){	$topicLink = replaceStr($topicLink,app_installdir,"../");} else	{ $topicLink = ".." .  $topicLink;}
		$path = dirname($topicLink);
	    if(!file_exists($path)){
			mkdir($path);
		}
		fwrite(fopen($topicLink,"wb"),$template->html);
		echo " 生成完毕 <a target='_blank' href='".$topicLink."'>&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
	}
}

function maketopic()
{
	$topic=  be("arr","mtopic");
	if(isN($topic)){ $topic = be("get","mtopic"); }
	if (isN($topic) || $topic==0){ alertUrl ("请选择专题...","admin_makehtml.php");}
	makeTopicById ($topic);
}

function maketopicall()
{
	global $flag,$makeinterval,$cache;
    $num=be("get","num");
	if ($flag=="art"){
		$topicarr = $cache[3];
	}
	else{
		$topicarr = $cache[2];
	}
	$topicarrconunt = count($topicarr);
	
	if (isN($num)){
		$num = 0;
	}
	else{
		if (intval($num)>intval($topicarrconunt)-1){
			alertUrl ("所有专题生成完毕","admin_makehtml.php");
		}
	}
	maketopicbyid ($topicarr[$num]["t_id"]);
	echo "<br>暂停".$makeinterval."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNexttype();\",".$makeinterval."000);function makeNexttype(){location.href='?action=topicall&flag=".$flag."&num=".($num + 1) ."';}</script>";
}

function makeTopicById($topicid)
{
	global $flag,$db,$mac,$mac,$cache,$template;
	if ($flag =="art"){
		$mac["arttopicid"] = $topicid;
		$typearr = getValueByArray($cache[3],"t_id",$topicid);
		$sql = "select  a_id from {pre}art where a_topic = ".$topicid."";
	}
	else{
		$mac["vodtopicid"] = $topicid;
		$typearr = getValueByArray($cache[2],"t_id",$topicid);
		$sql = "select  d_id from {pre}vod where d_topic = ".$topicid."";
	}
	$psize = $template->getPageListSizeByCache($flag."page");
	if (isN($psize)){ $psize = 10;}
	$nums = $db->getOne($sql);
	$pcount=ceil($nums/$psize);
	if ($nums==0){ echo "<font color='red'>ID为 ".$topicid." 的专题没有数据</font><br>" ; $pcount =1;}
	echo "正在开始生成专题<font color='red'>" . $typearr["t_name"] . "</font>的列表<br>";
	for ($i=1;$i<=$pcount;$i++){
		$mac["page"] = $i;
		$template->loadtopic ($flag,$typearr);
		$topicLink = $template->getPageLink($i);
		if (app_installdir != "/" ){ $topicLink = replaceStr($topicLink,app_installdir,"../");} else { $topicLink = ".." .$topicLink;}
		$path = dirname($topicLink);
	    if(!file_exists($path)){
			mkdir($path);
		}
		fwrite(fopen($topicLink,"wb"),$template->html);
		echo $typearr["t_name"] . " 生成完毕 <a target='_blank' href='".$topicLink."'>".$topicLink."&nbsp;&nbsp;<font color=red>浏览</font></a><br>";
	}
}
?>