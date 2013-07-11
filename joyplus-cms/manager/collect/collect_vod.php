<?php
require_once ("../admin_conn.php");
require_once (dirname(__FILE__)."/../tools/MailUtils.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
//chkLogin();
$action = be("all","action");
headAdminCollect ("视频采集入库管理");

switch($action)
{
	case "edit" : edit();break;
	case "editsave" : editsave();break;
	case "del" : del();break;
	case "delpl" : delpl();break;
	case "delall" : delall();break;
	case "delurl" : delurl();break;
	case "IDInflow" : IDInflow();break;
	case "IDInflowZhuiJu" : IDInflowZhuiJu();break;

	case "AllInflow" : AllInflow();break;
	case "noInflow" : noInflow();break;
	case "editype" : editype();break;

	case "noInflowProject" : noInflowProject();break;

	case "AllInflowProject" : AllInflowProject();break;
	case "main"  : main();break;
}

function editype()
{
	global $db;
	$m_typeid = be("post","m_typeid");
	if (isN($m_typeid)) {  alert("请选择分类！");}
	$ids = be("arr","m_id");
	$db->query("update {pre}cj_vod set m_typeid=".$m_typeid." where m_id in (" .$ids.")");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function IDInflow()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_id in (".$ids.") and m_typeid>0 and m_name IS NOT NULL AND m_name != ''  and m_playfrom not in ('tudou','kankan','cntv','wasu')");
		$sql="select * from {pre}cj_vod where m_id in (".$ids.") and m_typeid>0 and m_name IS NOT NULL AND m_name != ''  and m_playfrom not in ('tudou','kankan','cntv','wasu')";
		MovieInflow($sql,$count,false);
	}
	else{
		showmsg ("请选择入库数据！",$backurl);
	}
}

function IDInflowZhuiJu()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$sql="select * from {pre}cj_vod where m_id in (".$ids.") and m_typeid>0 and m_name IS NOT NULL AND m_name != ''  and m_playfrom not in ('tudou','kankan','cntv','wasu')";
		$rs=$db->query($sql);
		while ($row = $db ->fetch_array($rs))
		{   $typeid = $row["m_typeid"];
		if($typeid ==='2' || $typeid ==='3' || $typeid ==='131'){
			$db->Add('mac_cj_zhuiju', array("m_id", "m_pid", "m_name", "m_typeid", "m_playfrom", "m_urltest"),
			array($row["m_id"], $row["m_pid"], $row["m_name"], $typeid, $row["m_playfrom"], $row["m_urltest"]));
		}
		}
		showmsg ("添加成功！",$backurl);
	}
	else{
		showmsg ("请选择入库数据！",$backurl);
	}
}



function AllInflow()
{
	global $db;
	$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_typeid>0 and m_name IS NOT NULL AND m_name != ''  and m_playfrom not in ('tudou','kankan','cntv','wasu')");
	$sql="select * from {pre}cj_vod where m_typeid>0 and m_name IS NOT NULL AND m_name != '' and m_playfrom not in ('tudou','kankan','cntv','wasu')";
	MovieInflow($sql,$count,false);
}

function noInflow()
{
	global $db;
	$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_zt=0 and m_name IS NOT NULL AND m_name != '' and m_typeid>0  and m_playfrom not in ('tudou','kankan','cntv','wasu')");
	$sql="select * from {pre}cj_vod where m_zt=0 and m_name IS NOT NULL AND m_name != '' and m_typeid>0  and m_playfrom not in ('tudou','kankan','cntv','wasu')";
	MovieInflow($sql,$count,false);
}



function AllInflowProject()
{
	global $db;

	$keyword = be("get","keyword");
	$from= be("get","playfrom");
	$project = be("get","cj_vod_projects");

	$where =" and  m_playfrom not in ('tudou','kankan','cntv','wasu') ";
	if ($keyword != "") {
		$where = $where . " and m_name like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$where = $where . " and m_pid = " . $project;
	}

	if ($from!= "") {
		$where = $where . " and m_playfrom ='" . $from."' ";
	}
	//var_dump($where);
	$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_typeid>0 and m_name IS NOT NULL AND m_name != '' ".$where);
	$sql="select * from {pre}cj_vod where m_typeid>0 and m_name IS NOT NULL AND m_name != ''  ".$where;
	MovieInflow($sql,$count,false);
}

function noInflowProject()
{
	global $db;
	$keyword = be("get","keyword");
	$from= be("get","playfrom");
	$project = be("get","cj_vod_projects");

	$where ="  and m_playfrom not in ('tudou','kankan','cntv','wasu')  ";
	if ($keyword != "") {
		$where = $where . " and m_name like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$where = $where . " and m_pid = " . $project;
	}

	if ($from!= "") {
		$where = $where . " and m_playfrom ='" . $from."' ";
	}

	$count = $db->getOne("Select count(m_id) as cc from {pre}cj_vod where m_zt=0 and m_typeid>0 and m_name IS NOT NULL AND m_name != ''   ".$where);
	$sql="select * from {pre}cj_vod where m_zt=0 and m_typeid>0  and m_name IS NOT NULL AND m_name != ''  ".$where;
	//var_dump($count); var_dump($sql);
	MovieInflow($sql,$count,false);

}

function del()
{
	global $db;
	$m_id=be("get","m_id");
	$db->query("delete from {pre}cj_vod_url WHERE u_movieid = ".$m_id);
	$db->query("delete from {pre}cj_vod WHERE m_id =".$m_id);
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delpl()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$db->query("delete from {pre}cj_vod_url WHERE u_movieid in( ".$ids.")");
		$db->query("delete from {pre}cj_vod WHERE m_id in(".$ids.")");
		echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
	}
	else{
		alert ("请选择相应数据！");
	}
}

