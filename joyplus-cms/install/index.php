<?php
require_once ("../inc/config.php");
require_once ("../inc/class.php");
require_once ("../inc/function.php");
$action = be("all","action");
$db;
$rpath = str_replace("\\",'/',dirname(__FILE__));
$rpath = str_replace("\\",'/',substr($rpath,0,-7));
define("root",$rpath);

switch($action)
{
	case "ckdb": ckdb();break;
	case "a": show_header(); stepA(); show_footer();break;
	case "b": show_header(); stepB(); show_footer();break;
	case "c": show_header(); stepC(); show_footer();break;
	case "d": show_header(); stepD(); show_footer();break;
	default : show_header(); main(); show_footer();break;
}
dispseObj();

function getcon($varName)
{
	switch($res = get_cfg_var($varName))
	{
		case 0:
			return "NO";
			break;
		case 1:
			return "YES";
			break;
		default:
			return $res;
			break;
	}
}

function ckdb()
{   
	$server=be("get","server");
	$dbname=be("get","db");
	$id=be("get","id");
	$pwd=be("get","pwd");
	$lnk=mysql_connect($server,$dbname,$pwd); 
	if(!$lnk){
		echo('servererror');
	}else{
		$rs = @mysql_select_db($id,$lnk);
		if(!$rs){
			$rs = @mysql_query(" CREATE DATABASE `$id`; ",$lnk);
			if(!$rs)
			{
				echo('dberror');
			}
		}
	}
	@mysql_close($lnk);
	echo("ok");
}

function show_header()
{
	echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>悦视频视频管理系统 安装向导</title>
<link rel="stylesheet" href="../install/style.css" type="text/css" media="all" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/function.js"></script>
<script type="text/javascript">
	function showmessage(message) {
		document.getElementById('notice').innerHTML += message + '<br />';
	}
</script>
<meta content="Comsenz Inc." name="Copyright" />
</head>
<div class="container">
	<div class="header">
		<span>版本号：V1.1</span>
		<h1>悦视频视频管理系统 安装向导</h1>
EOT;
}

function show_footer()
{
	echo <<<EOT
		<div class="footer">&copy;2012-2013  <a href="http://www.joyplus.tv/">Joyplus CMS </a> Inc.</div>
	</div>
</div>
</body>
</html>
EOT;
}

function show_step($n,$t,$c)
{
	$laststep = 4;
	$stepclass = array();
	for($i = 1; $i <= $laststep; $i++) {
		$stepclass[$i] = $i == $n ? 'current' : ($i < $n ? '' : 'unactivated');
	}
	$stepclass[$laststep] .= ' last';
	echo <<<EOT
	<div class="setup step{$n}">
		<h2>$t</h2>
		<p>$c</p>
	</div>
	<div class="stepstat">
		<ul>
			<li class="$stepclass[1]">检查安装环境</li>
			<li class="$stepclass[2]">设置运行环境</li>
			<li class="$stepclass[3]">创建数据库</li>
			<li class="$stepclass[4]">安装</li>
		</ul>
		<div class="stepstatbg stepstat1"></div>
	</div>
</div>
<div class="main">
EOT;
}

function main()
{
	stepA();
}

