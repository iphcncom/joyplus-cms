<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("collect_vod_cjVideoUrl.php");
require_once ("tools/ContentManager.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("视频自定义采集项目管理");

switch(trim($action))
{
	case "add" :
	case "edit" : edit();break;
	case "editstep1" : editstep1();break;
	case "editstep2" :editstep2();break;
	case "lastsave" : lastsave();break;
	case "saveok" : saveok();break;
	case "del" : del();break;
	case "collectSimple" : collectSimple();break;
	case "collectVideo" : collectVideo();break;
	case "copy" : copynew();break;
	case "delall" : delall();break;
	case "export" : export(); break;
	case "upexp" : upexp(); break;
	case "upexpsave" : upexpsave(); break;
	case "breakpoint" : breakpoint(); break;
	case "collectVideoResult" :collectVideoResult();break;
	default :  clearSession(); main(); break;
}
function collectVideoResult(){
	$webUrl = be("all","site_url");
	$p_id=be("all","p_id");
	echo "url :".$webUrl."</br>";
//	echo "project id :".$p_id."\r\n";
	echo "<font color='red'>".getVideoUrlByProjectAndUrl($webUrl,$p_id).'</font>';
}
function export()
{
	global $db;
	$p_id = be("get","p_id");
	$fields = $db->getTableFields(app_dbname,"{pre}cj_vod_projects");
	$colsnum = mysql_num_fields($fields);
	$row = $db->getRow("select * from {pre}cj_vod_projects where p_id='".$p_id."'");
	$result="";
	$fileName= $row["p_name"];
	for($i = 0; $i < $colsnum; $i++){
		$colname = mysql_field_name($fields, $i);
		$result .= "<".$colname.">".$row[$colname]."</".$colname.">"."\r\n";
	} 
	unset($row);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", $fileName) .".txt";
	fwrite(fopen($filePath,"wb"),$result);
	echo "<script language=\"javascript\">setTimeout(\"gonextpage();\",0);function gonextpage(){location.href='collect_down.php?file=".$fileName."';}</script> ";
}

function upexp()
{
?>
<form enctype="multipart/form-data" action="?action=upexpsave" method="post">
<table width="96%" border=0 align=center cellpadding="4" cellSpacing=0 class=tb >
	<tr>
	<td colspan="2" align="center">
	上传视频采集规则
	<input type="file" id="file1" name="file1">
	<input type="submit" name="submit" value="开始导入">
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
		if($iar[1][$m] !="p_id" && $iar[1][$m] !="p_lzphasestart" && $iar[1][$m] !="p_lzphaseend" ){
			if ($rc){  $in1 .= ","; $in2 .= ","; }
			 $in1 .= $iar[1][$m];
			 $in2 .= "'". replaceStr($iar[2][$m],"'","\'") . "'";
			 $rc=true;
		}
	}
	$sql = "insert into {pre}cj_vod_projects (".$in1.") values(".$in2.")";
	$status = $db->query($sql);
	if($status){
		showmsg ("导入规则成功!","collect_vod_manage.php");
	}
	else{
		alert("导入失败，请检查规则是否正确!");
	}
}

function breakpoint()
{
	echo gBreakpoint("../../upload/vodbreakpoint") . "正在载入断点续传数据，请稍后......";
	exit;
}

function saveok()
{
	showmsg ("采集栏目添加成功","collect_vod_manage.php");
}

function copynew()
{
	global $db;
	$p_id = be("get","p_id");
    $sql = "INSERT INTO  {pre}cj_vod_projects(p_name, p_coding, p_playtype, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_starringtype, p_starringstart, p_starringend, p_titletype, p_titlestart, p_titleend, p_pictype, p_picstart, p_picend, p_timestart, p_timeend, p_areastart, p_areaend, p_typestart, p_typeend, p_contentstart, p_contentend, p_playcodetype, p_playcodestart, p_playcodeend, p_playurlstart, p_playurlend, p_playlinktype, p_playlinkstart, p_playlinkend, p_playspecialtype, p_playspecialrrul, p_playspecialrerul, p_server, p_hitsstart, p_hitsend, p_lzstart, p_lzend, p_colleclinkorder, p_lzcodetype, p_lzcodestart, p_lzcodeend, p_languagestart, p_languageend, p_remarksstart, p_remarksend,p_directedstart,p_directedend,p_setnametype,p_setnamestart,p_setnameend) SELECT p_name, p_coding, p_playtype, p_pagetype, p_url, p_pagebatchurl, p_manualurl, p_pagebatchid1, p_pagebatchid2, p_script, p_showtype, p_collecorder, p_savefiles, p_intolib, p_ontime, p_listcodestart, p_listcodeend, p_classtype, p_collect_type, p_time, p_listlinkstart, p_listlinkend, p_starringtype, p_starringstart, p_starringend, p_titletype, p_titlestart, p_titleend, p_pictype, p_picstart, p_picend, p_timestart, p_timeend, p_areastart, p_areaend, p_typestart, p_typeend, p_contentstart, p_contentend, p_playcodetype, p_playcodestart, p_playcodeend, p_playurlstart, p_playurlend, p_playlinktype, p_playlinkstart, p_playlinkend, p_playspecialtype, p_playspecialrrul, p_playspecialrerul, p_server, p_hitsstart, p_hitsend, p_lzstart, p_lzend, p_colleclinkorder, p_lzcodetype, p_lzcodestart, p_lzcodeend, p_languagestart, p_languageend, p_remarksstart, p_remarksend,p_directedstart,p_directedend,p_setnametype,p_setnamestart,p_setnameend FROM  {pre}cj_vod_projects WHERE p_id =" .$p_id;
	$db->query($sql);
    showmsg ("复制采集栏目成功！","collect_vod_manage.php");
}

function del()
{
	global $db;
	$p_id=be("get","p_id");
    $sql= "delete from {pre}cj_vod_projects WHERE p_id=".$p_id;
    $db->query($sql);
    showmsg ("采集项目删除成功！","collect_vod_manage.php");
}

function delall()
{
	global $db;
    $ids=be("arr","p_id");
    if (!isN($ids)){
	  $db->query("delete from {pre}cj_vod_projects WHERE p_id in (".$ids.")");
	}
    showmsg ("采集项目删除成功！","collect_vod_manage.php");
}

function collectSimple(){
	$p_id=be("all","p_id");
	$p_name=be("all","p_name");
	
?>
<form action="collect_vod_cj.php?action=collectSimpl" method="post" id="form1" name="form1">
<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
<table class="tb">
    <tr>
      <td width="20%" >项目：</td>
      <td>
      <?php echo $p_name?>
	  </td>
	   </tr>
	   <tr>
      <td width="20%" >采集视频地址：</td>
      <td>
      	<INPUT id="site_url" name="site_url" size="100" value="" >
	  </td>
	  </tr>
	  
	  <tr>
      <td width="20%" >视频名字：</td>
      <td>
      	<INPUT id="name" name="name" size="100" value="" >
	  </td>
	  </tr>
	  
	   <tr>
      <td width="20%" >主演：</td>
      <td>
      	<INPUT id="actor" name="actor" size="100" value="" >
	  </td>
	  </tr>
	  
	   <tr>
      <td width="20%" >海报地址：</td>
      <td>
      	<INPUT id="poster" name="poster" size="100" value="" >
	  </td>
	  </tr>
	  
	   <tr>
	<td  colspan="2"  ><input type="submit" class="btn" id="btnNext1" name="btnNext" value="下一步"></td>
	
    </tr>
 </table>
 </form>   
<?php 

}


function collectVideo(){
	$p_id=be("all","p_id");
	$p_name=be("all","p_name");
	
?>
<form action="collect_vod_manage.php?action=collectVideoResult" method="post" id="form1" name="form1">
<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
<table class="tb">
    <tr>
      <td width="20%" >项目：</td>
      <td>
      <?php echo $p_name?>
	  </td>
	   </tr>
	   <tr>
      <td width="20%" >采集视频地址：</td>
      <td>
      	<INPUT id="site_url" name="site_url" size="100" value="" >
	  </td>
	  </tr>
	  
	  
	   <tr>
	<td  colspan="2"  ><input type="submit" class="btn" id="btnNext1" name="btnNext" value="下一步"></td>
	
    </tr>
 </table>
 </form>   
<?php 

}

