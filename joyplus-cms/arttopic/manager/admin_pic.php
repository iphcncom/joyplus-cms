<?php
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("admin_conn.php");
chkLogin();
$action = be("all","action");
$wjs = be("get","wjs");

switch($action)
{
	case "downpic" : downpic();break;
	case "syncpic" : headAdmin ("视频远程图片同步"); syncpic();break;
	case "syncartpic" : headAdmin ("文章远程图片同步"); syncartpic();break;
	case "del" : del();break;
	case "picchk":
	case "picchkedit": picchkedit();break;
	default : headAdmin ("图片管理") ; pic();break;
}
dispseObj();

function del()
{
    $fnames = be("arr","fname");
    $arr = explode(",",$fnames);
    foreach($arr as $a){
      if(file_exists($a)){
          unlink($a);
      }
    }
    echo "删除记录成功";
}

function downpic()
{
	$path = be("get","path");
	$url = be("get","url");
	$pfile = be("get","file");
	savepic ($url,$path,$pfile);
}

function syncpic()
{
	global $db;
	$flag = "#err". date('Y-m-d',time());
	$sql = "SELECT count(d_id) FROM {pre}vod WHERE d_pic LIKE 'http://%' and instr(d_pic,'".$flag."')=0 ";
	$nums = $db->getOne($sql.$where);
	
	if($nums>0){
		$page = be("get","page");
		if (isN($page)){ $page=1;} else{ $page=intval($page);}
		$sql = "SELECT d_id,d_pic FROM {pre}vod WHERE d_pic LIKE 'http://%' and instr(d_pic,'".$flag."')=0 ";
		$pagecount = ceil($nums/20);
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		echo "<font color=red>同步失败的图片不再把图片地址设置为空，只能隔天后再同步！<br> 当前共".$nums."条数据需要同步下载,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		
		$num=0;
		while ($row = $db ->fetch_array($rs))
		{
				$d_pic = $row["d_pic"];
				if (strpos($d_pic,"#err")){
					$picarr = explode("#err",$d_pic);
					$d_pic =$picarr[0];
				}
				
				$status = false;
				$picname = time(). $num;
				if (strpos($d_pic,".jpg") || strpos($d_pic,".bmp") || strpos($d_pic,".png") || strpos($d_pic,".gif")){
					$extName= substring($d_pic,4,strlen($d_pic)-4);
				}
				else{
					$extName=".jpg";
				}
				$picpath = "../upload/vod" . "/" . getSavePicPath() . "/" ;
				$picpath = replaceStr($picpath,"///","/");
				$picpath = replaceStr($picpath,"//","/");
				if (!is_dir($picpath)) {
					mkdir($picpath);
				}
				$picfile = $picname . $extName;
				$status = savepic ($d_pic,$picpath,$picfile);
				if ($status){
					$d_pic = replaceStr($picpath,"../","").$picfile ;
				}
				else{
					$d_pic = $d_pic . $flag;
				}
				$num++;
				$db->query("UPDATE {pre}vod set d_pic='".$d_pic."' where d_id='".$row["d_id"]."'");
		}
		echo "<br><font color=red>暂停5秒后继续同步图片</font><br><script>setTimeout(\"updatenext();\",5000);function updatenext(){location.href='admin_pic.php?action=syncpic&page=".($page+1)."';}</script>";
		
	}
	else{
		alertUrl ("恭喜，所有外部图片已经成功同步到本地","admin_vod.php");
	}
	unset($rs);
}

