<?php

//$content = getPages("http://localhost:8080/ptv/pplay/82236/1.html", "utf-8");
//
//writetofile("d:\\log.txt",$content);



function writetofile($file_name,$text) {
     $date_time = date("Y-m-d H:i:s");
     $text = "$date_time: ".$text;
	 $date = date("Y-m-d");
	 $fileArray = explode(".", $file_name);
	 if(count($fileArray)==2){
	 	$file_name =$fileArray[0].'_'.$date.'.'.$fileArray[1];
	 } 
	 $file_name = dirname(__FILE__).'/../log/'.$file_name;
	// var_dump($file_name);
	if (!file_exists($file_name)) {
      touch($file_name);
      chmod($file_name,"744");
    }

   $fd = @fopen($file_name, "a");
   @fwrite($fd, $text."\r\n");
   @fclose($fd);

}

function writetofileNoAppend($file_name,$text) {	
	if (!file_exists($file_name)) {
      touch($file_name);
      chmod($file_name,"744");
    }

   $fd = @fopen($file_name, "a");
   @fwrite($fd, $text."\r\n");
   @fclose($fd);

}

function newfile($file_name,$text) {	
   if (!file_exists($file_name)) {
      touch($file_name);
      chmod($file_name,"744");
   }

   $fd = @fopen($file_name, "w");
   @fwrite($fd, $text."\r\n");
   @fclose($fd);

}

function redirect($url)
{
	header("Location:$url");
	exit;
}

function head()
{
	return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
}

function alert($str)
{
	echo '<script type="text/javascript">alert("' .$str. '\t\t");history.go(-1);</script>';
	exit;
}

function alertUrl($str,$url)
{
	echo '<script type="text/javascript">alert("' .$str. '\t\t");location.href="' .$url .'";</script>';
	exit;
}


function confirmMsg($msg,$url1,$url2)
{
	echo '<script>if(confirm("' .$msg. '")){location.href="' .$url1. '"}else{location.href="' .$url2. '"}</script>';
	exit; 
}

function showMsg($msg,$url)
{
    if($url == ""){ $url = "history.go(-1)"; 	} else{ $url = "location='" .$url. "'"; }
    echo '<style>body{text-align:center}</style><script>function JumpUrl(){' .$url. ';}document.write("<div style=\"background-color:white;border:1px solid #1C93E5;margin:0 auto;width:400px;text-align:left;\"><div style=\"padding:3px 3px;color:white;font-weight:700;line-height:21px;height:25px;font-size:12px;border-bottom:1px solid #1C93E5; text-indent:3px; background-color:#1C93E5;text-align:center\">系统提示信息</div><div style=\"font-size:12px;padding:40px 8px 50px;line-height:25px;text-align:center\">' .$msg. '稍后自动返回...</div></div>");setTimeout("JumpUrl()",1500);</script>';
    exit;
}

function errMsg($e,$d)
{
    echo '<style>body{text-align:center}</style><div style="background-color:white;border:1px solid #1C93E5;margin:0 auto;width:400px;text-align:left;"><div style="padding:3px 3px;color:white;font-weight:700;line-height:21px;height:25px;font-size:12px;border-bottom:1px solid #1C93E5; text-indent:3px; background-color:#1C93E5;text-align:center">【' .$e. '】</div><div style="font-size:12px;padding:40px 8px 50px;line-height:25px;text-align:center">' .$d. '</div></div>';
    exit;
}

function headAdmin($title)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title><?php echo $title?> - joyplus</title>
<link rel="stylesheet" type="text/css" href="../images/admin.css" />
<link rel="stylesheet" type="text/css" href="../images/default/easyui.css" />
<link rel="stylesheet" type="text/css" href="../images/icon.css" />
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/jquery.easyui.min.js"></script>
<script type="text/javascript" src="../js/adm/admin.js"></script>
</head>
<body>
<?php
}

function initObj()
{
	date_default_timezone_set('Etc/GMT-8');
	ini_set("display_errors","On");
	ini_set('max_execution_time', '0');
	error_reporting(7);
	set_error_handler("my_error_handler");
	define("appTime",execTime());	
	$rpath = ereg_replace("[/\\]{1,}",'/',dirname(__FILE__));
	$rpath = ereg_replace("[/\\]{1,}",'/',substr($rpath,0,-3));
	define("root",$rpath);
}

function dispseObj()
{
	global $db,$template,$mac;
	unset($db);
	unset($template);
	unset($mac);
	$mac=null;
	$db=null;
	$template=null;
}

function my_error_handler($errno, $errmsg, $filename, $linenum, $vars) 
{
	$the_time = date("Y-m-d H:i:s (T)");
	$errno = $errno & error_reporting();
    if($errno === 0) return;
	$filename=str_replace(getcwd(),"",$filename);
    $errortype = array (
    E_ERROR           => "Error",
    E_WARNING         => "Warning",
    E_PARSE           => "Parsing Error",
    E_NOTICE          => "Notice",
    E_CORE_ERROR      => "Core Error",
    E_CORE_WARNING    => "Core Warning",
    E_COMPILE_ERROR   => "Compile Error",
    E_COMPILE_WARNING => "Compile Warning",
    E_USER_ERROR      => "User Error",
    E_USER_WARNING    => "User Warning",
    E_USER_NOTICE     => "User Notice",
    E_STRICT          => "Runtime Notice"
	);
	$err = "系统提示:";
    $err .= "<br>\nMsg: " . $errmsg ;
    $err .= "<br>\nFile: " . $filename;
    $err .= "<br>\nLine: " . $linenum ;
    die($err);
}

function chkArray($arr1,$arr2)
{
	$res = true;
	if(is_array($arr1) && is_array($arr2)){
		if(count($arr1) != count($arr2)){
			$res = false;
		}
	}
	else{
		$res = false;
	}
	return $res;
}

function isN($str)
{
	if (is_null($str) || $str==''){ return true; }else{ return false;}
}

function isNum($str)
{
	if(!isN($str)){
		if(is_numeric($str)){return true;}else{ return false;}
  	}
}

function indexOf($str,$strfind)
{
	if(isN($str) || isN($strfind)){ return false; }
	if(strpos(",".$str,$strfind)>0){ return true; } else{ return false; }
}

function isIp($ip)
{
	$e="([0-9]|1[0-9]{2}|[1-9][0-9]|2[0-4][0-9]|25[0-5])";  
	if(ereg("^$e\.$e\.$e\.$e$",$ip)){ return true; } else{ return false; }
}

function isObjInstalled($objstr)
{
	return true;
}

function getRndNum($length)
{
	$pattern = "1234567890";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,10)};
	}
	return $res;
}

function rndNum($minnum,$maxuum)
{
	return rand($minnum,$maxuum);
}

function getRndStr($length)
{
	$pattern = "1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,36)};
	}
	return $res;
}

function be($mode,$key)
{ 
	ini_set("magic_quotes_runtime", 0);
	$magicq= get_magic_quotes_gpc();
	switch ($mode)
	{
		case 'post':
			$res=isset($_POST[$key]) ? $magicq?$_POST[$key]:addslashes($_POST[$key]) : '';
			break;
		case 'get':
			$res=isset($_GET[$key]) ? $magicq?$_GET[$key]:addslashes($_GET[$key]) : '';
			break;
		case 'arr':
			$arr =isset($_POST[$key]) ? $_POST[$key] : '';
			if($arr==""){
				$value="0";
			}
			else{
				for($i=0;$i<count($arr);$i++){
					$res=implode(',',$arr);
				} 
			}
			break;
		default:
			$res=isset($_REQUEST[$key]) ? $magicq?$_REQUEST[$key]:addslashes($_REQUEST[$key]) : '';
			break;
	}
	return $res;
}

function chkSql($str,$flag)
{
	$checkStr="<|>|%|%27|'|''|;|*|and|exec|dbcc|alter|drop|insert|select|update|delete|count|master|truncate|char|declare|where|set|declare|mid|chr";
	if (isN($str)){ return ""; }
	$arr=explode("|",$checkStr);
	for ($i=0;$i<count($arr);$i++){
		if (strpos(strtolower($str),$arr[$i]) >0){
			if ($flag==false){
				switch ($arr[$i]){
					case "<":$re="&lt;";break;
					case ">":$re="&gt;";break;
					case "'":
					case "\"":$re="&quot;";break;
					case ";":$re="；";break;
					default:$re="";break;
				}
				$str=str_replace($arr[$i],$re,$str);
			}
			else{
				errMsg ("系统提示","数据中包含非法字符");
			}
		}
	}
	return $str;
}

function getIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
		$cip = '';
	}
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = isset($cips[0]) ? $cips[0] : 'unknown';
	unset($cips);
	return $cip;
}