function edit()
{
	global $db;
	$p_id=be("all","p_id");
	if(!isN($p_id)){
		$sql="select * from {pre}cj_vod_projects where p_id = ".$p_id;
		$row = $db->getRow($sql);
		$p_name = $row["p_name"];
		$p_coding = $row["p_coding"];
		$p_playtype = $row["p_playtype"];
		$p_pagetype = $row["p_pagetype"];
		$p_url = $row["p_url"];
		$p_pagebatchurl = $row["p_pagebatchurl"];
		$p_manualurl = $row["p_manualurl"];
		$p_pagebatchid1 = $row["p_pagebatchid1"];
		$p_pagebatchid2 = $row["p_pagebatchid2"];
		$p_script = $row["p_script"];
		$p_collecorder = $row["p_collecorder"];
		$p_savefiles = $row["p_savefiles"];
		$p_intolib = $row["p_intolib"];
		$p_ontime = $row["p_ontime"];
		$p_server = $row["p_server"];
		$p_hitsstart = $row["p_hitsstart"];
		$p_hitsend = $row["p_hitsend"];
		$p_colleclinkorder = $row["p_colleclinkorder"];
		$p_showtype = $row["p_showtype"];
		unset($row);
	}
	else{
		$p_pagetype=0;
	}
?>
<form action="?action=editstep1" method="post" id="form1" name="form1">
<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
<table class="tb">
    <tr>
      <td width="20%" >项目名称：</td>
      <td>
      	<INPUT id="p_name" name="p_name" size="50" value="<?php echo $p_name?>" >
	  </td>
    </tr>
    <tr>
    <td>采集过程方式：</td>
	 <td>
	  <input name="p_showtype" type="radio" value="0" <?php if ($p_showtype==0) { echo "checked";} ?>>显示采集一个列表 &nbsp;&nbsp;
	  <input name="p_showtype" type="radio" value="1" <?php if ($p_showtype==1) { echo "checked";} ?>>显示采集一条数据 &nbsp;&nbsp;
	</td>
	</tr>
   <tr>
	<td>采集参数：</td>
	<td>
  	<input id="p_collecorder" name="p_collecorder" type="checkbox" value="1" <?php if ($p_collecorder==1){ echo "checked";} ?>>分页倒序采集 &nbsp;&nbsp;
  	<input id="p_colleclinkorder" name="p_colleclinkorder" type="checkbox" value="1" <?php if ($p_colleclinkorder==1){echo "checked";}?>>列表倒序采集 &nbsp;&nbsp;
  	<input id="p_savefiles" name="p_savefiles" type="checkbox" value="1" <?php if ($p_savefiles==1){ echo "checked";} ?>> 采集中保存图片(采集过程方式为:显示采集一条数据时使用)
	</td>
	</tr>
	<tr>
	<td>目标网页编码：</td>
	<td>&nbsp;
	<select id="p_coding" name="p_coding">
	<option value="GB2312" <?php if ($p_coding=="GB2312") { echo "selected";} ?>>GB2312</option>
	<option value="UTF-8" <?php if ($p_coding=="UTF-8") { echo "selected";} ?>>UTF-8</option>
	<option value="BIG5" <?php if ($p_coding=="BIG5") { echo "selected";} ?>>BIG5</option>
	</select>
	  </td>
    </tr>
    <tr>
      <td>播放器：</td>
      <td>
      	 &nbsp;<select id="p_playtype" name="p_playtype">
        <option value=''>暂没有数据</option>
        <?php echo makeSelectPlayer($p_playtype)?>
		</select>
	  </td>
    </tr>
    <tr>
      <td>服务器组：</td>
      <td>
      	 &nbsp;<select id="p_server" name="p_server">
        <option value='0'>无服务器组</option>
         <?php echo makeSelectServer($p_server)?>
        </select>
	  </td>
    </tr>
    <tr>
      <td>分页设置：</td>
      <td>
      	<input type="radio" value="0" id="p_pagetype" name="p_pagetype" checked="checked" onClick="ChangeCutPara(0);" <?php if ($p_pagetype==0) { echo "checked";} ?>>
不分页&nbsp;&nbsp;
<input type="radio" value="1" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(1);" <?php if ($p_pagetype==1 ) { echo "checked";} ?>>
批量分页&nbsp;
<input type="radio" value="2" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(2);" <?php if ($p_pagetype==2 ) { echo "checked";} ?>>
手动分页&nbsp;
<input type="radio" value="3" id="p_pagetype" name="p_pagetype" onClick="ChangeCutPara(3);" <?php if ($p_pagetype==3 ) { echo "checked";} ?>> 
按ID直接采集内容
	  </td>
    </tr>
    <tr ID="IndexCutPage" >
      <td>采集地址：</td>
      <td>
      	<INPUT id="p_url" name="p_url" size="50" value="<?php echo $p_url?>">
	  </td>
    </tr>
<tr ID="HandCutPage" style="display:none">
 <td><span id="CutPageName"></span>：</td>
 <td><input type="text"  name="p_pagebatchurl"  size="60" value="<?php echo $p_pagebatchurl?>"/>
 分页代码 <font color=red>{ID}</font><br>
标准格式：Http://www.xxxxx.com/list/list_{ID}.html<br>
采集范围：
<input name="p_pagebatchid1" type="text" value="<?php echo $p_pagebatchid1?>" size="4">
 To 
<input name="p_pagebatchid2" type="text" value="<?php echo $p_pagebatchid2?>" size="4">
例如：1 - 9</td>
 </tr>
 <tr ID="ListContent" style="display:none">
 <td>手动分页：</td>
 <td><textarea name="p_manualurl" cols="60" rows="3"><?php echo $p_manualurl?></textarea></td>
 </tr>
 
 <tr>
 <td>随机人气：</td>
 <td>
  从&nbsp;<input id="p_hitsstart" name="p_hitsstart" type="text" size="4" value="<?php echo $p_hitsstart?>"> 
  到 &nbsp; <input id="p_hitsend" name="p_hitsend" type="text" size="4" value="<?php echo $p_hitsend?>"> 
  之间 (前小后大)
</td>
 </tr>
 <tr>
 <td>下一步显示源码：</td>
 <td>
  <input type="checkbox" class="checkbox" name="showcode" id="showcode" value="1"/>显示
</td>
 </tr>
 <tr>
 <td>过滤选项：</td>
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
	<td  colspan="2"  ><input type="submit" class="btn" id="btnNext1" name="btnNext" value="下一步"></td>
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
	$("#CutPageName").html("批量分页");
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
	$("#CutPageName").html("按ID采集内容页");
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
	$p_playtype = be("post","p_playtype") ; $p_pagetype = be("all","p_pagetype") ; $p_url = be("post","p_url");
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
	$p_intolib = be("post","p_intolib") ; $p_ontime = be("post","p_ontime");
	$p_server = be("post","p_server") ; $p_hitsstart = be("post","p_hitsstart");
	$p_hitsend = be("post","p_hitsend"); $p_colleclinkorder = be("post","p_colleclinkorder");
	$showcode = be("post","showcode");  $p_showtype = be("post","p_showtype");
	
	if (isN($p_collecorder)) { $p_collecorder = 0;}
	if (isN($p_savefiles)) { $p_savefiles = 0;}
	if (isN($p_intolib)) { $p_intolib = 0;}
	if (isN($p_ontime)) { $p_ontime = 0;}
	if (isN($p_server)) { $p_server = 0;}
	if (isN($p_colleclinkorder)) {$p_colleclinkorder=0;}
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
		  else{
		  	$strlisturl = $p_manualurl;
		  }
	 	  break;
	}
	
	$strListCode = getPage($strlisturl,$p_coding);
	
	if ($strListCode ==false){
		errmsg ("采集系统提示","<li>在获取:".$strlisturl."网页源码时发生错误！</li>");
	}
	$_SESSION["strListCode"] = $strListCode;
		
	if( isN($p_id) ) {
		$sql="INSERT {pre}cj_vod_projects(p_name,p_coding,p_playtype,p_pagetype,p_url,p_pagebatchurl,p_manualurl,p_pagebatchid1,p_pagebatchid2,p_script,p_showtype,p_collecorder,p_savefiles,p_ontime,p_server,p_hitsstart,p_hitsend,p_time,p_colleclinkorder)  values ('".$p_name."','".$p_coding."','".$p_playtype."','".$p_pagetype."','".$p_url."','".$p_pagebatchurl."','".$p_manualurl."','".$p_pagebatchid1."','".$p_pagebatchid2."','".$p_script."','".$p_showtype."','".$p_collecorder."','".$p_savefiles."','".$p_ontime."','".$p_server."','".$p_hitsstart."','".$p_hitsend."','".date('Y-m-d H:i:s',time())."','".$p_colleclinkorder."')";
	}
	else{
		$sql="update {pre}cj_vod_projects set p_name='".$p_name."',p_coding='".$p_coding."',p_playtype='".$p_playtype."',p_pagetype='".$p_pagetype."',p_url='".$p_url."',p_pagebatchurl='".$p_pagebatchurl."',p_manualurl='".$p_manualurl."',p_pagebatchid1='".$p_pagebatchid1."',p_pagebatchid2='".$p_pagebatchid2."',p_script='".$p_script."',p_showtype='".$p_showtype."',p_collecorder='".$p_collecorder."',p_savefiles='".$p_savefiles."',p_ontime='".$p_ontime."',p_server='".$p_server."',p_hitsstart='".$p_hitsstart."',p_hitsend='".$p_hitsend."',p_colleclinkorder='".$p_colleclinkorder."' where p_id =" .$p_id;
	}
	$db->query($sql);
	if( isN($p_id) ) {
		$p_id = $db->insert_id();
	}
	$sql="select * from {pre}cj_vod_projects where p_id = ".$p_id;
	$row = $db->getRow($sql);
	
	$p_starringtype = $row["p_starringtype"];
	$p_starringstart = $row["p_starringstart"];
	$p_starringend = $row["p_starringend"];
	$p_titletype = $row["p_titletype"];
	$p_titlestart = $row["p_titlestart"];
	$p_titleend = $row["p_titleend"];
	$p_pictype = $row["p_pictype"];
	$p_picstart = $row["p_picstart"];
	$p_picend = $row["p_picend"];
	$p_listcodestart = $row["p_listcodestart"];
	$p_listcodeend = $row["p_listcodeend"];
	$p_listlinkstart = $row["p_listlinkstart"];
	$p_listlinkend = $row["p_listlinkend"];

if ($p_pagetype == 3){
 header( "Location:collect_vod_manage.php?action=editstep2&p_pagetype=".$p_pagetype."&p_id=".$p_id."&p_coding=".$p_coding."&listurl=".$strlisturl."&showcode=".$showcode);
 exit();
}

if ($showcode=="1"){
	?>
<table class="tb">
	<tr><td>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 200px" id="htmlcode" wrap="off" readOnly><?php echo $strListCode ?></TEXTAREA>
	</td></tr>
</table>
<?php
}
?>
<form action="?action=editstep2" method="post">
	<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
  	<INPUT id="listurl" name="listurl" type="hidden" value="<?php echo $strlisturl?>" >
  	<INPUT id="p_coding" name="p_coding" type="hidden" value="<?php echo $p_coding?>" >
  	<INPUT id="showcode" name="showcode" type="hidden" value="<?php echo $showcode?>" >
  	<INPUT id="p_playtype" name="p_playtype" type="hidden" value="<?php echo $p_playtype?>" >
<table class="tb">
  	<tr>
  		<td  colspan="2" align="center">列表连接设置 当前获取的测试地址：<?php echo $strlisturl ?></td>
  	</tr>
    <tr>
      <td width="20%">列表开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_listcodestart.rows>2)document.Form.p_listcodestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_listcodestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_listcodestart" cols="70" rows="3"><?php echo $p_listcodestart?></textarea>
	  </td>
    </tr>
    <tr>
      <td>列表结束代码：</td>
      <td>
<span onClick="if(document.Form.p_listcodeend.rows>2)document.Form.p_listcodeend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_listcodeend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_listcodeend" cols="70" rows="3"><?php echo $p_listcodeend?></textarea>
	  </td>
    </tr>
    <tr>
      <td>链接开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_listlinkstart.rows>2)document.Form.p_listlinkstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_listlinkstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_listlinkstart" cols="70" rows="3"><?php echo $p_listlinkstart?></textarea>
	  </td>
    </tr>
    <tr>
      <td>链接结束代码：</td>
      <td>
