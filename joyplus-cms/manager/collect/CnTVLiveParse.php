<?php
  require_once (dirname(__FILE__)."/../admin_conn.php");
  require_once (dirname(__FILE__)."/collect_fun.php");
  require_once (dirname(__FILE__)."/HttpClient.class.php");
  class CnTVLiveParse{  	
  	 const contentparmStart='<div class="bflb_contet" id="epg_list">'; //
  	 const contentparaend='#epg_list {';
  	 const cookieStart="Set-Cookie: "; //
  	 const cookieend=";";
  	 const BASE_EPISODE="/index.php?action=epg-list&date={DATE}&channel={TV_CODE}&mode=";
  	 const BASE_SHOW_EPISODE="http://video.baidu.com/htvshowsingles/?id={ID}&site={SITE_URL}&year={YEAR}";
  	 //http://video.baidu.com/hcomicsingles/?id=2758&site=pptv.com&callback=bd__cbs__bbtz3q
  	 const BASE_COMIC_EPISODE="http://video.baidu.com/hcomicsingles/?id={ID}&site={SITE_URL}";
  	  
  	 static function crawlerProgramItems($date,$chnnel){
  	 	$url=replaceStr(CnTVLiveParse::BASE_EPISODE, '{DATE}', $date);  	 	
  	 	$url=replaceStr($url, '{TV_CODE}', $chnnel);
  	 	$client = new HttpClient('tv.cntv.cn');  
  	 	$client->get('/epg');
  	 	$client->get($url);
  	 	writetofile("program_live_item_crawler.log", "url:[http://tv.cntv.cn".$url."]");
  	 	$content = $client->getContent(); 
  	 	
  	 	return CnTVLiveParse::parseMovieInfoByContent($content, $p_code,$type);
  	 }
  	 
  	 
     static function parseMovieInfoByContent($content,$p_code,$type){
  	 	$content= getBody($content, CnTVLiveParse::contentparmStart, CnTVLiveParse::contentparaend);
  	 	$items= getArray($content, "<dd>", "</dd>");
  	 	$itemArray=explode("{Array}", $items);
  	 	$prod_itmes = array();
  	 	foreach ($itemArray as $item){
  	 		 $item=filterScript($item,8191);
  	 		 $item=trim($item);
  	 		 $item=replaceStr($item, '回看', '');
  	 		 $date=substr($item, 0,5);
  	 		 $item=replaceStr($item, $date, '');
  	 		 $prod_itmes[$date]=$item;
  	 	}
  	 	if(count($prod_itmes)==1 ){
  	 		return false;
  	 	}
  	 	return $prod_itmes;  	 	
  	 }
  	 
  }
  
  
//  var_dump(CnTVLiveParse::crawlerProgramItems("2014-06-11", "cctvamericas"));  
 
  
?>