function delall()
{
	global $db;
	$db->query("delete from {pre}cj_vod_url");
	$db->query("delete from {pre}cj_Vod");
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delurl()
{
	global $db;
	$u_id=be("get","u_id");
	$db->query("delete from {pre}cj_vod_url WHERE u_id=".$u_id);
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",500);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function editsave()
{
	global $db;
	$m_id = be("post","m_id") ; $m_name = be("post","m_name");
	$m_typeid = be("post","m_typeid") ; $m_area = be("post","m_area");
	$m_playfrom = be("post","m_playfrom") ; $m_starring = be("post","m_starring");
	$m_pic = be("post","m_pic") ; $m_content = be("post","m_content");
	$m_zt = be("post","m_zt") ; $m_language = be("post","m_language");
	$m_year = be("post","m_year") ; $m_playserver = be("post","m_playserver");
	$m_hits = be("post","m_hits") ; $m_state = be("post","m_state");
	$m_directed = be("post","m_directed"); $m_remarks = be("post","m_remarks");
	$m_type= be("post","m_type");
	$backurl = be("post","backurl");

	if (isN($backurl)) { $backurl = "collect_vod.php";}
	if (!isNum($m_typeid)) { alert ( "分类不能为空!");}
	if (!isNum($m_hits)) { $m_hits = 0;}
	if (!isNum($m_playserver)) { $m_playserver=0;}
	if (!isNum($m_zt)) { $m_zt = 0 ; }
	if (!isNum($m_state)) { $m_state = 0 ; }

	$sql="update {pre}cj_vod set m_name='".$m_name."',m_type='".$m_type."',m_typeid='".$m_typeid."',m_area='".$m_area."',m_language='".$m_language."',m_playfrom='".$m_playfrom."',m_starring='".$m_starring."',m_directed='".$m_directed."',m_pic='".$m_pic."',m_content='".$m_content."',m_year='".$m_year."',m_zt='".$m_zt."',m_playserver='".$m_playserver."',m_hits='".$m_hits."',m_state='".$m_state."',m_remarks='".$m_remarks."',m_addtime='".date('Y-m-d H:i:s',time())."' where m_id=". $m_id;
	//var_dump($sql);
	$db->query($sql);

	$sql="select * from {pre}cj_vod_url where u_movieid=".$m_id ." order by u_id  asc" ;
	//var_dump($sql);
	$rs=$db->query($sql);
	$i=0;
	while ($row = $db ->fetch_array($rs))
	{
		$i=$i+1;
		if(!(isN(be("post","weburl".$i)) && isN(be("post","videourl".$i))) ) {
			$sql = "update {pre}cj_vod_url set u_url='".be("post","url".$i)."' , u_weburl='".be("post","weburl".$i)."' ,name='".be("post","setname".$i)."' , iso_video_url='".be("post","videourl".$i)."' where u_id=".$row["u_id"];
			//var_dump($sql);
			$db->query( $sql );
		}
	}
	showmsg ("修改数据成功!",'/manager/collect/collect_vod.php?action=edit&m_id='.$m_id);
}

function edit()
{
	global $db;
	$m_id = be("get","m_id");
	$sql="select * from {pre}cj_vod where m_id=". $m_id;
	$row = $db->getRow($sql);

	$m_name=$row["m_name"];
	$m_type=$row["m_type"];
	$m_typeid=$row["m_typeid"];
	$m_area=$row["m_area"];
	$m_playfrom=$row["m_playfrom"];
	$m_starring=$row["m_starring"];
	$m_directed=$row["m_directed"];
	$duraning=$row["duraning"];
	$m_pic=$row["m_pic"];
	$m_content=$row["m_content"];
	$m_year=$row["m_year"];
	$m_urltest=$row["m_urltest"];
	$m_zt=$row["m_zt"];
	$m_playserver = $row["m_playserver"];
	$m_hits = $row["m_hits"];
	$m_state = $row["m_state"];
	$m_remarks = $row["m_remarks"];
	$m_language = $row["m_language"];
	$backurl =  $_SERVER["HTTP_REFERER"];
	?>
<script>
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			m_name:{
				required:true,
				maxlength:255
			},
			m_typeid:{
				required:true
			},
			m_playfrom:{
				required:true
			},
			m_state:{
				number:true
			},
			m_remarks:{
				maxlength:255
			},
			m_starring:{
				maxlength:255
			},
			m_directed:{
				maxlength:255
			},
			m_year:{
				maxlength:32
			},
			m_hits:{
				number:true
			},
			m_area:{
				maxlength:32
			},
			m_language:{
				maxlength:32
			},
			m_typeid:{
				required:true
			}
		}
	});
});
</script>
<form id="form1" name="form1" action="?action=editsave" method="post"><input
	type="hidden" id="m_id" name="m_id" value="<?php echo $m_id?>"> <input
	id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">