<span onClick="if(document.Form.p_listlinkend.rows>2)document.Form.p_listlinkend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_listlinkend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_listlinkend" cols="70" rows="3"><?php echo $p_listlinkend?></textarea>
	  </td>
    </tr>
    <tr>
      <td>列表采集名称：</td>
      <td>
      	<input type="radio" value="0" id="p_titletype" name="p_titletype" <?php if ($p_titletype==0) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara(0,'trp_titlestart','trp_titleend');">
否&nbsp;&nbsp;
<input type="radio" value="1" id="p_titletype" name="p_titletype" <?php if ($p_titletype==1) { echo "checked=\"checked\"";}?> onClick="ChangeCutPara(1,'trp_titlestart','trp_titleend');">
是&nbsp;
	  </td>
    </tr>
    
    <tr id="trp_titlestart" style="display:none">
      <td>名称开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_titlestart.rows>2)document.Form.p_titlestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_titlestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>
	  </td>
    </tr>
    <tr id="trp_titleend" style="display:none">
      <td>名称结束代码：</td>
      <td>
<span onClick="if(document.Form.p_titleend.rows>2)document.Form.p_titleend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_titleend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>
	  </td>
    </tr>
    
    <tr>
      <td>列表采集主演：</td>
      <td>
      	<input type="radio" value="0" id="p_starringtype" name="p_starringtype" <?php if ($p_starringtype==0) {echo " checked=\"checked\"";}?> onClick="ChangeCutPara(0,'trp_starringstart','trp_starringend');">
否&nbsp;&nbsp;
<input type="radio" value="1" id="p_starringtype" name="p_starringtype" <?php if ($p_starringtype==1) {echo "checked=\"checked\"";}?> onClick="ChangeCutPara(1,'trp_starringstart','trp_starringend');">
是&nbsp;
	  </td>
    </tr>
    <tr id="trp_starringstart" style="display:none">
      <td>主演开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_starringstart.rows>2)document.Form.p_starringstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_starringstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_starringstart" cols="70" rows="3"><?php echo $p_starringstart?></textarea>
	  </td>
    </tr>
    <tr id="trp_starringend" style="display:none">
      <td>主演结束代码：</td>
      <td>
<span onClick="if(document.Form.p_starringend.rows>2)document.Form.p_starringend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_starringend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_starringend" cols="70" rows="3"><?php echo $p_starringend?></textarea>
	  </td>
    </tr>
    
    <tr>
      <td>列表采集图片：</td>
      <td>
      	<input type="radio" value="0" id="p_pictype" name="p_pictype" <?php if ($p_pictype==0 ){echo "checked=\"checked\"";}?> onClick="ChangeCutPara(0,'trp_picstart','trp_picend');">
否&nbsp;&nbsp;
<input type="radio" value="1" id="p_pictype" name="p_pictype" <?php if ($p_pictype==1){echo "checked=\"checked\"";}?> onClick="ChangeCutPara(1,'trp_picstart','trp_picend');">
是&nbsp;
	  </td>
    </tr>
    <tr id="trp_picstart" style="display:none">
      <td>图片开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_picstart.rows>2)document.Form.p_picstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_picstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_picstart" cols="70" rows="3"><?php echo $p_picstart?></textarea>
	  </td>
    </tr>
    <tr id="trp_picend" style="display:none">
	<td>图片结束代码：</td>
	<td>
<span onClick="if(document.Form.p_picend.rows>2)document.Form.p_picend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_picend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_picend" cols="70" rows="3"><?php echo $p_picend?></textarea>
	</td>
    </tr>
	<tr>
	 <td colspan="2"><input type="submit" class="btn" id="btnNext" name="btnNext" value="下一步"></td>
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
ChangeCutPara(<?php echo $p_starringtype?>,"trp_starringstart","trp_starringend");
ChangeCutPara(<?php echo $p_titletype?>,"trp_titlestart","trp_titleend");
ChangeCutPara(<?php echo $p_pictype?>,"trp_picstart","trp_picend");
</script>
<?php
}

function editstep2()
{
	global $db;
	$p_id = be("all","p_id");
	$strlisturl = be("post","listurl");
	$p_coding = be("post","p_coding");
	$p_starringtype = be("post","p_starringtype");
	$p_starringstart = be("post","p_starringstart");
	$p_starringend = be("post","p_starringend");
	$p_titletype = be("post","p_titletype");
	$p_pictype = be("post","p_pictype");
	$p_pagetype = be("all","p_pagetype");
	$p_listcodestart = be("post","p_listcodestart");
	$p_listcodeend = be("post","p_listcodeend");
	$p_titlestart = be("post","p_titlestart");
	$p_titleend = be("post","p_titleend");
	$p_listlinkstart = be("post","p_listlinkstart");
	$p_listlinkend = be("post","p_listlinkend");
	$p_picstart = be("post","p_picstart");
	$p_picend = be("post","p_picend");
	$showcode = be("post","showcode");
	$p_playtype = be("post","p_playtype");
	if( isN($_SESSION["strListCode"] )) {
		$strListCode = getPage($strlisturl,$p_coding);
		$_SESSION["strListCode"] = $strListCode;
	}
	else{
		$strListCode = $_SESSION["strListCode"];
	}
	$strListCode = getPage($strlisturl,$p_coding);
	
	if (isN($p_starringtype)) { $p_starringtype = 0;}
	if (isN($p_titletype)) { $p_titletype = 0;}
	if (isN($p_pictype)) { $p_pictype = 0;}
	
	if (isN($p_pagetype)){
		$strListCodeCut = getBody($strListCode,$p_listcodestart,$p_listcodeend);
		
		$linkarrcode = getArray($strListCodeCut,$p_listlinkstart,$p_listlinkend);
		
		
		
		
		$_SESSION["strListCodeCut"]=$strListCodeCut;
//		var_dump($strListCodeCut);
		if ($p_starringtype == 1){
			$starringarrcode = getArray($strListCodeCut,$p_starringstart,$p_starringend);
//			var_dump($starringarrcode);
		}
		if ($p_titletype == 1){
			$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
		}
		if ($p_pictype == 1){
			$picarrcode = getArray($strListCodeCut,$p_picstart,$p_picend);
		}
		
		switch ($linkarrcode)
		{
			Case False:
				errmsg ("采集提示","<li>在获取链接列表时出错。</li>") ;break;
			default:
				$_SESSION["linkarrcode"] = $linkarrcode;
				$linkarr = explode("{Array}",$linkarrcode);				
//				$linkarr=getHrefFromLink($tempLinkarr);
				$UrlTest = getHrefFromLink($linkarr[0]);
//				var_dump($linkarr[0]);
				$UrlTest = definiteUrl($UrlTest,$strlisturl);
				$linkcode = getPage($UrlTest,$p_coding);
				break;
		}
		
		if ($p_titletype == 1 ){
			switch ($titlearrcode)
			{
			Case False:
				errmsg ("采集提示","<li>在获取名称时出错。</li>") ;break;
			default:
				$titlearr = explode("{Array}",$titlearrcode);
				$titlecode = $titlearr[0];
				break;
			}
		}
		if ($p_starringtype == 1){
			switch ($starringarrcode)
			{
			Case False:
				errmsg ("采集提示","<li>在获取主演时出错。</li>");break;
			default:
				$starringarr = explode("{Array}",$starringarrcode);
				$starringcode = $starringarr[0];
//				var_dump($starringcode);
				break;
			}
		}
		if ($p_pictype == 1){
			switch ($picarrcode)
			{
				Case False:
					errmsg ("采集提示","<li>在获取图片时出错。</li>");break;
				default:
					$picarr = explode("{Array}",$picarrcode);
					$piccode = $picarr[0];
					break;
			}
	 	}
		
	}
	
	$sql="select * from {pre}cj_vod_projects Where p_id=".$p_id;
	$row = $db->getRow($sql);
	
	$strSet= "";
	if ($p_pagetype ==3 || $p_starringtype == 0){ 
		$p_starringstart = 	$row["p_starringstart"];
		$p_starringend = $row["p_starringend"];
	}
	else{
		$strSet.="p_starringstart='".$p_starringstart."',p_starringend='".$p_starringend."',";
	}
	if ($p_pagetype ==3 || $p_titletype ==0){
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
	}
	else{
		$strSet.= "p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',";
	}
	if ($p_pagetype ==3 || $p_pictype ==0){ 
		$p_picstart = $row["p_picstart"];
		$p_picend = $row["p_picend"];
	}
	else{
		$strSet.="p_picstart='".$p_picstart."',p_picend='".$p_picend."',";
	}
  $strSet.= "p_listcodestart='".$p_listcodestart."',p_listcodeend='".$p_listcodeend."',p_listlinkstart='".$p_listlinkstart."',p_listlinkend='".$p_listlinkend."',p_starringtype='".$p_starringtype."',p_titletype='".$p_titletype."',p_pictype='".$p_pictype."'";
   if ($p_pagetype ==3){
   	  $strSet.= " ,p_pagetype='".$p_pagetype."'";
   	   
   }
   $sql = "update {pre}cj_vod_projects set " .$strSet ." where p_id= ". $p_id;
   $db->query($sql);
	
	$p_timestart = $row["p_timestart"];
	$p_timeend = $row["p_timeend"];
	$p_areastart = $row["p_areastart"];
	$p_areaend = $row["p_areaend"];
	$p_classtype = $row["p_classtype"];
	$p_collect_type = $row["p_collect_type"];
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
	$p_lzstart = $row["p_lzstart"];
	$p_lzend = $row["p_lzend"];
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
	$p_playcodeApiUrl= $row["p_playcodeApiUrl"];
	$p_playcodeApiUrltype= $row["p_playcodeApiUrltype"];
	$p_playcodeApiUrlParamstart= $row["p_playcodeApiUrlParamstart"];
	$p_playcodeApiUrlParamend= $row["p_playcodeApiUrlParamend"];
	$p_videocodeApiUrl= $row["p_videocodeApiUrl"];
	$p_videocodeApiUrlParamstart= $row["p_videocodeApiUrlParamstart"];
	$p_videocodeApiUrlParamend= $row["p_videocodeApiUrlParamend"];
	$p_videourlstart= $row["p_videourlstart"];
	$p_videourlend= $row["p_videourlend"];
	$p_videocodeType= $row["p_videocodeType"];
	
	if (isN($p_lzcodetype)){$p_lzcodetype=0;}
	if (isN($p_videocodeType)){$p_videocodeType=0;}
	if (isN($p_playcodetype)){$p_playcodetype=0;}
	if (isN($p_playlinktype)){$p_playlinktype=0;}
	if (isN($p_playspecialtype)){$p_playspecialtype=0;}
	if (isN($p_setnametype)){$p_setnametype=0;}
    if (isN($p_playcodeApiUrltype)){$p_playcodeApiUrltype=0;}
	
	
if ($showcode=="1"){
?>
<table class="tb">
	<tr><td>
	<TEXTAREA style="WIDTH: 100%; HEIGHT: 200px" id="htmlcode" wrap="off" readOnly><?echo $linkcode ?></TEXTAREA>
  	</td></tr>
</table>
<?php
}
?>
<form name="form" action="?action=lastsave" method="post">
	<INPUT id="p_id" name="p_id" type="hidden" value="<?php echo $p_id?>" >
    <INPUT id="p_pagetype" name="p_pagetype" type="hidden" value="<?php echo $p_pagetype?>" >
  	<INPUT id="listurl" name="listurl" type="hidden" value="<?php echo $strlisturl?>" >
  	<INPUT id="p_coding" name="p_coding" type="hidden" value="<?php echo $p_coding?>" >
    <INPUT id="p_titletype" name="p_titletype" type="hidden" value="<?php echo $p_titletype?>" >
    <INPUT id="p_starringtype" name="p_starringtype" type="hidden" value="<?php echo $p_starringtype?>" >
    <INPUT id="p_pictype" name="p_pictype" type="hidden" value="<?php echo $p_pictype?>" >
    <INPUT id="showcode" name="showcode" type="hidden" value="<?php echo $showcode?>" >
    <INPUT id="p_playtype" name="p_playtype" type="hidden" value="<?php echo $p_playtype?>" >
   
<table class="tb">
	<tr>
	<td  colspan="2" align="center">采集内容设置 当前获取的测试地址：<?php echo $UrlTest?></td>
  	</tr>
  	<?php if ($p_titletype == 0) {?>
    <tr id="trp_titlestart">
      <td width="20%">名称开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_titlestart.rows>2)document.Form.p_titlestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_titlestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_titlestart" cols="70" rows="3"><?php echo $p_titlestart?></textarea>	  </td>
    </tr>
    <tr id="trp_titleend">
      <td>名称结束代码：</td>
      <td>
<span onClick="if(document.Form.p_titleend.rows>2)document.Form.p_titleend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_titleend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_titleend" cols="70" rows="3"><?php echo $p_titleend?></textarea>	  </td>
    </tr>
    <?php }?>
	<tr>
	 <td>连载代码范围：</td>
	 <td><input type="radio" value="0" name="p_lzcodetype" onClick="ChangeCutPara(0,'trp_lzcodestart','trp_lzcodeend');" <?php if ($p_lzcodetype==0) {echo "checked";} ?>>  
	 关闭&nbsp;&nbsp; <input type="radio" value="1" name="p_lzcodetype" onClick="ChangeCutPara(1,'trp_lzcodestart','trp_lzcodeend');" <?php if ($p_lzcodetype==1) {echo "checked";} ?>> 
	 开启</td>
	 </tr>
	 	 
 <tr id="trp_lzcodestart" <?php if ($p_lzcodetype <> 1 ) { echo "style=\"display:none\""; }?>>
 <td>连载范围开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_lzcodestart.rows>2)document.Form.p_lzcodestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_lzcodestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_lzcodestart" cols="70" rows="3" id="p_lzcodestart"><?php echo $p_lzcodestart ?></textarea></td>
 </tr>
  <tr id="trp_lzcodeend" <?php if ($p_lzcodetype <>1) { echo "style=\"display:none\"";} ?>>
 <td>连载范围结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_lzcodeend.rows>2)document.Form.p_lzcodeend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_lzcodeend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_lzcodeend" cols="70" rows="3" id="p_lzcodeend"><?php echo $p_lzcodeend?></textarea></td>
 </tr>
 		 
    <tr>
      <td vAlign=center >连载开始代码：</td>
      <td>
      <span onClick="if(document.Form.p_lzstart.rows>2)document.Form.p_lzstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_lzstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_lzstart" cols="70" rows="3"><?php echo $p_lzstart?></textarea>	 
 </td>
    </tr>
    <tr>
      <td vAlign=center >连载结束代码：</td>
      <td>
      <span onClick="if(document.Form.p_lzend.rows>2)document.Form.p_lzend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_lzend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_lzend" cols="70" rows="3"><?php echo $p_lzend?></textarea>	 
      </td>
    </tr>
 <tr>
      <td vAlign=center >备注开始代码：</td>
      <td>
      <span onClick="if(document.Form.p_remarksstart.rows>2)document.Form.p_remarksstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_remarksstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_remarksstart" cols="70" rows="3"><?php echo $p_remarksstart?></textarea>	 
 </td>
    </tr>
    <tr>
      <td vAlign=center >备注结束代码：</td>
      <td>
      <span onClick="if(document.Form.p_remarksend.rows>2)document.Form.p_remarksend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_remarksend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_remarksend" cols="70" rows="3"><?php echo $p_remarksend?></textarea>	 
      </td>
    </tr>
    
    <?php if ($p_starringtype ==0) {?>
    <tr id="trp_starringstart">
      <td>主演开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_starringstart.rows>2)document.Form.p_starringstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_starringstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_starringstart" cols="70" rows="3"><?php echo $p_starringstart?></textarea>	  </td>
    </tr>
    <tr id="trp_starringend">
      <td>主演结束代码：</td>
      <td>