function getReferer()
{
	return $_SERVER["HTTP_REFERER"];
}

function getUrl()
{
  if(!empty($_SERVER["REQUEST_URI"])){
		$nowurl = $_SERVER["REQUEST_URI"];
	}
	else{
		$nowurl = $_SERVER["PHP_SELF"];
	}
	return $nowurl;
}

function delCookie($key)
{
	setcookie($key,"",time()-3600,"/");
}

function getCookie($key)
{
	if(!isset($_COOKIE[$key])){
		return '';
	}
	else{
		return $_COOKIE[$key];
	}
}

function sCookie($key,$val)
{
	setcookie($key,$val,0,"/");
}

function execTime()
{
	$time = explode(" ", microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function getTimeSpan($sessionName)
{
	$lastTime = $_SESSION[$sessionName];
	if (isN($lastTime)){
		$lastTime= "1228348800";
	}
	$res = time() - intval($lastTime);
	return $res;
}

function getRunTime()
{
	global $db;
	$t2= execTime() - appTime;
	return "页面执行时间: ".round($t2,4)."秒&nbsp;" . $db->iqueryCount . "次数据查询";
}

function repSpecialChar($str)
{
	$str = str_replace("/","_",$str);
	$str = str_replace("\\","_",$str);
	$str = str_replace("[","",$str);
	$str = str_replace("]","",$str);
	$str = str_replace(" ","",$str);
	return $str;
}

function getTextt($num,$sname)
{
	if (isNum($num)){
		if (!isN($sname)){
			$res= substring($sname,$num);
		}
		else{
			$res="";
		}
	}
	else{
		$res=$sname;
	}
	return $res;
}

function getDatet($iformat,$itime)
{
	$iformat = str_replace("yyyy","Y",$iformat);
	$iformat = str_replace("yy","Y",$iformat);
	$iformat = str_replace("hh","H",$iformat);
	$iformat = str_replace("mm","m",$iformat);
	$iformat = str_replace("dd","d",$iformat);
	
	if (isN($iformat)) { $iformat = "Y-m-d";}
	$res = date($iformat,strtotime($itime));
	return $res;
}

function buildregx($regstr,$regopt)
{
	return '/'.str_replace('/','\/',$regstr).'/'.$regopt;
}

function replaceStr($text,$search,$replace)
{
	if(isN($text)){ return "" ;}
	$res=str_replace($search,$replace,$text);
	return $res;
}

function replaceLine($text)
{
	if(isN($text)){ return "" ;}
	$text=str_replace("\n","",$text);
    $text=str_replace("\r","",$text);
    $text=str_replace("\r\n","",$text);
	return $text;
}

function regReplace($str,$rule,$value)
{
	$rule = buildregx($rule,"is");
	if (!isN($str)){
		$res = preg_replace($rule,$value,$str);
	}
	return $res;
}

function getSubStrByFromAndEnd($str,$startStr,$endStr,$operType)
{
	switch ($operType)
	{
		case "start":
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strlen($str)+1;
			break;
		case "end":
			$location1=1;
			$location2=strpos($str,$endStr,$location1);
			break;
		default:
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strpos($str,$endStr,$location1);
			break;
	}
	$location3 = $location2-$location1;
	$res= substring1($str,$location3,$location1);
	return $res;
}

function regMatch($str, $rule)
{
	$rule = buildregx($rule,"is");
	preg_match_all($rule,$str,$MatchesChild);
	$matchfieldarr=$MatchesChild[1];
	$matchfieldstrarr=$MatchesChild[0];
	$matchfieldvalue="";
	foreach($matchfieldarr as $f=>$matchfieldstr)
	{
		$matchfieldvalue=$matchfieldstrarr[$f];
		$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
		break;
	}
	unset($MatchesChild);
	return $matchfieldstr;
}

function utf2ucs($str)
{
	$n=strlen($str);
	if ($n=3) {
		$highCode = ord($str[0]);
		$midCode = ord($str[1]);
		$lowCode = ord($str[2]);
		$a   = 0x1F & $highCode;
		$b   = 0x7F & $midCode;
		$c   = 0x7F & $lowCode;
		$ucsCode = (64*$a + $b)*64 + $c;
	}
	elseif ($n==2) {
		$highCode = ord($str[0]);
		$lowCode = ord($str[1]);
		$a   = 0x3F & $highCode;
		$b   = 0x7F & $lowCode;
		$ucsCode = 64*$a + $b; 
	}
	elseif($n==1) {
		$ucscode = ord($str);
	}
	return dechex($ucsCode);
}

function escape($str)
{
	preg_match_all("/[\xC0-\xE0].|[\xE0-\xF0]..|[\x01-\x7f]+/",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
	$ord = ord($v[0]);
	    if( $ord<=0x7F)
	      $ar[$k] = rawurlencode($v);
	    elseif ($ord<0xE0) {
	      $ar[$k] = "%u".utf2ucs($v);
	    }
		elseif ($ord<0xF0) {
	      $ar[$k] = "%u".utf2ucs($v);
		}
	}
	return join("",$ar);
}
  function utf16beToUTF8(&$str)
	{
		$uni = unpack('n*',$str);
		return unicodeToUTF8($uni);
	}
/**
	* This function converts a Unicode array back to its UTF-8 representation
	* @param string $str string to convert
	* @return string
	* @author Scott Michael Reynen <scott@randomchaos.com>
	* @link   http://www.randomchaos.com/document.php?source=php_and_unicode
	* @see	utf8ToUnicode()
	*/
	  function unicodeToUTF8( &$str )
	{
		$utf8 = '';
		foreach( $str as $unicode )
		{
			if ( $unicode < 128 )
			{
				$utf8.= chr( $unicode );
			}
			elseif ( $unicode < 2048 )
			{
				$utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
				$utf8.= chr( 128 + ( $unicode % 64 ) );
			}
			else
			{
				$utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
				$utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
				$utf8.= chr( 128 + ( $unicode % 64 ) );
			}
		}
		return $utf8;
	}
 function format($str){
	$delim = substr($str, 0, 1);
					$chrs = substr($str, 1, -1);
					$utf8 = '';
					$strlen_chrs = strlen($chrs);

					for ($c = 0; $c < $strlen_chrs; ++$c) {

						$substr_chrs_c_2 = substr($chrs, $c, 2);
						$ord_chrs_c = ord($chrs{$c});

						switch (true) {
							case $substr_chrs_c_2 == '\b':
								$utf8 .= chr(0x08);
								++$c;
								break;
							case $substr_chrs_c_2 == '\t':
								$utf8 .= chr(0x09);
								++$c;
								break;
							case $substr_chrs_c_2 == '\n':
								$utf8 .= chr(0x0A);
								++$c;
								break;
							case $substr_chrs_c_2 == '\f':
								$utf8 .= chr(0x0C);
								++$c;
								break;
							case $substr_chrs_c_2 == '\r':
								$utf8 .= chr(0x0D);
								++$c;
								break;

							case $substr_chrs_c_2 == '\\"':
							case $substr_chrs_c_2 == '\\\'':
							case $substr_chrs_c_2 == '\\\\':
							case $substr_chrs_c_2 == '\\/':
								if (($delim == '"' && $substr_chrs_c_2 != '\\\'') ||
								   ($delim == "'" && $substr_chrs_c_2 != '\\"')) {
									$utf8 .= $chrs{++$c};
								}
								break;

							case preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6)):
								// single, escaped unicode character
								$utf16 = chr(hexdec(substr($chrs, ($c+2), 2)))
									   . chr(hexdec(substr($chrs, ($c+4), 2)));
								$utf8 .= utf16beToUTF8($utf16);
								$c+=5;
								break;

							case ($ord_chrs_c >= 0x20) && ($ord_chrs_c <= 0x7F):
								$utf8 .= $chrs{$c};
								break;

							case ($ord_chrs_c & 0xE0) == 0xC0:
								// characters U-00000080 - U-000007FF, mask 110XXXXX
								//see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 2);
								++$c;
								break;

							case ($ord_chrs_c & 0xF0) == 0xE0:
								// characters U-00000800 - U-0000FFFF, mask 1110XXXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 3);
								$c += 2;
								break;

							case ($ord_chrs_c & 0xF8) == 0xF0:
								// characters U-00010000 - U-001FFFFF, mask 11110XXX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 4);
								$c += 3;
								break;

							case ($ord_chrs_c & 0xFC) == 0xF8:
								// characters U-00200000 - U-03FFFFFF, mask 111110XX
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 5);
								$c += 4;
								break;

							case ($ord_chrs_c & 0xFE) == 0xFC:
								// characters U-04000000 - U-7FFFFFFF, mask 1111110X
								// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
								$utf8 .= substr($chrs, $c, 6);
								$c += 5;
								break;

						}

					}

					return $utf8;
}
function unescape($str)
{
	$str = rawurldecode($str);
	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
		if(substr($v,0,2) == "%u"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,-4)));
		}
		else if(substr($v,0,3) == "&#x"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,3,-1)));
		}
		else if(substr($v,0,2) == "&#") {
			$ar[$k] = iconv("UCS-2","GB2312",pack("n",substr($v,2,-1)));
		}
	}
	unset($r);
	return join("",$ar);
}