<table class=tb>
	<tr>
		<td width="70">名称：</td>
		<td><input id="m_name" name="m_name" type="text"
			value="<?php echo $m_name?>" size="40"> &nbsp;分类：<select
			name="m_typeid" id="m_typeid">
			<option value="">请选择数据分类</option>
			<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$m_typeid)?>
		</select> &nbsp;播放类型：<select name="m_playfrom">
			<option value=''>暂没有数据</option>
			<?php echo makeSelectPlayer($m_playfrom)?>
		</select></td>
	</tr>
	<tr>
		<td>连载：</td>
		<td><input id="m_state" name="m_state" type="text"
			value="<?php echo $m_state?>" size="50"> &nbsp;备注：<input
			id="m_remarks" name="m_remarks" type="text"
			value="<?php echo $m_remarks?>" size="50"></td>
	</tr>
	<tr>
		<td>演员：</td>
		<td><input id="m_starring" name="m_starring" type="text"
			value="<?php echo $m_starring?>" size="50"> &nbsp;导演：<input
			id="m_directed" name="m_directed" type="text"
			value="<?php echo $m_directed?>" size="50"></td>
	</tr>
	<tr>
		<td>上映：</td>
		<td><input id="m_year" name="m_year" type="text"
			value="<?php echo $m_year?>" size="50"> &nbsp;人气：<input id="m_hits"
			name="m_hits" type="text" value="<?php echo $m_hits?>" size="50">
		&nbsp;时长：<?php echo $duraning?></td>
	</tr>
	<tr>
		<td>地区：</td>
		<td><input id="m_area" name="m_area" type="text"
			value="<?php echo $m_area?>" size="50"> &nbsp;语言：<input
			id="m_language" name="m_language" type="text"
			value="<?php echo $m_language?>" size="50"></td>
	</tr>
	<tr>
		<td>类别：</td>
		<td><input id="m_type" name="m_type" type="text"
			value="<?php echo $m_type?>" size="100"></td>
	</tr>
	<tr>
		<td>图片：</td>
		<td><input id="m_pic" name="m_pic" type="text"
			value="<?php echo $m_pic?>" size="113"></td>
	</tr>
	<tr>
		<td>入库状态：</td>
		<td><input type="radio" name="m_zt" value="0"
		<?php if ($m_zt==0) { echo "checked";} ?>> 未入库 <input type="radio"
			name="m_zt" value="1" <?php if ($m_zt==1) { echo "checked";} ?>> 已入库
		</td>
	</tr>

	<tr>
		<td>播放页面地址：</td>
		<td><?php echo $m_urltest; ?></td>
	</tr>

	<tr>
		<td>播放地址：</td>
		<td><?php
		$sql="Select * from {pre}cj_vod_url where u_movieid=".$m_id ." order by u_id  asc" ;
		$i=0;
		$rs= $db->query($sql);
		while ($row = $db ->fetch_array($rs))
		{
			$i=$i+1;
			echo "<input type=\"text\" name=\"url".$i."\" size=10 value=\"".$row["u_url"]."\">  视频地址<input type=\"text\" name=\"videourl".$i."\" size=40 value=\"".$row["iso_video_url"].$row["android_vedio_url"]."\">&nbsp;网页地址<input type=\"text\" name=\"weburl".$i."\" size=40 value=\"".$row["u_weburl"]."\">&nbsp;剧集<input type=\"text\" name=\"setname".$i."\" size=25 value=\"".$row["name"]."\">&nbsp;第".$i."集&nbsp;&nbsp;&nbsp;<a href=\"?action=delurl&u_id=".$row["u_id"]."\"><font color=\"#FF0000\">删除</font></a><br>\r\n</hr>";
		}
		?></td>
	</tr>
	<tr>
		<td>介绍：</td>
		<td><textarea id="m_content" name="m_content"
			style="width: 750px; height: 150px;"><?php echo $m_content?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" class="btn" name="submit1"
			value="修 改"> <input type="button" class="btn" name="button"
			value="返 回"
			onClick="window.location.href='javascript:history.go(-1)'"></td>
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

	$keyword = be("get","keyword");
	$from= be("get","playfrom");
	$project = be("get","cj_vod_projects");
	$zt = be("get","zt");

	$sql="Select a.*,b.p_name as p_name from {pre}cj_vod a,{pre}cj_vod_projects b where a.m_pid=b.p_id and m_playfrom not in ('tudou','kankan','cntv','wasu') ";
	if ($zt != "") {
		$sql = $sql . " and m_zt = " . $zt;
	}
	if ($keyword != "") {
		$keyword= trim($keyword);
		$sql = $sql . " and m_name like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$sql = $sql . " and a.m_pid = " . $project;
	}

	if ($from!= "") {
		$sql = $sql . " and a.m_playfrom ='" . $from."' ";
	}

	$sql = $sql . " order by  m_zt asc, m_addtime desc,m_name desc " ;

	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount = ceil($nums/app_pagenum);//总页数
	$sql = $sql ." limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
	?>
<script language="javascript">
$(document).ready(function(){
	$("#btnDel").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","?action=delpl");
			$("#form1").submit();
		}
	});
	$("#btnDelall").click(function(){
		if(confirm('确定要删除吗')){
			$("#form1").attr("action","?action=delall");
			$("#form1").submit();
		}
	});
	$("#btnSelin").click(function(){
		if(confirm('确定入库您所选择的数据吗')){
			$("#form1").attr("action","?action=IDInflow");
			$("#form1").submit();
		}
	});

	$("#btnSelinZhuiju").click(function(){
		if(confirm('确定添加您所选择的数据到自动采集/自动入库吗')){
			$("#form1").attr("action","?action=IDInflowZhuiJu");
			$("#form1").submit();
		}
	});
	
	$("#btnAllin").click(function(){
		if(confirm('全部入库你所采集的数据吗')){
			$("#form1").attr("action","?action=AllInflow");
			$("#form1").submit();
		}
	});
	$("#btnNoin").click(function(){
		if(confirm('确定入库所有未入库的数据吗')){
			$("#form1").attr("action","?action=noInflow");
			$("#form1").submit();
		}
	});

	$("#noInflowProject").click(function(){
		var keyword=$("#keyword").val();
		var playfrom=$("#playfrom").val();
		var project=$("#cj_vod_projects").val();
		if(confirm('确定所搜视频入库未入库吗')){
			$("#form1").attr("action","?action=noInflowProject&keyword="+keyword+"&playfrom="+playfrom+"&cj_vod_projects="+project);
			$("#form1").submit();
		}
	});


	$("#AllInflowProject").click(function(){
		var keyword=$("#keyword").val();
		var playfrom=$("#playfrom").val();
		var project=$("#cj_vod_projects").val();
		if(confirm('确定所搜视频全部入库吗')){
			$("#form1").attr("action","?action=AllInflowProject&keyword="+keyword+"&playfrom="+playfrom+"&cj_vod_projects="+project);
			$("#form1").submit();
		}
	});

	
	 $("#btnType").click(function(){
		if(confirm('确定更新所选数据的分类吗')){
			$("#form1").attr("action","?action=editype");
			$("#form1").submit();
		}
	});
});
</script>
<TABLE width="96%" border=0 align=center cellpadding=0 cellSpacing=0
	class=tbtitle>
	<TBODY>
		<tr>
			<td>
			<form action="collect_vod.php" method="get"><strong>搜索影片：</strong> <input
				type="hidden" name="action" value="main"> <input id=keyword size=40
				name=keyword value="<?php echo $keyword;?>"> <select id="playfrom"
				name=playfrom>
				<option value="">视频播放器</option>
				<?php echo makeSelectPlayer($from)?>
			</select> <select id="cj_vod_projects" name="cj_vod_projects">
				<option value="">全部采集项目</option>
				<?php echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","","&nbsp;|&nbsp;&nbsp;",$project)?>
			</select> <INPUT class=inputbut type=submit value=搜索 name=submit></form>
			</td>
		</tr>
	</TBODY>
</TABLE>