<span onClick="if(document.Form.p_starringend.rows>2)document.Form.p_starringend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_starringend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_starringend" cols="70" rows="3"><?php echo $p_starringend?></textarea>	  </td>
    </tr>
    <?php }?>
    <tr id="trp_directedstart">
      <td>导演开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_directedstart.rows>2)document.Form.p_directedstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_directedstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_directedstart" cols="70" rows="3"><?php echo $p_directedstart?></textarea>	  </td>
    </tr>
    <tr id="trp_directedend">
      <td>导演结束代码：</td>
      <td>
<span onClick="if(document.Form.p_directedend.rows>2)document.Form.p_directedend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_directedend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_directedend" cols="70" rows="3"><?php echo $p_directedend?></textarea>	  </td>
    </tr>
    <?php if ($p_pictype ==0) {?>
    <tr id="trp_picstart">
      <td>图片开始代码：</td>
      <td>
 <span onClick="if(document.Form.p_picstart.rows>2)document.Form.p_picstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_picstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_picstart" cols="70" rows="3"><?php echo $p_picstart?></textarea>	  </td>
    </tr>
    <tr id="trp_picend">
      <td>图片结束代码：</td>
      <td>
<span onClick="if(document.Form.p_picend.rows>2)document.Form.p_picend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_picend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_picend" cols="70" rows="3"><?php echo $p_picend?></textarea>	  </td>
    </tr>
    <?php }?>
    
 	<tr>
        <td><font color="#FF0000">栏目设置：</font></td>
        <td>
		<input type="radio" value="0" name="p_classtype" onClick="$('#trp_typestart').css('display','none');$('#trp_typeend').css('display','none');$('#trp_classtype').css('display','');$('#p_collect_type').css('display','');" <?php if ($p_classtype==0) { echo "checked";} ?>>
          固定栏目&nbsp;&nbsp; 
		<input type="radio" value="1" name="p_classtype" onClick="$('#trp_classtype').css('display','none');$('#p_collect_type').css('display','none');$('#trp_typestart').css('display','');$('#trp_typeend').css('display','');" <?php if ($p_classtype==1 ) { echo "checked";} ?>>
按对应栏目自动转换</td>
	  </tr>
	  <tr id="trp_classtype" <?php if ($p_classtype==1 ) { echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">选择入库栏目：</font></td>
        <td id="CollectClassN2" >
		<select name="p_collect_type" id="CollectClass" size="1">
	  	<option value="0">请选择入库分类</option>
		<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$p_collect_type)?>
      </select></td>
    </tr>
      <tr id="trp_typestart" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">栏目开始代码：</font></td>
        <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_typestart.rows>2)document.Form.p_typestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_typestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
          <textarea name="p_typestart" cols="70" rows="3" id="p_typestart"><?php echo $p_typestart?></textarea></td>
      </tr>
      <tr id="trp_typeend" <?php if ($p_classtype==0 ){ echo "style=\"display:none\"";} ?>>
        <td><font color="#FF0000">栏目结束代码：</font></td>
        <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_typeend.rows>2)document.Form.p_typeend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_typeend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
          <textarea name="p_typeend" cols="70" rows="3" id="p_typeend"><?php echo $p_typeend?></textarea></td>
      </tr>	
      
  <tr>
 <td>日期开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_timestart.rows>2)document.Form.p_timestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_timestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_timestart" cols="70" rows="3" id="p_timestart"><?php echo $p_timestart?></textarea></td>
 </tr>
 <tr>
 <td>日期结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_timeend.rows>2)document.Form.p_timeend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_timeend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_timeend" cols="70" rows="3" id="p_timeend"><?php echo $p_timeend?></textarea></td>
 </tr>
 
  <tr>
 <td>地区开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_areastart.rows>2)document.Form.p_areastart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_areastart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_areastart" id="p_areastart" cols="70" rows="3"><?php echo $p_areastart?></textarea></td>
 </tr>
 
 <tr>
 <td>地区结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_areaend.rows>2)document.Form.p_areaend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_areaend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_areaend" id="p_areaend" cols="70" rows="3"><?php echo $p_areaend?></textarea></td>
 </tr>
 <tr>
 <td>语言开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_languagestart.rows>2)document.Form.p_languagestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_languagestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_languagestart" id="p_languagestart" cols="70" rows="3"><?echo $p_languagestart?></textarea></td>
 </tr>
 <tr>
 <td>语言结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_languageend.rows>2)document.Form.p_languageend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_languageend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
 <textarea name="p_languageend" id="p_languageend" cols="70" rows="3"><?echo $p_languageend?></textarea></td>
 </tr>
 <tr>
 <td>介绍开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_contentstart.rows>2)document.Form.p_contentstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_contentstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_contentstart" cols="70" rows="3" id="p_contentstart"><?php echo $p_contentstart?></textarea></td>
 </tr>
 <tr>
 <td>介绍结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_contentend.rows>2)document.Form.p_contentend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_contentend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_contentend" cols="70" rows="3" id="p_contentend"><?php echo $p_contentend?></textarea></td>
 </tr>