function htmlEncode($str)
{
	if (!isN($str)){
		$str = replaceStr($str, chr(38), "&#38;");
		$str = replaceStr($str, ">", "&gt;");
		$str = replaceStr($str, "<", "&lt;");
		$str = replaceStr($str, chr(39), "&#39;");
		$str = replaceStr($str, chr(32), "&nbsp;");
		$str = replaceStr($str, chr(34), "&quot;");
		$str = replaceStr($str, chr(9), "&nbsp;&nbsp;&nbsp;&nbsp;");
		$str = replaceStr($str, chr(13), "");
		$str = replaceStr($str, chr(10), "<br />");
	}
	return $str;
}

function htmlDecode($str)
{
	if (!isN($str)){
		$str = replaceStr($str, "<br/>", chr(13)&chr(10));
		$str = replaceStr($str, "<br>", chr(13)&chr(10));
		$str = replaceStr($str, "<br />", chr(13)&chr(10));
		$str = replaceStr($str, "&nbsp;&nbsp;&nbsp;&nbsp;", Chr(9));
		$str = replaceStr($str, "&amp;", chr(38));
		$str = replaceStr($str, "&#39;", chr(39));
		$str = replaceStr($str, "&apos;", chr(39));
		$str = replaceStr($str, "&nbsp;", chr(32));
		$str = replaceStr($str, "&quot;", chr(34));
		$str = replaceStr($str, "&gt;", ">");
		$str = replaceStr($str, "&lt;", "<");
		$str = replaceStr($str, "&#38;", chr(38));
	}
	return $str;
}

function htmlFilter($str)
{
	$str = strip_tags($str);
	$str = str_replace("\"","",$str);
	$str = str_replace("'","",$str);
	return $str;
}

function htmltojs($content)
{
	$arrLines = explode(chr(10),$content);
	for ($i=0 ;$i<count($arrLines);$i++){
		$sLine = replaceStr( $arrLines[$i] , "\\" , "\\\\");
		$sLine = replaceStr( $sLine , "/" , "\/");
		$sLine = replaceStr( $sLine , "'" , "\'");
		$sLine = replaceStr( $sLine , "\"\"" , "\"");
		$sLine = replaceStr( $sLine , chr(13) , "" );
		$strNew = $strNew . "document.writeln('". $sLine  . "');" . chr(10);
	}
	unset($arrLines);
	return $strNew;
}

function jstohtml($str)
{
	if (!isN($str)){
		$str = replaceStr( $str , "document.writeln('" , "");
		$str = replaceStr( $str , "\'" , "'");
		$str = replaceStr( $str , "\"" , "\"\"");
		$str = replaceStr( $str , "\\\\" , "\\");
		$str = replaceStr( $str , "\/" , "/");
		$str = replaceStr( $str , "');" , "");
	}
    return $str;
}

function jsEncode($str)
{
	if (!isN($str)){
		$str = replaceStr($str,chr(92),"\\");
		$str = replaceStr($str,chr(34),"\"");
		$str = replaceStr($str,chr(39),"\'");
		$str = replaceStr($str,chr(9),"\t");
		$str = replaceStr($str,chr(13),"\r");
		$str = replaceStr($str,chr(10),"\n");
		$str = replaceStr($str,chr(12),"\f");
		$str = replaceStr($str,chr(8),"\b");
	}
	return $str;
}

function badFilter($str)
{
	$arr=explode(",",app_filter);
	for ($i=0;$i<count($arr);$i++){
		$str= replaceStr($str,$arr[$i],"***");
	}
	unset($arr);
	return $str;
}

function asp2phpif($str)
{
	$str= str_replace("not","!",$str);
	$str= str_replace("==","=",$str);
	$str= str_replace("=","==",$str);
	$str= str_replace("<>","!=",$str);
	$str= str_replace("and","&&",$str);
	$str= str_replace("or","||",$str);
	$str= str_replace("mod","%",$str);
	return $str;
}

function bytesToBstr($Body,$CharSet)
{
	return "";
}

function substring1($str,$len, $start) {
     $tmpstr = "";
     $len = $start + $len;
     for($i = $start; $i < $len; $i++){
         if(ord(substr($str, $i, 1)) > 0xa0) {
             $tmpstr .= substr($str, $i, 2);
             $i++;
         } else
             $tmpstr .= substr($str, $i, 1);
     }
     return $tmpstr;
} 

function substring($str, $lenth, $start=0) 
{ 
	$len = strlen($str); 
	$r = array(); 
	$n = 0;
	$m = 0;
	
	for($i=0;$i<$len;$i++){ 
		$x = substr($str, $i, 1); 
		$a = base_convert(ord($x), 10, 2); 
		$a = substr( '00000000 '.$a, -8);
		
		if ($n < $start){ 
            if (substr($a, 0, 1) == 0) { 
            }
            else if (substr($a, 0, 3) == 110) { 
              $i += 1; 
            }
            else if (substr($a, 0, 4) == 1110) { 
              $i += 2; 
            } 
            $n++; 
		}
		else{ 
            if (substr($a, 0, 1) == 0) { 
             	$r[] = substr($str, $i, 1); 
            }else if (substr($a, 0, 3) == 110) { 
             	$r[] = substr($str, $i, 2); 
            	$i += 1; 
            }else if (substr($a, 0, 4) == 1110) { 
            	$r[] = substr($str, $i, 3); 
             	$i += 2; 
            }else{ 
             	$r[] = ' '; 
            } 
            if (++$m >= $lenth){ 
              break; 
            } 
        }
	}
	return  join('',$r);
}

function getFolderItem($tmppath){
	$fso=@opendir($tmppath);
    $attr=array();
    $i=0;
	while (($file=@readdir($fso))!==false){
		if($file!=".." && $file!="."){
			array_unshift($attr,$file);
			$i=$i+1;
		}
	}
	closedir($fso);
	unset($fso);
	return $attr;
}

function convert_encoding($str,$nfate,$ofate){
	if ($ofate=="UTF-8"){ return $str; }
	if ($ofate=="GB2312"){ $ofate="GBK"; }
	
	if(function_exists("mb_convert_encoding")){
		$str=mb_convert_encoding($str,$nfate,$ofate);
	}
	else{
		$ofate.="//IGNORE";
		$str=iconv(  $nfate , $ofate ,$str);
	}
	return $str;
}

function getPages($url, $charset){
	if(!empty($url)) {
//			if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
				$ch = curl_init();
				$timeout = 5;
				curl_setopt($ch, CURLOPT_URL, $url); //
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5');
	//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; )');
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$content = @curl_exec($ch);
				curl_close($ch);
				return $content;
//			}
	}
}


function getHrefFromLink($link){
//some time we can't get link like <li class="p_link"><a title="爱情公寓 第三季" href="
				if(isN($link)){
					return "";
				}
					try {
						$pos = strpos($link, "href=\"");
						  if ($pos !== false) {
							$link=substr($link, $pos+6);
						  }
						  $pos = strpos($link, "\"");
						  if ($pos !== false) {
							$link=substr($link, 0,$pos);
						  }	
						  
//						  $pos = strpos($link, "http://");
//						  if ($pos === false) {
//							$link="http://".$link;
//						  }	
						
					} catch (Exception $e) {
					}
					
					return $link;
}

function getHrefFromImg($link){
//some time we can't get link like <li class="p_link"><a title="爱情公寓 第三季" href="
				if(isN($link)){
					return "";
				}
					try {
						$pos = strpos($link, "src=\"");
						  if ($pos !== false) {
							$link=substr($link, $pos+5);
						  }
						  $pos = strpos($link, "\"");
						  if ($pos !== false) {
							$link=substr($link, 0,$pos);
						  }	
						  
//						  $pos = strpos($link, "http://");
//						  if ($pos === false) {
//							$link="http://".$link;
//						  }	
						
					} catch (Exception $e) {
					}
					
					return $link;
}
 function getLocation($url){
// 	var_dump($url);
 
//  $header = get_headers($url,1); 	
	try {
	  	if(isset($header) && is_array($header)) { 
	  		$location= $header['Location'];
	  		if(isset($location) && is_array($location)) {
	  			return $location[(count($location)-1)];
	  		}else if (isset($location) && !is_null($location)) {
	  			return $location;
	  		}else {
	  			return $url;
	  		}
	  	}
	  	return $url;
	}catch (Exception $e){
		return $url;
	}
//  	  var_dump($header);
//  	 writetofile("daa.txt", json_encode($header));
  	
//  		  var_dump($header);
 }
 
 