function syncartpic()
{
	global $db;
	$ids = be("get","ids");
	$sql = "SELECT count(a_id) FROM {pre}art WHERE a_content LIKE '%src=\"http://%' ";
	if(!isN($ids)){
		$where = " and a_id not in (" .$ids.") ";
	}
	else{
		$ids="0";
	}
	$nums = $db->getOne($sql.$where);
	if($nums>0){
		$page = be("get","page");
		if (isN($page)){ $page=1;} else{ $page=intval($page);}
		$sql = "SELECT a_id,a_content FROM {pre}art WHERE a_content LIKE '%src=\"http://%' " .$where;
		$pagecount = ceil($nums/20);
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		echo "<font color=red>共".$nums."条数据需要同步下载,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		
		$num=0;
		while ($row = $db ->fetch_array($rs))
		{
				$a_content = $row["a_content"];
				$status = false;
				
				$rule = buildregx("<img[^>]*src\s*=\s*['".chr(34)."]?([\w/\-\:.]*)['".chr(34)."]?[^>]*>","is");
				preg_match_all($rule,$a_content,$matches);
				
				$matchfieldarr=$matches[1];
				$matchfieldstrarr=$matches[0];
				$matchfieldvalue="";
				foreach($matchfieldarr as $f=>$matchfieldstr)
				{
					$matchfieldvalue=$matchfieldstrarr[$f];
					$a_pic = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
					
					$picname = time(). $num;
					if (strpos($a_pic,".jpg") || strpos($a_pic,".bmp") || strpos($a_pic,".png") || strpos($a_pic,".gif")){
						$extName= substring($a_pic,4,strlen($a_pic)-4);
					}
					else{
						$extName=".jpg";
					}
					$picpath = "../upload/art" . "/" . getSavePicPath() . "/" ;
					$picpath = replaceStr($picpath,"///","/");
					$picpath = replaceStr($picpath,"//","/");
					if (!is_dir($picpath)) {
						mkdir($picpath);
					}
					$picfile = $picname . $extName;
					$status = savepic ($a_pic,$picpath,$picfile);
					if ($status){
						$a_content = replaceStr($a_content,$a_pic,  replaceStr($picpath.$picfile,"../", app_installdir ) );
					}
					else{
						$a_content = replaceStr($a_content,$a_pic,  "" );
					}
				}
				$num++;
				$db->query("UPDATE {pre}art set a_content='".$a_content."' where a_id='".$row["a_id"]."'");
		}
		echo "<br><font color=red>暂停5秒后继续同步图片</font><br><script>setTimeout(\"updatenext();\",5000);function updatenext(){location.href='admin_pic.php?action=syncartpic&page=".($page+1)."&ids=".$ids."';}</script>";
		
	}
	else{
		if ($ids!="0"){ $des= "以下文章ID:" . substring($ids,strlen($ids)-1,2). "的图片同步失败，请检查图片链接是否失效"; }else { $des = "恭喜，所有外部图片已经成功同步到本地！"; }
		alertUrl ("$des","admin_art.php");
	}
	unset($rs);
}

function savepic($picUrl,$picpath,$picfile)
{
	global $wjs;
	if($picfile==""){
		echo "file参数不正确";
		$status= false;
	}
	else{
		mkdirs( dirname($picpath) );
		$imgsbyte= getPage($picUrl,"utf-8");
		$size = round(strlen($imgsbyte)/1024, 3) ;
		if (strlen($imgsbyte) <100 || strpos(",".$imgsbyte,"<html") >0 || strpos(",".$imgsbyte,"<HTML") >0 ){
			echo "保存失败：<font color=red>非正常的图片文件，请编辑数据复制图片地址在浏览器中访问测试下是否正常</font>图片地址是: <a target=_blank href=".$picUrl.">". $picUrl ."</a><br>";
			$status=false;
		}
		else{
			fwrite(fopen($picpath . $picfile,"wb"),$imgsbyte);
			if(app_watermark==1){
				imageWaterMark($picpath . $picfile,getcwd()."\\editor",app_waterlocation,app_waterfont);
			}
			if (app_ftp==1){
				uploadftp( $picpath ,$picfile );
			}
			echo "<a target=_blank href=".$picpath.$picfile.">".$picpath.$picfile."</a>保存成功：<font color=red>".$size."Kb</font><br>";
			$status=true;
		}
		if (!isN($wjs)) { echo "<script>parent.dstate=true;</script>";}
	}
	return $status;
}

function pic()
{
	$rootPath = be("get","rootpath");
?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","?action=del");
				$("#form1").submit();
			}
			else{return false}
		});
		$("#btnChk").click(function(){
			if(confirm('确定要清理垃圾图片吗?')){
				location.href= "?action=picchk";
			}
			else{return false}
		});
		$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
		});
	});