<tr>
 <td>列表范围：</td>
 <td><input type="radio" value="0" name="p_playcodetype" onClick="ChangeCutPara(0,'trp_playcodestart','trp_playcodeend');ChangeCutParaList(0,'trp_playcodeApiUrl,trp_playcodeApiUrltype,trp_playcodeApiUrlParamstart,trp_playcodeApiUrlParamend');" <?php if ($p_playcodetype==0 ){ echo "checked" ;}?>>  
 关闭&nbsp;&nbsp; <input type="radio" value="1" name="p_playcodetype" onClick="ChangeCutPara(1,'trp_playcodestart','trp_playcodeend');ChangeCutParaList(0,'trp_playcodeApiUrl,trp_playcodeApiUrltype,trp_playcodeApiUrlParamstart,trp_playcodeApiUrlParamend');" <?php if ($p_playcodetype==1 ){ echo "checked";} ?>> 
 开启&nbsp;&nbsp; <input type="radio" value="2" name="p_playcodetype" onClick="ChangeCutParaList(0,'trp_playcodestart,trp_playcodeend');ChangeCutParaList(1,'trp_playcodeApiUrl,trp_playcodeApiUrltype,trp_playcodeApiUrlParamstart,trp_playcodeApiUrlParamend');" <?php if ($p_playcodetype==2 ){ echo "checked" ;}?>>  
 来自api</td>
 </tr> 
 <tr id="trp_playcodestart" <?php if ($p_playcodetype !=1 ){ echo "style=\"display:none\"";} ?>>
 <td>播放列表开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playcodestart.rows>2)document.Form.p_playcodestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playcodestart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playcodestart" cols="70" rows="3" id="p_playcodestart"><?php echo $p_playcodestart?></textarea></td>
 </tr>
  <tr id="trp_playcodeend" <?php if ($p_playcodetype !=1 ){ echo "style=\"display:none\"";} ?>>
 <td>播放列表结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playcodeend.rows>2)document.Form.p_playcodeend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playcodeend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playcodeend" cols="70" rows="3" id="p_playcodeend"><?php echo $p_playcodeend?></textarea></td>
 </tr>
 
 
 <tr id="trp_playcodeApiUrl" <?php if ($p_playcodetype !=2 ){ echo "style=\"display:none\"";} ?>>
 <td>播放列表API Url：</td>
 <td>&nbsp;&nbsp;page={PAGE_NO} /channel_id={PROD_ID} /pid={PROD_ID}： <b></b><br>
  <input name="p_playcodeApiUrl" value=<?php echo $p_playcodeApiUrl?>" size="80">></td>
 </tr>
 
 <tr id="trp_playcodeApiUrltype" <?php if ($p_playcodetype !=2 ){ echo "style=\"display:none\"";} ?>>
 <td><font color="#FF0000">获取参数设置：</font></td>
 <td><input type="radio" value="0" name="p_playcodeApiUrltype"  <?php if ($p_playcodeApiUrltype==0) {echo "checked" ;}?>>
  内容页直接获取地址&nbsp;&nbsp; 
<input type="radio" value="1" name="p_playcodeApiUrltype" <?php if ($p_playcodeApiUrltype==1){ echo "checked";}?>>
  	 链接中获取地址  	
</td>
 </tr>
 
 
<tr id="trp_playcodeApiUrlParamstart" <?php if ($p_playcodetype !=2 ){ echo "style=\"display:none\"";} ?>>
 <td>API参数开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <br>
  <textarea name="p_playcodeApiUrlParamstart" cols="70" rows="3" id="p_playcodeApiUrlParamstart"><?php echo $p_playcodeApiUrlParamstart?></textarea></td>
 </tr>
 <tr id="trp_playcodeApiUrlParamend" <?php if ($p_playcodetype !=2 ){ echo "style=\"display:none\"";} ?>>
 <td>API参数结束代码：</td>
 <td>&nbsp;&nbsp;输入区域：<br>
  <textarea name="p_playcodeApiUrlParamend" cols="70" rows="3" id="p_playcodeApiUrlParamend"><?php echo $p_playcodeApiUrlParamend?></textarea></td>
 </tr>
 
<tr>
 <td><font color="#FF0000">获取地址设置：</font></td>
 <td><input type="radio" value="0" name="p_playlinktype" onClick="ChangeCutPara(0,'trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==0) {echo "checked" ;}?>>
  内容页直接获取地址&nbsp;&nbsp; <input type="radio" value="1" name="p_playlinktype" onClick="ChangeCutPara(1,'trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==1){ echo "checked";}?>>
  	 &nbsp;&nbsp; 播放页获取地址
<input type="radio" value="2" name="p_playlinktype" onClick="ChangeCutPara(1,'trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==2){ echo "checked";}?>>
  	 播放链接中获取地址
  	&nbsp;&nbsp; <input type="radio" value="3" name="p_playlinktype" onClick="ChangeCutPara(1,'trp_playlinkstart','trp_playlinkend');" <?php if ($p_playlinktype==3) {echo "checked" ;}?> >
  单播放页获取所有播放地址
</td>
 </tr>
 <tr id="trp_playlinkstart">
 <td>播放链接开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playlinkstart.rows>2)document.Form.p_playlinkstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playlinkstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playlinkstart" cols="70" rows="3" id="p_playlinkstart"><?php echo $p_playlinkstart?></textarea></td>
 </tr>
 <tr id="trp_playlinkend">
 <td>播放链接结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playlinkend.rows>2)document.Form.p_playlinkend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playlinkend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playlinkend" cols="70" rows="3" id="p_playlinkend"><?php echo $p_playlinkend?></textarea></td>
 </tr>
 <tr id="trp_playspecialtype">
 <td>特殊播放链接处理：</td>
 <td><input type="radio" value="0" name="p_playspecialtype" checked="checked" onClick="ChangeCutPara(0,'listurl2','listurl3');" <?php if ($p_playspecialtype==0 ){ echo "checked";}?>>
  不作设置&nbsp;&nbsp;
  <input type="radio" value="1" name="p_playspecialtype" onClick="ChangeCutPara(1,'listurl2','listurl3');" <?php if ($p_playspecialtype==1) { echo "checked";}?>>
  替换地址&nbsp;&nbsp;
  <input type="radio" value="2" name="p_playspecialtype" onClick="ChangeCutPara(1,'listurl2','listurl3');" <?php if ($p_playspecialtype==2) { echo "checked";}?>>
  合并地址<br>
  <font color="red">对于使用了JavaScript:openwindow形式的连接请使用以下格式处理:<br>
  脚本连接:内容[变量] 内容 如:javaScript:OpenWnd([变量])<br>
  实际连接:内容[变量] 内容 如:play.php?id=[变量]</font></td>
 </tr>
 <tr id="listurl2" <?php if ($p_playspecialtype!=1) { echo "style=\"display:none\"";} ?>>
 <td>要替换的地址：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playspecialrrul.rows>2)document.Form.p_playspecialrrul.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playspecialrrul.rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_playspecialrrul);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
  <textarea name="p_playspecialrrul" cols="70" rows="3" id="p_playspecialrrul"><?php echo $p_playspecialrrul?></textarea></td>
 </tr>
 <tr id="listurl3" <?php if ($p_playspecialtype !=1) { echo "style=\"display:none\"";} ?>>
 <td>替换为的地址：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playspecialrerul.rows>2)document.Form.p_playspecialrerul.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playspecialrerul.rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_playspecialrerul);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
  <textarea name="p_playspecialrerul" cols="70" rows="3" id="p_playspecialrerul"><?php echo $p_playspecialrerul?></textarea></td>
 </tr>
 <tr>
 <td>地址开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playurlstart.rows>2)document.Form.p_playurlstart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playurlstart.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playurlstart" cols="70" rows="3" id="p_playurlstart"><?php echo $p_playurlstart?></textarea></td>
 </tr>
 <tr>
 <td>地址结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_playurlend.rows>2)document.Form.p_playurlend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_playurlend.rows+=1" style='cursor:hand'><b>扩大</b></span><br>
  <textarea name="p_playurlend" cols="70" rows="3" id="p_playurlend"><?php echo $p_playurlend?></textarea></td>
 </tr>
 
 

 
 <tr>
 <td><font color="#FF0000">获取视频地址设置：</font></td>
 <td><input type="radio" value="0" name="p_videocodeType" onClick="ChangeCutParaList(0,'trp_videourlend,trp_videourlstart,trp_videocodeApiUrl,trp_videocodeApiUrlParamstart,trp_videocodeApiUrlParamend');" <?php if ($p_videocodeType==0) {echo "checked" ;}?>>
  不能获取
  	&nbsp;&nbsp; <input type="radio" value="1" name="p_videocodeType" onClick="ChangeCutParaList(0,'trp_videocodeApiUrlParamstart,trp_videocodeApiUrlParamend,trp_videocodeApiUrl');ChangeCutParaList(1,'trp_videourlstart,trp_videourlend');" <?php if ($p_videocodeType==1) {echo "checked" ;}?> >
  Base64Decode 获得视频地址&nbsp;&nbsp; <input type="radio" value="2" name="p_videocodeType" onClick="ChangeCutParaList(0,'trp_videocodeApiUrlParamstart,trp_videocodeApiUrlParamend');ChangeCutParaList(1,'trp_videocodeApiUrl,trp_videourlstart,trp_videourlend');" <?php if ($p_videocodeType==2){ echo "checked";}?>>
  	 &nbsp;&nbsp; 直接构造
<input type="radio" value="3" name="p_videocodeType" onClick="ChangeCutParaList(1,'trp_videourlend,trp_videourlstart,trp_videocodeApiUrl,trp_videocodeApiUrlParamstart,trp_videocodeApiUrlParamend');" <?php if ($p_videocodeType==3){ echo "checked";}?>>
  	 通过api来获取
</td>
 </tr>
 
  <tr id="trp_videocodeApiUrl" <?php if ($p_videocodeType !=3  || $p_videocodeType !=2 ) { echo "style=\"display:none\"";} ?>>
 <td>视频地址API Url：</td>
 <td>&nbsp;&nbsp;如果是api：channel_id={PROD_ID} /pid={PROD_ID}： <br>
 &nbsp;&nbsp;如果是直接构造：/vid/{PROD_ID}/type/mp4/ts/3333333224/useKeyframe/0/v.m3u8  <br>
  <input name="p_videocodeApiUrl" value="<?php echo $p_videocodeApiUrl?>" size="80"></td>
 </tr>