<form action="" method="post" name="form1" id="form1">
<table class="collect_vod tb">
	<tr>
		<td width="4%">&nbsp;</td>
		<td>影片名称</td>
		<td width="7%">状态</td>
		<td width="7%">播放器</td>
		<td width="10%">栏目分类</td>
		<td width="10%">地区</td>
		<td width="10%">上映日期</td>
		<td width="15%">所属采集项目</td>
		<td width="13%">更新时间</td>
		<td width="8%">操作</td>
	</tr>
	<?php
	if (!$rs){
		?>
	<tr>
		<td align="center" colspan="9">没有任何记录!</td>
	</tr>
	<?php
	}
	else{
		$i=0;
		while ($row = $db ->fetch_array($rs))
		{
			?>
	<tr>
		<td><input name="m_id[]" type="checkbox" id="m_id"
			value="<?php echo $row["m_id"]?>" /></td>
		<td><?php echo $row["m_name"]?> (连载:<?php echo $row["m_state"]?>)</td>
		<td><?php if ($row["m_zt"]==1) { echo "<font color=\"#FF0000\">已入库</font>";} Else  { echo "未入库" ;}?></td>
		<td><?php echo $row["m_playfrom"]?></td>
		<td><?php
		if ($row["m_typeid"]==0){
			?> <font color="#FF0000">没找到对应分类请配置</font> <?php
		}
		else{
			$typearr = getValueByArray($cache[0], "t_id" , $row["m_typeid"] );
			echo $typearr["t_name"];
		}
		?></td>
		<td><?php echo $row["m_area"]?></td>
		<td><?php echo $row["m_year"]?></td>
		<td><?php echo $row["p_name"]?></td>
		<td><?php echo isToDay( $row['m_addtime'] ) ?></td>
		<td><A href="?action=edit&m_id=<?php echo $row["m_id"]?>">修改</A>｜<A
			href="?action=del&m_id=<?php echo $row["m_id"]?>">删除</A></td>
	</tr>
	<?php
		}
	}
	?>
	<tr class="formlast">
		<td colspan="9"><input name="chkall" type="checkbox" id="chkall"
			value="1" onClick="checkAll(this.checked,'m_id[]');" /> 全选 <input
			type="button" id="btnDel" value="批量删除" class="btn" /> <!--	&nbsp;<input type="button" id="btnDelall" value="删除所有" class="btn"  />-->
		&nbsp;<input type="button" id="btnSelin" class="btn" name="Submit"
			value="入库所选"> &nbsp;<input type="button" id="btnSelinZhuiju"
			class="btn" name="Submit" value="添加到自动采集入库"> <!--	&nbsp;<input type="button" id="btnAllin" class="btn" name="Submit" value="全部入库" >-->
		<!--    &nbsp;<input type="button" id="btnNoin" class="btn" name="Submit" value="入库未入库" >-->
		&nbsp;<input type="button" id="AllInflowProject" class="btn"
			name="Submit" value="所搜视频全部入库"> &nbsp;<input type="button"
			id="noInflowProject" class="btn" name="Submit" value="所搜视频入库未入库"> <font
			color="#FF0000">&nbsp; </font></td>
	</tr>
	<tr class="formlast">
		<td colspan="9">入库同名处理: <input type="radio" name="CCTV" value="0"
			checked>自动处理 <input type="radio" name="CCTV" value="1">始终新增数据 <input
			type="radio" name="CCTV" value="2">新增播放器组 <input type="radio"
			name="CCTV" value="3">不处理 <br />
		强制覆盖数据: <input type="checkbox" name="CCTV1" value="1">年份 <input
			type="checkbox" name="CCTV2" value="2">地区 <input type="checkbox"
			name="CCTV3" value="3">演员 <input type="checkbox" name="CCTV4"
			value="4">图片 <input type="checkbox" name="CCTV5" value="5">简介 <input
			type="checkbox" name="CCTV6" value="6">语言 <input type="checkbox"
			name="CCTV7" value="7">备注 <input type="checkbox" name="CCTV8"
			value="8">导演 <input type="checkbox" name="CCTV9" value="9">影片长度 <br />
		<font color="#FF0000">注意 ：自动判断播放来源，如遇到相同来源则更新数据。</font></td>
	</tr>
	<tr align="center" class="formlast">
		<td colspan="9"><?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod.php?action=main&page={p}&cj_vod_projects=".$project."&keyword=".$keyword."&playfrom=".$from) ?></td>
	</tr>
</table>
</form>
	<?php
}