</script>
</head>
<body>
<form action="?action=del" method="post" id="form1" name="form1"> 
<table class="tb">
	<tr>
	<td><strong>文件名</strong></td>
    <td width="10%"><strong>文件大小</strong></td>
    <td width="20%"><strong>修改时间</strong></td>
    <td width="15%"><strong>操作</strong>  </td>
	</tr>
<?php
if( isN($rootPath) ){ $rootPath = "../upload/"; }
bianliFolder($rootPath);
?>
<tr><td colspan=4>
<input type="checkbox" name="chkall" onClick="checkAll(this.checked,'fname[]')"/>全选
<input name=button type=submit class=input value=删除 id=btnDel>
<input name=button type=button class=input value=清理垃圾图片 id=btnChk>
</td>
</tr>
</table>
</form>
</body>
</html>
<?php
}

function bianliFolder($currentPath)
{
	$upperFolder = substring($currentPath,10);
	if ($upperFolder==".."){ $upperFolder = "../upload/";}
	if ($upperFolder!="../upload/"){
		echo "<tr><td colspan=\"5\">非法访问目录</td></tr>";
	}
	
	if ($currentPath !="../upload/"){
		echo "<tr><td colspan=\"5\"><img src=\"../images/icons/dir2.gif\"> <a href=\"?rootpath=" .$upperFolder."\">上级目录</a></td></tr>";
	}
    
    if(is_dir($currentPath)){
		$ocfolder = opendir( $currentPath );
		$num = 0; $sumsize = 0;
		while ( $sFile = readdir( $ocfolder ) ){
			if ( $sFile != '.' && $sFile != '..' ){
				if ( is_dir( $currentPath . $sFile ) ){
					echo "<tr><td colspan=\"5\"><img src=\"../images/icons/dir.gif\"> <a href=\"?rootpath=".$currentPath.$sFile."/\">".$sFile."</a></td></tr>";
				}
				else{
					$num++;
					$fsize = @filesize( $currentPath . $sFile ) ;
					$filetime = filemtime ($currentPath . $sFile);
					$sumsize = $sumsize+$fsize;
					echo "<tr><td><img src=\"../images/icons/asp.gif\"> ".$sFile."</td><td>".round( $fsize / 1024 )." KB</td><td>".$filetime."</td><td><a href=\"".$currentPath . $sFile."\" target=\"_blank\">浏览</a> &nbsp;<input type=\"checkbox\" name=\"fname[]\" value=\"".$currentPath . $sFile."\"></td></tr>";
				}
			}
		}
		echo "<tr><td colspan=\"4\">本目录下共有<font color=red><b>".$num."</b></font>个文件; 占用<font color=red><b>".$sumsize."</b></font><font color=#FF0000><b>K</b></font>空间</td></tr>";
		closedir($ocfolder);
		unset($ocfolder);
	}
}

function picchkedit()
{
	listFolderContents("../upload/vod/");
	echo "清理完毕";
}

function listFolderContents($strPath)
{
	global $db;
	$d= $strPath;
	
	if (is_dir($d)) {
		$dh=opendir($d);
		$filecount= sizeof(scandir($d)) -2 ;
		echo "<b>&nbsp;<font color='#ff0000'>".$d." >> 共&nbsp;" . $filecount . " 个文件和文件夹 </font> <br>";
			
		while (false !== ( $file = readdir ($dh))){
			if($file!="." && $file!=".."){ 
      			$fullpath=$d.$file;
      			
      			if(!is_dir($fullpath)) {
      				$tmpfile = replaceStr($fullpath,"../upload/","upload/");
					$sql="select d_pic from {pre}vod where d_pic='$tmpfile'";
					$row=$db->getOne($sql);
					if(!$row){
						unlink($fullpath);
						echo "<font style=font-size:12px;><font color=red>".$file."</font>"."无效图片已经删除...</font><br/>";
					}
     			} 
     			else{
     				listFolderContents($fullpath ."/");
     			}
			}
   		 }
		closedir($dh);
		unset($dh);
	}
	
}
?>