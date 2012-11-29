<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("鏂囩珷鑷畾涔夐噰闆嗛」鐩鐞�);

switch($action)
{
	case "add" :
	case "edit" : edit();break;
	case "editstep1" : editstep1();break;
	case "editstep2" :editstep2();break;
	case "lastsave" : lastsave();break;
	case "saveok" : saveok();break;
	case "del" : del();break;
	case "copy" : copynew();break;
	case "delall" : delall();break;
	case "export" : export(); break;
	case "upexp" : upexp(); break;
	case "upexpsave" : upexpsave(); break;
	case "breakpoint" : breakpoint(); break;
	default :  clearSessionart();	main();break;
}

function export()
{
	global $db;
	$p_id= be("get","p_id");
	$fields = $db->getTableFields(app_dbname,"{pre}cj_art_projects");
	$colsnum = mysql_num_fields($fields);
	$row = $db->getRow("select * from {pre}cj_art_projects where p_id='".$p_id."'");
	$result="";
	$fileName= $row["p_name"];
	for ($i = 0; $i < $colsnum; $i++) {
		$colname = mysql_field_name($fields, $i);
		$result .= "<".$colname.">".$row[$colname]."</".$colname.">"."\r\n";
	} 
	unset($row);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", $fileName) .".txt";
	fwrite(fopen($filePath,"wb"),$result);
	redirect ("collect_down.php?file=".$fileName);
}

function upexp()
{
?>
<form enctype="multipart/form-data" action="?action=upexpsave" method="post">
<table width="96%" border=0 align=center cellpadding="4" cellSpacing=0 class=tb >
  	<tr>
	<td colspan="2" align="center">
	涓婁紶鏂囩珷閲囬泦瑙勫垯
	<input type="file" id="file1" name="file1">
	<input type="submit" name="submit" value="寮�瀵煎叆">
	</td>
	</tr>
</table>
</form>
<?php
}

function upexpsave()
{
	global $db;
	$str = file_get_contents($_FILES['file1']['tmp_name']);
	$labelRule = buildregx("<(p_[\s\S]*?)>(.*?)</(p_[\s\S]*?)>","is");
	preg_match_all($labelRule,$str,$iar);
	$arlen=count($iar[1]);
	$in1="";
	$in2="";
	$rc=false;
	for($m=0;$m<$arlen;$m++){
		if($iar[1][$m] !="p_id"){
			if ($rc){  $in1 .= ","; $in2 .= ","; }
		 	$in1 .= $iar[1][$m] ;
		 	$in2 .= "'". replaceStr($iar[2][$m],"'","\'") . "'";
		 	$rc=true;
		}
	}
	$sql = "insert into {pre}cj_art_projects (".$in1.") values(".$in2.")";
	
	$status = $db->query($sql);
	if($status){
		showmsg ("瀵煎叆瑙勫垯鎴愬姛!","collect_art_manage.php");
	}
	else{
		alert("瀵煎叆澶辫触锛岃妫�煡瑙勫垯鏄惁姝ｇ‘!");
	}
}

function breakpoint()
{
	echo gBreakpoint("../../upload/artbreakpoint") . "姝ｅ湪杞藉叆鏂偣缁紶鏁版嵁锛岃绋嶅悗......";
	exit;
}

function saveok()
{
	showmsg ("閲囬泦鏍忕洰娣诲姞鎴愬姛","collect_art_manage.php");
}

function copynew()
{
	global $db;
	$p_id=be("get","p_id");
    $sql = "INSERT INTO  {pre}cj_art_projects(p_name, p_coding, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_authortype, p_authorstart, p_authorend, p_titletype, p_titlestart, p_titleend, p_timestart, p_timeend, p_typestart, p_typeend, p_contentstart, p_contentend, p_hitsstart, p_hitsend, p_cpagetype, p_cpagecodestart, p_cpagecodeend, p_cpagestart, p_cpageend) SELECT p_name, p_coding, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_authortype, p_authorstart, p_authorend, p_titletype, p_titlestart, p_titleend, p_timestart, p_timeend, p_typestart, p_typeend, p_contentstart, p_contentend, p_hitsstart, p_hitsend, p_cpagetype, p_cpagecodestart, p_cpagecodeend, p_cpagestart, p_cpageend FROM  {pre}cj_art_projects WHERE p_id =" .$p_id;
	$db->query($sql);
    showmsg ("澶嶅埗閲囬泦鏍忕洰鎴愬姛锛�,"collect_art_manage.php");
}

function del()
{
	global $db;
	$p_id=be("get","p_id");
    $sql= "delete from {pre}cj_art_projects WHERE p_id=".$p_id;
    $db->query($sql);
    showmsg ("閲囬泦椤圭洰鍒犻櫎鎴愬姛锛�,"collect_art_manage.php");
}

function delall()
{
	global $db;
    $ids=be("arr","p_id");
    if (!isN($ids)){
	  $db->query("delete from {pre}cj_art_projects WHERE p_id in (".$ids.")");
	}
    showmsg ("閲囬泦椤圭洰鍒犻櫎鎴愬姛锛�,"collect_art_manage.php");
}

function edit()
{
	global $db;
	$p_id=be("get","p_id");
	if(!isN($p_id)){
		$sql="select * from {pre}cj_art_projects where p_id = ".$p_id;
		$row = $db->getRow($sql);
		$p_name = $row["p_name"];
		$p_coding = $row["p_coding"];
		$p_pagetype = $row["p_pagetype"];
		$p_url = $row["p_url"];
		$p_pagebatchurl = $row["p_pagebatchurl"];
		$p_manualurl = $row["p_manualurl"];
		$p_pagebatchid1 = $row["p_pagebatchid1"];
		$p_pagebatchid2 = $row["p_pagebatchid2"];
		$p_script = $row["p_script"];
		$p_showtype = $row["p_showtype"];
		$p_collecorder = $row["p_collecorder"];
		$p_savefiles = $row["p_savefiles"];
		$P_IntoLib = $row["P_IntoLib"];
		$p_ontime = $row["p_ontime"];
		$p_hitsstart = $row["p_hitsstart"];
		$p_hitsend = $row["p_hitsend"];
	}
	else{
		$p_pagetype=0;
	}
?>
<form action="?action=editstep1" method="post">
<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
<table class="tb">
    <tr>
      <td width="20%">椤圭洰鍚嶇О锛�/td>
      <td>
      	<INPUT id="p_name" name="p_name" size="50" value="<?php echo $p_name?>" >
	  </td>
    </tr>
     
    <tr>
    <td>閲囬泦杩囩▼鏂瑰紡锛�/td>
	 <td>
	  <input name="p_showtype" type="radio" value="0" <?php if ($p_showtype==0) { echo "checked";} ?>>鏄剧ず閲囬泦涓�釜鍒楄〃 &nbsp;&nbsp;
	  <input name="p_showtype" type="radio" value="1" <?php if ($p_showtype==1) { echo "checked";} ?>>鏄剧ず閲囬泦涓�潯鏁版嵁 &nbsp;&nbsp;
	</td>
	</tr>
	<tr>
	 <td>閲囬泦鍙傛暟锛�/td>
	 <td>
	<input id="p_collecorder" name="p_collecorder" type="checkbox" value="1" <?php if ($p_collecorder==1){ echo "checked";} ?>>鍊掑簭閲囬泦 &nbsp;&nbsp; 
	</td>
	</tr>
	<tr>
	<td>鐩爣缃戦〉缂栫爜锛�/td>
	<td>&nbsp;
	<select id="p_coding" name="p_coding" size="1">
	<option value="GB2312" <?php if ($p_coding=="GB2312") { echo "selected";} ?>>GB2312</option>
	<option value="UTF-8" <?php if ($p_coding=="UTF-8") { echo "selected";} ?>>UTF-8</option>
	<option value="BIG5" <?php if ($p_coding=="BIG5") { echo "selected";} ?>>BIG5</option>
	</select>
	</td>
    </tr>
    <tr>
	<td>鍒嗛〉璁剧疆锛�/td>
	<td>
	<input type="radio" value="0" id="p_pagetype" name="p_pagetype" checked="checked" onClick="ChangeCutPara(0);" <?php if ($p_pagetype==0) { echo "checked";} ?>>
涓嶅垎椤�nbsp;&nbsp;
<input type="radio" value="1" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(1);" <?php if ($p_pagetype==1 ) { echo "checked";} ?>>
鎵归噺鍒嗛〉&nbsp;
<input type="radio" value="2" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(2);" <?php if ($p_pagetype==2 ) { echo "checked";} ?>>
鎵嬪姩鍒嗛〉&nbsp;
<input type="radio" value="3" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(3);" <?php if ($p_pagetype==3 ) { echo "checked";} ?>> 
鎸塈D鐩存帴閲囬泦鍐呭
	</td>
    </tr>
	<tr id="IndexCutPage" >
	<td>閲囬泦鍦板潃锛�/td>
	<td>
	<INPUT id="p_url" name="p_url" size="80" value="<?php echo $p_url?>">
	</td>
	</tr>
<tr id="HandCutPage" style="display:none">
 <td><span id="CutPageName"></span>锛�/td>
 <td><input name="p_pagebatchurl" type="text" value="<?php echo $p_pagebatchurl?>" size="80">
 鍒嗛〉浠ｇ爜 <font color=red>{ID}</font><br>
鏍囧噯鏍煎紡锛欻ttp://www.xxxxx.com/list/list_{ID}.html<br>
閲囬泦鑼冨洿锛�
<input name="p_pagebatchid1" type="text" value="<?php echo $p_pagebatchid1?>" size="4">
 To 
<input name="p_pagebatchid2" type="text" value="<?php echo $p_pagebatchid2?>" size="4">
渚嬪锛� - 9</td>
 </tr>
 <tr id="ListContent" style="display:none">
 <td>鎵嬪姩鍒嗛〉锛�/td>
 <td><textarea name="p_manualurl" cols="60" rows="3"><?php echo $p_manualurl?></textarea></td>
 </tr>
 <tr>
 <td>闅忔満浜烘皵锛�/td>
 <td>
  浠�nbsp;<input id="p_hitsstart" name="p_hitsstart" type="text" size="4" value="<?php echo $p_hitsstart?>"> 
  鍒�&nbsp; <input id="p_hitsend" name="p_hitsend" type="text" size="4" value="<?php echo $p_hitsend?>"> 
  涔嬮棿 (鍓嶅皬鍚庡ぇ)
</td>
 </tr>
 <tr>
 <td>杩囨护閫夐」锛�/td>
 <td height=16 >
 <input name="p_script[]" type="checkbox" value="1" <?php if (($p_script & 1)>0) { echo "checked";} ?>>
Iframe
<input name="p_script[]" type="checkbox" value="2" <?php if (($p_script & 2)>0) { echo "checked";} ?>>
Object
<input name="p_script[]" type="checkbox" value="4" <?php if (($p_script & 4)>0) { echo "checked";} ?>>
Script
<input name="p_script[]" type="checkbox" value="8" <?php if (($p_script & 8)>0) { echo "checked";} ?>>
Div
<input name="p_script[]" type="checkbox" value="16" <?php if (($p_script & 16)>0) { echo "checked";} ?>>
Class
<input name="p_script[]" type="checkbox" value="32" <?php if (($p_script & 32)>0) { echo "checked";} ?>>
Table<br>
&nbsp;&nbsp; <br>
<input name="p_script[]" type="checkbox" value="64" <?php if (($p_script & 64)>0) { echo "checked";} ?>>
Span
<input name="p_script[]" type="checkbox" value="128" <?php if (($p_script & 128)>0) { echo "checked";} ?>>
Img
<input name="p_script[]" type="checkbox" value="256" <?php if (($p_script & 256)>0) { echo "checked";} ?>>
Font
<input name="p_script[]" type="checkbox" value="512" <?php if (($p_script & 512)>0) { echo "checked";} ?>>
A
<input name="p_script[]" type="checkbox" value="1024" <?php if (($p_script & 1024)>0) { echo "checked";} ?>>
Tr
<input name="p_script[]" type="checkbox" value="2048" <?php if (($p_script & 2048)>0) { echo "checked";} ?>>
Td
<input name="p_script[]" type="checkbox" value="4096" <?php if (($p_script & 4096)>0) { echo "checked";} ?>>
Html
	</td>
	</tr>
	<tr>
	<td colspan="2"><input type="submit" class="btn" name="Submit" value="涓嬩竴姝�></td>
	</tr>
</table>
</form>
<script language="JavaScript">
function ChangeCutPara(Flag)
{
	switch (Flag)
	{
	case 0 :
	$("#IndexCutPage").css("display","");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","none");
	break;
	case 1 :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","");
	$("#ListContent").css("display","none");
	$("#CutPageName").html("鎵归噺鍒嗛〉");
	break;
	case 2 :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","");
	break;
	case 3 :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","");
	$("#ListContent").css("display","none");
	$("#CutPageName").html("鎸塈D閲囬泦鍐呭椤�);
	break;
	default :
	$("#IndexCutPage").css("display","none");
	$("#HandCutPage").css("display","none");
	$("#ListContent").css("display","none");
	break;
	}
}
ChangeCutPara(<?php echo $p_pagetype?>);
</script>
<?php
}

function editstep1()
{
	global $db;
	$p_id = be("all","p_id");
	$p_name = be("post","p_name") ; $p_coding = be("post","p_coding") ;
	$p_pagetype = be("post","p_pagetype") ; $p_url = be("post","p_url");
	$p_pagebatchurl = be("post","p_pagebatchurl") ; $p_manualurl = be("post","p_manualurl");
	$p_pagebatchid1 = be("post","p_pagebatchid1") ; $p_pagebatchid2 = be("post","p_pagebatchid2");
	
	$p_script = be("arr","p_script");
	$sarr =explode(",",$p_script);
	$p_script = 0;
	foreach($sarr as $s){
		if (!isN($s)){
			$p_script = $p_script | intval($s);
		}
	}
	
	$p_collecorder = be("post","p_collecorder") ; $p_savefiles = be("post","p_savefiles");
	$p_ontime = be("post","p_ontime");  $p_hitsstart = be("post","p_hitsstart");
	$p_hitsend = be("post","p_hitsend"); $p_showtype = be("post","p_showtype");
	
	if (isN($p_collecorder)) { $p_collecorder = 0;}
	if (isN($p_savefiles)) { $p_savefiles = 0;}
	if (isN($P_IntoLib)) { $P_IntoLib = 0;}
	if (isN($p_ontime)) { $p_ontime = 0;}
	if (isN($p_server)) { $p_server = 0;}
	if(!isNum($p_pagebatchid1)){$p_pagebatchid1=1;}
	if(!isNum($p_pagebatchid2)){$p_pagebatchid2=1;}
	
	switch($p_pagetype)
	{
		case 0:
			$strlisturl = $p_url;break;
		case 1 or 3:
			$strlisturl = replaceStr($p_pagebatchurl,"{ID}",$p_pagebatchid1);
			break;
		case 2:
		  if (strpos($p_manualurl,"|")) {
		  	$strlisturl=substring($p_manualurl,strpos($p_manualurl,"|")-1);
			}
		  Else{
		  	$strlisturl = $p_manualurl;
			}
			break;
	}
	
	$strListCode = getPage($strlisturl,$p_coding);
	
	if ($strListCode ==false){
		errmsg ("閲囬泦绯荤粺鎻愮ず","<li>鍦ㄨ幏鍙�".$strlisturl."缃戦〉婧愮爜鏃跺彂鐢熼敊璇紒</li>");
	}
	$_SESSION["strListCodeart"] = $strListCode;
	
	if(isN($p_id)){
		$sql="INSERT {pre}cj_art_projects(p_name,p_coding,p_pagetype,p_url,p_pagebatchurl,p_manualurl,p_pagebatchid1,p_pagebatchid2,p_script,p_showtype,p_collecorder,p_savefiles,p_ontime,p_hitsstart,p_hitsend,p_time)  values ('".$p_name."','".$p_coding."','".$p_pagetype."','".$p_url."','".$p_pagebatchurl."','".$p_manualurl."','".$p_pagebatchid1."','".$p_pagebatchid2."','".$p_script."','".$p_showtype."','".$p_collecorder."','".$p_savefiles."','".$p_ontime."','".$p_hitsstart."','".$p_hitsend."','".date('Y-m-d H:i:s',time())."')";
	}
	else{
		$sql="update {pre}cj_art_projects set p_name='".$p_name."',p_coding='".$p_coding."',p_pagetype='".$p_pagetype."',p_url='".$p_url."',p_pagebatchurl='".$p_pagebatchurl."',p_manualurl='".$p_manualurl."',p_pagebatchid1='".$p_pagebatchid1."',p_pagebatchid2='".$p_pagebatchid2."',p_script='".$p_script."',p_showtype='".$p_showtype."',p_collecorder='".$p_collecorder."',p_savefiles='".$p_savefiles."',p_ontime='".$p_ontime."',p_hitsstart='".$p_hitsstart."',p_hitsend='".$p_hitsend."' where p_id =" .$p_id;
	}
	$db->query($sql);
	if( isN($p_id) ) {
		$p_id = $db->insert_id();
	}
	$sql="select * from {pre}cj_art_projects where p_id = ".$p_id;
	$row = $db->getRow($sql);
	
	$p_authortype = $row["p_authortype"];
	$p_authorstart = $row["p_authorstart"];
	$p_authorend = $row["p_authorend"];
	$p_titletype = $row["p_titletype"];
	$p_titlestart = $row["p_titlestart"];
	$p_titleend = $row["p_titleend"];
	$p_listcodestart = $row["p_listcodestart"];
	$p_listcodeend = $row["p_listcodeend"];
	$p_listlinkstart = $row["p_listlinkstart"];
	$p_listlinkend = $row["p_listlinkend"];
	
	if ($p_pagetype == 3){
	 header( "Location:collect_art_manage.php?action=editstep2&p_id=".$p_id."&p_coding=".$p_coding."&listurl=".$strlisturl);
	}
?>
<form action="?action=editstep2" method="post">
	<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
  	<INPUT id="listurl" name="listurl" type="hidden" value="<?php echo $strlisturl?>" >
  	<INPUT id="p_coding" name="p_coding" type="hidden" value="<?php echo $p_coding?>" >
<table class="tb">
	<tr>
	<td  colspan="2" align="center">鍒楄〃杩炴帴璁剧疆 褰撳墠鑾峰彇鐨勬祴璇曞湴鍧�細<?php echo $strlisturl ?> </td>
  	</tr>
    <tr>
	<td>鍒楄〃寮�浠ｇ爜锛�/td>
	<td>
	<span onClick="if(document.Form.p_listcodestart.rows>2)document.Form.p_listcodestart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_listcodestart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_listcodestart" cols="70" rows="3"><?php echo $p_listcodestart?></textarea>
	</td>
	</tr>
	<tr>
	<td>鍒楄〃缁撴潫浠ｇ爜锛�/td>
	<td>
<span onClick="if(document.Form.p_listcodeend.rows>2)document.Form.p_listcodeend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_listcodeend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_listcodeend" cols="70" rows="3"><?php echo $p_listcodeend?></textarea>
	  </td>
    </tr>
    <tr>
      <td>閾炬帴寮�浠ｇ爜锛�/td>
      <td>
 <span onClick="if(document.Form.p_listlinkstart.rows>2)document.Form.p_listlinkstart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_listlinkstart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_listlinkstart" cols="70" rows="3"><?php echo $p_listlinkstart?></textarea>
	  </td>
    </tr>
    <tr>
      <td>閾炬帴缁撴潫浠ｇ爜锛�/td>
      <td>
<span onClick="if(document.Form.p_listlinkend.rows>2)document.Form.p_listlinkend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_listlinkend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_listlinkend" cols="70" rows="3"><?php echo $p_listlinkend?></textarea>
	  </td>
    </tr>
    <tr>
      <td>鍒楄〃閲囬泦鍚嶇О锛�/td>
      <td>
      	<input type="radio" value="0" id="p_titletype" name="p_titletype" <?php if ($p_titletype==0) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara(0,'trp_titlestart','trp_titleend');">
鍚�nbsp;&nbsp;
<input type="radio" value="1" id="p_titletype" name="p_titletype" <?php if ($p_titletype==1) { echo "checked=\"checked\"";}?> onClick="ChangeCutPara(1,'trp_titlestart','trp_titleend');">
鏄�nbsp;
	  </td>
    </tr>
    
    <tr id="trp_titlestart" style="display:none">
      <td>鍚嶇О寮�浠ｇ爜锛�/td>
      <td>
 <span onClick="if(document.Form.p_titlestart.rows>2)document.Form.p_titlestart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_titlestart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>
	  </td>
    </tr>
    <tr id="trp_titleend" style="display:none">
      <td>鍚嶇О缁撴潫浠ｇ爜锛�/td>
      <td>
<span onClick="if(document.Form.p_titleend.rows>2)document.Form.p_titleend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_titleend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>
	  </td>
    </tr>
    <tr>
      <td>鍒楄〃閲囬泦浣滆�锛�/td>
      <td>
      	<input type="radio" value="0" id="p_authortype" name="p_authortype" <?php if ($p_authortype==0) {echo " checked=\"checked\"";}?> onClick="ChangeCutPara(0,'trp_authorstart','trp_authorend');">
鍚�nbsp;&nbsp;
<input type="radio" value="1" id="p_authortype" name="p_authortype" <?php if ($p_authortype==1) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara(1,'trp_authorstart','trp_authorend');">
鏄�nbsp;
	  </td>
    </tr>
   
    <tr id="trp_authorstart" style="display:none">
      <td>浣滆�寮�浠ｇ爜锛�/td>
      <td>
 <span onClick="if(document.Form.p_authorstart.rows>2)document.Form.p_authorstart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_authorstart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_authorstart" cols="70" rows="3"><?php echo $p_authorstart?></textarea>
	  </td>
    </tr>
    <tr id="trp_authorend" style="display:none">
      <td>浣滆�缁撴潫浠ｇ爜锛�/td>
      <td>
<span onClick="if(document.Form.p_authorend.rows>2)document.Form.p_authorend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_authorend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_authorend" cols="70" rows="3"><?php echo $p_authorend?></textarea>
	</td>
	</tr>
	<tr>
	<td  colspan="2"  ><input type="submit" class="btn" id="btnNext" name="btnNext" value="涓嬩竴姝�></td>
	</tr>
</table>
</form>
<script language="JavaScript">
function ChangeCutPara(flag,element1,element2)
{
	switch (flag)
	{
	case 0 :
	$("#"+element1).css("display","none");
	$("#"+element2).css("display","none");
	break;
	case 1 :
	$("#"+element1).css("display","");
	$("#"+element2).css("display","");
	break;
	}
}
ChangeCutPara(<?php echo $p_authortype?>,"trp_authorstart","trp_authorend");
ChangeCutPara(<?php echo $p_titletype?>,"trp_titlestart","trp_titleend");
</script>
<?php
}

function editstep2()
{
	global $db;
	$p_id = be("all","p_id");
	$strlisturl = be("post","listurl");
	$p_coding = be("post","p_coding");
	$p_authortype = be("post","p_authortype");
	$p_authorstart = be("post","p_authorstart");
	$p_authorend = be("post","p_authorend");
	$p_titletype = be("post","p_titletype");
	$p_pagetype = be("post","p_pagetype");
	$p_listcodestart = be("post","p_listcodestart");
	$p_listcodeend = be("post","p_listcodeend");
	$p_titlestart = be("post","p_titlestart");
	$p_titleend = be("post","p_titleend");
	$p_listlinkstart = be("post","p_listlinkstart");
	$p_listlinkend = be("post","p_listlinkend");
	
	if( isN($_SESSION["strListCodeart"] )) {
		$strListCode = getPage($strlisturl,$p_coding);
		$_SESSION["strListCodeart"] = $strListCode;
	}
	else{
		$strListCode = $_SESSION["strListCodeart"];
	}
	
	if (isN($p_authortype)) { $p_authortype = 0;}
	if (isN($p_titletype)) { $p_titletype = 0;}
	
	if (isN($p_pagetype)){
		$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
		$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
		
		$_SESSION["strListCodeCutart"]=$strListCodeCut;
		
		if ($p_authortype == 1){
			$starringarr = getArray($strListCodeCut,$p_authorstart,$p_authorend);
		}
		if ($p_titletype == 1){
			$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
		}
		
		switch ($linkarrcode)
		{
			Case False:
				errmsg ("閲囬泦鎻愮ず","<li>鍦ㄨ幏鍙栭摼鎺ュ垪琛ㄦ椂鍑洪敊銆�/li>");
			default:
				$_SESSION["linkarrcodeart"] = $linkarrcode;
				$linkarr = explode("{Array}",$linkarrcode);
				$UrlTest = $linkarr[0];
				$UrlTest = definiteUrl($UrlTest,$strlisturl);
				$linkcode = getPage($UrlTest,$p_coding);
				break;
		}
		if ($p_titletype == 1 ){
			switch ($titlearrcode)
			{
			Case False:
				errmsg ("閲囬泦鎻愮ず","<li>鍦ㄨ幏鍙栧悕绉版椂鍑洪敊銆�/li>") ;break;
			default:
				$titlearr = explode("{Array}",$titlearrcode);
				$titlecode = $titlearr[0];
				break;
			}
		}
		if ($p_authortype == 1){
			switch ($starringarrcode)
			{
			Case False:
				errmsg ("閲囬泦鎻愮ず","<li>鍦ㄨ幏鍙栦富婕旀椂鍑洪敊銆�/li>");break;
			default:
				$starringarr = explode("{Array}",$starringarrcode);
				$starringcode = $starringarr[0];
				break;
			}
		}
	}
	
	$sql="select * from {pre}cj_art_projects Where p_id=".$p_id;
	$row = $db->getRow($sql);
	
	$strSet= "";
	if ($p_pagetype ==3 || $p_authortype == 0){ 
		$p_authorstart = 	$row["p_authorstart"];
		$p_authorend = $row["p_authorend"];
	}
	else{
		$strSet.="p_authorstart='".$p_authorstart."',p_authorend='".$p_authorend."',";
	}
	if ($p_pagetype ==3 || $p_titletype ==0){
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
	}
	else{
		$strSet.= "p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',";
	}
	
	$strSet.= "p_listcodestart='".$p_listcodestart."',p_listcodeend='".$p_listcodeend."',p_listlinkstart='".$p_listlinkstart."',p_listlinkend='".$p_listlinkend."',p_authortype='".$p_authortype."',p_titletype='".$p_titletype."' ";
   $sql = "update {pre}cj_art_projects set " .$strSet ." where p_id= ". $p_id;
	
   $db->query($sql);
	
	$p_timestart = $row["p_timestart"];
	$p_timeend = $row["p_timeend"];
	$p_classtype = $row["p_classtype"];
	$p_collect_type = $row["p_collect_type"];
	$p_typestart = $row["p_typestart"];
	$p_typeend = $row["p_typeend"];
	$p_contentstart = $row["p_contentstart"];
	$p_contentend = $row["p_contentend"];

?>
<form name="Form" action="?action=lastsave" method="post">
	<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
    <INPUT id="p_pagetype" name="p_pagetype" type="hidden" value="<?php echo $p_pagetype?>" >
  	<INPUT id="listurl" name="listurl" type="hidden" value="<?php echo $strlisturl?>" >
  	<INPUT id="p_coding" name="p_coding" type="hidden" value="<?php echo $p_coding?>" >
    <INPUT id="p_titletype" name="p_titletype" type="hidden" value="<?php echo $p_titletype?>" >
    <INPUT id="p_authortype" name="p_authortype" type="hidden" value="<?php echo $p_authortype?>" >
<table class="tb">
  	<tr>
  		<td  colspan="2" align="center">閲囬泦鍐呭璁剧疆  褰撳墠鑾峰彇鐨勬祴璇曞湴鍧�細<?php echo $UrlTest?></td>
  	</tr>
  	<?php if ($p_titletype == 0) {?>
    <tr id="trp_titlestart">
      <td>鏍囬寮�浠ｇ爜锛�/td>
      <td>
 <span onClick="if(document.Form.p_titlestart.rows>2)document.Form.p_titlestart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_titlestart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>
	  </td>
    </tr>
    <tr id="trp_titleend">
      <td>鏍囬缁撴潫浠ｇ爜锛�/td>
      <td>
<span onClick="if(document.Form.p_titleend.rows>2)document.Form.p_titleend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_titleend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>
	  </td>
    </tr>
    <?php
    	}
    if ($p_authortype ==0) {
    ?>
    <tr id="trp_authorstart">
      <td>浣滆�寮�浠ｇ爜锛�/td>
      <td>
 <span onClick="if(document.Form.p_authorstart.rows>2)document.Form.p_authorstart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_authorstart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_authorstart" cols="70" rows="3"><?php echo $p_authorstart?></textarea>
	  </td>
    </tr>
    <tr id="trp_authorend">
      <td>浣滆�缁撴潫浠ｇ爜锛�/td>
      <td>
<span onClick="if(document.Form.p_authorend.rows>2)document.Form.p_authorend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_authorend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
 <textarea name="p_authorend" cols="70" rows="3"><?php echo $p_authorend?></textarea>
	  </td>
    </tr>
    <?php
    	}
    ?>
	  <tr>
        <td><font color="#FF0000">鏍忕洰璁剧疆锛�/font></td>
        <td>
		<input type="radio" value="0" name="p_classtype" onClick="$('#trp_typestart').css('display','none');$('#trp_typeend').css('display','none');$('#trp_classtype').css('display','');$('#p_collect_type').css('display','');" <?php if ($p_classtype==0) { echo "checked";} ?>>
          鍥哄畾鏍忕洰&nbsp;&nbsp; 
		<input type="radio" value="1" name="p_classtype" onClick="$('#trp_classtype').css('display','none');$('#p_collect_type').css('display','none');$('#trp_typestart').css('display','');$('#trp_typeend').css('display','');" <?php if ($p_classtype==1 ) { echo "checked";} ?>>
鎸夊搴旀爮鐩嚜鍔ㄨ浆鎹�
		</td>
	  </tr>
	  <tr  id="trp_classtype" <?php if ($p_classtype==1 ) { echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">閫夋嫨鍏ュ簱鏍忕洰锛�/font></td>
        <td id="CollectClassN2" >
		<select name="p_collect_type" id="CollectClass" size="1">
	  	<option value="0">璇烽�鎷╁叆搴撳垎绫�/option>
		<?php echo makeSelectAll("{pre}art_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$p_collect_type)?>
      </select></td>
    </tr>
 	<tr id="trp_typestart" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">鏍忕洰寮�浠ｇ爜锛�/font></td>
        <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_typestart.rows>2)document.Form.p_typestart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_typestart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
          <textarea name="p_typestart" cols="70" rows="3" id="p_typestart"><?php echo $p_typestart?></textarea></td>
      </tr>
      <tr id="trp_typeend" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">鏍忕洰缁撴潫浠ｇ爜锛�/font></td>
        <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_typeend.rows>2)document.Form.p_typeend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_typeend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
          <textarea name="p_typeend" cols="70" rows="3" id="p_typeend"><?php echo $p_typeend?></textarea></td>
      </tr>
  <tr>
 <td>鍙戝竷鏃ユ湡寮�浠ｇ爜锛�/td>
 <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_timestart.rows>2)document.Form.p_timestart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_timestart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
  <textarea name="p_timestart" cols="70" rows="3" id="p_timestart"><?php echo $p_timestart?></textarea></td>
 </tr>
 <tr>
 <td>鍙戝竷鏃ユ湡缁撴潫浠ｇ爜锛�/td>
 <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_timeend.rows>2)document.Form.p_timeend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_timeend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
  <textarea name="p_timeend" cols="70" rows="3" id="p_timeend"><?php echo $p_timeend?></textarea></td>
 </tr>

 
 <tr>
 <td>鏂囩珷鍐呭寮�浠ｇ爜锛�/td>
 <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_contentstart.rows>2)document.Form.p_contentstart.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_contentstart.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
  <textarea name="p_contentstart" cols="70" rows="3" id="p_contentstart"><?php echo $p_contentstart?></textarea></td>
 </tr>
 
 <tr>
 <td>鏂囩珷鍐呭缁撴潫浠ｇ爜锛�/td>
 <td>&nbsp;&nbsp;杈撳叆鍖哄煙锛�<span onClick="if(document.Form.p_contentend.rows>2)document.Form.p_contentend.rows-=1" style='cursor:hand'><b>缂╁皬</b></span> <span onClick="document.Form.p_contentend.rows+=1" style='cursor:hand'><b>鎵╁ぇ</b></span><br>
  <textarea name="p_contentend" cols="70" rows="3" id="p_contentend"><?php echo $p_contentend?></textarea></td>
	</tr>
	<tr>
	<td  colspan="2"  ><input type="submit" class="btn" id="btnNext" name="btnNext" value="涓嬩竴姝�></td>
	</tr>
</table>
</form>
<script language="JavaScript">
function ChangeCutPara(flag,element1,element2)
{
	switch (flag)
	{
	case 0 :
	$("#"+element1).css("display","none");
	$("#"+element2).css("display","none");
	break;
	case 1 :
	$("#"+element1).css("display","");
	$("#"+element2).css("display","");
	break;
	}
}

currObj = "uuuu";
function getActiveText(obj)
{
	obj.focus();
	currObj = obj;
}
function addTag(ibTag)
{
	var isClose = false;
	var obj_ta = currObj;
	if (obj_ta.isTextEdit)
	{
	obj_ta.focus();
	var sel = document.selection;
	var rng = sel.createRange();
	rng.colapse;
	if((sel.type == "Text" || sel.type == "None") && rng != null)
	{
	rng.text = ibTag;
	}
	obj_ta.focus();
	return isClose;
	}
	else return false;
}
</script>
<?php
}

function lastsave()
{	
	global $db,$cache;
	
	$p_id = be("all","p_id") ; $p_timestart=  be("post","p_timestart") ;
	$p_timeend = be("post","p_timeend") ; $p_classtype=  be("post","p_classtype") ;
	$p_collect_type = be("post","p_collect_type") ; $p_typestart=  be("post","p_typestart") ;
	$p_typeend = be("post","p_typeend") ; $p_contentstart=  be("post","p_contentstart") ;
	
	$p_contentend = be("post","p_contentend") ;
	$p_listcodestart = be("post","p_listcodestart");
	$p_listcodeend = be("post","p_listcodeend");
	$p_authortype = be("post","p_authortype");
	$p_authorstart = be("post","p_authorstart");
	$p_authorend = be("post","p_authorend");
	$p_titletype = be("post","p_titletype");
	$p_titlestart = be("post","p_titlestart");
	$p_titleend = be("post","p_titleend");
	$p_listlinkstart = be("post","p_listlinkstart");
	$p_listlinkend = be("post","p_listlinkend");
	$strlisturl = be("post","listurl");
	$p_coding = be("post","p_coding");
	
	if (isN($p_authortype)) { $p_authortype = 0;}
	if (isN($p_titletype)) { $p_titletype = 0;}
	
	$sql="select * from {pre}cj_art_projects Where p_id=".$p_id;
	$row = $db->getRow($sql); 
	
	$p_pagetype = $row["p_pagetype"];
	$strSet="";
	if ($p_pagetype ==3 || $p_authortype ==0) { 
		$strSet.="p_authorstart='".$p_authorstart."',p_authorend='".$p_authorend."',";
	}
	else{
		$p_authorstart = 	$row["p_authorstart"];
		$p_authorend = $row["p_authorend"];
	}
	if ($p_pagetype ==3 || $p_titletype ==0) {
		$strSet.="p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',";
	}
	else{
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
	}
	
	$strSet.="p_timestart='".$p_timestart."',p_timeend='".$p_timeend."',p_classtype='".$p_classtype."',p_collect_type='".$p_collect_type."',p_typestart='".$p_typestart."',p_typeend='".$p_typeend."',p_contentstart='".$p_contentstart."',p_contentend='".$p_contentend."'";

 	$db->query("update {pre}cj_art_projects set " .$strSet . " where p_id=" . $p_id);
 	
	$p_listcodestart =  $row["p_listcodestart"];
	$p_listcodeend =  $row["p_listcodeend"];
	$p_listlinkstart = $row["p_listlinkstart"];
	$p_listlinkend = $row["p_listlinkend"];
    
	$p_pagebatchurl = $row["p_pagebatchurl"];
	$p_pagebatchid1 = $row["p_pagebatchid1"];
	$p_pagebatchid2 = $row["p_pagebatchid2"];
	
	$p_script = $row["p_script"];
	
	if ($p_pagetype != 3){ 
		if( isN($_SESSION["strListCodeart"] )){
			$strListCode = getPage($strlisturl,$p_coding);
			$_SESSION["strListCodeart"] = $strListCode;
		}
		else{
			$strListCode = $_SESSION["strListCodeart"];
		}
		
		if( isN($_SESSION["strListCodeCutart"] )){
			$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
			$_SESSION["strListCodeCutart"] = $strListCodeCut;
		}
		else{
			$strListCodeCut = $_SESSION["strListCodeCutart"];
		}
		
		if( isN($_SESSION["linkarrcodeart"] )){
			$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
			$_SESSION["linkarrcodeart"] = $linkarrcode;
		}
		else{
			$linkarrcode = $_SESSION["linkarrcodeart"];
		}
		
		if ($p_authortype ==1){
			$starringarr = getArray($strListCodeCut,$p_authorstart,$p_authorend);
		}
		if ($p_titletype ==1) {
			$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
		}
		
		
		switch($linkarrcode)
		{
		Case False:
			errmsg ("閲囬泦鎻愮ず","<li>鍦ㄨ幏鍙栭摼鎺ュ垪琛ㄦ椂鍑洪敊銆�/li>");break;
		default:
			$linkarr = explode("{Array}",$linkarrcode);
			$UrlTest = $linkarr[0];
			$UrlTest = definiteUrl($UrlTest,$strlisturl);
			$linkcode = getPage($UrlTest,$p_coding);
			
			break;
		}
	}
	else{
		$strlisturl = $p_pagebatchurl;
		$p_pagebatchurl = replaceStr($p_pagebatchurl,"{ID}",$p_pagebatchid1);
		$linkcode = getPage($p_pagebatchurl,$p_coding);
	}
	
	if ($p_titletype ==1) {
		switch($titlearrcode)
		{
		Case False:
			$titlecode = "鑾峰彇澶辫触";break;
		default:
			$titlearr = explode("{Array}",$titlearrcode);
			$titlecode = $titlearr[0];
			break;
		}
	}
	else{
		$titlecode = getBody($linkcode,$p_titlestart,$p_titleend);
	}
	
	if ($p_authortype ==1) {
		switch($titlearrcode)
		{
		Case False:
			$starringcode = "鑾峰彇澶辫触";break;
		default:
			$starringarr = explode("{Array}",$starringarrcode);
			$starringcode = $starringarr[0];
			break;
		}
	}
	else{
		$starringcode = getBody($linkcode,$p_authorstart,$p_authorend);
	}
	
	$timecode = getBody($linkcode,$p_timestart,$p_timeend);
	$timecode = replaceStr($timecode,"False",now);
	$contentcode = getBody($linkcode,$p_contentstart,$p_contentend);
	$contentcode = replaceStr($contentcode,"False","鏈煡");
	$contentcode = replaceFilters($contentcode,$p_id,2,1);
	
	if ($p_classtype ==1) {
		$typecode = getBody($linkcode,$p_typestart,$p_typeend);
	}
	else{
		$typecode = $p_collect_type;
		$typearr = getValueByArray($cache[1], "t_id" ,$typecode );
		$typecode = $typearr["t_name"];
	}
	
	$titlecode = filterScript($titlecode,$p_script);
	$titlecode = replaceFilters($titlecode,$p_id,1,1);
	$starringcode = filterScript($starringcode,$p_script);
	$timecode = filterScript($timecode,$p_script);
	$typecode = filterScript($typecode,$p_script);
	
	
?>
<form name="form" action="?action=saveok" method="post">
<table class="tb">
  	<tr>
		<td  colspan="2" align="center">閲�闆�娴�璇�缁�鏋�/td>
  	</tr>
    <tr>
		<td width="20%">鏍囬锛�/td>
		<td> <?php echo $titlecode?> </td>
    </tr>
    <tr>
		<td>浣滆�锛�/td>
		<td> <?php echo $starringcode?> </td>
    </tr>
    <tr>
		<td>鏃ユ湡锛�/td>
		<td> <?php echo $timecode?> </td>
    </tr>
    <tr>
		<td>鏍忕洰锛�/td>
		<td> <?php echo $typecode?> </td>
    </tr>
    <tr>
	<td>鍐呭锛�/td>
		<td> <div style="height:300px;overflow:hidden;overflow-y:auto;"><?php echo strip_tags($contentcode)?> </div></td>
    </tr>
	<tr>
	<td  colspan="2"><input name="button" type="button" class="btn" id="button" onClick="window.location.href='javascript:history.go(-1)'" value="涓婁竴姝�>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="Submit" type="submit" class="btn" id="Submit" value="瀹�鎴�></td>
	</tr>
</table>
</form>
<?php
} 

function main()
{
	global $db,$cache;
	$pagenum = be("get","page");
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	 $sql = "select * from {pre}cj_art_projects ";
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//鎬昏褰曟暟
	$pagecount=ceil($nums/app_pagenum);//鎬婚〉鏁�
	$sql = $sql ."limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		鑿滃崟锛�a href="collect_art_manage.php?action=add">娣诲姞閲囬泦瑙勫垯</a> | <a href="collect_art_manage.php?action=upexp">瀵煎叆閲囬泦瑙勫垯</a> | <a href="collect_art_change.php">鍒嗙被杞崲</a> | <a href="collect_art_filters.php">淇℃伅杩囨护</a> 
	</td>
	</tr>
</table>
<form action="" method="post" name="form1">
<table class=tb >
	<tr>
	<td width="4%">&nbsp;</td>
	<td>椤圭洰鍚嶇О</td>
	<td width="25%">鍏ュ簱鍒嗙被</td>
	<td width="20%">涓婃閲囬泦</td>
	<td width="25%">鎿嶄綔</td>
	</tr>
	<?php
	if (!$rs){
	?>
    <tr><td align="center" colspan="7" >娌℃湁浠讳綍璁板綍!</td></tr>
    <?php
	}
	else{
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
    <tr>
	  <td><input name="p_id[]" type="checkbox" id="p_id" value="<?php echo $row["p_id"]?>" /></td>
      <td><a href="?action=edit&p_id=<?php echo $row["p_id"]?>"><?php echo $row["p_name"]?></a></td>
	  <td>
	  <?php
	  	if ($row["p_classtype"] == 1) {
	  		echo "<font color=red>鑷畾涔夊垎绫�/font>";
	  }
	  	else{
	  		$typearr = getValueByArray($cache[1], "t_id", $row["p_collect_type"]);
	  		echo $typearr["t_name"];
	  	}
	  ?>
	  </td>
      <td><?php echo isToDay($row['p_time']) ?></td>
 	  <td>
 	   <A href="collect_art_cj.php?p_id=<?php echo  $row["p_id"] ?>">閲囬泦</A>锝�
 	  <A href="?action=edit&p_id=<?php echo $row["p_id"]?>">淇敼</A>锝�
 	  <A href="?action=copy&p_id=<?php echo  $row["p_id"] ?>">澶嶅埗</A>锝�
 	  <A href="?action=export&p_id=<?php echo  $row["p_id"] ?>">瀵煎嚭</A>锝�
 	  <A href="?action=del&p_id=<?php echo $row["p_id"]?>">鍒犻櫎</A>
 	  </td>
    </tr>
	<?php
		}
	}
	?>
	<tr>
	<td  colspan="7">
	鍏ㄩ�<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'p_id[]');"/>&nbsp;
	<input type="submit" value="鎵归噺鍒犻櫎" onClick="if(confirm('纭畾瑕佸垹闄ゅ悧')){form1.action='?action=delall';}else{return false}"  class="input"/>
	<input type="submit" value="鎵归噺閲囬泦" onClick="if(confirm('纭畾瑕佹壒閲忛噰闆嗗悧')){form1.action='collect_art_cj.php?action=pl';}else{return false}"  class="input"/>
	</td>
	</tr>
    <tr align="center" bgcolor="#f8fbfb">
	<td colspan="7">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_art_manage.php?page={p}") ?>
	</td>
	</tr>
  </form>
</table>
<?php
}
?>