function MovieInflow($sql_collect,$MovieNumW,$isMandCollect){
	global $db;
	?>
<table class=tb>
	<tr>
		<td colspan="2" align="center">入 库 状 态</td>
		<div id="refreshlentext" style="background: #006600"></div>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><span id="storagetext">正 在 入 库...</span></td>
	</tr>
</table>
	<?php
	$iscover= be("iscover","get");
	$rs = $db->query($sql_collect);
	$rscount = $MovieNumW;

	if($rscount==0){
		echo "<script>alert('没有可入库的数据!'); location.href='collect_vod.php';</script>";
		exit;
	}

	if ($rscount > 10000){
		$rscount = 1000;
	}
	elseif ($rscount > 5000) {
		$rscount = 500;
	}
	elseif ($rscount > 1000){
		$rscount = 100;
	}
	else{
		$rscount = 10;
	}


	while ($row = $db ->fetch_array($rs))
	{    if(!(isset($row["m_playfrom"]) && !is_null($row["m_playfrom"]) && strlen(trim($row["m_playfrom"]))>0) ){
		continue;
	}
	if(isset($row["m_playfrom"]) && !is_null($row["m_playfrom"]) && ($row["m_playfrom"] ==='cntv' || $row["m_playfrom"] ==='wasu' || $row["m_playfrom"] ==='kankan' || $row["m_playfrom"] ==='tudou' || $row["m_playfrom"] ==='' )){
		continue;
	}
	$flag=false;
	$title = $row["m_name"];

	$d_type = $row["m_typeid"];
	$title= replaceStr($title, "&lt;", "<<");
	$title= replaceStr($title, "&gt;", ">>");
	$title =trim(replaceStr($title, "&nbsp;", ' '));
	$title= replaceStr($title, " 国语", "");
	$testUrl = $row["m_urltest"];
	$year=$row['m_year'];
	$title = replaceStr($title,"'","''");
	$titlenolang=$title;
	$d_language = $row["m_language"];
	$flag_lang=false;
	$d_state = $row["m_state"];
	if($d_type==='131' && strpos($year, ',') !==false){
		var_dump($title." 是综艺而不是动漫。");
		continue;
	}

	if($d_type==='3' && !is_null($d_state) && strlen($d_state) != 8){
		var_dump($title." 是动漫而不是综艺。");
		continue;
	}

	if(!isN($d_language)){
		$titlenolang =trim(replaceStr($titlenolang, $d_language, ''));
			
		$titlenolang =trim($titlenolang);
			
		if (strpos($title, $d_language) !==false){
			$flag_lang=true;
		}
	}

	$strSet="";
	$sql = "SELECT * FROM {pre}vod WHERE d_name = '".$titlenolang."' and d_type = '".$d_type."' ";
	//		var_dump($sql);
	$rowvod = $db->getRow($sql);
	//	     var_dump($rowvod["d_id"]);
	if(!isN($rowvod["d_status"]) && ( $rowvod["d_status"]===1 || $rowvod["d_status"] ==='1') ){
		var_dump($titlenolang." is locked");
		if(!$isMandCollect){
			continue;
		}
	}

	if ($flag_lang && ( isN($rowvod["d_id"]) || be("post","CCTV")=="1") ) {
		$sql = "SELECT * FROM {pre}vod WHERE d_name = '".$title."' and d_type = '".$d_type."' ";
		$rowvod = $db->getRow($sql);
		if(!isN($rowvod["d_status"]) && ( $rowvod["d_status"]===1 || $rowvod["d_status"] ==='1') ){
			var_dump($title." is locked");
			if(!$isMandCollect){
				continue;
			}
		}
	}


	//插入新数据开始
	if ( isN($rowvod["d_id"]) || be("post","CCTV")=="1") {
		$flag=true;
		$d_pic= replaceStr($row["m_pic"],"'","''");
		$d_pic_ipad= replaceStr($row["d_pic_ipad"],"'","''");
		$d_addtime= date('Y-m-d H:i:s',time());
		$d_year=$row["m_year"];
		if(isN($d_year) || $d_year==='未知'){
			$d_year='其他';
		}
		$d_content=$row["m_content"];
		$d_hits= $row["m_hits"];
		$d_area = $row["m_area"];
		if(isN($d_area) || $d_area==='未知'){
			$d_area='其他';
		}
		$d_remarks = $row["m_remarks"];
		$d_state = $row["m_state"];
		$d_starring = $row["m_starring"];
		$d_directed = $row["m_directed"];
		$duraning = $row["duraning"];
		$d_name = $title;
		$typeName=$row["m_type"];
		if(isN($typeName) || $typeName==='未知'){
			$typeName='其他';
		}
		$d_enname = hanzi2pinyin($d_name);
		$d_capital_name=Hanzi2PinYin_Captial($d_name);
		if (isN($d_letter)) { $d_letter = strtoupper(substring($d_enname,1)); }
		if ($row["m_typeid"] > 0) {
			$d_type = $row["m_typeid"];
		}
		else{
			if (!isN($row["m_type"])){
				$sql = "select * from {pre}vod_type where t_name like '%" . $row["m_type"]."%' ";
				$rowtype = $db->getRow($sql);
				if ($rowtype) { $d_type = $rowtype["t_id"];}
				unset($rowtype);
			}
		}
		if(!($d_type ==='1' || $d_type ===1)){
			$duraning='';
		}
		//writetofile("gaoca.txt", $duraning);
		if(isNum($d_state) && isNum($d_remarks)){
			$sql="insert {pre}vod (d_pic_ipad,duraning,d_type_name,d_type,d_pic,d_addtime,d_time,d_year,d_content,d_hits,d_area,d_language,d_name,d_enname,d_starring,d_directed,d_state,d_remarks,d_capital_name) values('".$d_pi."' ,'".$duraning."' , '".$typeName."','".$d_type."','".$d_pic."','".$d_addtime."','".$d_addtime."','".$d_year."','".$d_content."','".$d_hits."','".$d_area."','".$d_language."','".$d_name."','".$d_enname."','".$d_starring."','".$d_directed."','".$d_state."','".$d_remarks."','".$d_capital_name."') ";
		}else if (!isNum($d_state) && $isMandCollect){
			$sql="insert {pre}vod (d_pic_ipad,duraning,d_type_name,d_type,d_pic,d_addtime,d_time,d_year,d_content,d_hits,d_area,d_language,d_name,d_enname,d_starring,d_directed,d_remarks,d_capital_name) values('".$d_pi."' ,'".$duraning."' , '".$typeName."','".$d_type."','".$d_pic."','".$d_addtime."','".$d_addtime."','".$d_year."','".$d_content."','".$d_hits."','".$d_area."','".$d_language."','".$d_name."','".$d_enname."','".$d_starring."','".$d_directed."','".$d_remarks."','".$d_capital_name."') ";
			sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 连载信息非法'.$row["m_playfrom"]);
		}else if (!isNum($d_remarks) && $isMandCollect){
			$sql="insert {pre}vod (d_pic_ipad,duraning,d_type_name,d_type,d_pic,d_addtime,d_time,d_year,d_content,d_hits,d_area,d_language,d_name,d_enname,d_starring,d_directed,d_state,d_capital_name) values('".$d_pi."' ,'".$duraning."' , '".$typeName."','".$d_type."','".$d_pic."','".$d_addtime."','".$d_addtime."','".$d_year."','".$d_content."','".$d_hits."','".$d_area."','".$d_language."','".$d_name."','".$d_enname."','".$d_starring."','".$d_directed."','".$d_state."','".$d_capital_name."') ";
			sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 备注信息非法'.$row["m_playfrom"]);
		}else if (!isNum($d_remarks) && $isMandCollect && !isNum($d_state)){
			$sql="insert {pre}vod (d_pic_ipad,duraning,d_type_name,d_type,d_pic,d_addtime,d_time,d_year,d_content,d_hits,d_area,d_language,d_name,d_enname,d_starring,d_directed,d_capital_name) values('".$d_pi."' ,'".$duraning."' , '".$typeName."','".$d_type."','".$d_pic."','".$d_addtime."','".$d_addtime."','".$d_year."','".$d_content."','".$d_hits."','".$d_area."','".$d_language."','".$d_name."','".$d_enname."','".$d_starring."','".$d_directed."','".$d_capital_name."') ";
			sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 连载、备注信息非法'.$row["m_playfrom"]);
		}
		writetofile("gaoca.txt", $sql);
		$db->query($sql);
		$did = $db->insert_id();
		//			if($d_type === '2' || $d_type === '131' ){
		//				$d_addtime= date('Y-m-d H:i:s',time());
		//			  	$db->query("INSERT INTO mac_vod_pasre_item (prod_id,create_date) VALUES('".$did."','".$d_addtime."')");
		//			}
	}
	//插入新数据结束
	else{  //同名不处理， 如果是电影也不更新
		if( be("post","CCTV")=="3" ){
			//var_dump("dd");
			continue;
		}
		//更新数据开始
		if ($row["m_typeid"] > 0) {
			$d_type = $row["m_typeid"];
		}
		else{
			if (!isN($row["m_type"])){
				$sql = "select * from {pre}vod_type where t_name like '%" . $row["m_type"]."%' ";
				$rowtype = $db->getRow($sql);
				if ($rowtype) { $d_type = $rowtype["t_id"];}
				unset($rowtype);
			}
		}
			
			
			
		$strSet .=" d_type='".$d_type."', ";
			
		$strSet .=" d_type_name='".$typeName."', ";
			
		$strSet .=" d_name='".$title."', ";
			
		$d_enname = hanzi2pinyin($title);
		$strSet .=" d_enname='".d_enname."', ";
			
		$d_capital_name=Hanzi2PinYin_Captial($title);
			
		$strSet .=" d_capital_name='".$d_capital_name."', ";
			
		if (be("post","CCTV2")=="2") {
			$d_area = $row["m_area"];
			if(isN($d_area) || $d_area==='未知'){
				$d_area='其他';
			}
			$strSet .="d_area='".$d_area."',";
		}
		if (be("post","CCTV6")=="6") {
			$d_language = $row["m_language"];
			$strSet .= "d_language='".$d_language."',";
		}
		if (be("post","CCTV7")=="7") {
			$d_remarks = $row["m_remarks"];
			if (isNum($d_remarks)){
			   $strSet .="d_remarks='".$d_remarks."',";
			}else if(!isNum($d_remarks) && $isMandCollect){
				sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 备注信息非法'.$row["m_playfrom"]);
			}
		}
		if (be("post","CCTV8")=="8") {
			$d_directed = $row["m_directed"];
			$strSet .="d_directed='".$d_directed."',";
		}
			
		if (be("post","CCTV9")=="9" && !isN($duraning)) {
			if(!($d_type ==='1' || $d_type ===1)){
				$duraning='';
			}
			$strSet .=" duraning='".$duraning."', ";
		}
			
		if (be("post","CCTV1")=="1") {
			$d_year=$row["m_year"];
			if(isN($d_year) || $d_year==='未知'){
				$d_year='其他';
			}
			$strSet .="d_year='".$d_year."',";
		}
		if (be("post","CCTV3")=="3") {
			$d_starring=$row["m_starring"];
			$strSet .="d_starring='".$d_starring."',";
		}
		if (be("post","CCTV4")=="4") {
			$d_pic = $row["m_pic"];
			$strSet .="d_pic='".$d_pic."',";
		}
		if (be("post","CCTV5")=="5") {
			$d_content = $row["m_content"];
			$strSet .="d_content='".$d_content."',";
		}
		$d_state = $row["m_state"];
		if(!isN($d_state) && $d_state !=='0'){
			if (isNum($d_state)){
			  $strSet .="d_state='".$d_state."',";
			}else if(!isNum($d_state) && $isMandCollect) {
				sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 连载信息非法'.$row["m_playfrom"]);
			}
		}
			
		$strSet .="d_name='".$title."',";
		$d_enname = hanzi2pinyin($title);
		$strSet .="d_enname='".$d_enname."',";
		if (isN($d_letter)) { $d_letter = strtoupper(substring($d_enname,1)); }
		$strSet .="d_letter='".$d_letter."',";
		$d_addtime= date('Y-m-d H:i:s',time());
		$strSet .="d_time='".$d_addtime."',";
			
		if($d_type === '2' || $d_type === '131'  ){
			if(!($d_state  === $rowvod["d_state"]) && $rowvod["favority_user_count"]>0) {
				$t_id=$rowvod["d_id"];
				$info=$db->getRow('select prod_id from mac_vod_pasre_item where prod_id='.$t_id);
				if($info ===false ){
					$d_addtime= date('Y-m-d H:i:s',time());
					$db->query("INSERT INTO mac_vod_pasre_item (prod_id,create_date) VALUES('".$t_id."','".$d_addtime."')");
				}
			}
		}
			
	}
	//更新数据结束

	if ($flag == false){
		$did= $rowvod["d_id"];
	}



	//获取影片URL
	$playAndWebArray= getVodPlanAndWebUrl($row["m_id"],$testUrl,$row["m_playfrom"],$d_type);

	if($playAndWebArray['noVideoUrlFlag'] && $isMandCollect){
			
		sendMail(array('darkdotter.zhou@joyplus.tv','dale.wu@joyplus.tv'),'<CMS>:影片《'.$title.'》 视频地址为空,不能入库','<CMS>:影片《'.$title.'》 视频地址为空，播放器：'.$row["m_playfrom"]);
			
		continue ;
	}

	//		$urls = getVodUrl($row["m_id"]);
	//   var_dump($playAndWebArray);
	$urls =$playAndWebArray['playUrl'];
	$webUrls =$playAndWebArray['webUrl'];
	if(!(isset($webUrls) && !is_null($webUrls) && strlen($webUrls)>0)){
		$webUrls=$testUrl;
	}
	$videoUrls=$playAndWebArray['videoUrl'];
	if(isN($videoUrls)){
		$videoUrls="";
	}else if (strpos( $videoUrls,"$") ===0){
		$videoUrls=substr($videoUrls, 1);
	}
	//       var_dump($videoUrls);

	$tmpplayurl = $rowvod["d_playurl"];
	$tmpweburl = $rowvod["webUrls"];
	$tmpvideourl = $rowvod["d_downurl"];
	$tmpplayfrom = $rowvod["d_playfrom"];
	$tmpplayserver = $rowvod["d_playserver"];

	if (isN($tmpplayurl)) { $tmpplayurl="";}
	if (isN($tmpvideourl)) { $tmpvideourl="";}
	if (isN($tmpweburl)) { $tmpweburl="";}
	if (isN($tmpplayfrom)) { $tmpplayfrom="";}

	if(isN($tmpplayfrom)){
		if(isN($videoUrls)){
			$strSet .="d_playfrom='".$row["m_playfrom"]."',d_playserver='".$row["m_playserver"]."',d_playurl='".$urls."',webUrls='".$webUrls."'";
		}else {
			$strSet .="d_playfrom='".$row["m_playfrom"]."',d_playserver='".$row["m_playserver"]."',d_playurl='".$urls."',webUrls='".$webUrls."' ,d_downurl='".$row["m_playfrom"].'$$'.$videoUrls."'";
		}
	}
	else if (strpos( ",". $tmpplayfrom , $row["m_playfrom"] ) >0){
		if (be("post","CCTV")=="2") {
			if(isN($videoUrls)){
				$strSet .="d_playfrom='".$row["m_playfrom"]. "$$$".$tmpplayfrom  ."',d_playserver='". $row["m_playserver"]."$$$".$tmpplayfrom."',d_playurl='". $urls ."$$$".$tmpplayurl ."' "."',webUrls='". $webUrls ."$$$". $tmpweburl."' ,d_downurl='". $tmpvideourl ."'";
			}else {
				$strSet .="d_playfrom='".$row["m_playfrom"] . "$$$". $tmpplayfrom ."',d_playserver='". $row["m_playserver"]."$$$".$tmpplayfrom."',d_playurl='". $urls ."$$$".$tmpplayurl ."' "."',webUrls='". $webUrls ."$$$". $tmpweburl."' ,d_downurl='". $tmpvideourl ."$$$".$row["m_playfrom"].'$$'. $videoUrls."'";
			}
		}
		else{
			$arr1 = explode("$$$",$tmpplayurl);
			$tempWebArray1 = explode("$$$",$tmpweburl);
			$tempVideoArray1 = explode("$$$",$tmpvideourl );

			$arr2 = explode("$$$",$tmpplayfrom);
			$tmpplayurl = "";
			$tmpweburl = "";
			$tmpvideourl = "";

			$rc = false;
			for ($k=0;$k<count($arr2);$k++){
				if ($rc) { $tmpweburl = $tmpweburl . "$$$";}
				$rc = false;
				if ($arr2[$k] !== $row["m_playfrom"] ) {
					$tmpweburl = $tmpweburl . $tempWebArray1[$k];
					$rc = true;
				}else {
					if(isN($webUrls)){
						$webUrls=$tempWebArray1[$k];
					}
				}
			}
			$tmpweburl=$webUrls. "$$$".$tmpweburl;


			$rc = false;
			for ($k=0;$k<count($arr2);$k++){
				if ($rc) { $tmpplayurl = $tmpplayurl . "$$$";}
				$rc = false;
				if ($arr2[$k] !== $row["m_playfrom"] ) {
					$tmpplayurl = $tmpplayurl . $arr1[$k];
					$rc = true;
				}else {
					if(isN($urls)){
						$urls=$arr1[$k];
					}
				}
			}

			$tmpplayurl=$urls. "$$$".$tmpplayurl;

			$rc = false;
			for ($k=0;$k<count($tempVideoArray1);$k++){
				if ($rc) { $tmpvideourl = $tmpvideourl . "$$$";}
				$rc = false;
				$arr2=explode("$$",$tempVideoArray1[$k]);
				if ($arr2[0] !== $row["m_playfrom"] ) {
					if(!isN($tempVideoArray1[$k])){
						$tmpvideourl = $tmpvideourl . $tempVideoArray1[$k];
						$rc = true;
					}
				}else {
					if(isN($videoUrls) && count($arr2)>1){
						$videoUrls=$arr2[1];
					}
				}
			}
			if(isN($videoUrls)){
				$tmpvideourl =  $tmpvideourl ;
					
			}else {
				$tmpvideourl =  $row["m_playfrom"].'$$'.$videoUrls."$$$".$tmpvideourl ;
					
			}

			//				if(!isN($str))
			$strSet .="d_playurl='".$tmpplayurl."' , webUrls='".$tmpweburl."' , d_downurl='".$tmpvideourl."'";

			//				$tmpplayfrom , $row["m_playfrom"]

			$tmpplayfrom=replaceStr($tmpplayfrom, " ", "");
			$tmpplayfrom=replaceStr($tmpplayfrom, $row["m_playfrom"]."$$$", "");
			$tmpplayfrom=replaceStr($tmpplayfrom, $row["m_playfrom"], "");
			$strSet .=",d_playfrom='".$row["m_playfrom"]. "$$$". $tmpplayfrom ."' ";

		}
	}
	else{
			
		if(isN($videoUrls)){
			$strSet .="d_playfrom='".$row["m_playfrom"]. "$$$". $tmpplayfrom ."',d_playserver='". $row["m_playserver"] ."$$$".$tmpplayserver."',d_playurl='".  $urls."$$$". $tmpplayurl."' ,webUrls='".  $webUrls ."$$$". $tmpweburl ."' ";
		}else {
			$tempVideoArray1 = explode("$$$",$tmpvideourl );
			$tmpvideourl = "";
			$rc = false;
			//			   var_dump($tempVideoArray1);
			for ($k=0;$k<count($tempVideoArray1);$k++){
				if ($rc) { $tmpvideourl = $tmpvideourl . "$$$";}
				$rc = false;
				$arr2=explode("$$",$tempVideoArray1[$k]);
				if ($arr2[0] !== $row["m_playfrom"] ) {
					if(!isN($tempVideoArray1[$k])){
						$tmpvideourl = $tmpvideourl . $tempVideoArray1[$k];
						$rc = true;
					}
				}
			}

			if(isN($videoUrls)){
				$tmpvideourl =  $tmpvideourl ;
			}else {
				$tmpvideourl =  $row["m_playfrom"].'$$'.$videoUrls."$$$".$tmpvideourl ;
			}

			$strSet .="d_playfrom='".$row["m_playfrom"] . "$$$". $tmpplayfrom."',d_playserver='".$tmpplayserver ."$$$".$row["m_playserver"]."',d_playurl='".  $urls."$$$". $tmpplayurl."' ,webUrls='".  $webUrls."$$$". $tmpweburl."' ,d_downurl='". $tmpvideourl .'\' ';
		}
	}

	if(strpos($tmpvideourl, "http") ===false){
		//iPad,iphone,apad,aphone,web
		$strSet .=" , can_search_device='iPad,iphone,apad,aphone,web'  ";
	}

	//	    writetofile("d:\\up.txt", $strSet);
	$sql= "update {pre}vod set ".$strSet." where d_id=" .$did;
	//		writetofile("d:\\ts.txt", "update {pre}vod set ".$strSet." where d_id=" .$did);
	$filePath = "../../upload/export/". iconv("UTF-8", "GBK", 'dd') .".txt";
	fwrite(fopen($filePath,"wb"),$sql);
	//		var_dump($sql);
	$db->query($sql);


	$db->query("update {pre}cj_vod set m_zt=1 where m_id=".$row["m_id"]);

	$MovieInflowNum=$MovieInflowNum+1;
	if ($MovieInflowNum >= $MovieNumW){
		//			echo "<script type=\"text/javascript\" language=\"javascript\">";
		//			echo "document.getElementById(\"refreshlentext\").style.width = \"100%\";";
		//			echo "document.getElementById(\"refreshlentext\").innerHTML = \"100%\";";
		//			echo "document.getElementById(\"storagetext\").innerHTML = \"入库完毕 <a href='collect_vod.php'>返回</a>\";";
		//			echo "alert('入库完毕'); location.href='collect_vod.php';";
		//			echo "</script>";
	}
	elseif (  fmod($MovieInflowNum,$rscount) == 0) {
		echo "<script type=\"text/javascript\" language=\"javascript\">";
		echo "document.getElementById(\"refreshlentext\").style.width = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
		echo "document.getElementById(\"refreshlentext\").innerHTML = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
		echo "document.getElementById(\"storagetext\").innerHTML = \"正在入库......\";";
		echo "</script>";
	} 
	}
	echo "<script type=\"text/javascript\" language=\"javascript\">";
	echo "document.getElementById(\"refreshlentext\").style.width = \"100%\";";
	echo "document.getElementById(\"refreshlentext\").innerHTML = \"100%\";";
	echo "document.getElementById(\"storagetext\").innerHTML = \"入库完毕 <a href='".getReferer()."'>返回</a>\";";
	echo "alert('入库完毕'); ";
	echo "var current_date='".(date('Y-m-d H:i:s',time()))."'";
	echo "</script>";
	unset($rs);
}