function getPage($url,$charset)
{   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
//			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			$content = @curl_exec($ch);	
//			var_dump($content);
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
//			if($httpCode == 302){
////				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
//				echo $httpCode.$url;
//			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
////			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
//		var_dump($content);
		$temp = getBody($content, "charset=", "\"");

		 if($temp ==='' || $temp ===false){
		 	
		 	$content = convert_encoding($content,"utf-8",$charset);
		 }else{
		$content = convert_encoding($content,"utf-8",$temp);
		 }
	}
	return ($content);
}


function ascii_decode($str){
	    preg_match_all( "/(d{2,5})/", $str,$a);
	    $a = $a[0];
	     foreach ($a as $dec)
	     {
	         if ($dec < 128)
	         {
	            $utf .= chr($dec);
	         }
	         else if ($dec < 2048)
	        {
	            $utf .= chr(192 + (($dec - ($dec % 64)) / 64));
	            $utf .= chr(128 + ($dec % 64));
	         }
	         else
	         {
	            $utf .= chr(224 + (($dec - ($dec % 4096)) / 4096));
	            $utf .= chr(128 + ((($dec % 4096) - ($dec % 64)) / 64));
	            $utf .= chr(128 + ($dec % 64));
	         }
	     }
	     return $utf;
	}

	
	
	
	function ascii_encode($c)
{
    $len = strlen($c);
    $a = 0;
     while ($a < $len)
     {
        $ud = 0;
         if (ord($c{$a}) >=0 && ord($c{$a})<=127)
         {
            $ud = ord($c{$a});
            $a += 1;
         }
         else if (ord($c{$a}) >=192 && ord($c{$a})<=223)
         {
            $ud = (ord($c{$a})-192)*64 + (ord($c{$a+1})-128);
            $a += 2;
         }
         else if (ord($c{$a}) >=224 && ord($c{$a})<=239)
         {
            $ud = (ord($c{$a})-224)*4096 + (ord($c{$a+1})-128)*64 + (ord($c{$a+2})-128);
            $a += 3;
         }
         else if (ord($c{$a}) >=240 && ord($c{$a})<=247)
         {
            $ud = (ord($c{$a})-240)*262144 + (ord($c{$a+1})-128)*4096 + (ord($c{$a+2})-128)*64 + (ord($c{$a+3})-128);
            $a += 4;
         }
         else if (ord($c{$a}) >=248 && ord($c{$a})<=251)
         {
            $ud = (ord($c{$a})-248)*16777216 + (ord($c{$a+1})-128)*262144 + (ord($c{$a+2})-128)*4096 + (ord($c{$a+3})-128)*64 + (ord($c{$a+4})-128);
            $a += 5;
         }
         else if (ord($c{$a}) >=252 && ord($c{$a})<=253)
         {
            $ud = (ord($c{$a})-252)*1073741824 + (ord($c{$a+1})-128)*16777216 + (ord($c{$a+2})-128)*262144 + (ord($c{$a+3})-128)*4096 + (ord($c{$a+4})-128)*64 + (ord($c{$a+5})-128);
            $a += 6;
         }
         else if (ord($c{$a}) >=254 && ord($c{$a})<=255)
         { //error
            $ud = false;
         }
        $scill .= "&#$ud;";
     }
     return $scill;
}
 


function getPageHeaderValue($url,$charset,$header)
{   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
//			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0');
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			//if(is_array($header) && count($header) >0){
			   curl_setopt ( $ch, CURLOPT_HTTPHEADER,array (
				'X-Requested-With: XMLHttpRequest',
  	 	        'Cookie: PHPSESSID=bu3m8315m60lpvtbc8csuaelg3'
		));
			//}
			$content = @curl_exec($ch);	
//			var_dump($content);
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
//			if($httpCode == 302){
////				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
//				echo $httpCode.$url;
//			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
////			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
//		var_dump($content);
		$temp = getBody($content, "charset=", "\"");

		 if($temp ==='' || $temp ===false){
		 	
		 	$content = convert_encoding($content,"utf-8",$charset);
		 }else{
		$content = convert_encoding($content,"utf-8",$temp);
		 }
	}
	return ($content);
}

function getPageHeader($url,$charset)
{   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
//			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0');
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			$content = @curl_exec($ch);	
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			//@curl_
//			if($httpCode == 302){
////				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
//				echo $httpCode.$url;
//			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
////			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
		$content = convert_encoding($content,"utf-8",$charset);
	}
	return ($content);
}


function getPageHeaders($url,$charset,$cookie)
{    var_dump($cookie);
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
//			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Cookie: ' . $cookie
		) );
			$content = @curl_exec($ch);	
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			//@curl_
//			if($httpCode == 302){
////				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
//				echo $httpCode.$url;
//			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
////			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
		$content = convert_encoding($content,"utf-8",$charset);
	}
	return ($content);
}
function getPageSSL($url,$charset){
	$c = curl_init ();
		curl_setopt ( $c, CURLOPT_TIMEOUT, 10 );
		curl_setopt ( $c, CURLOPT_USERAGENT, 'parseRestClient/1.0' );
		curl_setopt ( $c, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $c, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $c, CURLOPT_RETURNTRANSFER, true );
		
		curl_setopt ( $c, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt ( $c, CURLOPT_URL, $url );
		$response = curl_exec ( $c );
		return $response;
}

function getPageWindow($url,$charset)
{   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
//			curl_setopt($ch, CURLOPT_URL, $url);
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3');
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:16.0) Gecko/20100101 Firefox/16.0');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			$content = @curl_exec($ch);
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			if($httpCode == 302){
//				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
				echo $httpCode.$url;
			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
//			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
	   $temp = getBody($content, "charset=", "\"");

		 if($temp ==='' || $temp ===false){
		 	
		 	$content = convert_encoding($content,"utf-8",$charset);
		 }else{
		$content = convert_encoding($content,"utf-8",$temp);
		 }
	}
	return ($content);
}

function getFormatPage($url,$charset)
{   
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') &&  function_exists('curl_exec')  ){
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $url); //
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5');
//			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; )');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
			$content = @curl_exec($ch);	
			$httpCode = @curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
			if($httpCode == 302){
//				echo @curl_getinfo($ch,CURLOPT_AUTOREFERER);
				echo $httpCode;
			}//GET /ajax/getFocusVideo.php?jsonp=LETV.Runtime.getPTVData&pid=52240&p=1&top=50&max=10 HTTP/1.1
//			echo $url;		
			curl_close($ch);
		}
		else if( function_exists('file_get_contents') && ini_get('allow_url_fopen')==1){
//			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集，请检查php配置中allow_url_fopen是否为On；');
		}
		$content = convert_encoding($content,"utf-8",$charset);
	}
	return format($content);
}

function getBody($strBody,$strStart,$strEnd)
{
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
    $strStart=stripslashes($strStart);
   	$strEnd=stripslashes($strEnd);
	//var_dump($strStart);var_dump(strpos($strBody,$strStart));
	if(strpos($strBody,$strStart)!=""){
		$str = substr($strBody,strpos($strBody,$strStart)+strlen($strStart));
		$str = substr($str,0,strpos($str,$strEnd));
	}
	else{
		$str=false;
	}	
	return $str;
}

function getBodys($strBody,$strStart)
{
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	
	
    $strStart=stripslashes($strStart);
   	
	
	if(strpos($strBody,$strStart)!=""){
		$str = substr($strBody,strpos($strBody,$strStart)+strlen($strStart));
//		$str = substr($str,0,strpos($str,$strEnd));
	}
	else{
		$str=false;
	}	
	return $str;
}