<tr id="trp_videocodeApiUrlParamstart" <?php if ($p_videocodeType !=3) { echo "style=\"display:none\"";} ?>>
 <td>视频地址API参数开始代码：</td>
 <td>&nbsp;&nbsp;输入区域： <br>
  <textarea name="p_videocodeApiUrlParamstart" cols="70" rows="3" id="p_videocodeApiUrlParamstart"><?php echo $p_videocodeApiUrlParamstart?></textarea></td>
 </tr>
 <tr id="trp_videocodeApiUrlParamend" <?php if ($p_videocodeType !=3) { echo "style=\"display:none\"";} ?>>
 <td>视频地址API参数结束代码：</td>
 <td>&nbsp;&nbsp;输入区域：<br>
  <textarea name="p_videocodeApiUrlParamend" cols="70" rows="3" id="p_videocodeApiUrlParamend"><?php echo $p_videocodeApiUrlParamend?></textarea></td>
 </tr>
 
 <tr id="trp_videourlstart" <?php if ($p_videocodeType ==0) { echo "style=\"display:none\"";} ?>>
 <td>视频地址开始代码：</td>
 <td>&nbsp;&nbsp;输入区域：<br>
  <textarea name="p_videourlstart" cols="70" rows="3" id="p_playurlstart"><?php echo $p_videourlstart?></textarea></td>
 </tr>
 <tr id="trp_videourlend" <?php if ($p_videocodeType ==0) { echo "style=\"display:none\"";} ?>>
 <td>视频地址结束代码：</td>
 <td>&nbsp;&nbsp;输入区域： <br>
  <textarea name="p_videourlend" cols="70" rows="3" id="p_videourlend"><?php echo $p_videourlend?></textarea></td>
 </tr>
 
 <tr id="tr_SetNameType">
 <td>截取集数名称：</td>
 <td><input type="radio" value="0" name="p_setnametype" checked="checked" onClick="ChangeCutPara(0,'trP_SetNameStart','trP_SetNameEnd');" <?php if( $p_setnametype==0) { echo "checked";}?>>
  不截取&nbsp;&nbsp;
  <input type="radio" value="1" name="p_setnametype" onClick="ChangeCutPara(1,'trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==1){echo "checked";}?>>
  播放地址中截取&nbsp;&nbsp;
  <input type="radio" value="2" name="p_setnametype" onClick="ChangeCutPara(1,'trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==2){echo "checked";}?>>
  播放页中截取&nbsp;&nbsp;
  <input type="radio" value="3" name="p_setnametype" onClick="ChangeCutPara(1,'trP_SetNameStart','trP_SetNameEnd');" <?php if($p_setnametype==3){echo "checked";}?>>
  内容页中截取&nbsp;&nbsp;
	<br>
	</td>
	</tr>
	<tr  id="trP_SetNameStart" <?php if ($p_setnametype ==0) {echo "style=\"display:none\"";} ?>>
	<td>集数名称开始代码：</td>
	<td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_setnamestart.rows>2)document.Form.p_setnamestart.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_setnamestart.rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_setnamestart);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_setnamestart" cols="70" rows="3" id="p_setnamestart"><?php echo $p_setnamestart?></textarea></td>
	</tr>
	<tr id="trP_SetNameEnd" <?php if ($p_setnametype ==0){ echo "style=\"display:none\"";} ?>>
	<td>集数名称结束代码：</td>
 	<td>&nbsp;&nbsp;输入区域： <span onClick="if(document.Form.p_setnameend.rows>2)document.Form.p_setnameend.rows-=1" style='cursor:hand'><b>缩小</b></span> <span onClick="document.Form.p_setnameend.rows+=1" style='cursor:hand'><b>扩大</b></span> &nbsp;&nbsp;可用标签：<font onmouseover="getActiveText(document.Form.p_setnameend);" onClick="addTag('[变量]')" style="CURSOR: hand"><b>[变量]</b></font><br />
	<textarea name="p_setnameend" cols="70" rows="3" id="p_setnameend"><?php echo $p_setnameend?></textarea></td>
	</tr>
	<tr>
	<td colspan="2"><input type="submit" class="btn" id="btnNext" name="btnNext" value="下一步"></td>
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

function ChangeCutParaList(flag,listElement)
{   var elments = listElement.split(",");
    var x;
    for(x in elments){
	switch (flag)
	{
	case 0 :
	$("#"+elments[x]).css("display","none");
	//$("#"+element2).css("display","none");
	break;
	case 1 :
	$("#"+elments[x]).css("display","");
	//$("#"+element2).css("display","");
	break;
	}
	}
}

ChangeCutPara(<?php echo $p_lzcodetype?>,'trp_zzcodestart','trp_zzcodeend');
ChangeCutPara(<?php echo $p_playcodetype?>,'trp_playcodestart','trp_playcodeend');
ChangeCutPara(<?php echo $p_playlinktype?>,'trp_playlinkstart','trp_playlinkend');
ChangeCutPara(<?php echo $p_playspecialtype?>,'listurl2','listurl3');
ChangeCutPara(<?php echo $p_setnametype?>,'trp_setnamestart','trp_setnameend');

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
	$p_timeend = be("post","p_timeend") ; $p_areastart=  be("post","p_areastart") ;
	$p_areaend = be("post","p_areaend") ; $p_classtype=  be("post","p_classtype") ;
	$p_collect_type = be("post","p_collect_type") ; $p_typestart=  be("post","p_typestart") ;
	$p_typeend = be("post","p_typeend") ; $p_contentstart=  be("post","p_contentstart") ;
	
	$p_contentend = be("post","p_contentend") ; $p_playcodetype=  be("post","p_playcodetype") ;
	$p_playcodestart = be("post","p_playcodestart") ; $p_playcodeend=  be("post","p_playcodeend") ;
	
	$p_playurlstart = be("post","p_playurlstart") ; $p_playurlend=  be("post","p_playurlend") ;
	$p_playlinktype = be("post","p_playlinktype") ; $p_playlinkstart=  be("post","p_playlinkstart") ;
	
	$p_playlinkend = be("post","p_playlinkend") ; $p_playspecialtype=  be("post","p_playspecialtype") ;
	$p_playspecialrrul = be("post","p_playspecialrrul") ; $p_timestart=  be("post","p_timestart") ;
	$p_playspecialrerul = be("post","p_playspecialrerul");
	
	$p_starringtype = be("post","p_starringtype");
	$p_starringstart = be("post","p_starringstart"); $p_starringend = be("post","p_starringend");
	$p_titletype = be("post","p_titletype");  $p_pictype = be("post","p_pictype");
	$p_pagetype = be("all","p_pagetype"); 
	
	$p_listcodestart = be("post","p_listcodestart"); 	$p_listcodeend = be("post","p_listcodeend");
	$p_titlestart = be("post","p_titlestart"); $p_titleend = be("post","p_titleend");
	$p_listlinkstart = be("post","p_listlinkstart"); $p_listlinkend = be("post","p_listlinkend");
	$p_picstart = be("post","p_picstart");$p_picend = be("post","p_picend");
	$p_lzstart = be("post","p_lzstart"); 	$p_lzend = be("post","p_lzend");
	
	$strlisturl = be("post","listurl"); $p_coding = be("post","p_coding");
	
	$p_lzcodetype = be("post","p_lzcodetype"); 
	$p_lzcodestart = be("post","p_lzcodestart"); $p_lzcodeend = be("post","p_lzcodeend");
	$p_languagestart = be("post","p_languagestart"); $p_languageend = be("post","p_languageend");
	$p_remarksstart = be("post","p_remarksstart"); $p_remarksend = be("post","p_remarksend");
	
	$p_directedstart = be("post","p_directedstart"); $p_directedend = be("post","p_directedend");
	
	$p_setnametype = be("post","p_setnametype"); 
	$p_setnamestart = be("post","p_setnamestart"); $p_setnameend = be("post","p_setnameend");
	
	$p_setnametype = be("post","p_setnametype"); 
	
	$p_playtype = be("post","p_playtype"); 
	
	//api start
	$playcodeApiUrl = be("post","p_playcodeApiUrl") ; $playcodeApiUrltype=  be("post","p_playcodeApiUrltype") ;
	$p_playcodeApiUrlParamend = be("post","p_playcodeApiUrlParamend") ; $playcodeApiUrlParamstart=  be("post","p_playcodeApiUrlParamstart") ;
	 if (isN($playcodeApiUrltype)) { $playcodeApiUrltype = 0;}
	 
	 $p_videocodeApiUrl=be("post","p_videocodeApiUrl");
	$p_videocodeApiUrlParamstart= be("post","p_videocodeApiUrlParamstart");
	$p_videocodeApiUrlParamend= be("post","p_videocodeApiUrlParamend");
	$p_videourlstart= be("post","p_videourlstart");
	$p_videourlend= be("post","p_videourlend");
	$p_videocodeType=be("post","p_videocodeType");
	//api end 
	
    
	if (isN($p_videocodeType)) { $p_videocodeType = 0;}
    if (isN($p_starringtype)) { $p_starringtype = 0;}
	if (isN($p_titletype)) { $p_titletype = 0;}
	if (isN($p_pictype)) { $p_pictype = 0;}
	
	$sql="select * from {pre}cj_vod_projects Where p_id=".$p_id;
	$row = $db->getRow($sql); 
	
	$p_pagetype = $row["p_pagetype"];
	$strSet="";
	
	if ($p_pagetype ==3 || $p_starringtype ==0) { 
		$strSet.="p_starringstart='".$p_starringstart."',p_starringend='".$p_starringend."',";
	}
	else{
		$p_starringstart = 	$row["p_starringstart"];
		$p_starringend = $row["p_starringend"];
	}
	if ($p_pagetype ==3 || $p_titletype ==0) {
		$strSet.="p_titlestart='".$p_titlestart."',p_titleend='".$p_titleend."',";
	}
	else{
		$p_titlestart = $row["p_titlestart"];
		$p_titleend = $row["p_titleend"];
	}
	
	if ($p_pagetype ==3 || $p_pictype ==0) { 
		$strSet.="p_picstart='".$p_picstart."',p_picend='".$p_picend."',";
	}
	else{
		$p_picstart = $row["p_picstart"];
		$p_picend = $row["p_picend"];
	}

	$strSet.="p_lzstart='".$p_lzstart."',p_lzend='".$p_lzend."',p_timestart='".$p_timestart."',p_timeend='".$p_timeend."',p_areastart='".$p_areastart."',p_areaend='".$p_areaend."',p_classtype='".$p_classtype."',p_collect_type='".$p_collect_type."',p_typestart='".$p_typestart."',p_typeend='".$p_typeend."',p_contentstart='".$p_contentstart."',p_contentend='".$p_contentend."',p_playcodetype='".$p_playcodetype."',p_playcodestart='".$p_playcodestart."',p_playcodeend='".$p_playcodeend."',p_playurlstart='".$p_playurlstart."',p_playurlend='".$p_playurlend."',p_playlinktype='".$p_playlinktype."',p_playlinkstart='".$p_playlinkstart."',p_playlinkend='".$p_playlinkend."',p_playspecialtype='".$p_playspecialtype."',p_playspecialrrul='".$p_playspecialrrul."',p_playspecialrerul='".$p_playspecialrerul."',p_lzcodetype='".$p_lzcodetype."',p_lzcodestart='".$p_lzcodestart."',p_lzcodeend='".$p_lzcodeend."',p_languagestart='".$p_languagestart."',p_languageend='".$p_languageend."',p_remarksstart='".$p_remarksstart."',p_remarksend='".$p_remarksend."',p_directedstart='".$p_directedstart."',p_directedend='".$p_directedend."',p_setnametype='".$p_setnametype."',p_setnamestart='".$p_setnamestart."',p_setnameend='".$p_setnameend."'";
	$strSet=$strSet.",p_playcodeApiUrl='".$playcodeApiUrl."',p_playcodeApiUrltype='".$playcodeApiUrltype."',p_playcodeApiUrlParamend='".$p_playcodeApiUrlParamend."',p_playcodeApiUrlParamstart='".$playcodeApiUrlParamstart."'";
	$strSet=$strSet.",p_videocodeApiUrl='".$p_videocodeApiUrl."',p_videocodeApiUrlParamstart='".$p_videocodeApiUrlParamstart."',p_videocodeApiUrlParamend='".$p_videocodeApiUrlParamend."',p_videourlstart='".$p_videourlstart."',p_videourlend='".$p_videourlend."',p_videocodeType='".$p_videocodeType."'"; 
	
 	$db->query("update {pre}cj_vod_projects set " .$strSet . " where p_id=" .$p_id);
 	
	$p_listcodestart =  $row["p_listcodestart"];
	$p_listcodeend =  $row["p_listcodeend"];
	$p_listlinkstart = $row["p_listlinkstart"];
	$p_listlinkend = $row["p_listlinkend"];
	$p_playcodestart = $row["p_playcodestart"];
	$p_playcodeend = $row["p_playcodeend"];
	$p_pagebatchurl = $row["p_pagebatchurl"];
	$p_pagebatchid1 = $row["p_pagebatchid1"];
	$p_pagebatchid2 = $row["p_pagebatchid2"];
	$p_server =  $row["p_server"];
	$UrlTestMoive='';
	if ($p_server > 0 ) { $p_server_address = $db->getOne("select ds_url from {pre}vod_server where ds_id=".$p_server);}
	$p_script = $row["p_script"];
