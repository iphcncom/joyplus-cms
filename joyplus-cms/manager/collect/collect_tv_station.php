<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/collect_fun.php");
require_once (dirname(__FILE__)."/CnTVLiveParse.php");

$areaArray = array(
   '北京' =>'http://epg.tvsou.com/program/TV_38/Channel_60/W3.htm',
  '上海' =>'http://epg.tvsou.com/program/TV_11/Channel_81/W3.htm',
  '天津' =>'http://epg.tvsou.com/program/TV_35/Channel_57/W3.htm',
  '重庆' =>'http://epg.tvsou.com/program/TV_37/Channel_59/W3.htm',
  '香港' =>'http://epg.tvsou.com/program/TV_3/Channel_22/W3.htm' ,
  '澳门' =>'http://epg.tvsou.com/program/TV_150/Channel_737/W3.htm' ,
  '台湾' =>'http://epg.tvsou.com/program/TV_68/Channel_246/W3.htm' ,
  '湖南' =>'http://epg.tvsou.com/program/TV_24/Channel_46/W3.htm',
  '湖北' =>'http://epg.tvsou.com/program/TV_25/Channel_47/W3.htm',
  '江苏' =>'http://epg.tvsou.com/program/TV_22/Channel_44/W3.htm',
  '江西' =>'http://epg.tvsou.com/program/TV_23/Channel_45/W3.htm',
  '安徽' =>'http://epg.tvsou.com/program/TV_20/Channel_42/W3.htm',
  '浙江' =>'http://epg.tvsou.com/program/TV_21/Channel_43/W3.htm',
  '福建' =>'http://epg.tvsou.com/program/TV_29/Channel_51/W3.htm',
  '广东' =>'http://epg.tvsou.com/program/TV_39/Channel_70/W3.htm',
  '广西' =>'http://epg.tvsou.com/program/TV_28/Channel_50/W3.htm',
  '四川' =>'http://epg.tvsou.com/program/TV_36/Channel_58/W3.htm',
  '云南' =>'http://epg.tvsou.com/program/TV_27/Channel_49/W3.htm',
  '贵州' =>'http://epg.tvsou.com/program/TV_26/Channel_48/W3.htm',
  '海南' =>'http://epg.tvsou.com/program/TV_8/Channel_31/W3.htm' ,
  '山东' =>'http://epg.tvsou.com/program/TV_19/Channel_41/W3.htm',
  '山西' =>'http://epg.tvsou.com/program/TV_17/Channel_39/W3.htm',
  '河南' =>'http://epg.tvsou.com/program/TV_16/Channel_38/W3.htm',
  '河北' =>'http://epg.tvsou.com/program/TV_15/Channel_37/W3.htm',
  '陕西' =>'http://epg.tvsou.com/program/TV_18/Channel_40/W3.htm',
  '甘肃' =>'http://epg.tvsou.com/program/TV_30/Channel_52/W3.htm',
  '黑龙江' =>'http://epg.tvsou.com/program/TV_12/Channel_34/W3.htm',
  '辽宁' =>'http://epg.tvsou.com/program/TV_14/Channel_36/W3.htm',
  '吉林' =>'http://epg.tvsou.com/program/TV_13/Channel_35/W3.htm',
  '新疆' =>'http://epg.tvsou.com/program/TV_33/Channel_55/W3.htm',
  '西藏' =>'http://epg.tvsou.com/program/TV_32/Channel_54/W3.htm',
  '宁夏' =>'http://epg.tvsou.com/program/TV_31/Channel_53/W3.htm',
  '青海' =>'http://epg.tvsou.com/program/TV_114/Channel_592/W3.htm' ,
  '内蒙古' =>'http://epg.tvsou.com/program/TV_34/Channel_56/W3.htm',
);
 
