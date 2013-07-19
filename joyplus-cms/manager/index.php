<?php
require_once ("admin_conn.php");
require_once ("version.php");
$action = be("get","action");
switch(trim($action))
{
	case "login" : login();break;
	case "check" : checkLogin();break;
	case "logout" : logout();break;
	case "go" : gourl();break;
	case "wel" : chkLogin(); headAdmin ("欢迎页面") ; wel();break;
	default : chkLogin(); main();break;
}
dispseObj();

function gourl()
{
	$url = be("get","url");
	if($url!=""){
		if (strpos($url,".")<1) { $url.=".php"; }
		echo "<script language=\"javascript\">setTimeout(\"gourl();\",500);function gourl(){location.href='".$url."';}</script>";
	}
	else{
		echo "url参数不能为空";
	}
}

function checkLogin()
{
	global $db;
	$m_name = be("post","m_name");
	$m_name = chkSql($m_name,true);
	$m_password = be("post","m_password");
	$m_password = chkSql($m_password,true);
	$m_password = md5($m_password);

	$m_check = be("post","m_check");
	if (isN($m_name) || isN($m_password) || isN($m_check)){
		alertUrl ("请输入您的用户名或密码!","?action=login");
	}
	$row = $db->getRow("SELECT * FROM {pre}manager WHERE m_name='". $m_name ."' AND m_password = '". $m_password ."' AND m_status=1");
	if ($row && ($m_check==app_safecode)){
		sCookie ("adminid",$row["m_id"]);
		sCookie ("adminname",$row["m_name"]);
		sCookie ("adminlevels",$row["m_levels"]);
		$randnum = md5(rand(1,99999999));
		sCookie ("admincheck",md5($randnum . $row["m_name"] .$row["m_id"]));
		$db->Update("{pre}manager",array("m_logintime","m_loginip","m_random"),array(date("Y-m-d H:i:s"),getIP(),$randnum)," m_id=". $row["m_id"]);
		echo "<script>top.location.href='index.php';</script>";
	}
	else{
		alertUrl ("您输入的用户名和密码不正确或者您不是系统管理员!","?action=login");
	}
}

function logout()
{
	sCookie ("adminname","");
	sCookie ("adminid","");
	sCookie ("adminlevels","");
	sCookie ("admincheck", "");
	echo "<script>top.location.href='index.php?action=login';</script>";
}