function getArray($strBody,$strStart,$strEnd)
{
	$strStart=stripslashes($strStart);
    $strEnd=stripslashes($strEnd);
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
	$strStart = replaceStr($strStart,"(","\(");
	$strStart = replaceStr($strStart,")","\)");
	$strStart = replaceStr($strStart,"'","\'");
	$strStart = replaceStr($strStart,"?","\?");
	$strEnd = replaceStr($strEnd,"(","\(");
	$strEnd = replaceStr($strEnd,")","\)");
	$strEnd = replaceStr($strEnd,"'","\'");
	$strEnd = replaceStr($strEnd,"?","\?");
	
	$labelRule = $strStart."(.*?)".$strEnd;
//	echo $labelRule;
	//'/'.str_replace('/','\/',$regstr).'/'.$regopt
	$labelRule = buildregx($labelRule,"is"); 
	preg_match_all($labelRule,$strBody,$tmparr);
	$tmparrlen=count($tmparr[1]);
	
	$rc=false;
	for($i=0;$i<$tmparrlen;$i++)
	{
		if($rc){ $str .= "{Array}"; }
		$str .= $tmparr[1][$i];
		$rc=true;
	}
	
	if (isN($str)) { return false ;}
	$str=replaceStr($str,$strStart,"");
	$str=replaceStr($str,$strEnd,"");
	$str=replaceStr($str,"\"\"","");
	$str=replaceStr($str,"'","");
	$str=replaceStr($str," ","");
	if (isN($str)) { return false ;}
	return $str;
}

$span="";
function makeSelect($tabName,$colID,$colName,$colSort,$byurl,$separateStr,$id)
{
	global $db,$span;
	if (!isN($colSort)){ $strOrder=" order by ".$colSort." asc";} 
	if (isN($id)){ $id=0; }
	$sql="select ".$colID.",".$colName." from ".$tabName.$strOrder;
	$rs=$db->query($sql);
	while($row = $db ->fetch_array($rs))
	{
		if (intval($id)==$row[$colID]){
			$strSelected=" selected";
		}
		else{
			$strSelected="";
		} 
		if (isN($byurl)){
			$strValue=$row[$colID];
		}
		else{
			$strValue=$byurl;
			if(strpos($byurl,"?")>0){
				$strValue.="&";
			}
			else{
				$strValue.="?";
			}
			$strValue.= $tabName."=".$row[$colID];
		}
		$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$row[$colName]."</option>";
	} 
	if (!isN($span)){
		$span=substr($span,0,strlen($span)-strlen($separateStr));
	}
	return $str;
}

function makeSelectWhere($tabName,$colID,$colName,$colSort,$byurl,$separateStr,$id,$where)
{
	global $db,$span;
	if (!isN($colSort)){ $strOrder=" order by ".$colSort." asc";} 
	if (isN($id)){ $id=0; }
	$sql="select ".$colID.",".$colName." from ".$tabName." ".$where." ".$strOrder;
//	var_dump($sql);
	$rs=$db->query($sql);
	while($row = $db ->fetch_array($rs))
	{
		if (intval($id)==$row[$colID]){
			$strSelected=" selected";
		}
		else{
			$strSelected="";
		} 
		if (isN($byurl)){
			$strValue=$row[$colID];
		}
		else{
			$strValue=$byurl;
			if(strpos($byurl,"?")>0){
				$strValue.="&";
			}
			else{
				$strValue.="?";
			}
			$strValue.= $tabName."=".$row[$colID];
		}
		$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$row[$colName]."</option>";
	} 
	if (!isN($span)){
		$span=substr($span,0,strlen($span)-strlen($separateStr));
	}
	return $str;
}

function makeSelectAll($tabName,$colID,$colName,$colPID,$colSort,$pid,$byurl,$separateStr,$id)
{
	global $db,$span;
	if (isN($id)){ $id=0; } 
	$sql="select ".$colID.",".$colName." from ".$tabName." where t_hide=0 and ".$colPID." = ".$pid." order by ".$colSort." Asc";
	$rs=$db->query($sql);
 	while ($row = $db ->fetch_array($rs))
	{
		if ($pid!=0){
			$span .=$separateStr;
		} 
		if (intval($id)==$row[$colID]){
			$strSelected=" selected";
		}
		else{
			$strSelected="";
		} 
		if (isN($byurl)){
			$strValue=$row[$colID];
		}
		else{
			$strValue=$byurl."?".$tabName."=".$row[$colID];
		} 
		$str=$str."<option value='".$strValue."' ".$strSelected.">".$span."&nbsp;|—".$row[$colName]."</option>";
		$str=$str.makeSelectAll($tabName,$colID,$colName,$colPID,$colSort,$row[$colID],$byurl,$separateStr,$id);
	} 
	if (!isN($span)){
		$span=substr($span,0,strlen($span)-strlen($separateStr));
	}
	return $str;
}

function makeSelectPlayer($strfrom)
{
	$xmlpath = root ."inc/vodplay.xml";
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName("player");
	
	foreach($nodes as $node){
		$status = $node->attributes->item(0)->nodeValue;
		if ($status == "1"){
			$from = $node->attributes->item(2)->nodeValue;
			$show = $node->attributes->item(3)->nodeValue;
			if ($strfrom == $from) { $strSelected=" selected"; } else{ $strSelected=""; }
			$str = $str. "<option value='" .$from. "' " .$strSelected. ">" .$show. "</option>";
		}
	}
	unset($nodes);
	unset($xmlnode);
	unset($doc);
	return $str;
}

function makeSelectDown($strfrom)
{
	$xmlpath = root ."inc/voddown.xml";
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName("down");
	
	foreach($nodes as $node){
		$status = $node->attributes->item(0)->nodeValue;
		if ($status == "1"){
			$from = $node->attributes->item(2)->nodeValue;
			$show = $node->attributes->item(3)->nodeValue;
			if ($strfrom == $from) { $strSelected=" selected"; } else{ $strSelected=""; }
			$str = $str. "<option value='" .$from. "' " .$strSelected. ">" .$show. "</option>";
		}
	}
	unset($nodes);
	unset($xmlnode);
	unset($doc);
	return $str;
}

function makeSelectServer($strserver)
{
	$xmlpath = root ."inc/vodserver.xml";
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName("server");
	
	foreach($nodes as $node){
		$status = $node->attributes->item(0)->nodeValue;
		if ($status == "1"){
			$from = $node->attributes->item(2)->nodeValue;
			$show = $node->attributes->item(3)->nodeValue;
			if ($strserver == $from) { $strSelected=" selected"; } else{ $strSelected=""; }
			$str = $str. "<option value='" .$from. "' " .$strSelected. ">" .$show. "</option>";
		}
	}
	unset($nodes);
	unset($xmlnode);
	unset($doc);
	return $str;
}

