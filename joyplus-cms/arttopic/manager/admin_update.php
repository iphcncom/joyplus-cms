<?php
ob_end_clean();
ob_implicit_flush(true);
require_once ("admin_conn.php");
require_once ("version.php");
chkLogin();

$action = be("get","action");
$updateserver = "http://www.maccms.com/update/p/";
$updatelog = "bak/update.xml";

$verstr = getPage($updateserver . "?v=". version,"utf-8");
$adpath = $_SERVER["SCRIPT_NAME"];
$adpath= substring($adpath,strripos($adpath,"/"));
$n = strripos($adpath,"/");
$adpath= substring($adpath,strlen($adpath)-$n ,$n+1) ."/";

switch($action)
{
	case "checkversion" : checkversion();break;
	case "showfilelist" : headAdmin ("更新列表"); showfilelist();break;
	case "showfile" : headAdmin ("更新列表"); showfile();break;
}
dispseObj();

function checkversion()
{
	global $verstr;
	if(strpos($verstr,"</maccms>")>0){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> loadxml($verstr);
		$xmlnode = $doc -> documentElement;
		$serverversion = $xmlnode->getElementsByTagName("version")->item(0)->nodeValue;
		$vardec = $xmlnode->getElementsByTagName("des")->item(0)->nodeValue;
		unset($xmlnode);
	    unset($doc);
    }
    if(isN($vardec)){ $vardec = "获取更新信息失败，请访问官方网站获取信息" ; }
	if (version != $serverversion){
		echo $vardec;
	}
	else{
		echo "False";
	}
}

function getcheckversion()
{
	global $verstr;
	if(strpos($verstr,"</maccms>")>0){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> loadxml($verstr);
		$xmlnode = $doc -> documentElement;
		$serverversion = $xmlnode->getElementsByTagName("version")->item(0)->nodeValue;
		$vardec = $xmlnode->getElementsByTagName("des")->item(0)->nodeValue;
		unset($xmlnode);
	    unset($doc);
    }
    else{
    	$serverversion = version;
    }
    return (version != $serverversion);
}

function showfile()
{
	global $adpath,$updateserver,$verstr,$updatelog;
?>
<link rel="stylesheet" type="text/css" href="../images/manage.css" />
	<form action="?action=showfile" method="post" name="updateform">
	<table class="tb">
	<tr>
	<td align="center">升级文件</td>
	<td width="15%" align="center">更新时间</td>
	<td width="40%" align="center">描述信息</td>
	<td width="10%" align="center">更新状态</td>
	</tr>
<?php
	$files = be("arr","f_id");
	$logStr = file_get_contents($updatelog);
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($updatelog);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName("file");
	
	if (!isN($files)){
		$filesarr = explode(",",$files);
		$filescount= count($filesarr);
		
		for ($i=0;$i<$filescount;$i++){
			$fileTemp=""; $filearr="";$filesrc="";$filetime="";$savefilepath="";$fileStr="";
			
			$fileTemp = trim($filesarr[$i]);
			$filearr = explode("||||",$fileTemp);
			$filesrc = $filearr[0];
			$filetime = $filearr[1];
			
			if (strpos($filesrc,".txt")>0){
				$savefilepath = replaceStr($filesrc,"txt","php");
			}
			else{
				$savefilepath = $filesrc;
			}
			
			$savefilepath= replaceStr($savefilepath,chr(10),"");
			$savefilepath= replaceStr($savefilepath,chr(13),"");
			$savefilepath = "../".$savefilepath;
			$savefilepath = replaceStr($savefilepath,"admin/",$adpath);
			$fileStr = getPage($updateserver.$filesrc,"utf-8");
			
			if ($fileStr != ""){
				fwrite(fopen( replaceStr($savefilepath,version,"") ,"wb"),$fileStr);
				
				if (strpos($logStr,"<file src=\"".$filesrc."\"") > 0){
					foreach($nodes as $node){
						if ($filesrc == $node->attributes->item(0)->nodeValue){
							$node->attributes->item(1)->nodeValue = $filetime;
							$doc -> save($updatelog);
						}
					}
				}
				else{
					$nodenew = $doc -> createElement("file");
					$nodesrc1 =  $doc -> createAttribute("src");
					$nodesrc2 =  $doc -> createTextNode($filesrc);
					$nodesrc1 -> appendChild($nodesrc2);
					$nodetime1 =  $doc -> createAttribute("time");
					$nodetime2 =  $doc -> createTextNode($filetime);
					$nodetime1 -> appendChild($nodetime2);
					$nodenew -> appendChild($nodesrc1);
					$nodenew -> appendChild($nodetime1);
					$doc->getElementsByTagName("updatefiles")-> item(0)  -> appendChild($nodenew);
					$doc -> save($updatelog);
					unset($nodenew);
				}
			?>
		<tr>
		<td><?php echo $filesrc?></td>
		<td align="center"><?php echo $filetime?></td>
		<td>更新成功</td>
		</tr>
		<?php
			}
	    }
	}
	?>
	</table>
	</form>
	<?php
	unset($nodes);
	unset($xmlnode);
    unset($doc);
	showmsg ("更新完毕!","?action=showfilelist");
}

function showfilelist()
{
	global $verstr;	
?>
<form action="?action=showfile" method="post" name="updateform">
	<table class="tb" >
	<tr>
	<td width="4%">&nbsp;</td>
	<td width="15%">升级文件</td>
	<td width="15%">更新时间</td>
	<td width="40%">描述信息</td>
	<td width="10%">更新状态</td>
	</tr>
	<?php
	if (getcheckversion()){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> loadxml($verstr);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("file");
		
		foreach($nodes as $node){
			$filedes = $node->attributes->item(0)->nodeValue;
			$filetime = $node->attributes->item(1)->nodeValue;
			$filesrc = $node->attributes->item(2)->nodeValue;
		?>
		<tr>
		<td><input type="checkbox" value="<?php echo $filesrc?>||||<?php echo $filetime?>" name="f_id[]" id="f_id"/></td>
		<td><?php echo replaceStr($filesrc,version,"")?></td>
		<td><?php echo $filetime?></td>
		<td><?php echo $filedes?></td>
		<td><?php echo getFileIsUpdate($filesrc,$filetime)?></td>
		</tr>
	<?php
		}
	?>
	<tr><td colspan="5"><label>全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'f_id[]')" /></label>
	<input class="inputbut" type="submit" value="批量升级" name="Submit">升级前最好备份数据，以免升级失败造成不必要的麻烦！</td></tr>
	<?php
	}
	else{
		echo "<tr><td colspan=\"5\" >已经是最新版本，无需升级，请随时关注 www.maccms.com 官方信息！</td></tr>";
	}
	unset($nodes);
	unset($xmlnode);
    unset($doc);
	?>
	</table></form></body></html>
<?php
}
function getFileIsUpdate($filesrc,$filetime)
{
	global $updatelog;
	$logStr = file_get_contents($updatelog);
	$strTemp = "<file src=\"".$filesrc."\" time=\"".$filetime."\"/>";
	if (strpos($logStr,$strTemp) > 0){
		return "已更新";
	}
	else{
		return "<font color=red>未更新</font>";
	}
}
?>