function wel()
{
	?>

</head>
<body>
	<table class="index tb">
		<tr>
			<td colspan="4">站点信息</td>
		</tr>
		<tr>
			<td width="90">服务器类型：</td>
			<td width="400"><?php echo PHP_OS;?></td>
			<td width="90">脚本解释引擎：</td>
			<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
		</tr>
		<tr>
			<td>站点物理路径：</td>
			<td><?php echo $_SERVER['PATH_TRANSLATED'];?></td>
			<td>服务器名：</td>
			<td><?php echo $_SERVER["SERVER_NAME"];?></td>
		</tr>
		<tr>
			<td>访问远程URL allow_url_fopen：</td>
			<td><?php echo getcon("allow_url_fopen");?></td>
			<td>访问远程URL curl_init：</td>
			<td><?php echo isfun('curl_init');?></td>
		</tr>
		<tr>
			<td>mb_string 函数支持库：</td>
			<td><?php echo isfun("mb_convert_encoding");?></td>
			<td>xml解析DOMDocument：</td>
			<td><?php echo isfun("dom_import_simplexml");?></td>
		</tr>
		<tr>
			<td>单页最大使用内存 memory_limit：</td>
			<td><?php echo getcon("memory_limit")?></td>
			<td>POST最大数据量 post_max_size：</td>
			<td><?php echo getcon("post_max_size");?></td>
		</tr>
		<tr>
			<td>最大上传文件 upload_max_filesize：</td>
			<td><?php echo getcon("upload_max_filesize");?></td>
			<td>页面最长运行 max_execution_time：</td>
			<td><?php echo getcon("max_execution_time");?></td>
		</tr>
		<tr>
			<td>目录权限检测：</td>
			<td colspan=3><?php
			echo "/";
			if(is_writable("../")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/";
			if(is_writable("../inc/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/config.php";
			if(is_writable("../inc/config.php")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/ftp.php";
			if(is_writable("../inc/ftp.php")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/cache.php";
			if(is_writable("../inc/cache.php")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/inc/timmingset.xml";
			//				if(is_writable("../inc/timmingset.xml")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/inc/voddown.xml";
			//				if(is_writable("../inc/voddown.xml")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/vodplay.xml";
			if(is_writable("../inc/vodplay.xml")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/inc/vodserver.xml";
			//				if(is_writable("../inc/vodserver.xml")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/vodarea.txt";
			if(is_writable("../inc/vodarea.txt")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/inc/vodlang.txt";
			if(is_writable("../inc/vodlang.txt")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/";
			if(is_writable("../upload/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/upload/art/";
			//				if(is_writable("../upload/art/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/vod/";
			if(is_writable("../upload/vod/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/cache/";
			if(is_writable("../upload/cache/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/export/";
			if(is_writable("../upload/export/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/topic/";
			if(is_writable("../upload/topic/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			echo "<br>/upload/thirdpartlogo/";
			if(is_writable("../upload/thirdpartlogo/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/upload/playdata/";
			//				if(is_writable("../upload/playdata/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/js/player.js";
			//				if(is_writable("../js/player.js")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			//				echo "<br>/admin/bak/";
			//				if(is_writable("bak/")){ echo "<font color=green>OK</font>";}else{ echo "<font color=red>NO</font>";}
			?>; <br>备注：如使用在线更新补丁需程序所有目录有写权限； upload及其子目录都需要有写权限； <br>本网站不支持IE浏览器，因为IE浏览器在浪费前端开发的时间；
			</td>
		</tr>

	</table>

</body>
</html>
			<?php
}

function main()
{
	$menustr = file_get_contents( "../inc/dim_menu.txt" );
	$menustr = replaceStr($menustr,chr(10),"");

	if(!is_null($menustr) && strlen($menustr)>0){
		$menuarr = explode(chr(13),$menustr);
		$rc=false;
	}else {
		$menuarr =array();
	}

	$menudiy = "\"welcome\":{\"text\":\"欢迎页面\",\"url\":\"index.php?action=wel\"}";
	if( count($menuarr)>0) { $menudiy = $menudiy.","; }
	for ($i=0;$i<count($menuarr);$i++){
		$name="";
		$icon="line";
		$url="#";
		if ($rc) { $menudiy = $menudiy . ",";}
		if ($menuarr[$i] != ""){
			$valarr = explode(",",$menuarr[$i]);
			if (count($valarr)==2) { $icon = "icon-100".$i; $name = $valarr[0]; $url = $valarr[1]; }
		}
		$menudiy = $menudiy ."\"diym".$i."\":{\"text\":\"".$name."\",\"url\":\"".$url."\"}";

		$rc = true;
	}
	//	echo($menudiy);
	$menudiy = $menudiy .",\"diym_1\":{\"text\":\"<font>修改密码</font>\",\"url\":\"admin_forgot_pwd.php\"}";
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>管理中心</title>
<link rel="stylesheet" type="text/css" href="../images/adm/style.css" />
<link rel="stylesheet" type="text/css" href="../images/adm/form.css" />
<script language="javascript" src="../js/jquery.js"></script>
<script language="javascript" src="../js/jquery.pngFix.js"></script>
</head>
<body>
	<script type="text/javascript">
	function updateindex(){
		$("#cachestate").text("Loading....");
		$.get("admin_cache.php?action=uptoindex&rnd"+Math.random(),function(obj){
			if(obj !="" && obj !=undefined){
				$("#cachestate").text("静态首页删除失败！");
			}
			else{
				$("#cachestate").text("静态首页删除完毕！");
			}
		});
	}
	
var menu = {
	"m1":{"text":"首页快捷","default":"welcome","children":{<?php echo $menudiy;?> }},
	
	"m2":{"text":"系统管理","default":"player_config","children":{"sql":{"text":"执行SQL语句","url":"admin_sql.php"},"player_config":{"text":"播放器管理","url":"admin_player.php"},"leftdim_config":{"text":"快捷菜单配置","url":"admin_leftdim.php"},"weixin_keyword":{"text":"微信配置","url":"http://weixin.joyplus.tv/admin_keyword.php"}}},
	
	
	"m4":{"text":"视频管理","default":"vod","children":{"vodtype":{"text":"视频分类","url":"admin_vod_type.php"},"arealang":{"text":"地区语言","url":"admin_vod_arealang.php"},"vodtopic":{"text":"视频榜单","url":"admin_vod_topic.php"},"vodpopular":{"text":"视频轮播图","url":"admin_vod_popular.php"},"vod":{"text":"视频数据","url":"admin_vod.php"},"vodadd":{"text":"添加视频","url":"admin_vod.php?action=add"},"vod_feedback":{"text":"用户视频反馈","url":"admin_vod_feedback.php"}}},
	

	
	"m6":{"text":"用户管理","default":"manager","children":{"manager":{"text":"用户管理","url":"admin_manager.php"}}},
	
	
	"m8":{"text":"采集管理","default":"vodcj","children":{"vodcj":{"text":"视频自定义采集","url":"collect/collect_vod_manage.php"},"artcjdatazhuiju":{"text":"追剧管理","url":"collect/collect_vod_zhuiju.php"},"vodcjdata":{"text":"入库管理","url":"collect/collect_vod.php?action=main"}}},

	 "m9":{"text":"消息推送","default":"subscribe","children":{"wel":{"text":"介绍页面","url":"message_default.php"},"subscribe":{"text":"追剧推送","url":"admin_subscribe.php"},"onlinesubscribe":{"text":"实时推送","url":"admin_online_subscribe.php"}}},
	"m7":{"text":"开放API","default":"api","children":{"api":{"text":"配置","url":"api_manager.php"}}},
    "m10":{"text":"电视直播","default":"program","children":{"program":{"text":"电视频道管理","url":"admin_program.php"},"program_items":{"text":"节目单管理","url":"admin_program_items.php"},"program_play":{"text":"电视直播源管理","url":"admin_program_play.php"},"program_play_cj":{"text":"导入直播源","url":"admin_program_play_import.php"},"program_items_config":{"text":"相关配置","url":"admin_program_config.php"}}}, 
	

};
var currTab = 'm1';
var firstOpen = [];
var levels = '1, <?php echo getCookie("adminlevels")?>';
</script>
	<div id="loading">
		数据加载中...<img src="../images/loading.gif" />
	</div>
	<div class="back_nav">
		<div class="back_nav_list">
			<dl>
				<dt></dt>
				<dd>
					<a href="javascript:;" onclick="openItem('','');none_fn();"></a>
				</dd>
			</dl>
		</div>
		<div class="shadow"></div>
		<div class="close_float">
			<img src="../images/adm/close2.gif" />
		</div>
	</div>
	<div id="head">
		<div id="logo">
			<img src="../images/adm/joylogo.png" />
		</div>
		<div id="menu">
			<span>您好，<strong><?php echo getCookie("adminname")?> </strong> [<a
				href="?action=logout" title="注销登陆">注销</a>]</span>


		</div>

		<ul id="nav"></ul>
		<!-- div id="headBg"></div -->
	</div>
	<div id="content">
		<div id="left">
			<div id="leftMenus">
				<dl id="submenu">
					<dt>
						<a class="ico1" id="submenuTitle" href="javascript:;"></a>
					</dt>
				</dl>
			</div>
			<div class="copyright">
				<p>&copy; 2012-2013</p>
				<p>
					Powered by <a href="http://www.joyplus.tv" target="_blank">Joyplus</a>
				</p>
			</div>
		</div>
		<div id="right">
			<iframe hspace="0" vspace="0" frameborder="0" scrolling="auto"
				style="display: none;" width="100%" id="workspace" name="workspace"></iframe>
		</div>
		<div class="clear"></div>
	</div>
	<script type="text/javascript" src="../js/adm/index.js"></script>
</body>
</html>
	<?php
}
function login()
{
	?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>登录管理中心</title>
<link rel="stylesheet" type="text/css" href="../images/adm/login.css" />
<script language="javascript" src="../js/jquery.js"></script>
<script language="javascript" src="../js/jquery.pngFix.js"></script>
</head>
<body>
	<div id="header">
		<div class="logo"></div>
	</div>
	<div id="wrapper">
		<div class="console_left">
			<div class="title">欢迎使用悦视频视频管理系统</div>
			<p>
				<span>悦视频后台是采用PHP(mysql)和yii技术构建的一款专注于电视及移动互联网的视频管理系统！
				特别感谢开源软件（<a href="http://www.maccms.com" target="_blank" style="text-decoration:none;color:rgb(102, 102, 102);">苹果CMS</a>）开发者提供的辛勤工作。</span>

			</p>
			<div class="intro_1">轻松管理和配置各种信息</div>
			<div class="intro_2">轻松发布在线视频资源</div>
			<div class="intro_3">安全验证过滤无效信息</div>
		</div>
		<div class="console_right">
			<div class="title">请登录</div>
			<div class="login">
				<form action="?action=check" method="post" name="form1" id="form1"
					class="s_lo_f" autocomplete="off">
					<div class="user">
						<label>用户名:</label><input tabindex="1" type="text" name="m_name"
							id="m_name" size="20" maxLength="20" value="">

					</div>
					<div class="pwd">
						<label>密 码:</label><input tabindex="2" type="password"
							name="m_password" id="m_password" size="20" maxLength="20"
							value="">

					</div>
					<div class="code">
						<label>安全码:</label><input tabindex="3" type="password"
							name="m_check" id="m_check" size="20" maxLength="20" value="">

					</div>
					<div class="btn_login">
						<input type="submit" name="login" id="login" value="登陆" />
					</div>
				</form>
			</div>
		</div>
		<div class="clear"></div>
		<div class="reg"></div>
		<hr class="hr_solid" />
	</div>
	</div>
	<div id="footer">
		<span class="left">&copy;2012-2013 Powered by <a
			href="http://www.joyplus.tv" target="_blank">JoyPlus</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;阅读joyplusCMS
			<a
			href="https://github.com/joyplus/joyplus-cms/wiki/joyplus%E6%9C%8D%E5%8A%A1%E5%88%97%E8%A1%A8"
			target="_blank"><font style="color: #00a1d9">《API文档》</font> </a> </span>
	</div>
	<script>
var cururl=",<?php echo geturl();?>";
$(document).ready(function(){
	$("#login").click(
		function(){   
			if($('#m_name').val() == ""){
				alert( "请输入用户名" );
				$('#m_name').focus();
				return false;
			}
			if($('#m_password').val() == ""){
				alert( "请输入密码" );
				$('#m_password').focus();
				return false;
			}
			if($('#m_check').val() == ""){
				alert( "请输入安全码" );
				$('#m_check').focus();
				return false;
			}
			$("#form1").submit();
			$("#login").attr("disabled", "disabled");
		}
	);
	$('#m_name').focus();
    $("img").pngfix();
    if(cururl.indexOf("/admin/") >0){alert('请将文件夹admin改名,避免被黑客入侵攻击');}
});
</script>
</body>
</html>
	<?php
}
?>