function makeSelectAreaLang($flag,$val)
{
	global $cache;
	if($flag=="area"){
		$arr = $cache[4];
	}
	else{
		$arr = $cache[5];
	}
	$i=0;
	foreach($arr as $v){
		if ($val == $v){ $i++; $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str . "<option value='" .$v. "' " .$strSelected. ">" .$v. "</option>";
	}
	if($val!="" && $i==0){
		$str = $str . "<option value='" .$val. "' selected>" .$val. "</option>";
	}
	return $str;
}

function makeSelectTV_live($flag,$val)
{
   $filename='';
	if($flag=="area"){
		$filename='program_area.txt';
	}
	else if($flag=="country"){
		$filename='program_country.txt';
	}else if($flag=="prod_type"){
		$filename='program_type.txt';
	}else if($flag=="tv_playfrom"){
		$filename='tv_playfrom.txt';
	}
	
	
	//直播国家
	try{
		$str = file_get_contents(root."inc/".$filename);
		$str = replaceStr($str,chr(10),"");
		$cacheprogram_country = explode(chr(13),$str);
		//setGlobalCache("cache_vodlang",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		$cacheprogram_country=array();
	}
	
	$arr = $cacheprogram_country;
	
	$i=0;
	foreach($arr as $v){
		if ($val == $v){ $i++; $strSelected=" selected"; } else{ $strSelected=""; }
		$str = $str . "<option value='" .$v. "' " .$strSelected. ">" .$v. "</option>";
	}
	if($val!="" && $i==0){
		$str = $str . "<option value='" .$val. "' selected>" .$val. "</option>";
	}
	return $str;
}

function pagelist_manage($pagecount,$page,$recordcount,$pagesize,$url)
{
	if( $recordcount ==0 ){
		return "";	
	}
	$str = "{<<} {循环} {>>} {跳转} 共{总条数}数据&nbsp;每页{每页数量}条&nbsp; 页次:{当前页}/{总页数}";
	$str=str_replace("{总页数}",$pagecount,$str);
	$str=str_replace("{总条数}",$recordcount,$str);
	$str=str_replace("{当前页}",$page,$str);
	$str=str_replace("{每页数量}",$pagesize,$str);
	$str=str_replace("{<<}","<a href=".str_replace("{p}",1,$url)." class='page'><<</a>",$str);
	$str=str_replace("{>>}","<a href=".str_replace("{p}",$pagecount,$url)." class='page'>>></a>",$str);
	
	if ($page>1){
		$str=str_replace("{<}","<a href=".str_replace("{p}",$page-1,$url)." class='page'><</a>",$str);
	}
	else{
		$str=str_replace("{<}","<span class='page'><</span>",$str);
	} 
	if ($page<$pagecount){
		$str=str_replace("{>}","<a href=".str_replace("{p}",$page+1,$url)." class='page'>></a>",$str);
	}
	else{
		$str=str_replace("{>}","<span class='page'>></span>",$str);
	} 
	
	if (strpos($url,"onclick")>0){
    	$clickstr = getBody($url,"onclick=\"",";");
    	$clickstr = replaceStr($clickstr, "{p}", "document.getElementById('page').value");
    }
    else{
    	$clickstr = "location.href='" . replaceStr($url, "{p}", "' + document.getElementById('page').value + '") . "'";
    }
    	
	$jumpurl = "<input name=\"page\" type=\"text\" id=\"page\"><input name=\"go\" type=\"button\" id=\"go\" value=\"GO\" onclick=\"var intstr=/^\d+$/;if(intstr.test(document.getElementById('page').value)&&document.getElementById('page').value<=" . $pagecount . "&&document.getElementById('page').value>=1){". $clickstr.";}\">";
		
	$str=str_replace("{跳转}",$jumpurl,$str);
	$i=$page-4; 
	$j=$page+5;
	if ($i<1){
		$j=$j+(1-$i); 
		$i=1;
	} 
	if ($j>$pagecount){
		$i=$i+($pagecount-$j); 
		$j=$pagecount;
		if ($i<1){
			$i=1;
		} 
	} 
	$loopurl="";
	for ($m=$i; $m<=$j; $m=$m+1){
		if ($m==$page){
			$loopurl=$loopurl." <a href=".str_replace("{p}",$m,$url)." class='pagein'>".$m."</a>";
		}
		else{
			$loopurl=$loopurl." <a href=".str_replace("{p}",$m,$url)." class='page'>".$m."</a>";
		}
	}
	$str=str_replace("{循环}",$loopurl,$str);
	return $str;
}

function isToDay($strTime)
{
	if (isN($strTime)) { return $strTime; }
	$strNow = date('Y-m-d',time());
	if (strpos(",".$strTime,$strNow)>0){ $strColor = "color=\"#FF0000\""; }
	return  "<font " .$strColor. ">" .$strTime. "</font>";
}

function getVodXml($name,$path)
{
	$arr = array();
	if (chkCache($name)){
		$arr = getCache($name,"php");
	}
	else{
		$xmlpath = root ."inc/" .$name;
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xmlpath);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName($path);
		$i=0;
		
		foreach($nodes as $node){
			$from = $node->attributes->item(2)->nodeValue;
			$show = $node->attributes->item(3)->nodeValue;
			$des = $node->attributes->item(4)->nodeValue;
			$tip = $node->getElementsByTagName("tip")->item(0)->nodeValue;
			$arr[$i] = $from. "__" . $show . "__" . $des . "__" . $tip;
			$i++;
		}
		setCache($name,$arr,1,"php");
		unset($nodes);
		unset($xmlnode);
		unset($doc);
	}
	return $arr;
}

function getVodXmlText($name,$path,$from,$k)
{
	$fromarr = explode("$$$",$from);
	$arr1 = getVodXml($name,$path);
	$rc=false;
	$res="";
	
	for($i=0;$i<count($fromarr);$i++){
		foreach($arr1 as $a){
			
			$arr2 = explode("__",$a);
			if ($fromarr[$i] == $arr2[0]){
				if($rc){ $res.=","; }
				$res.= $arr2[$k];
				$rc=true;
			}
		}
	}
	return $res;
}

function updateCacheFile()
{
	global $db;
	//视频分类缓存
	$arr =array();
	try{
		$cachevodtype= $db->queryarray("SELECT *,\"\" AS childids FROM {pre}vod_type");
		$i=0;
		foreach($cachevodtype as $v){
			$strchild="";
			$rc=false;
			$rs= $db->query("SELECT t_id FROM {pre}vod_type WHERE t_pid=" .$v["t_id"]);
			while ($row = $db ->fetch_array($rs)){
				if($rc){ $strchild .=","; }
				$strchild .= $row["t_id"];
				$rc=true;
			}
			unset($rs);
			if (isN($strchild)){ $strchild = $v["t_id"];} else{$strchild = $v["t_id"] . "," . $strchild;}
			$cachevodtype[$i]["childids"] = $strchild;
			$i++;
		}
		
		//setGlobalCache("cache_vodtype",$cachevodtype,1,'php');
	}
	catch(Exception $e){ 
		echo "更新视频分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachevodtype;

	
	//视频专题缓存
	try{
		$cachevodtopic=$db->queryarray("SELECT * FROM {pre}vod_topic");
		//setGlobalCache("cache_vodtopic",$cachevodtopic,1,'php');
	}
	catch(Exception $e){ 
		echo "更新视频专题缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachevodtopic;
	
	
	
	//地区缓存
	try{
		$str = file_get_contents(root."inc/vodarea.txt");
		$str = replaceStr($str,chr(10),"");
		$cachearea = explode(chr(13),$str);
		//setGlobalCache("cache_vodarea",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		echo "更新地区缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachearea;
	
	//语言缓存
	try{
		$str = file_get_contents(root."inc/vodlang.txt");
		$str = replaceStr($str,chr(10),"");
		$cachelang = explode(chr(13),$str);
		//setGlobalCache("cache_vodlang",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		echo "更新语言缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符";
		exit;
	}
	$arr[] = $cachelang;
	

	
	
	
	
	setGlobalCache("cache",$arr,1,'php');
	echo "";
}

function getValueByArray($arr,$item,$val)
{
	foreach($arr as $row){
		if($row[$item] == $val){
			$res =  $row;
			break;
		}
	}
	return $res;
}

function chkCache($cacheName,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	$mintime = time() - app_cachetime*60;
	if(app_cache ==0){ return false; }
	if (file_exists($cacheFile) && ($mintime < filemtime($cacheFile))){
		return true;
	}
	else{
		return false;
	}
}

function getCache($cacheName,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	if($flag=='inc'){
		$result= file_get_contents($cacheFile);
	}
	else{
		$result= @include $cacheFile;
	}
	return $result;
}

function setCache($cacheName,$cacheValue,$cacheType,$flag='inc')
{
	$cacheFile=root.'upload/cache/'.app_cacheid.$cacheName.'.'.$flag;
	if (app_cache==1){
		if($cacheType==1){
			$cacheValue = "<?php\nreturn ".var_export($cacheValue, true).";\n?>";
	        $strlena = file_put_contents($cacheFile, $cacheValue);
		}
		fwrite(fopen($cacheFile,"wb"),$cacheValue);
	}
}

function chkGlobalCache($cacheName,$flag='inc')
{
	$cacheFile=root.'inc/'.app_cacheid.$cacheName.'.'.$flag;
	$mintime = time() - app_cachetime*60;
	if (file_exists($cacheFile) && ($mintime < filemtime($cacheFile))){
		return true;
	}
	else{
		return false;
	}
}

function getGlobalCache($cacheName,$flag='inc')
{
	$cacheFile=root.'inc/'.$cacheName.'.'.$flag;
	if($flag=='inc'){
		$result= file_get_contents($cacheFile);
	}
	else{
		$result= @include $cacheFile;
	}
	return $result;
}

function setGlobalCache($cacheName,$cacheValue,$cacheType,$flag='inc')
{
	$cacheFile=root.'inc/'.$cacheName.'.'.$flag;
	if($cacheType==1){
		$cacheValue = "<?php\nreturn ".var_export($cacheValue, true).";\n?>";
		$strlena = file_put_contents($cacheFile, $cacheValue);
	}
	fwrite(fopen($cacheFile,"wb"),$cacheValue);
}

function getFileByCache($cacheName,$filePath)
{
	if(!file_exists($filePath)){
		die("找不到文件：".$filePath);
	}
	else{
		$res=file_get_contents($filePath);
	}
	return $res;
}

function attemptCacheFile($cPath,$cName)
{
	if(app_dynamiccache==1){
		$cacheFile = root."upload/cache/".$cPath."/".$cName.".html";
		$mintime = time() - app_cachetime*60;
		
		if(file_exists($cacheFile)){
			if($mintime < filemtime($cacheFile)){
				$cachecontent = file_get_contents($cacheFile);
				$cachecontent = replaceStr($cachecontent,"{maccms_runtime}",getRunTime(appTime));
				echo $cachecontent;
				exit;
			}
		}
	}
}

function setCacheFile($cPath,$cName,$cValue)
{
	if(app_dynamiccache==1){
		$cacheFile = root."upload/cache/".$cPath."/".$cName.".html";
		fwrite(fopen($cacheFile,"wb"),$cValue);
	}
}

function getVodCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " where STR_TO_DATE(d_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}vod" .$where);
}

function getArtCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " where STR_TO_DATE(a_time,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}art".$where);
}