function getVodUrl($id)
{
	global $db;
	$TempUrl="";
	$sql2="select * from {pre}cj_vod_url where u_movieid=".$id ." order by u_id  asc";
	$rs_collect2= $db->query($sql2);
	$num=1;
	while ($row = $db ->fetch_array($rs_collect2))
	{
		if ($num ==1) {
			$TempUrl .= $row["u_url"];
		}
		Else{
			$TempUrl .= "#".$row["u_url"];
		}
		$num=$num+1;
	}
	unset($rs_collect2);
	return  $TempUrl;
}

function getNumber($url){
	if(!isN($url)){
		$urlA= explode("/", $url);
		if(isset($urlA) && is_array($urlA) && count($urlA)>0){
			$num= $urlA[count($urlA)-1];
			$num=replaceStr($num, ".html", "");
			return $num;

		}
	}
	return $url;
}

function getVodPlanAndWebUrl($id,$testUrl,$m_playfrom,$d_type)
{
	global $db;
	$playUrl="";
	$webUrl="";
	$videoUrl="";
	if($d_type ==='3'){
		$sql2="select * from {pre}cj_vod_url where u_movieid=".$id ." order by name desc ";
	}else {
		$sql2="select * from {pre}cj_vod_url where u_movieid=".$id ."  ORDER BY cast( name AS unsigned int ) ASC,u_id asc";
	}
	$rs_collect2= $db->query($sql2);
	$rscount =$db->getOne("Select count(m_id) as cc from {pre}cj_vod_url where u_movieid=".$id );
	$num=1;
	$playNum=1;
	$noVideoUrlFlag=false;
	while ($row = $db ->fetch_array($rs_collect2))
	{
		if ($num ==1) {
			$playUrl .= $row["u_url"];
		}
		Else{
			$playUrl .= "#".$row["u_url"];
		}

		$urstee=$row["u_weburl"];
		$setname = $row['name'];
		if($rscount==1 && (!(isset($urstee) && !is_null($urstee)))){
			$urstee=$testUrl;
		}

		if(isset($setname) && !is_null($setname) ){
			//			var_dump($m_playfrom);var_dump($d_type);
			$playNum=$setname.'$';
		}else {
			$playNum=$num.'$';
			if( $m_playfrom === 'letv' && $d_type ==='2'){
				$playNum=getNumber($urstee).'$';
			}
			//			var_dump($m_playfrom);var_dump($d_type);var_dump($urstee);
		}
			
		if(strpos($urstee, 'video.baidu.com') !==false){
			$urstee='';
		}

		if ($num ==1) {
			$webUrl .= $playNum.$urstee;
		}
		Else{
			$webUrl .= "{Array}".$playNum.$urstee;
		}


		$videourstee=$row["iso_video_url"];

		$android_vedio_url=$row["android_vedio_url"];
		if(!isN($videourstee)){
			if(!isN($android_vedio_url)){
				$videourstee=$android_vedio_url.MovieType::VIDEO_SEP_VERSION.$videourstee;
			}
		}else {
			$videourstee=$android_vedio_url;
		}


		if((isset($videourstee) && !is_null($videourstee))){

			//			if($rscount==1 ){
			//				$videoUrl=$videourstee;
			//			}else {
			if(isset($setname) && !is_null($setname) ){
				//			var_dump($m_playfrom);var_dump($d_type);
				$playNum=$setname.'$';
			} else {
				$playNum=$num.'$';
				if( $m_playfrom === 'letv' && $d_type ==='2'){
					$playNum=getNumber($urstee).'$';
				}
			}

			if(!isN($videourstee)){
				if ($num ==1) {
					$videoUrl .= $playNum.$videourstee;
				}
				Else{
					$videoUrl .= "{Array}".$playNum.$videourstee;
				}
			}
			//				if(!isN($android_vedio_url))
			//			}
		}else {
			$noVideoUrlFlag=true;
		}

		$num=$num+1;
	}
	//	var_dump($webUrl);
	unset($rs_collect2);
	if(strpos($videoUrl, 'http') ===false){
		$noVideoUrlFlag=true;
	}
	return  array(
	   'playUrl'=>$playUrl,
	   'webUrl'=>$webUrl,	
	   'videoUrl'=>$videoUrl,
	   'noVideoUrlFlag'=>$noVideoUrlFlag
	);
}
?>