//	echo $p_pagetype;
	if ($p_pagetype != 3){
		if( isN($_SESSION["strListCode"] )){
			$strListCode = getPage($strlisturl,$p_coding);
			$_SESSION["strListCode"] = $strListCode;
		}
		else{
			$strListCode = $_SESSION["strListCode"];
		}
		
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
		
		if ($p_starringtype ==1){
			$starringarrcode = getArray($strListCodeCut,$p_starringstart,$p_starringend);
		}
		if ($p_titletype ==1) {
			$titlearrcode = getArray($strListCodeCut,$p_titlestart,$p_titleend);
		}
		if ($p_pictype ==1) {
			$picarrcode = getArray($strListCodeCut,$p_picstart,$p_picend);
		}
		
		switch($linkarrcode)
		{
			Case False:
				errmsg ("采集提示","<li>在获取链接列表时出错。".$linkarrcode."</li>");break;
			default:
				$linkarr = explode("{Array}",$linkarrcode);
				
				$UrlTest = getHrefFromLink($linkarr[0]);
				$UrlTest = definiteUrl($UrlTest,$strlisturl);
//				var_dump($UrlTest);
				$linkcode = getPage($UrlTest,$p_coding);
				$UrlTestMoive=$UrlTest;
				echo ("<li>采集提示：采集页面：".$UrlTest."</li>");
			break;
		}
	
	}
	else{
		$strlisturl = $p_pagebatchurl;
		$p_pagebatchurl = replaceStr($p_pagebatchurl,"{ID}",$p_pagebatchid1);
		$linkcode = getPage($p_pagebatchurl,$p_coding);
	}
	var_dump($p_playtype);
	if ($linkcode ==False) { 
		errmsg ("采集提示","获取内容页失败!" );
	    return;
	}
	
	if ($p_titletype ==1) {
		switch($titlearrcode)
		{
		Case False:
			$titlecode = "获取失败";break;
		default:
			$titlearr = explode("{Array}",$titlearrcode);
			$titlecode = $titlearr[0];
			break;
		}
	}
	else{
		$titlecode = getBodytt($linkcode,$p_titlestart,$p_titleend);
		
		var_dump($titlecode);
		
	}
	

	$titlecode=chr($titlecode);
	var_dump($titlecode);
	if ($p_starringtype ==1) {
		switch($starringarrcode)
		{
		Case False:
			$starringcode = "获取失败";break;
		default:
			$starringarr = explode("{Array}",$starringarrcode);
			$starringcode = $starringarr[0];
			break;
		}
	}
	else{
		$starringcode = getBody($linkcode,$p_starringstart,$p_starringend);
	}
	
	if ($p_pictype ==1) {
		switch($picarrcode)
		{
		Case False:
			$piccode = "获取失败";break;
		default:
			$picarr = explode("{Array}",$picarrcode);
			$piccode = $picarr[0];
			break;
		}
	}
	else{
		$piccode = getBody($linkcode,$p_picstart,$p_picend);
	}
	$piccode = definiteUrl($piccode,$strlisturl);
	
	if ($p_lzcodetype ==1){
		$lzfwcode = getBody($linkcode,$p_lzcodestart,$p_lzcodeend);
		$lzcode = getBody($lzfwcode,$p_lzstart,$p_lzend);
		$lzcode = replaceStr($lzcode,"False","0");
	}
	else{
		$lzcode = getBody($linkcode,$p_lzstart,$p_lzend);
		$lzcode = replaceStr($lzcode,"False","0");
	}
	
	
	$remarkscode = getBody($linkcode,$p_remarksstart,$p_remarksend);
	$remarkscode = replaceStr($remarkscode,"False","");
	$directedcode = getBody($linkcode,$p_directedstart,$p_directedend);
	$directedcode = replaceStr($directedcode,"False","");
	
	$languagecode = getBody($linkcode,$p_languagestart,$p_languageend);
	$languagecode = replaceStr($languagecode,"False","未知");
	
	$areacode = getBody($linkcode,$p_areastart,$p_areaend);
	if ($areacode ==false){ $areacode = "未知" ;}
	
	$timecode = getBody($linkcode,$p_timestart,$p_timeend);
	if ($timecode ==false){ $timecode = date('Y-m-d',time()); ;}
	
	$contentcode = getBody($linkcode,$p_contentstart,$p_contentend);
	if ($contentcode ==false){ $contentcode = "未知" ;}
	$contentcode = replaceFilters($contentcode,$p_id,2,0);
	
	if ($p_classtype ==1) {
		$typecode = getBody($linkcode,$p_typestart,$p_typeend);
	}
	else{
		$typecode = $p_collect_type;
		$typearr = getValueByArray($cache[0], "t_id" ,$typecode );
		$typecode = $typearr["t_name"];
	}
	
	if ($p_playcodetype ==1) {
		$playcode = getBody($linkcode,$p_playcodestart,$p_playcodeend);
		if ($p_playlinktype >0) {
			$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
		}
		else{
			$weburl = getArray($playcode,$p_playurlstart,$p_playurlend);
		//	var_dump($playcode);
		}
		if ($p_setnametype == 3) {
			$setnames = getArray($playcode,$p_setnamestart,$p_setnameend);
		}
	}else if ($p_playcodetype ==2) { //from api
//		writetofile("d:\\s.txt",$linkcode) ;
//		echo $p_playcodeApiUrlParamend .'=='.$playcodeApiUrlParamstart;
   
//		echo $playcodeApiUrlParamstart .'\n' .$p_playcodeApiUrlParamend .'  = '.$playcodeApiUrltype;
		if($playcodeApiUrltype ==0){
		  $paracode = getBody($linkcode,$playcodeApiUrlParamstart,$p_playcodeApiUrlParamend);
		}else {
			 $paracode = getBody($UrlTestMoive,$playcodeApiUrlParamstart,$p_playcodeApiUrlParamend);
		}
		
//		echo $paracode;
        
		$p_apibatchurl = replaceStr($playcodeApiUrl,"{PROD_ID}",$paracode);
		$p_apibatchurls = replaceStr($p_apibatchurl,"{PAGE_NO}",1);
//		writetofile("d:\\ts.txt", $p_apibatchurls."\n");
		$playcode=getFormatPage($p_apibatchurls,$p_coding);		
//		echo $playcode."\n";
		$weburl = getArray($playcode,$p_playlinkstart,$p_playlinkend);
//		writetofile("d:\\ts.txt",'aaaaa('.$p_playlinkstart.")\n\t(".$p_playlinkend.")\n\t");
		$page_num=2;
//		writetofile("d:\\ts.txt",$weburl);
//		echo "page 1 :".$weburl .'\n';
		$flag=true;
		while ($flag && strpos($playcodeApiUrl, "{PAGE_NO}") !==false){
			$p_apibatchurls = replaceStr($p_apibatchurl,"{PAGE_NO}",$page_num);
//			echo $p_apibatchurls .'\n';
		    $playcode=getFormatPage($p_apibatchurls,$p_coding);		
		    $weburls = getArray($playcode,$p_playlinkstart,$p_playlinkend);
//		    writetofile("d:\\ts.txt", "page ".$page_num." :".$weburls .'\n');
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
			$weburl = getArray($linkcode,$p_playlinkstart,$p_playlinkend);
		}
		else{
			$weburl = getArray($linkcode,$p_playurlstart,$p_playurlend);
			
		}
		if ($p_setnametype == 3) {
			$setnames = getArray($linkcode,$p_setnamestart,$p_setnameend);
		}
	}
	$titlecode = filterScript($titlecode,$p_script);
	$titlecode = replaceFilters($titlecode,$p_id,1,0);
	$starringcode = filterScriptStar($starringcode,$p_script);
	$directedcode = filterScriptStar($directedcode,$p_script);
	$timecode = filterScript($timecode,$p_script);
	$typecode = filterScript($typecode,$p_script);
	$areacode =filterScript($areacode,$p_script);
	$piccode = filterScript($piccode,$p_script);
	$remarkscode = filterScript($remarkscode,$p_script);
	$languagecode = filterScript($languagecode,$p_script);