function getUserCount($flag)
{
	global $db;
	if ($flag == "day"){
		$where = " where STR_TO_DATE(u_regdate,'%Y-%m-%d')='".date("Y-m-d")."'";
	}
	return $db->getOne("SELECT count(*) FROM {pre}user ".$where);
}

function getKeysLink($key,$ktype)
{
	if (!isN($key)){
		$key = str_replace(","," ",$key);
		$key = str_replace("|"," ",$key);
		
		$arr = explode(" ",$key);
		for ($i=0;$i<count($arr);$i++){
			if (!isN($arr[$i])){
				$str = $str . "<a target='_blank' href='".app_installdir."search.php?".$ktype."=". urlencode($arr[$i])."'>".$arr[$i]."</a>&nbsp;";
			}
		}
	}
	return $str;
}

function repPse($txt,$id)
{
	$id = $id % 7;
	if (isN($txt)){ $txt=""; }
	$psecontent = getFileByCache("dim_pse2",root. "inc/dim_pse2.txt" );
	if (isN($psecontent)){ $psecontent = ""; }
	$psecontent = replaceStr($psecontent,chr(10),"");
	$psearr = explode(chr(13),$psecontent);
	$i=count($psearr)+1;
	$j=strpos($txt,"<br>");
	
	if ($j==0){ $j=strpos($txt,"<br/>");}
	if ($j==0){ $j=strpos($txt,"<br />");}
	if ($j==0){ $j=strpos($txt,"</p>");}
	if ($j==0){ $j=strpos($txt,"。")+1;}
	
	if ($j>0){
		$res= substring($txt,$j-1) . $psearr[$id % $i] . substring($txt,strlen($txt)-$j,$j);
	}
	else{
		$res= $psearr[$id % 1]. $txt;
	}
	return $res;
}

function getTypeIDS($id,$tabName)
{
	global $db;
	$rc=false;
	$arr = $db->queryarray("select t_id,t_pid from ". $tabName ." a order by t_sort asc");
	$tmpSp= explode(",",$id);
	$str="";
	for($j=0;$j<count($tmpSp);$j++){
		if (isN($str)){ $str = $tmpSp[$j];} else {$str = $str . "," . $tmpSp[$j]; }
		for($i=0;$i<count($arr);$i++){
			if($arr[$i]["t_pid"]=="".$tmpSp[$j]){
				$str= $str .",". getTypeIDS($arr[$i]["t_id"],$tabName);
			}
		}
	}
	return $str;
}


function getPlayInfo($vodname, $vodfrom, $vodserver, $vodurl)
{
    if (isN($vodurl)) { $vodurl="";}
    if (isN($vodfrom)) { $vodfrom="";}
    $rc=false;
    
    $playurlarr = explode("$$$",$vodurl); $playurlarrlen = count($playurlarr);
    $playfromarr = explode("$$$",$vodfrom); $playfromarrlen = count($playfromarr);
    $playserverarr = explode("$$$",$vodserver); $playserverarrlen = count($playserverarr);
    
    for ($i=0;$i<$playfromarrlen;$i++){
        $rc1 = false;
        if ($rc) { $str = $str . "$$$"; }
        
        $from = $playfromarr[$i];
        if (isN($from)) { $from = "null"; }
        
        if ($playserverarrlen >= $i){
        	$server = getVodXmlText("vodserver.xml","server", $playserverarr[$i] , 2);
        }
        if (isN($server)) { $server = "null"; }
        
        if ($playurlarrlen >= $i){
            $url = $playurlarr[$i];
            if (isN($url)){
            	$url = "null";
            }
            else{
                $urlarr = explode("#",$url);
                $url = "";
                for ($j=0;$j<count($urlarr);$j++){
                    if (!isN($urlarr[$j])){
                        if ($rc1) { $url = $url ."#"; }
                        $url = $url . $urlarr[$j];
                        $rc1 = true;
                    }
                }
            }
        }
        $str = $str . $server . "$$" . $from . "$$" . $url;
        $rc = true;
    }
    $str = replaceStr($str,"'","\'");
    if (app_encrypt == 1){
        $urlStr = "var maccms_playlist = unescape('" . escape($str) . "');";
    }
    else if (app_encrypt == 2){
        $urlStr = base64_encode(escape($str));
        $urlStr = "var maccms_playlist = unescape(base64decode('" . $urlStr . "'));";
    }
    else{
        $urlStr = "var maccms_playlist = '" . $str . "';";
//        $urlStr="var maccms_playlist = 'null$\$youku$$点事成金 121108$118227977#点事成金 121108$118112724#点事成金 121108$118023674#点事成金 121108$117907313#点事成金 121108$117792147#117705899#117597769#117504182#117392015#117288515#117193372#117086293#116992063#116877444#116766614#116761331#116536811#116429055#116302728#116202497#116083112#115987654#115861508#115758127#115659179#115533681#115442863#115319069#115212884#115113116#114998622#114500547#114404017#114470927#114233779#114091698#114020148#113827556#113725376#113635171#113525050#113451228#113365506#113265979#113162505#113084427#112951465#112860896#112760172#112662600#112560846#112465062#112375942#112279805#112189311#112105304#112025068#111931375#111841521#111404139#111312363#111218445#111127378#110942078#110858666#110771900#110681298#110593957#110502824#110408717#110312517#110217484#110127621#110031027#109935071#109834869#109739200#109643411#109554424#109467611#109379976#109290619#109198064#109111046#109019314#108930443#108846432#108761729#108662293#108571987#108477078#108382978#108290232#108207189#108110070#108010289#107911273#107809015#107707628#107599449#118427840#118331655';";
	}
//	writetofile('test.log',"var playname='" . replaceStr($vodname,"'","\'") . "';" . "\n" . $urlStr );
    return  "var playname='" . replaceStr($vodname,"'","\'") . "';" . "\n" . $urlStr ;
}