$keys=array_keys($areaArray);
foreach ($keys as $key){
	$value=$areaArray[$key];
	var_dump($key .'==='.$value);
	 $channels= parseStation($key,$value);
 insertTvEpg($channels);
// break;
}

 function insertTvEpg($channels){
  	$keys =array_keys($channels);
	  global $db;
	  foreach ($keys as $key){
	  	$keyArray=explode("{Array}", $key);
	  	$area=$keyArray[0];
	  	$station=$keyArray[1];
	  	$channel=$keyArray[2];
	  	$url=$channels[$key];
	  	$tv_code=replaceStr($url, "http://epg.tvsou.com/program/", "");
	  	$tv_code=replaceStr($tv_code, "/W3.htm", "");
	  	
	  	//var_dump($area .'=='.$station.'=='.$channel.'=='.$url.'=='.$tv_code);
	  	
	  
	  	$row = $db->getRow('select id from mac_tv where tv_name =\''.$channel.'\'');
	  	$insertSql="";
	  	
	  	if ($row){
	  		$tv_id=$row['id'];
	  		$insertSql= "insert into mac_tv_egp_config(tv_name,tv_code,tv_playfrom,tv_id) values('".$channel."','".$tv_code."' , 'tvsou' ,'".$tv_id."')";
	  	}else {
	  		$insertSql= "insert into mac_tv_egp_config(tv_name,tv_code,tv_playfrom) values('".$channel."','".$tv_code."' , 'tvsou' )";
	  	}
	  	
	  	var_dump($insertSql);
	  	$db->query($insertSql);
	  }
  }
  
  
  function insertChannel($channels){
  	$keys =array_keys($channels);
	  global $db;
	  foreach ($keys as $key){
	  	$keyArray=explode("{Array}", $key);
	  	$area=$keyArray[0];
	  	$station=$keyArray[1];
	  	$channel=$keyArray[2];
	  	$url=$channels[$key];
	  	$tv_code=replaceStr($url, "http://epg.tvsou.com/program/", "");
	  	$tv_code=replaceStr($tv_code, "/W3.htm", "");
	  	
	  	//var_dump($area .'=='.$station.'=='.$channel.'=='.$url.'=='.$tv_code);
	  	
	  
	  	$row = $db->getRow('select id from mac_tv where tv_name =\''.$channel.'\'');
	  	  $insertSql="";
	  	  
	  	if (!$row){
	  		$insertSql= "insert into mac_tv(country,area,tv_name,tv_group_name) values('中国','".$area."','".$channel."','".$station."')";
	  	}else {
	  		$insertSql="update mac_tv set area='".$area."', tv_group_name='".$station."' where id=".$row['id'];
	  	}
	  	
	  	var_dump($insertSql);
	  	$db->query($insertSql);
	  }
  }
  
 function parseStation($area,$url){
 	$content = getPage($url, "");
 	$stations = getArray($content, '<div class="listmenu" >', '</div>');
 	$stationsArray =explode("{Array}", $stations);
// 	var_dump($stationsArray);
 	$channels= array();
 	foreach ($stationsArray as $station){
 		$url ='http://epg.tvsou.com/program/'.getBody($station, '/program/', '"');
 		$stationname =getBody($station, 'class=blue2>', '</a>');
// 		$stations[$stationname]=$url;
        $station_channels =parseChannel($area, $url, $stationname);
 		$channels=array_merge($channels,$station_channels);
 	}
 	
 	return $channels;
 }
 
  function parseChannel($area,$url,$stationname){
  	global $db;
 	$contents = getPage($url, "");
 	$content=getBody($contents, '<div class="listmenu2" >', '<div class="listmenu" >');
 	
 	if($content ===false || isN($content)){
 		$content=getBodys($contents, '<div class="listmenu2" >');
 	}
// 	var_dump($content);
 	$codes=getArray($content, "/program/", '</a>');
//
$codesArray = explode("{Array}", $codes);
////http://epg.tvsou.com/program/

$channels=array();
$currentStation= getBody($content, '<font color="#FF6600">', '</font>');
$channels[$area.'{Array}'.$stationname.'{Array}'.$currentStation]=$url;
foreach ($codesArray as $code){
	$ste= explode('"class=blue2>', $code);
	if(!isN($ste[1])){
	  $channels[$area.'{Array}'.$stationname.'{Array}'.$ste[1]]='http://epg.tvsou.com/program/'.$ste[0];	  
	}
}
//var_dump($channels);
return $channels;
 }
    
   
    
    
  
	
	

?>