function dir_writeable($dir) {
	$writeable = 0;
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.txt", 'w')) {
			@fclose($fp);
			@unlink("$dir/test.txt");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function stepA()
{
	show_step(1,"开始安装","环境以及文件目录权限检查");
	$os = PHP_OS;
	$pv = PHP_VERSION;
	$up = getcon("upload_max_filesize");
	$cj1 = getcon("allow_url_fopen");

	echo <<<EOT
<div class="main"><h2 class="title">环境检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;">
<tr>
	<th>项目</th>
	<th class="padleft">所需配置</th>
	<th class="padleft">最佳配置</th>
	<th class="padleft">当前服务器</th>
</tr>
<tr>
<td>操作系统</td>
<td class="padleft">不限制</td>
<td class="padleft">类Unix</td>
<td class="w pdleft1">$os</td>
</tr>
<tr>
<td>PHP 版本</td>
<td class="padleft">4.4</td>
<td class="padleft">5.0</td>
<td class="w pdleft1">$pv</td>
</tr>
<tr>
<td>附件上传</td>
<td class="padleft">不限制</td>
<td class="padleft">2M</td>
<td class="w pdleft1">$up</td>
</tr>
<tr>
<td>远程访问</td>
<td class="padleft">allow_url_fopen</td>
<td class="padleft">开启</td>
<td class="w pdleft1">$cj1</td>
</tr>
</table>
<h2 class="title">目录、文件权限检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
	<tr>
	<th>目录文件</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;
	$arr = array("inc/config.php","inc/config.ftp.php","inc/config.interface.php","inc/cache.php","inc/timmingset.xml","inc/vodarea.txt","inc/vodlang.txt","upload/","upload/vod/","upload/topic/","upload/cache/","upload/export/","upload/thirdpartlogo/","install/index.php","log");
	foreach($arr as $f){
		$st="可写";
		$cs="w";
		if(strpos($f,".")>0){
			if(!is_writable(root.$f)){
				$st="不可写";
				$cs="nw";
			}
		}
		else{
			if(!dir_writeable(root.$f)){
				$st="不可写";
				$cs="nw";
			}
		}
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">可写</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);
	echo <<<EOT
</table>
<h2 class="title">函数依赖性检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
<tr>
	<th>函数名称</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;

	$arr=array("mysql_connect","curl_init","curl_exec","mb_convert_encoding","dom_import_simplexml");
	foreach($arr as $f){
		$st="支持";
		$cs="w";
		if(!function_exists($f)){
			$st="不支持";
			$cs="nw";
		}
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">支持</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);

	echo <<<EOT
</table>
</div>
<form method="get" autocomplete="off" action="index.php">
<input type="hidden" name="action" value="b" />
<div class="btnbox marginbot">

<input class="right btnnext" type="submit" value="下一步">
</div>
</form>
EOT;
}

function stepB()
{
	show_step(2,"安装配置","网站默认配置信息");

	$strpath = $_SERVER["SCRIPT_NAME"];
	$strpath = substring($strpath, strripos($strpath, "/"));
	$strpath = replaceStr($strpath,"install/","");
	$strpath = replaceStr($strpath,"/install","/");

	?>
<script language="javascript">
		$(function(){
			$("#btnLicense").click(function(){
				window.location.href= "?action=a";
				return false;
			});
			$("#btnStep1a").click(function(){
				location.href= "?action=";
			});
			$("#btnStep1b").click(function(){
				
				if($("#m_name").val()==""){
					alert("帐号不能为空");
					$("#m_name").focus();
					return false;	
				}
				if($("#m_password1").val()==""){
					alert("密码不能为空");
					$("#m_password1").focus();
					return false;
				}
				if($("#m_password1").val() != $("#m_password2").val()){
					alert("验证密码不同");
					$("#m_password2").focus();
					return false;
				}
				if($("#app_safecode").val()==""){
					alert("安全码不能为空");
					$("#app_safecode").focus();
					return false;
				}

				$("#form2").submit();
			});
			$("#btnStep2a").click(function(){
				location.href= "?action=a";
			});
			$("#btnStep2b").click(function(){
				location.href= "?action=c";
			});
		});
		function setdb(dbtype){
			if (dbtype == "access"){
				$("#sql").css("display","none");
				$("#acc").css("display","");
			}
			else{
				$("#sql").css("display","");
				$("#acc").css("display","none");
			}
		}

function checkdb(){
    	var server=$("#app_dbserver").val();
		var dbname=$("#app_dbname").val();
		var id=$("#app_dbuser").val();
		var pwd=$("#app_dbpass").val();
		if(server=="" || dbname=="" || id=="" ){
			alert("数据库信息不能为空");return;
		}
    	$.ajax({cache: false, dataType: 'html', type: 'GET', url: 'index.php?action=ckdb&server='+server+'&db='+dbname+'&id='+id+'&pwd='+pwd,
        	
    		success: function(obj) {
				if(obj=='ok'){
					$("#checkinfo").html( "<font color=green>&nbsp;&nbsp;连接数据库服务器成功!</font>" );
				}
				else if(obj=='dberror'){
					$("#checkinfo").html ("<font color=red>&nbsp;&nbsp;连接数据库服务器成功，但是找不到该数据库，也没有权限创建该数据库!</font>");
				}
				else {
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接数据库服务器失败!</font>");
				}
			},
			complete: function (XMLHttpRequest, textStatus) {
				if( XMLHttpRequest.responseText.length >10){
					
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接服务器失败!</font>");
				}
			}
		});
    }
</script>

<div class="main">
	<form id="form2" action="index.php?action=b" method="post">
		<div id="form_items_3">
			<br />


			<div class="desc">
				<b>填写数据库信息</b>
			</div>
			<table class="tb2" style="margin: 20px 0 20px 55px;">
				<tr>
					<th class="tbopt" align="left">&nbsp;数据库类型:</th>
					<td><select name="app_dbtype" id="app_dbtype"
						onChange="setdb(this.value);">
							<option value="mysql">mysql数据库</option>
					</select></td>
					<td>网站使用数据库的类型</td>
				</tr>
				<tr style="display: none">
					<th class="tbopt" align="left">&nbsp;表前缀:</th>
					<td><input class="txt" type="text" name="app_tablepre"
						id="app_tablepre" value="mac_" /></td>
					<td>数据库表名前缀</td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;数据库服务器:</th>
					<td><input class="txt" type="text" name="app_dbserver"
						id="app_dbserver" value="localhost" /></td>
					<td>数据库服务器地址, 一般为 localhost</td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;数据库名称:</th>
					<td><input class="txt" type="text" name="app_dbname"
						id="app_dbname" value="" /></td>
					<td></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;数据库用户名:</th>
					<td><input class="txt" type="text" name="app_dbuser"
						id="app_dbuser" value="" /></td>
					<td></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;数据库密码:</th>
					<td><input class="txt" type="text" name="app_dbpass"
						id="app_dbpass" value="" /></td>
					<td></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;测试连接数据库:</th>
					<td><strong><a onclick="checkdb()" style="cursor: pointer;"><font
								color="red">>>>MYSQL连接测试</font> </a> </strong></td>
					<td></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp;</th>
					<td><span id="checkinfo"></span></td>
					<td></td>
				</tr>
			</table>
			<div class="desc">
				<b>填写管理员信息<font color="red">（请牢记你所填写的信息，登录时需要）</font> </b>
			</div>
			<table class="tb2" style="margin: 20px 0 20px 55px;">
				<tr>
					<th class="tbopt" align="left">&nbsp; 管理员账号:</th>
					<td><input class="txt" type="text" name="m_name" id="m_name"
						value="" /></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp; 管理员密码:</th>
					<td><input class="txt" type="password" name="m_password1"
						id="m_password1" value="" /></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp; 确认密码:</th>
					<td><input class="txt" type="password" name="m_password2"
						id="m_password2" value="" /></td>
				</tr>
				<tr>
					<th class="tbopt" align="left">&nbsp; 安全码:</th>
					<td><input class="txt" type="password" name="app_safecode"
						id="app_safecode" value="" /></td>
				</tr>
			</table>
		</div>
		<table class="tb2 btn2" style="width: 100%;">
			<tr>
				<th class="tbopt" align="left">&nbsp;</th>
				<td><input type="hidden" name="action" value="c" />
					<div class="btnbox marginbot">
						<input class="left btnpre" type="button" onclick="history.back();"
							value="上一步"> <input class="right btnnext" id="btnStep1b"
							type="button" value="下一步">
				
				</td>
				<td></td>
			</tr>
		</table>
	</form>
	<?php
}

function checkField($sIndexName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW COLUMNS FROM ".$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Field"];
	}
	if(in_array($sIndexName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function isExistTable($tableName,$dbname)
{
	global $db;
	$dbarr = array();
	$rs = $db->query("SHOW TABLES ");
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row["Tables_in_".dbname];
	}
	if(in_array($tableName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function stepC()
{
	global $db;
	$app_siteurl = be("post","app_siteurl");
	$app_sitename = be("post","app_sitename");
	$app_installdir = be("post","app_installdir");
	$app_keywords = be("post","app_keywords");
	$app_description = be("post","app_description");
	$app_dbtype = be("post","app_dbtype");
	$app_dbpath = "inc/" & be("post","app_dbpath");
	$app_dbserver = be("post","app_dbserver");
	$app_dbname = be("post","app_dbname");
	$app_dbuser = be("post","app_dbuser");
	$app_dbpass = be("post","app_dbpass");
	$app_tablepre = be("post","app_tablepre");

	$m_name = be("post","m_name");
	$m_password1 = be("post","m_password1");
	$m_password2 = be("post","m_password2");
	$app_safecode = be("post","app_safecode");

	show_step(3,"安装数据库","正在执行数据库安装写入配置文件");

	echo <<<EOT
	<div class="main"> 
	<div class="btnbox"><div id="notice"></div></div>
	<div class="btnbox margintop marginbot"><form method="get" autocomplete="off" action="index.php">
	<table class="tb2 btn2" style="margin-top:10px; width:100%;"><tr><th class="tbopt" align="left">&nbsp;</th><td>
<input type="hidden" name="action" value="d" /><div class="btnbox marginbot"><input class="left btnpre" type="button" onclick="history.back();" value="上一步"><input  class="right btnnext" type="submit" value="下一步"></td><td></td></tr></table></form></div>
EOT;

	$configstr = file_get_contents( "../inc/config.php" );
	$configstr = regReplace($configstr,"\"app_siteurl\",\"(\S*?)\"","\"app_siteurl\",\"".$app_siteurl."\"");
	$configstr = regReplace($configstr,"\"app_installdir\",\"(\S*?)\"","\"app_installdir\",\"".$app_installdir."\"");
	$configstr = regReplace($configstr,"\"app_keywords\",\"(\S*?)\"","\"app_keywords\",\"".$app_keywords."\"");
	$configstr = regReplace($configstr,"\"app_description\",\"(\S*?)\"","\"app_description\",\"".$app_description."\"");
	$configstr = regReplace($configstr,"\"app_dbtype\",\"(\S*?)\"","\"app_dbtype\",\"".$app_dbtype."\"");
	$configstr = regReplace($configstr,"\"app_dbpath\",\"(\S*?)\"","\"app_dbpath\",\"".$app_dbpath."\"");
	$configstr = regReplace($configstr,"\"app_dbserver\",\"(\S*?)\"","\"app_dbserver\",\"".$app_dbserver."\"");
	$configstr = regReplace($configstr,"\"app_dbname\",\"(\S*?)\"","\"app_dbname\",\"".$app_dbname."\"");
	$configstr = regReplace($configstr,"\"app_dbuser\",\"(\S*?)\"","\"app_dbuser\",\"".$app_dbuser."\"");
	$configstr = regReplace($configstr,"\"app_dbpass\",\"(\S*?)\"","\"app_dbpass\",\"".$app_dbpass."\"");
	$configstr = regReplace($configstr,"\"app_safecode\",\"(\S*?)\"","\"app_safecode\",\"".$app_safecode."\"");
	$configstr = regReplace($configstr,"\"app_tablepre\",\"(\S*?)\"","\"app_tablepre\",\"".$app_tablepre."\"");
	$configstr = regReplace($configstr,"\"app_install\",(\S*?)\)\;","\"app_install\",1);");
	fwrite(fopen("../inc/config.php","wb"),$configstr);
	echo '<script type="text/javascript">showmessage(\'写入网站配置文件... 成功  \');</script>';

	error_reporting(E_NOTICE );
	$dbck=false;

	$lnk=@mysql_connect($app_dbserver,$app_dbuser,$app_dbpass);
	if(!$lnk){
		echo '<script type="text/javascript">showmessage(\'数据库设置出错：mysql请检查数据库连接信息... \');</script>';
	}
	else{
		if(!@mysql_select_db($app_dbname,$lnk)){
			echo '<script type="text/javascript">showmessage(\'数据库服务器连接成功，没有找到【 '.$app_dbname.' 】数据... \');</script>';
		}
		else{
			$dbck=true;
		}
	}
	error_reporting(7 );

	if ($dbck){

		$db = new AppDataBase($app_dbserver,$app_dbuser,$app_dbpass,$app_dbname);
		$app_tablepre='';
		echo '<script type="text/javascript">showmessage(\'开始创建数据库结构... \');</script>';

		if(!isExistTable("".$app_tablepre."apk_category",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_category` (`id` int(11) NOT NULL AUTO_INCREMENT,
                                                                       `parent_id` int(11) DEFAULT NULL,
                                                                       `name` varchar(250) DEFAULT NULL,
  																		`status` int(11) NOT NULL DEFAULT '1',
  																		`disp_order` int(11) NOT NULL DEFAULT '0',
																	  `type_desc` text NOT NULL,
																	  `type_key` varchar(100) NOT NULL,
																	  `t_hide` int(11) NOT NULL DEFAULT '1',
																	  PRIMARY KEY (`id`))ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_category... \');</script>';

		}

		if(!isExistTable("".$app_tablepre."apk_company",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_company` ( `id` int(11) NOT NULL AUTO_INCREMENT,
																	  `name` varchar(250) DEFAULT NULL,
																	  `email` varchar(60) DEFAULT NULL,
																	  `contact` varchar(50) DEFAULT NULL,
																	  `zipcode` varchar(10) DEFAULT NULL,
																	  `adress` varchar(450) DEFAULT NULL,
																	  `description` text,
																	  `status` int(11) NOT NULL DEFAULT '1',
																	  PRIMARY KEY (`id`))  ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_company... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_device",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_device` (`id` int(11) NOT NULL AUTO_INCREMENT,
																	  `device_name` varchar(250) DEFAULT NULL,
																	  `device_mac_address` varchar(50) DEFAULT NULL,
																	  `pin_code` varchar(50) DEFAULT NULL,
																	  `channel_name` varchar(200) DEFAULT NULL,
																	  PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_device... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_master_base",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_master_base` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `company_id` int(11) DEFAULT NULL,
																		  `company_name` varchar(250) DEFAULT NULL,
																		  `package_name` varchar(250) DEFAULT NULL,
																		  `category_id` int(11) DEFAULT NULL,
																		  `category_name` varchar(250) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  `status` int(11) NOT NULL DEFAULT '1',
																		  `download_count` int(11) DEFAULT NULL,
																		  `upload_count` int(11) DEFAULT NULL,
																		  `disp_order` int(11) DEFAULT NULL,
																		  `apk_tag` varchar(1150) DEFAULT NULL,
																		  `app_name` varchar(100) NOT NULL,
																		  `description` text NOT NULL,
																		  `latest_item_id` int(11) NOT NULL,
																		  `apk_icon` varchar(300) NOT NULL,
																		  PRIMARY KEY (`id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_master_base... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_master_info",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_master_info` (`id` int(11) NOT NULL AUTO_INCREMENT,
																  `app_id` int(11) DEFAULT NULL,
																  `language_id` varchar(10) DEFAULT NULL,
																  `display_name` varchar(350) DEFAULT NULL,
																  `description` text,
																  PRIMARY KEY (`id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_master_info... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_master_items",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_master_items` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `apk_id` int(11) DEFAULT NULL,
																		  `file_url` varchar(350) DEFAULT NULL,
																		  `file_type` varchar(20) DEFAULT NULL,
																		  `qiniu_file_key` varchar(150) DEFAULT NULL,
																		  `file_size` int(11) DEFAULT NULL,
																		  `status` int(11) NOT NULL DEFAULT '1',
																		  `md5` varchar(50) DEFAULT NULL,
																		  `version_code` varchar(10) DEFAULT NULL,
																		  `version_name` varchar(20) DEFAULT NULL,
																		  `min_sdk_version` varchar(10) DEFAULT NULL,
																		  `target_sdk_version` varchar(10) DEFAULT NULL,
																		  `file_name` varchar(50) DEFAULT NULL,
																		  `package_name` varchar(250) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  `disp_order` int(11) DEFAULT NULL,
																		  `upload_count` int(11) DEFAULT NULL,
																		  `download_count` int(11) DEFAULT NULL,
																		  `description` varchar(50) DEFAULT NULL,
																		  `apk_icon` varchar(300) NOT NULL,
																		  PRIMARY KEY (`id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_master_items... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_master_temp",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_master_temp` ( `id` int(11) NOT NULL AUTO_INCREMENT,
																		  `apk_id` int(11) DEFAULT NULL,
																		  `package_name` varchar(250) DEFAULT NULL,
																		  `upload_count` int(11) DEFAULT NULL,
																		  `file_url` varchar(350) DEFAULT NULL,
																		  `file_type` varchar(20) DEFAULT NULL,
																		  `qiniu_file_key` varchar(150) DEFAULT NULL,
																		  `file_size` int(11) DEFAULT NULL,
																		  `status` int(11) NOT NULL DEFAULT '1',
																		  `md5` varchar(50) DEFAULT NULL,
																		  `version_code` varchar(10) DEFAULT NULL,
																		  `version_name` varchar(20) DEFAULT NULL,
																		  `min_sdk_version` varchar(10) DEFAULT NULL,
																		  `target_sdk_version` varchar(10) DEFAULT NULL,
																		  `file_name` varchar(50) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  `app_name` varchar(100) NOT NULL,
																		  `state` int(11) NOT NULL DEFAULT '1',
																		  `description` text NOT NULL,
																		  `author_id` int(11) NOT NULL DEFAULT '0',
																		  `apk_icon` varchar(300) NOT NULL,
																		  PRIMARY KEY (`id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_master_temp... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_meta_lang",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_meta_lang` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `meta_key` varchar(100) DEFAULT NULL,
																		  `language_id` varchar(10) DEFAULT NULL,
																		  `meta_des` text,
																		  PRIMARY KEY (`id`)
																		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_meta_lang... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."apk_push_msg_history",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."apk_push_msg_history` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `package_name` varchar(250) DEFAULT NULL,
																			  `file_url` varchar(350) DEFAULT NULL,
																			  `qiniu_file_key` varchar(150) DEFAULT NULL,
																			  `status` int(11) NOT NULL DEFAULT '1',
																			  `file_name` varchar(50) DEFAULT NULL,
																			  `version_code` varchar(10) DEFAULT NULL,
																			  `version_name` varchar(20) DEFAULT NULL,
																			  `md5` varchar(50) DEFAULT NULL,
																			  `create_date` datetime DEFAULT NULL,
																			  `app_name` varchar(100) NOT NULL,
																			  `mac_address` varchar(300) NOT NULL,
																			  PRIMARY KEY (`id`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'apk_push_msg_history... \');</script>';
		}


		if(!isExistTable("".$app_tablepre."mac_cj_change",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_change` (`c_id` int(11) NOT NULL AUTO_INCREMENT,
																		  `c_name` varchar(64) DEFAULT NULL,
																		  `c_toid` int(11) NOT NULL DEFAULT '0',
																		  `c_pid` int(11) NOT NULL DEFAULT '0',
																		  `c_type` int(4) NOT NULL DEFAULT '0',
																		  `c_sys` int(11) NOT NULL DEFAULT '0',
																		  PRIMARY KEY (`c_id`),
																		  KEY `i_c_projectid` (`c_pid`),
																		  KEY `i_c_type` (`c_type`)
																		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_change... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_cj_filters",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_filters` ( `f_id` int(11) NOT NULL AUTO_INCREMENT,
																		  `f_name` varchar(64) DEFAULT NULL,
																		  `f_object` int(11) NOT NULL DEFAULT '0',
																		  `f_type` int(11) NOT NULL DEFAULT '0',
																		  `f_content` varchar(64) DEFAULT NULL,
																		  `f_strstart` text,
																		  `f_strend` text,
																		  `f_rep` varchar(255) DEFAULT NULL,
																		  `f_flag` int(11) NOT NULL DEFAULT '0',
																		  `f_pid` int(11) NOT NULL DEFAULT '0',
																		  `f_sys` int(11) NOT NULL DEFAULT '0',
																		  PRIMARY KEY (`f_id`),
																		  KEY `i_f_type` (`f_type`),
																		  KEY `i_f_object` (`f_object`),
																		  KEY `i_f_projectid` (`f_pid`)
																		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_filters... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_cj_vod",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_vod` (`m_id` int(11) NOT NULL AUTO_INCREMENT,
																	  `m_pid` int(11) NOT NULL DEFAULT '0',
																	  `m_name` varchar(255) DEFAULT NULL,
																	  `m_type` varchar(64) DEFAULT NULL,
																	  `m_typeid` int(11) NOT NULL DEFAULT '0',
																	  `m_area` varchar(64) DEFAULT NULL,
																	  `m_playfrom` varchar(64) DEFAULT NULL,
																	  `m_starring` varchar(255) DEFAULT NULL,
																	  `m_directed` varchar(255) DEFAULT NULL,
																	  `m_pic` varchar(255) DEFAULT NULL,
																	  `m_content` text,
																	  `m_year` varchar(64) DEFAULT NULL,
																	  `m_addtime` varchar(64) DEFAULT NULL,
																	  `m_urltest` varchar(255) DEFAULT NULL,
																	  `m_zt` int(11) NOT NULL DEFAULT '0',
																	  `m_playserver` int(11) NOT NULL DEFAULT '0',
																	  `m_hits` int(11) NOT NULL DEFAULT '0',
																	  `m_state` int(11) NOT NULL DEFAULT '0',
																	  `m_language` varchar(64) DEFAULT NULL,
																	  `m_remarks` varchar(255) DEFAULT NULL,
																	  `duraning` varchar(10) NOT NULL DEFAULT '',
																	  `can_search_device` varchar(300) DEFAULT NULL,
																	  `m_pic_ipad` varchar(500) NOT NULL,
																	  PRIMARY KEY (`m_id`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54350 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_vod... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_cj_vod_projects",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_vod_projects` (`p_id` int(11) NOT NULL AUTO_INCREMENT,
																			  `p_name` varchar(128) DEFAULT NULL,
																			  `p_coding` varchar(64) DEFAULT NULL,
																			  `p_playtype` varchar(11) DEFAULT NULL,
																			  `p_pagetype` int(11) NOT NULL DEFAULT '0',
																			  `p_url` varchar(255) DEFAULT NULL,
																			  `p_pagebatchurl` varchar(255) DEFAULT NULL,
																			  `p_manualurl` varchar(255) DEFAULT NULL,
																			  `p_pagebatchid1` varchar(128) DEFAULT NULL,
																			  `p_pagebatchid2` varchar(128) DEFAULT NULL,
																			  `p_script` int(11) NOT NULL DEFAULT '0',
																			  `p_showtype` int(11) NOT NULL DEFAULT '0',
																			  `p_collecorder` int(11) NOT NULL DEFAULT '0',
																			  `p_savefiles` int(11) NOT NULL DEFAULT '0',
																			  `p_intolib` int(11) NOT NULL DEFAULT '0',
																			  `p_ontime` int(11) NOT NULL DEFAULT '0',
																			  `p_listcodestart` text,
																			  `p_listcodeend` text,
																			  `p_classtype` int(11) NOT NULL DEFAULT '0',
																			  `p_collect_type` int(11) NOT NULL DEFAULT '0',
																			  `p_time` datetime DEFAULT NULL,
																			  `p_listlinkstart` text,
																			  `p_listlinkend` text,
																			  `p_starringtype` int(11) NOT NULL DEFAULT '0',
																			  `p_starringstart` text,
																			  `p_starringend` text,
																			  `p_titletype` int(11) NOT NULL DEFAULT '0',
																			  `p_titlestart` text,
																			  `p_titleend` text,
																			  `p_pictype` int(11) NOT NULL DEFAULT '0',
																			  `p_picstart` text,
																			  `p_picend` text,
																			  `p_timestart` text,
																			  `p_timeend` text,
																			  `p_areastart` text,
																			  `p_areaend` text,
																			  `p_typestart` text,
																			  `p_typeend` text,
																			  `p_contentstart` text,
																			  `p_contentend` text,
																			  `p_playcodetype` int(11) NOT NULL DEFAULT '0',
																			  `p_playcodestart` text,
																			  `p_playcodeend` text,
																			  `p_playurlstart` text,
																			  `p_playurlend` text,
																			  `p_playlinktype` int(11) NOT NULL DEFAULT '0',
																			  `p_playlinkstart` text,
																			  `p_playlinkend` text,
																			  `p_playspecialtype` int(11) NOT NULL DEFAULT '0',
																			  `p_playspecialrrul` text,
																			  `p_playspecialrerul` text,
																			  `p_server` varchar(128) DEFAULT NULL,
																			  `p_hitsstart` int(11) NOT NULL DEFAULT '0',
																			  `p_hitsend` int(11) NOT NULL DEFAULT '0',
																			  `p_lzstart` text,
																			  `p_lzend` text,
																			  `p_colleclinkorder` int(11) NOT NULL DEFAULT '0',
																			  `p_lzcodetype` int(11) NOT NULL DEFAULT '0',
																			  `p_lzcodestart` text,
																			  `p_lzcodeend` text,
																			  `p_languagestart` text,
																			  `p_languageend` text,
																			  `p_remarksstart` text,
																			  `p_remarksend` text,
																			  `p_directedstart` text,
																			  `p_directedend` text,
																			  `p_setnametype` int(11) NOT NULL DEFAULT '0',
																			  `p_setnamestart` text,
																			  `p_setnameend` text,
																			  `p_playcodeApiUrl` varchar(300) NOT NULL DEFAULT '',
																			  `p_playcodeApiUrltype` int(1) NOT NULL DEFAULT '0',
																			  `p_playcodeApiUrlParamstart` varchar(300) NOT NULL DEFAULT '',
																			  `p_playcodeApiUrlParamend` varchar(300) NOT NULL DEFAULT '',
																			  `p_videocodeApiUrl` varchar(255) DEFAULT NULL,
																			  `p_videocodeApiUrlParamstart` text,
																			  `p_videocodeApiUrlParamend` text,
																			  `p_videourlstart` text,
																			  `p_videourlend` text,
																			  `p_videocodeType` int(1) DEFAULT '0',
																			  PRIMARY KEY (`p_id`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=186 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_vod_projects... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_cj_vod_url",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_vod_url` (`u_id` int(11) NOT NULL AUTO_INCREMENT,
																		  `u_url` varchar(255) DEFAULT NULL,
																		  `u_weburl` varchar(255) DEFAULT NULL,
																		  `u_movieid` int(11) NOT NULL DEFAULT '0',
																		  `iso_video_url` text,
																		  `android_vedio_url` text,
																		  `name` varchar(255) DEFAULT NULL,
																		  `pic` varchar(255) DEFAULT NULL,
																		  `time` varchar(10) DEFAULT NULL,
																		  `u_desc` text,
																		  PRIMARY KEY (`u_id`),
																		  KEY `i_u_movieid` (`u_movieid`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=649433 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_vod_url... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_cj_zhuiju",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_cj_zhuiju` (`m_id` int(11) NOT NULL AUTO_INCREMENT,
																		  `m_pid` int(11) NOT NULL DEFAULT '0',
																		  `m_name` varchar(255) DEFAULT NULL,
																		  `m_typeid` int(11) NOT NULL DEFAULT '0',
																		  `m_playfrom` varchar(64) DEFAULT NULL,
																		  `m_urltest` varchar(255) DEFAULT NULL,
																		  `crontab_desc` varchar(255) DEFAULT NULL,
																		  `status` int(11) NOT NULL DEFAULT '0',
																		  PRIMARY KEY (`m_id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54350 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_cj_zhuiju... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_comment",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_comment` (`c_id` int(11) NOT NULL AUTO_INCREMENT,
																	  `c_type` int(11) DEFAULT '0',
																	  `c_vid` int(11) DEFAULT '0',
																	  `c_rid` int(11) DEFAULT '0',
																	  `c_audit` int(11) DEFAULT '0',
																	  `c_name` varchar(64) DEFAULT NULL,
																	  `c_ip` varchar(32) DEFAULT NULL,
																	  `c_content` varchar(128) DEFAULT NULL,
																	  `c_time` datetime DEFAULT NULL,
																	  PRIMARY KEY (`c_id`),
																	  KEY `c_vid` (`c_vid`),
																	  KEY `c_type` (`c_type`),
																	  KEY `c_rid` (`c_rid`),
																	  KEY `c_time` (`c_time`),
																	  KEY `c_audit` (`c_audit`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16249 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_comment... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_manager",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_manager` (`m_id` int(11) NOT NULL AUTO_INCREMENT,
																	  `m_name` varchar(32) DEFAULT NULL,
																	  `m_password` varchar(32) DEFAULT NULL,
																	  `m_levels` text,
																	  `m_status` int(11) DEFAULT '0',
																	  `m_logintime` datetime DEFAULT NULL,
																	  `m_loginip` varchar(32) DEFAULT NULL,
																	  `m_random` varchar(64) DEFAULT NULL,
																	  PRIMARY KEY (`m_id`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;
																				");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_manager... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_thirdpart_config",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_thirdpart_config` (`id` int(11) NOT NULL AUTO_INCREMENT,
																				  `device_name` text,
																				  `company_name` varchar(250) DEFAULT NULL,
																				  `api_url` varchar(250) DEFAULT NULL,
																				  `logo_url` varchar(250) DEFAULT NULL,
																				  `app_key` varchar(250) DEFAULT NULL,
																				  `create_date` datetime DEFAULT NULL,
																				  `status` int(11) NOT NULL DEFAULT '1',
																				  `user_id` int(11) NOT NULL,
																				  PRIMARY KEY (`id`)
																				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_thirdpart_config... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_vod",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod` (`d_id` int(11) NOT NULL AUTO_INCREMENT,
																  `d_name` varchar(255) DEFAULT NULL,
																  `d_subname` varchar(255) DEFAULT NULL,
																  `d_enname` varchar(255) DEFAULT NULL,
																  `d_type` int(11) DEFAULT '0',
																  `d_letter` char(1) DEFAULT NULL,
																  `d_state` int(11) DEFAULT '0',
																  `d_color` varchar(8) DEFAULT NULL,
																  `d_pic` varchar(800) DEFAULT NULL,
																  `d_starring` varchar(255) DEFAULT NULL,
																  `d_directed` varchar(255) DEFAULT NULL,
																  `d_area` varchar(32) DEFAULT NULL,
																  `d_year` varchar(32) DEFAULT NULL,
																  `d_language` varchar(32) DEFAULT NULL,
																  `d_level` int(11) DEFAULT '0',
																  `d_stint` int(11) DEFAULT '0',
																  `d_hits` int(11) DEFAULT '0',
																  `d_dayhits` int(11) DEFAULT '0',
																  `d_weekhits` int(11) DEFAULT '0',
																  `d_monthhits` int(11) DEFAULT '0',
																  `d_topic` int(11) DEFAULT '0',
																  `d_content` text,
																  `d_remarks` varchar(255) DEFAULT NULL,
																  `d_hide` int(11) DEFAULT '0',
																  `d_good` int(11) DEFAULT '0',
																  `d_bad` int(11) DEFAULT '0',
																  `d_usergroup` int(11) DEFAULT '0',
																  `d_score` float DEFAULT '0',
																  `d_scorecount` int(11) DEFAULT '0',
																  `d_addtime` datetime DEFAULT NULL,
																  `d_time` datetime DEFAULT NULL,
																  `d_hitstime` datetime DEFAULT NULL,
																  `d_playfrom` varchar(255) DEFAULT NULL,
																  `d_playserver` varchar(255) DEFAULT NULL,
																  `d_playurl` longtext,
																  `d_downurl` longtext,
																  `webUrls` longtext NOT NULL,
																  `publish_owner_id` int(11) NOT NULL,
																  `love_user_count` int(11) NOT NULL DEFAULT '0',
																  `watch_user_count` int(11) NOT NULL DEFAULT '0',
																  `favority_user_count` int(11) NOT NULL DEFAULT '0',
																  `d_type_name` varchar(100) DEFAULT NULL,
																  `d_pic_ipad` varchar(1600) DEFAULT NULL,
																  `share_number` int(11) NOT NULL DEFAULT '0',
																  `good_number` int(11) NOT NULL DEFAULT '0',
																  `total_comment_number` int(11) NOT NULL DEFAULT '0',
																  `d_pic_ipad_tmp` text,
																  `d_play_check` int(11) NOT NULL DEFAULT '0',
																  `d_play_num` bigint(20) NOT NULL DEFAULT '0',
																  `d_video_desc_url` varchar(255) NOT NULL,
																  `d_douban_id` int(11) NOT NULL DEFAULT '0',
																  `can_play_device` int(2) NOT NULL DEFAULT '0',
																  `d_day_play_num` int(11) NOT NULL DEFAULT '0',
																  `d_status` int(3) NOT NULL DEFAULT '0',
																  `can_search_device` varchar(300) NOT NULL,
																  `duraning` varchar(10) NOT NULL DEFAULT '',
																  `d_capital_name` varchar(20) NOT NULL,
																  PRIMARY KEY (`d_id`),
																  KEY `d_type` (`d_type`),
																  KEY `d_state` (`d_state`),
																  KEY `d_level` (`d_level`),
																  KEY `d_hits` (`d_hits`),
																  KEY `d_dayhits` (`d_dayhits`),
																  KEY `d_weekhits` (`d_weekhits`),
																  KEY `d_monthhits` (`d_monthhits`),
																  KEY `d_stint` (`d_stint`),
																  KEY `d_hide` (`d_hide`),
																  KEY `d_score` (`d_score`),
																  KEY `d_topic` (`d_topic`),
																  KEY `d_letter` (`d_letter`),
																  KEY `d_name` (`d_name`),
																  KEY `d_starring` (`d_starring`),
																  KEY `d_directed` (`d_directed`),
																  KEY `d_type_name_index` (`d_type_name`),
																  KEY `d_id_index` (`d_id`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=984429 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_vod_pasre_item",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_pasre_item` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `prod_id` int(11) DEFAULT NULL,
																			  `prod_name` varchar(300) DEFAULT NULL,
																			  `create_date` datetime DEFAULT NULL,
																			  `d_status` int(2) DEFAULT '1',
																			  `channels` text NOT NULL,
																			  PRIMARY KEY (`id`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_pasre_item... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_vod_popular",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_popular` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `iphone_pic_url` varchar(255) DEFAULT NULL,
																		  `vod_id` int(11) NOT NULL,
																		  `ipad_pic_url` varchar(255) DEFAULT NULL,
																		  `disp_order` int(11) NOT NULL DEFAULT '1',
																		  `status` int(11) NOT NULL DEFAULT '1',
																		  `info_desc` text,
																		  `type` int(11) DEFAULT '0',
																		  PRIMARY KEY (`id`),
																		  UNIQUE KEY `vod_id_unique` (`vod_id`),
																		  KEY `vod_id_index` (`vod_id`)
																		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_popular... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_vod_subscribe",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_subscribe` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `prod_id` int(11) DEFAULT NULL,
																			  `create_date` datetime DEFAULT NULL,
																			  `subscriber_num` int(11) DEFAULT NULL,
																			  `prod_name` varchar(300) DEFAULT NULL,
																			  `author_id` int(11) DEFAULT NULL,
																			  PRIMARY KEY (`id`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_subscribe... \');</script>';
		}

		if(!isExistTable("".$app_tablepre."mac_vod_topic",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_topic` (`t_id` int(11) NOT NULL AUTO_INCREMENT,
																		  `t_name` varchar(64) DEFAULT NULL,
																		  `t_enname` varchar(128) DEFAULT NULL,
																		  `t_sort` int(11) DEFAULT '0',
																		  `t_template` varchar(128) DEFAULT NULL,
																		  `t_pic` varchar(255) DEFAULT NULL,
																		  `t_des` varchar(255) DEFAULT NULL,
																		  `t_type` int(11) NOT NULL DEFAULT '0',
																		  `t_flag` int(1) DEFAULT '0',
																		  `t_userid` int(11) DEFAULT '0',
																		  `create_date` datetime DEFAULT NULL,
																		  `t_bdtype` int(1) DEFAULT NULL,
																		  `t_toptype` int(11) DEFAULT '0',
																		  `t_tag_name` varchar(1000) NOT NULL,
																		  `can_search_device` varchar(300) DEFAULT NULL,
																		  PRIMARY KEY (`t_id`),
																		  KEY `t_sort` (`t_sort`),
																		  KEY `t_userid` (`t_userid`),
																		  KEY `t_id` (`t_id`),
																		  KEY `t_bdtype` (`t_bdtype`),
																		  KEY `t_flag` (`t_flag`),
																		  KEY `t_type` (`t_type`),
																		  KEY `t_name_index` (`t_name`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7285 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_topic... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."mac_vod_topic_items",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_topic_items` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `topic_id` int(11) NOT NULL,
																			  `author_id` int(11) NOT NULL DEFAULT '0',
																			  `vod_id` int(11) NOT NULL,
																			  `vod_name` varchar(150) DEFAULT NULL,
																			  `vod_pic` varchar(255) DEFAULT NULL,
																			  `vod_pic_ipad` varchar(255) DEFAULT NULL,
																			  `flag` int(1) DEFAULT '1',
																			  `disp_order` int(5) DEFAULT '0',
																			  `create_date` datetime DEFAULT NULL,
																			  PRIMARY KEY (`id`),
																			  UNIQUE KEY `unique_key` (`topic_id`,`vod_id`),
																			  KEY `author_id` (`author_id`),
																			  KEY `vod_id` (`vod_id`)
																			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7244 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_topic_items... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."mac_vod_type",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."mac_vod_type` (`t_id` int(11) NOT NULL AUTO_INCREMENT,
																	  `t_name` varchar(64) DEFAULT NULL,
																	  `t_enname` varchar(128) DEFAULT NULL,
																	  `t_sort` int(11) NOT NULL,
																	  `t_pid` int(11) DEFAULT '0',
																	  `t_key` varchar(255) DEFAULT NULL,
																	  `t_des` varchar(255) DEFAULT NULL,
																	  `t_template` varchar(64) DEFAULT NULL,
																	  `t_vodtemplate` varchar(64) DEFAULT NULL,
																	  `t_playtemplate` varchar(64) DEFAULT NULL,
																	  `t_hide` int(11) DEFAULT '0',
																	  `t_union` text,
																	  PRIMARY KEY (`t_id`),
																	  KEY `t_sort` (`t_sort`),
																	  KEY `t_pid` (`t_pid`),
																	  KEY `t_hide` (`t_hide`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=174 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'mac_vod_type... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_comments",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_comments` (`id` int(11) NOT NULL AUTO_INCREMENT,
																	  `author_id` int(11) DEFAULT NULL,
																	  `content_type` int(11) DEFAULT NULL,
																	  `content_name` varchar(50) DEFAULT NULL,
																	  `content_id` int(11) DEFAULT NULL,
																	  `create_date` datetime DEFAULT NULL,
																	  `status` int(11) DEFAULT NULL,
																	  `like_number` int(11) DEFAULT NULL,
																	  `content_pic_url` varchar(300) DEFAULT NULL,
																	  `comments` text,
																	  `thread_id` int(11) DEFAULT NULL,
																	  `thread_author_id` int(11) DEFAULT NULL,
																	  `author_photo_url` varchar(300) DEFAULT NULL,
																	  `author_username` varchar(50) DEFAULT NULL,
																	  `comments_leaf` tinyint(4) DEFAULT NULL,
																	  `comment_type` int(2) NOT NULL DEFAULT '0',
																	  `douban_comment_id` int(11) NOT NULL,
																	  `comment_title` varchar(300) NOT NULL,
																	  PRIMARY KEY (`id`),
																	  KEY `FK_Reference_5` (`content_id`),
																	  KEY `FK_Reference_7` (`author_id`),
																	  KEY `FK_Reference_8` (`thread_id`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38745 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_comments... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_feedback",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_feedback` (`id` int(11) NOT NULL AUTO_INCREMENT,
																	  `author_id` int(11) DEFAULT '0',
																	  `author_name` varchar(255) DEFAULT '',
																	  `email` varchar(255) DEFAULT '',
																	  `content` text NOT NULL,
																	  `ip` varchar(255) DEFAULT '',
																	  `user_agent` varchar(455) DEFAULT '',
																	  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
																	  PRIMARY KEY (`id`)
																	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_feedback... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_lookup",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_lookup` (`id` int(11) NOT NULL AUTO_INCREMENT,
																	  `content` varchar(50) DEFAULT NULL,
																	  `search_count` int(11) DEFAULT NULL,
																	  `last_search_date` datetime DEFAULT NULL,
																	  `keyword_order` int(11) DEFAULT NULL,
																	  PRIMARY KEY (`id`),
																	  KEY `i_content` (`content`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1883 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_lookup... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_my_dynamic",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_my_dynamic` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `author_id` int(11) DEFAULT NULL,
																		  `content_type` int(11) DEFAULT NULL,
																		  `content_name` varchar(50) DEFAULT NULL,
																		  `content_id` int(11) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  `status` int(11) DEFAULT NULL,
																		  `order_position` int(11) DEFAULT NULL,
																		  `dynamic_type` tinyint(4) DEFAULT NULL,
																		  `content_pic_url` varchar(300) DEFAULT NULL,
																		  `content_desc` varchar(300) DEFAULT NULL,
																		  PRIMARY KEY (`id`),
																		  KEY `i_author_id` (`author_id`),
																		  KEY `i_dynamic_type` (`dynamic_type`),
																		  KEY `content_id` (`content_id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2353 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_my_dynamic... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_my_friend",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_my_friend` ( `id` int(11) NOT NULL AUTO_INCREMENT,
																	  `author_id` int(11) DEFAULT NULL,
																	  `friend_id` int(11) DEFAULT NULL,
																	  `create_date` datetime DEFAULT NULL,
																	  `status` int(11) DEFAULT NULL,
																	  `friend_photo_url` varchar(300) DEFAULT NULL,
																	  `friend_username` varchar(50) DEFAULT NULL,
																	  PRIMARY KEY (`id`),
																	  KEY `FK_Reference_2` (`friend_id`),
																	  KEY `i_author_id` (`author_id`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1450 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_my_friend... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_notfiy_msg",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_notfiy_msg` ( `id` int(11) NOT NULL AUTO_INCREMENT,
																		  `author_id` int(11) DEFAULT NULL,
																		  `notify_type` int(11) DEFAULT NULL,
																		  `nofity_user_id` int(11) DEFAULT NULL,
																		  `notify_user_name` varchar(80) DEFAULT NULL,
																		  `content_id` int(11) DEFAULT NULL,
																		  `content_info` varchar(300) DEFAULT NULL,
																		  `status` int(11) DEFAULT NULL,
																		  `created_date` datetime DEFAULT NULL,
																		  `notify_user_pic_url` varchar(300) DEFAULT NULL,
																		  `content_type` int(11) DEFAULT NULL,
																		  `content_desc` varchar(300) NOT NULL,
																		  PRIMARY KEY (`id`),
																		  KEY `i_author_id` (`author_id`),
																		  KEY `i_notify_type` (`notify_type`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=440 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_notfiy_msg... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_play_history",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_play_history` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `author_id` int(11) DEFAULT NULL,
																		  `prod_type` int(11) DEFAULT NULL,
																		  `prod_name` varchar(100) DEFAULT NULL,
																		  `prod_subname` varchar(200) DEFAULT NULL,
																		  `prod_id` int(11) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  `status` int(11) DEFAULT NULL,
																		  `play_type` int(11) DEFAULT NULL,
																		  `playback_time` int(11) DEFAULT NULL,
																		  `video_url` varchar(500) DEFAULT NULL,
																		  `duration` int(11) DEFAULT NULL,
																		  PRIMARY KEY (`id`),
																		  KEY `i_author_id` (`author_id`),
																		  KEY `i_prod_type` (`prod_type`),
																		  KEY `i_prod_id` (`prod_id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1537 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_play_history... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_play_records",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_play_records` (`id` int(11) NOT NULL AUTO_INCREMENT,
																		  `author_id` int(11) DEFAULT NULL,
																		  `prod_type` int(11) DEFAULT NULL,
																		  `prod_name` varchar(100) DEFAULT NULL,
																		  `prod_subname` varchar(200) DEFAULT NULL,
																		  `client` varchar(100) DEFAULT NULL,
																		  `prod_id` int(11) DEFAULT NULL,
																		  `create_date` datetime DEFAULT NULL,
																		  PRIMARY KEY (`id`),
																		  KEY `i_author_id` (`author_id`),
																		  KEY `i_prod_type` (`prod_type`),
																		  KEY `i_prod_id` (`prod_id`)
																		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_play_records... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_system_config",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_system_config` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `sys_value` text,
																			  `sys_key` varchar(100) DEFAULT NULL,
																			  `create_date` datetime DEFAULT NULL,
																			  `sys_desc` varchar(500) DEFAULT NULL,
																			  `status` int(11) DEFAULT '1',
																			  PRIMARY KEY (`id`),
																			  UNIQUE KEY `sys_key_unique` (`sys_key`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_system_config... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_user",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_user` (`id` int(11) NOT NULL AUTO_INCREMENT,
																  `username` varchar(100) DEFAULT NULL,
																  `password` varchar(50) DEFAULT NULL,
																  `email` varchar(50) DEFAULT NULL,
																  `phone` varchar(20) DEFAULT NULL,
																  `sina_wb_user_id` varchar(50) DEFAULT NULL,
																  `qq_wb_user_id` varchar(50) DEFAULT NULL,
																  `ren_user_id` varchar(50) DEFAULT NULL,
																  `douban_user_id` varchar(50) DEFAULT NULL,
																  `create_date` datetime DEFAULT NULL,
																  `last_modify_date` datetime DEFAULT NULL,
																  `last_login_date` datetime DEFAULT NULL,
																  `user_photo_url` varchar(200) DEFAULT NULL,
																  `user_bg_photo_url` varchar(200) DEFAULT NULL,
																  `status` int(11) DEFAULT NULL,
																  `signature` varchar(300) DEFAULT NULL,
																  `other_part_one_user_id` varchar(50) DEFAULT NULL,
																  `other_part_two_user_id` varchar(50) DEFAULT NULL,
																  `other_part_three_user_id` varchar(50) DEFAULT NULL,
																  `other_part_four_user_id` varchar(50) DEFAULT NULL,
																  `like_number` int(11) DEFAULT '0',
																  `watch_number` int(11) DEFAULT '0',
																  `fan_number` int(11) DEFAULT '0',
																  `category` varbinary(20) DEFAULT NULL,
																  `grade` int(11) DEFAULT NULL,
																  `prestige` int(11) DEFAULT NULL,
																  `nickname` varchar(80) NOT NULL,
																  `good_number` int(11) NOT NULL DEFAULT '0',
																  `top_number` int(11) NOT NULL DEFAULT '0',
																  `favority_number` int(11) NOT NULL DEFAULT '0',
																  `share_number` int(11) NOT NULL DEFAULT '0',
																  `device_number` varchar(300) DEFAULT NULL,
																  `device_type` varchar(200) DEFAULT NULL,
																  PRIMARY KEY (`id`),
																  KEY `i_username` (`username`),
																  KEY `i_phone` (`phone`),
																  KEY `i_sina_wb_user_id` (`sina_wb_user_id`),
																  KEY `i_douban_user_id` (`douban_user_id`),
																  KEY `i_ren_user_id` (`ren_user_id`),
																  KEY `i_qq_wb_user_id` (`qq_wb_user_id`),
																  KEY `i_prestige` (`prestige`)
																) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2869 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_user... \');</script>';
		}
		if(!isExistTable("".$app_tablepre."tbl_video_feedback",$app_dbname)){
			$db->query( "CREATE TABLE `".$app_tablepre."tbl_video_feedback` (`id` int(11) NOT NULL AUTO_INCREMENT,
																			  `author_id` int(11) DEFAULT NULL,
																			  `prod_type` int(11) DEFAULT NULL,
																			  `prod_name` varchar(100) DEFAULT NULL,
																			  `client` varchar(100) DEFAULT NULL,
																			  `prod_id` int(11) DEFAULT NULL,
																			  `create_date` datetime DEFAULT NULL,
																			  `feedback_memo` varchar(1000) DEFAULT NULL,
																			  `feedback_type` varchar(50) NOT NULL DEFAULT '8',
																			  `status` int(11) NOT NULL DEFAULT '1',
																			  PRIMARY KEY (`id`),
																			  KEY `i_author_id` (`author_id`),
																			  KEY `i_prod_type` (`prod_type`),
																			  KEY `i_prod_id` (`prod_id`)
																			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2970 ;");
			echo '<script type="text/javascript">showmessage(\'创建数据表 '.$app_tablepre.'tbl_video_feedback... \');</script>';
		}

		echo '<script type="text/javascript">showmessage(\'数据库结构创建完成... \');</script>';


		$db->query( "insert into ".$app_tablepre."mac_manager(m_id,m_name,m_password,m_status,m_levels) values('1','".$m_name."','".md5($m_password1)."',1,'2, 4, 6, 7, 8 ,9, 10')");
		echo '<script type="text/javascript">showmessage(\'管理员帐号'.$m_name.'初始化成功... \');</script>';



		$db->query("
					INSERT INTO `mac_vod_type` (`t_id`, `t_name`, `t_enname`, `t_sort`, `t_pid`, `t_key`, `t_des`, `t_template`, `t_vodtemplate`, `t_playtemplate`, `t_hide`, `t_union`) VALUES
					(1, '电影', 'dianying', 1, 0, '', '', 'vodlist.html', 'vod.html', 'vodplay.html', 0, ''),
					(2, '连续剧', 'lianxuju', 2, 0, '', '', 'vodlist.html', 'vod.html', 'vodplay.html', 0, ',114_7,'),
					(3, '综艺', 'zongyi', 3, 0, '', '', 'vodlist.html', 'vod.html', 'vodplay.html', 0, ',114_16,'),
					(4, '视频', 'shiping', 4, 0, '', '', 'vodlist.html', 'vod.html', 'vodplay.html', 1, ''),
					
					(131, '动漫', '动漫', 83, 0, '动漫', '动漫', 'vodlist.html', 'vod.html', 'vodplay.html', 0, NULL),
					(132, '记录片', '记录片', 84, 0, '记录片', '记录片', 'vodlist.html', 'vod.html', 'vodplay.html', 1, NULL),
					(133, '教育', '教育', 85, 0, '教育', '教育', 'vodlist.html', 'vod.html', 'vodplay.html', 0, NULL),
					(173, '混合类', 'hunhelei', 125, 0, 'hunhelei', 'hunhelei', 'vodlist.html', 'vod.html', 'vodplay.html', 0, NULL);");

		$file = "joyplus.sql";
		if (file_exists($file)){

			global $db;
			$buffer = '';
			// Defaults for parser
			$sql = '';
			$start_pos = 0;
			$i = 0;
			$len= 0;
			$big_value = 2147483647;
			$delimiter_keyword = 'DELIMITER '; // include the space because it's mandatory
			$length_of_delimiter_keyword = strlen($delimiter_keyword);
			$buffer=iconv("UTF-8","UTF-8", file_get_contents($file));
			$len = strlen($buffer);
			$GLOBALS['finished']=true;
			$sql_delimiter = ';';
			while ($i < $len) {
				$found_delimiter = false;
				// Find first interesting character
				$old_i = $i;
				// this is about 7 times faster that looking for each sequence i
				// one by one with strpos()
				if (preg_match('/(\'|"|#|-- |\/\*|`|(?i)(?<![A-Z0-9_])' . $delimiter_keyword . ')/', $buffer, $matches, PREG_OFFSET_CAPTURE, $i)) {
					// in $matches, index 0 contains the match for the complete
					// expression but we don't use it
					$first_position = $matches[1][1];
				} else {
					$first_position = $big_value;
				}
				/**
				 * @todo we should not look for a delimiter that might be
				 *       inside quotes (or even double-quotes)
				 */

				// the cost of doing this one with preg_match() would be too high
				$first_sql_delimiter = strpos($buffer, $sql_delimiter, $i);
				if ($first_sql_delimiter === false) {
					$first_sql_delimiter = $big_value;
				} else {
					$found_delimiter = true;
				}

				// set $i to the position of the first quote, comment.start or delimiter found
				$i = min($first_position, $first_sql_delimiter);

				if ($i == $big_value) {
					// none of the above was found in the string

					$i = $old_i;
					if (!$GLOBALS['finished']) {
						break;
					}
					// at the end there might be some whitespace...
					if (trim($buffer) == '') {
						$buffer = '';
						$len = 0;
						break;
					}
					// We hit end of query, go there!
					$i = strlen($buffer) - 1;
				}

				// Grab current character
				$ch = $buffer[$i];

				// Quotes
				if (strpos('\'"`', $ch) !== false) {
					$quote = $ch;
					$endq = false;
					while (!$endq) {
						// Find next quote
						$pos = strpos($buffer, $quote, $i + 1);
						/*
						 * Behave same as MySQL and accept end of query as end of backtick.
						 * I know this is sick, but MySQL behaves like this:
						 *
						 * SELECT * FROM `table
						 *
						 * is treated like
						 *
						 * SELECT * FROM `table`
						 */
						if ($pos === false && $quote == '`' && $found_delimiter) {
							$pos = $first_sql_delimiter - 1;
							// No quote? Too short string
						} elseif ($pos === false) {
							// We hit end of string => unclosed quote, but we handle it as end of query
							if ($GLOBALS['finished']) {
								$endq = true;
								$i = $len - 1;
							}
							$found_delimiter = false;
							break;
						}
						// Was not the quote escaped?
						$j = $pos - 1;
						while ($buffer[$j] == '\\') $j--;
						// Even count means it was not escaped
						$endq = (((($pos - 1) - $j) % 2) == 0);
						// Skip the string
						$i = $pos;

						if ($first_sql_delimiter < $pos) {
							$found_delimiter = false;
						}
					}
					if (!$endq) {
						break;
					}
					$i++;
					// Aren't we at the end?
					if ($GLOBALS['finished'] && $i == $len) {
						$i--;
					} else {
						continue;
					}
				}

				// Not enough data to decide
				if ((($i == ($len - 1) && ($ch == '-' || $ch == '/'))
				|| ($i == ($len - 2) && (($ch == '-' && $buffer[$i + 1] == '-')
				|| ($ch == '/' && $buffer[$i + 1] == '*')))) && !$GLOBALS['finished']) {
					break;
				}

				// Comments
				if ($ch == '#'
				|| ($i < ($len - 1) && $ch == '-' && $buffer[$i + 1] == '-'
				&& (($i < ($len - 2) && $buffer[$i + 2] <= ' ')
				|| ($i == ($len - 1)  && $GLOBALS['finished'])))
				|| ($i < ($len - 1) && $ch == '/' && $buffer[$i + 1] == '*')
				) {
					// Copy current string to SQL
					if ($start_pos != $i) {
						$sql .= substr($buffer, $start_pos, $i - $start_pos);
					}
					// Skip the rest
					$start_of_comment = $i;
					// do not use PHP_EOL here instead of "\n", because the export
					// file might have been produced on a different system
					$i = strpos($buffer, $ch == '/' ? '*/' : "\n", $i);
					// didn't we hit end of string?
					if ($i === false) {
						if ($GLOBALS['finished']) {
							$i = $len - 1;
						} else {
							break;
						}
					}
					// Skip *
					if ($ch == '/') {
						$i++;
					}
					// Skip last char
					$i++;
					// We need to send the comment part in case we are defining
					// a procedure or function and comments in it are valuable
					$sql .= substr($buffer, $start_of_comment, $i - $start_of_comment);
					// Next query part will start here
					$start_pos = $i;
					// Aren't we at the end?
					if ($i == $len) {
						$i--;
					} else {
						continue;
					}
				}
				// Change delimiter, if redefined, and skip it (don't send to server!)
				if (strtoupper(substr($buffer, $i, $length_of_delimiter_keyword)) == $delimiter_keyword
				&& ($i + $length_of_delimiter_keyword < $len)) {
					// look for EOL on the character immediately after 'DELIMITER '
					// (see previous comment about PHP_EOL)
					$new_line_pos = strpos($buffer, "\n", $i + $length_of_delimiter_keyword);
					// it might happen that there is no EOL
					if (false === $new_line_pos) {
						$new_line_pos = $len;
					}
					$sql_delimiter = substr($buffer, $i + $length_of_delimiter_keyword, $new_line_pos - $i - $length_of_delimiter_keyword);
					$i = $new_line_pos + 1;
					// Next query part will start here
					$start_pos = $i;
					continue;
				}

				// End of SQL
				if ($found_delimiter || ($GLOBALS['finished'] && ($i == $len - 1))) {
					$tmp_sql = $sql;
					if ($start_pos < $len) {
						$length_to_grab = $i - $start_pos;

						if (! $found_delimiter) {
							$length_to_grab++;
						}
						$tmp_sql .= substr($buffer, $start_pos, $length_to_grab);
						unset($length_to_grab);
					}
					// Do not try to execute empty SQL
					if (! preg_match('/^([\s]*;)*$/', trim($tmp_sql))) {
						$sql = $tmp_sql;
						$sql=preg_replace("/--.*\n/iU","",$sql);//去掉注释
						$sql = trim($sql);
						if((stripos($sql, "CREATE") !==false && stripos($sql, "CREATE") ==0 )|| (stripos($sql, "INSERT") !==false && strpos($sql, "INSERT")==0)
						|| (stripos($sql, "UPDATE") !==false && strpos($sql, "UPDATE")==0)|| (stripos($sql, "DELETE") !==false && strpos($sql, "DELETE")==0)){

							$rs2=$db->query($sql);
						}else{
							$sql = str_replace("/*!40101", "", $sql);
							$sql = str_replace("*/", "", $sql);
							$rs2=$db->query($sql);
						}

						$buffer = substr($buffer, $i + strlen($sql_delimiter));
						$len = strlen($buffer);
						$sql = '';
						$i = 0;
						$start_pos = 0;
						// Any chance we will get a complete query?
						//if ((strpos($buffer, ';') === false) && !$GLOBALS['finished']) {
						if ((strpos($buffer, $sql_delimiter) === false) && !$GLOBALS['finished']) {
							break;
						}
					} else {
						$i++;
						$start_pos = $i;
					}
				}
			}


		}


		echo '<script type="text/javascript">showmessage(\'数据分类初始化成功... \');</script>';
		updateCacheFile();
		echo '<script type="text/javascript">showmessage(\'数据缓存初始化成功... \');</script>';
	}
	unset($db);
}

function stepD()
{
	show_step(4,"安装完毕","正在删除安装脚本");
	//if (file_exists("index.php")){
	//	@unlink("index.php");
	//}
	?>
	
	<div class="main">
		<div class="desc">如果没有自动删除install/index.php，请手工删除。 5秒后自动跳转到后台管理登录页面...</div>
		<script> setTimeout("gonextpage();",5000); function gonextpage(){location.href='../manager/index.php';} </script>
		<?php
}
?>