function getPlayInfos($vodname, $vodfrom, $vodserver, $vodurl,$weburl)
{
    if (isN($vodurl)) { $vodurl="";}
    if (isN($weburl)) { $weburl="";}
    if (isN($vodfrom)) { $vodfrom="";}
    $rc=false;
    global $mac;
//    var_dump($mac);
    $playurlarr = explode("$$$",$vodurl); $playurlarrlen = count($playurlarr);
    $weburslarr = explode("$$$",$weburl); $weburlarrlen = count($weburslarr);
    $playfromarr = explode("$$$",$vodfrom); $playfromarrlen = count($playfromarr);
    $playserverarr = explode("$$$",$vodserver); $playserverarrlen = count($playserverarr);
    if (!isNum($mac['vodsrc'])) { $vodsrc=0;}else { $vodsrc=intval($mac['vodsrc']-1);}
     $from = $playfromarr[$vodsrc];
//      var_dump($playfromarr);
     if (isN($from)) { $from = "null"; }
            if($from ==='youku'|| $from ==='pptv'){
            	 $url = $weburslarr[$vodsrc];
            	 $url= replaceStr($url,"{Array}","#");
            }else if($from ==='tudou'){
            	 $url = $playurlarr[$vodsrc];
            }else {
               $url = $playurlarr[$vodsrc];
            }
            
             $weburlTemp = $weburslarr[$vodsrc];
             $weburlTemp= replaceStr($weburlTemp,"{Array}","#");
             $weburlTempArr = explode("#",$weburlTemp);   
            if (isN($url)){
              $url = "null";
            }
            else{            	
                $urlarr = explode("#",$url);                              
                $url = "";
                for ($j=0;$j<count($urlarr);$j++){
                    if (!isN($urlarr[$j])){
                        if ($rc1) { $url = $url ."#"; }
                        if($from ==='youku'){
                        	 $urlone =  replaceStr(replaceStr($urlarr[$j],"http://v.youku.com/v_show/id_",""),".html","");
                        	 //$urlone =replaceStr($urlarr[$j],"{Ar}","#"); http://v.pptv.com/show/
			            }else  if($from ==='pptv'){
                        	 $urlone =  replaceStr(replaceStr($urlarr[$j],"http://v.pptv.com/show/",""),".html","");
                        	 //$urlone =replaceStr($urlarr[$j],"{Ar}","#"); http://v.pptv.com/show/
			            }else  if($from ==='tudou'){
			            	 $urlone = $urlarr[$j];
                        	 $urloneTemp =  getBody($weburlTempArr[$j],"http://www.tudou.com/albumplay/",".html");
                        	 if(!isN($urloneTemp)){
                        	 	$urloneTempArr=explode("/", $urloneTemp);
                        	 	$urlone=$urlone."_".$urloneTempArr[0];
                        	 }
                        	 //$urlone =replaceStr($urlarr[$j],"{Ar}","#"); http://v.pptv.com/show/
			            }else{
			              $urlone = $urlarr[$j];
			            }	   
			          
			            $nameUrl=explode("$",$urlone); 
			             $weburlarr = explode("{Array}",$weburslarr[$vodsrc]);   
                        if(count($nameUrl) == 1){                        	 
                        	 if($j<count($weburlarr)){ 
                        	 	$t= explode("$",$weburlarr[$j]);
                        	 	if(count($t)==2){
	                        	 	if(isN($t[0])){
	                        			$urlone='第'.($j+1).'集$'.$urlone;
		                        	}else if(isNum($t[0])){
		                        		$urlone='第'.($t[0]).'集$'.$urlone;
		                        	}else {
                        	 		  $urlone=$t[0].'$'.$urlone;
		                        	}
                        	 	}else {
                        	 		$urlone='第'.($j+1).'集$'.$urlone;
                        	 	}
                        	 }else {
                        	   $urlone='第'.($j+1).'集$'.$urlone;
                        	 }
                        } else if(count($nameUrl) == 2){
                        	$name=$nameUrl[0];
                        	if(isN($name)){
                        		$urlone='第'.($j+1).'集$'.$nameUrl[1];
                        	}else if(isNum($name)){
                        		$urlone='第'.($name).'集$'.$nameUrl[1];
                        	}
                        } 
                      if($j<count($weburlarr)){ 
                         $t= explode("$",$weburlarr[$j]);
                         if(count($t)==2){
                         	$urlone=$urlone.'{Array}'.base64_encode($t[1]);
                         }else if(count($t)==1){
                         	$urlone=$urlone.'{Array}'.base64_encode($t[0]);
                         }
                      }
                         $url = $url . $urlone;                    
                        $rc1 = true;
                    }
                }
            }
        
        $str =   $url;
//    for ($i=0;$i<$playfromarrlen;$i++){ 
//        $rc1 = false;
//        if ($rc) { $str = $str . "$$$"; }
//        
//        $from = $playfromarr[$i];
//        if (isN($from)) { $from = "null"; }
//        
//        if ($playserverarrlen >= $i){
//        	$server = getVodXmlText("vodserver.xml","server", $playserverarr[$i] , 2);
//        }
//        if (isN($server)) { $server = "null"; }
//        
//        if ($playurlarrlen >= $i){
//            $url = $playurlarr[$i];
//            if (isN($url)){
//            	$url = "null";
//            }
//            else{
//                $urlarr = explode("#",$url);
//                $url = "";
//                for ($j=0;$j<count($urlarr);$j++){
//                    if (!isN($urlarr[$j])){
//                        if ($rc1) { $url = $url ."#"; }
//                        $url = $url . $urlarr[$j];
//                        $rc1 = true;
//                    }
//                }
//            }
//        }
//        $str = $str . $server . "$$" . $from . "$$" . $url;
//        $rc = true;
//    }
    
	if(isNum($mac['vodname'])){
	   $mac['vodname']='第'.($mac['vodname']).'集';
	}
    $str = replaceStr($str,"'","\'");
    if (app_encrypt == 1){
        $urlStr = "var maccms_playlist = unescape('" . escape($str) . "');";
    }
    else if (app_encrypt == 2){
        $urlStr = base64_encode(escape($str));
        $urlStr = "var maccms_playlist = unescape(base64decode('" . $urlStr . "'));";
    }
    else{
        $urlStr = "var maccms_playlist = '" . $str . "';";
//        $urlStr="var maccms_playlist = 'null$\$youku$$点事成金 121108$118227977#点事成金 121108$118112724#点事成金 121108$118023674#点事成金 121108$117907313#点事成金 121108$117792147#117705899#117597769#117504182#117392015#117288515#117193372#117086293#116992063#116877444#116766614#116761331#116536811#116429055#116302728#116202497#116083112#115987654#115861508#115758127#115659179#115533681#115442863#115319069#115212884#115113116#114998622#114500547#114404017#114470927#114233779#114091698#114020148#113827556#113725376#113635171#113525050#113451228#113365506#113265979#113162505#113084427#112951465#112860896#112760172#112662600#112560846#112465062#112375942#112279805#112189311#112105304#112025068#111931375#111841521#111404139#111312363#111218445#111127378#110942078#110858666#110771900#110681298#110593957#110502824#110408717#110312517#110217484#110127621#110031027#109935071#109834869#109739200#109643411#109554424#109467611#109379976#109290619#109198064#109111046#109019314#108930443#108846432#108761729#108662293#108571987#108477078#108382978#108290232#108207189#108110070#108010289#107911273#107809015#107707628#107599449#118427840#118331655';";
	}
//	writetofile('test.log',"var playname='" . replaceStr($vodname,"'","\'") . "';" . "\n" . $urlStr );
    return  "var playname='" . replaceStr($vodname,"'","\'") . "';" . "\n" . $urlStr . "\nvar play_vodid='" . $mac['vodid']. "';\nvar play_vodnum='" . urldecode($mac['vodname'])."';\nvar play_from='" . $from."'; \n getPlayer();\nvar play_prenum='" . urldecode($mac['vodname'])."'; \nvar play_nextnum='" . urldecode($mac['vodname'])."'; ";
}

function getPlayer()
{
    return "";
}

function getUserFlag($flag)
{
	switch ($flag)
	{
		case 1:
			$res="包时";
			break;
		case 2:
			$res="IP段";
			break;
		default:
			$res="计点";
			break;
	}
	return $res;
}

function getTypeByPopedomFilter($flag)
{
	global $cache;
	$a="0,";
	$b="0,";
	$rc=false;
	$userid=$_SESSION["userid"];
	$usergroup=$_SESSION["usergroup"];
	if (isN($usergroup)) { $usergroup=0; } else { $usergroup=intval($usergroup);}
	
	
	$arr = getValueByArray($cache[6],"ug_id" ,$usergroup);
	for ($i=0;$i<count( $cache[6] );$i++){
		$arr1 = $cache[6][$i];
		
		if ($usergroup==0 ){
			if  ( strpos(",".$arr1["ug_popedom"], ",1,") >0 ){
				$a .= $a . $arr1["ug_type"];
			}
		}
		else{
			if (  strpos(",".$arr1["ug_popedom"], ",1,") >0  && $arr1["ug_popvalue"] > $arr["ug_popvalue"] ){
				$a .= $a . $arr1["ug_type"];
			}
			if ($arr1["ug_popvalue"] <= $arr["ug_popvalue"]){
				$b = $b . $arr1["ug_id"]. ",";
			}
		}
	}
	
	$a = replaceStr($a,",,",",");
	$b = replaceStr($b,",,",",");
	
	if (substring($a,1,strlen($a)-1) == ","){ $a = substring($a,strlen($a)-1); }
	if (substring($b,1,strlen($b)-1) == ","){ $b = substring($b,strlen($b)-1); }
	
	if ($flag=="menu"){
		$res = " and t_id not in(". $a .") ";
	}
	else{
		$res = " and d_type not in(". $a .") and d_usergroup in(". $b .") ";
	}
	return $res;
}

function getUserPopedom($id,$flag)
{
	global $cache;
	
	$userid=$_SESSION["userid"];
	$usergroup=$_SESSION["usergroup"];
	if (isN($usergroup)) { $usergroup=0; } else { $usergroup=intval($usergroup);}
	$result=false;
	$ug_popvalue1=0;
	$num=0;
	if ($flag== "list"){
		$flag = "1";
	}
	else if ($flag== "vod"){
		$flag = "2";
	}
	else if ($flag=="play"){
		$flag = "3";
	}
	
	for ($i=0;$i<count($cache[6]);$i++){
		$ug_id=$cache[6][$i]["ug_id"];
		if ($ug_id==$usergroup){ $ug_popvalue1=$cache[6][$i]["ug_popvalue"]; }
		
		if (strpos(",".$cache[6][$i]["ug_type"],",".$id.",")>0 && strpos(",".$cache[6][$i]["ug_popedom"],",".$flag.",")>0){
			 $num++;
			 if ($ug_popvalue1 >= $cache[6][$i]["ug_popvalue"]){ $result=true; break;}
		}
	}
	if ($num==0){ $result=true; }
	return $result;
}
?>