?>
<form name="form" action="?action=saveok" method="post">
<table class="tb">
  	<tr>
  		<td  colspan="2" align="center">采 集 测 试 结 果</td>
  	</tr>
    <tr>
      <td width="20%">名称：</td>
      <td> <?php echo $titlecode?>  连载:<?php echo $lzcode?> 备注：<?php echo $remarkscode?></td>
    </tr>
    <tr>
      <td>演员：</td>
      <td> <?php echo $starringcode?> </td>
    </tr>
    <tr>
      <td>导演：</td>
      <td> <?php echo $directedcode?> </td>
    </tr>
    <tr>
      <td>日期：</td>
      <td> <?php echo $timecode?> </td>
    </tr>
    <tr>
      <td>栏目：</td>
      <td> <?php echo $typecode?> </td>
    </tr>
    <tr>
      <td>地区：</td>
      <td> <?php echo $areacode?> </td>
    </tr>
    <tr>
      <td>语言：</td>
      <td> <?php echo $languagecode?> </td>
    </tr>
    <tr>
      <td>图片：</td>
      <td> <?php echo getHrefFromImg($piccode)?> </td>
    </tr>
    <tr>
      <td>介绍：</td>
      <td> <?php echo strip_tags($contentcode)?> </td>
    </tr>
    <?php  
		 if ($weburl != False) {
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
              $webArray=$webArraTemp;
			  for ($i=0 ;$i<count($webArray);$i++){
			  	$UrlTest = $webArray[$i];
				if ($p_playspecialtype ==1 && strpos(",".$p_playspecialrrul,"[变量]")) {
					$Keyurl = explode("[变量]",$p_playspecialrrul);
					$urli = getBody ($UrlTest,$Keyurl[0],$Keyurl[1]);
				    if ($urli==False) { break; }
					$UrlTest = replaceStr($p_playspecialrerul,"[变量]",$urli);
				}
				
			  if ($p_playspecialtype ==2 ) {
					$urArray = explode("/", $UrlTestMoive);
//					writetofile("d:\\ts.txt","ss:".$UrlTestMoive);
					$ur="";
					for($k=0;$k<count($urArray)-1;$k++){
						$ur=$ur.$urArray[$k]."/";
					}
					$UrlTest=$ur.$UrlTest.".html";
				}
				
			  	
//				writetofile("d:\\ts.txt", $UrlTest);
				
				if ($p_playlinktype ==1) {
					$UrlTest = getHrefFromLink($UrlTest);
					$UrlTest = definiteUrl($UrlTest,$strlisturl);
					$webCode = getPage($UrlTest,$p_coding);
					$url = getBody($webCode,$p_playurlstart,$p_playurlend);
					
					$url = replaceFilters($url,$p_id,3,0);
					$url = replaceLine($url);
					$androidUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseAndroidVideoUrlByContent($webCode, $p_coding, $p_script);
				    $videoAddressUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseIOSVideoUrlByContent($webCode, $p_coding, $p_script);
				    $videoAddressUrl=$androidUrl.'{====}'.$videoAddressUrl;
					
				}
				else if($p_playlinktype ==2) {
					$UrlTest = getHrefFromLink($UrlTest);
					if (isN($p_playurlend)){
						$tmpA = strpos($UrlTest, $p_playurlstart);
                		$url = substr($UrlTest,strlen($UrlTest)-$tmpA-strlen($p_playurlstart)+1);
					}
					else{
						$url = getBody($UrlTest,$p_playurlstart,$p_playurlend);
					}
				}
				else if($p_playlinktype ==3) {
					$UrlTest = getHrefFromLink($UrlTest);
					$UrlTest = definiteUrl($UrlTest,$strlisturl);
					$webCode = getPage($UrlTest,$p_coding);
					$tmpB = getArray($webCode,$p_playurlstart,$p_playurlend);
					$tmpC = explode("$Array$",$tmpB);
					foreach($tmpC as $tmpD)
					{
						$url = $tmpD;
						?><tr>
					      <td>播放列表：</td>
					      <td> <?php echo $p_server_address . $UrlTest?> </td>
					    </tr>
						<tr>
					      <td>地址：</td>
					      <td> <?php echo $p_server_address . $url?> </td>
					    </tr>
						<?php
					}
					break;
				}
				else{
					$url = replaceFilters($UrlTest,$p_id,3,0);
					$url = replaceLine($url);
//					echo $url;
					$webCode = getPage($UrlTestMoive,$p_coding);
					
					$androidUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseAndroidVideoUrlByContent($webCode, $p_coding, $p_script);
				    $videoAddressUrl = ContentProviderFactory::getContentProvider($p_playtype)->parseIOSVideoUrlByContent($webCode, $p_coding, $p_script);
				    $videoAddressUrl=$androidUrl.'{====}'.$videoAddressUrl;
					?><tr>
					      <td>播放列表：</td>
					      <td> <?php echo $p_server_address . $UrlTestMoive?> </td>
					    </tr>
					    <tr>
					      <td>视频地址列表：</td>
					      <td> <?php echo $p_server_address .  replaceStr($videoAddressUrl,"\\","")?> </td>
					    </tr>
						<tr>
					      <td>地址：</td>
					      <td> <?php echo $p_server_address . $url?> </td>
					    </tr>
						<?php
						continue;
				}
				if ($p_setnametype == 1) {
					$setname = getBody($url,$p_setnamestart,$p_setnameend);
//					$url = $setname ."$" .$url;
				}
				else if($p_setnametype == 2 && $p_playlinktype ==1) {
					$setname = getBody($webCode,$p_setnamestart,$p_setnameend);
//					$url = $setname ."$" .$url;
				}
				else if($p_setnametype==3){
					$setname= $setnamesArray[$i];
//					$url = $setnamesArray[$i] . "$" .$url;
				}
		?>
		    <tr>
		    <td>播放列表：</td>
			<td> <?php echo $UrlTest?> </td>
		    </tr><tr>
					      <td>视频地址列表：</td>
					      <td> <?php echo $p_server_address . replaceStr($videoAddressUrl,"\\","")?> </td>
					    </tr>
		    <tr>
			<td>地址：</td>
			<td> <?php echo $url?>  集数： <?php echo filterScriptStar($setname,$p_script)?> </td>
			</tr>
       <?php
           }
		 }
	?>
	<tr>
	<td  colspan="2"><input name="button" type="button" class="btn" id="button" onClick="window.location.href='javascript:history.go(-1)'" value="上一步">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="Submit" type="submit" class="btn" id="Submit" value="完 成"></td>
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
	
	$sql = "select * from {pre}cj_vod_projects ";
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount=ceil($nums/app_pagenum);//总页数
	$sql = $sql ."limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		菜单：<a href="collect_vod_manage.php?action=add">添加采集规则</a> | <a href="collect_vod_manage.php?action=upexp">导入采集规则</a> | <a href="collect_vod_change.php">分类转换</a> | <a href="collect_vod_filters.php">信息过滤</a> 
	</td>
	</tr>
</table>
<form action="" method="post" name="form1">
<table class="tb">
    <tr>
	  <td width="4%">&nbsp;</td>
      <td>项目名称</td>
      <td width="10%">播放类型</td>
      <td width="10%">入库分类</td>
      <td width="10%">上次采集</td>
      <td width="50%">操作</td>
    </tr>
	<?php
	if (!$rs){
	?>
    <tr><td align="center" colspan="7" >没有任何记录!</td></tr>
    <?php
	}
	else{
	  	while ($row = $db ->fetch_array($rs))
	  	{
	?>
    <tr>
	  <td><input name="p_id[]" type="checkbox" id="p_id" value="<?php echo $row["p_id"]?>" /></td>
      <td><a href="?action=edit&p_id=<?php echo $row["p_id"]?>"><?php echo $row["p_name"]?></a></td>
      
	  <td><?php echo $row["p_playtype"]?></td>
	  <td>
	  <?php
	  	if ($row["p_classtype"] == 1){
	  		echo "<font color=red>自定义分类</font>";
	  }
	  	else{
	  		$typearr = getValueByArray($cache[0], "t_id", $row["p_collect_type"]);
	  		echo $typearr["t_name"];
	  	}
	  ?>
	  </td>
      <td><?php echo isToDay($row['p_time']) ?></td>
 	  <td>
 	  <A href="collect_vod_cj.php?ignoreExistM=true&p_id=<?php echo  $row["p_id"] ?>">采集</A>｜
 	   <A href="collect_vod_cj.php?p_id=<?php echo  $row["p_id"] ?>">采集(包括已经采集过)</A>｜
 	  <A href="?action=collectSimple&p_id=<?php echo $row["p_id"]?>&p_name=<?php echo $row["p_name"]?>">采集单个视频</A>｜
 	  <A href="?action=collectVideo&p_id=<?php echo $row["p_id"]?>&p_name=<?php echo $row["p_name"]?>">采集视频地址</A>｜
 	  <A href="?action=edit&p_id=<?php echo $row["p_id"]?>">修改</A>｜
 	  <A href="?action=copy&p_id=<?php echo  $row["p_id"] ?>">复制</A>｜
 	  <A href="?action=export&p_id=<?php echo  $row["p_id"] ?>">导出</A>｜
 	  <A href="?action=del&p_id=<?php echo $row["p_id"]?>">删除</A>
 	  
 	  </td>
    </tr>
	<?php
		}
	}
	?>
	<tr>
	<td  colspan="6">
	全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'p_id[]');"/>&nbsp;
	<input type="submit" value="批量删除" onClick="if(confirm('确定要删除吗')){form1.action='?action=delall';}else{return false}"  class="input"/>
	<input type="submit" value="批量采集" onClick="if(confirm('确定要批量采集吗')){form1.action='collect_vod_cj.php?action=pl';}else{return false}"  class="input"/>
	</td>
	</tr>
    <tr align="center" bgcolor="#f8fbfb">
      <td colspan="7">
        <?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod_manage.php?page={p}") ?>
      </td>
    </tr>
</table>
</form>
<?php
}
?>