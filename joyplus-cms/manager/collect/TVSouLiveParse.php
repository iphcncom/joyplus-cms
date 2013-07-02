<?php
  require_once (dirname(__FILE__)."/../admin_conn.php");
  require_once (dirname(__FILE__)."/collect_fun.php");
  require_once (dirname(__FILE__)."/HttpClient.class.php");
  class TVSouLiveParse{  	
  	 const contentparmStart='<!--节目列表开始-->'; //
  	 const contentparaend='<!--节目列表结束-->';
  	 const cookieStart="Set-Cookie: "; //
  	 const cookieend=";";
  	 const BASE_EPISODE="http://epg.tvsou.com/program/{TV_CODE}/W{DATE}.htm";
  	 const BASE_SHOW_EPISODE="http://video.baidu.com/htvshowsingles/?id={ID}&site={SITE_URL}&year={YEAR}";
  	 //http://video.baidu.com/hcomicsingles/?id=2758&site=pptv.com&callback=bd__cbs__bbtz3q
  	 const BASE_COMIC_EPISODE="http://video.baidu.com/hcomicsingles/?id={ID}&site={SITE_URL}";
  	  
  	 static function crawlerProgramItems($date,$chnnel){
  	 	$dateTime = strtotime($date) 	 ;	
  	 	$date =date('w',$dateTime); 
  	 	if($date ==='0'){
  	 		$date='7';
  	 	}  	 	
  	 	$url=replaceStr(TVSouLiveParse::BASE_EPISODE, '{DATE}', $date);
  	 	$url=replaceStr($url, '{TV_CODE}', $chnnel);  	 	
  	 	$content = getPage($url, "gb2312"); 
  	 	writetofile("program_live_item_crawler.log", "url:[".$url."]");
//  	 	var_dump($url);
  	 	return TVSouLiveParse::parseMovieInfoByContent($content, $p_code,$type);
  	 }
  	 
  	 
     static function parseMovieInfoByContent($content,$p_code,$type){
  	 	$content= getBody($content, TVSouLiveParse::contentparmStart, TVSouLiveParse::contentparaend);
//  	 	var_dump($content);color='#CC9966'
       $content=replaceStr($content, '#CC9966', '#6699CC');
  	 	$times= getArray($content, "<font color='#6699CC'>", "</font>");
  	 	$names= getArray($content, "<div id='e2' >", "</div>");
//  	 	var_dump($names);
//  	 	 $names=filterScript($names,8191);

  	 	$timesArray=explode("{Array}", $times);
  	 	$namesArray=explode("{Array}", $names);
//  	 	var_dump($timesArray);
  	 	$prod_itmes = array();
  	 	$index=0;
  	 	foreach ($timesArray as $timeItem){  
  	 		 $name=$namesArray[$index];
  	 		 $nameArray=explode('<ahref=', $name);
  	 		 if(!isN($nameArray[0])){
  	 		 	$itemName=$nameArray[0];
  	 		 }else {
  	 		 	$itemName=filterScript($name,8191);
  	 		 }
  	 		 
  	 		 $prod_itmes[$timeItem]=$itemName;
  	 		 $index++;
  	 	}
//  	 	var_dump($prod_itmes);
  	 	if(count($prod_itmes)==1 ){
  	 		return false;
  	 	}
  	 	
  	 	return $prod_itmes;  	 	
  	 }
  	 
  }
  
  
// var_dump(TVSouLiveParse::crawlerProgramItems("2013-04-17", "TV_36/Channel_164"));  
 
  
?>