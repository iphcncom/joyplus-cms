<?php
require_once (dirname(__FILE__)."/YouKuContent.php");
require_once (dirname(__FILE__)."/LetvContent.php");
require_once (dirname(__FILE__)."/PPTVContent.php");
require_once (dirname(__FILE__)."/SohuContent.php");
require_once (dirname(__FILE__)."/SinaContent.php");
require_once (dirname(__FILE__)."/M1905Content.php");
require_once (dirname(__FILE__)."/IqiyiContent.php");
require_once (dirname(__FILE__)."/TudouContent.php");
require_once (dirname(__FILE__)."/DefaultContent.php");
require_once (dirname(__FILE__)."/WLContent.php"); 
require_once (dirname(__FILE__)."/FengXingContent.php");//http://www.56.com/u42/v_NjY3MTUyOTU.html
require_once (dirname(__FILE__)."/../MovieType.php");
require_once (dirname(__FILE__)."/../../admin_conn.php");
  class ContentProviderFactory{
  	const YOU_KU="youku";
  	const TU_DOU="tudou";
  	const PPTV="pptv";
  	const LETV="letv";
  	const Wl_56="56";
  	const FENG_XING="fengxing";
  	const QQ="qq";
  	const SOHU="sohu";
  	const SINA="sinahd";
  	const QI_YI="qiyi";
  	const TANG_DOU="tangdou";
        const M1905="m1905";
  	
  	const YOU_KU_CAN_PLAY_CONTENT="优酷网未能找到您所访问的地址";
  	const TU_DOU_CAN_PLAY_CONTENT="哎呀！你想访问的网页不存在。";
  	const PPTV_CAN_PLAY_CONTENT="很抱歉,您要访问的页面无法找到。";
  	const LETV_CAN_PLAY_CONTENT="页面没有找到";
  	const Wl_56_CAN_PLAY_CONTENT="很抱歉，您访问的页面不存在";
  	const FENG_XING_CAN_PLAY_CONTENT="很抱歉，您查看的影片不存在";
  	const QQ_CAN_PLAY_CONTENT="很抱歉，您所请求的页面不存在或链接错误";
  	const SOHU_CAN_PLAY_CONTENT="非常抱歉！无法替您找到页面";
  	const SINA_CAN_PLAY_CONTENT="Not Found"; //对不起，这个页面已经木有啦
  	const QI_YI_CAN_PLAY_CONTENT="很不碰巧，您想访问的页面丢了";
  	const TANG_DOU_CAN_PLAY_CONTENT="视频已被删除";
  	
  	public static function getContentProvider($providerName){
  		if(ContentProviderFactory::LETV===$providerName ){
  			return new LetvContent();
  		}else if(ContentProviderFactory::TU_DOU===$providerName ){
//  			return new TudouContent();
               return new DefaultContent();
  		}else if(ContentProviderFactory::PPTV===$providerName ){
//  		 return new PPTVContent();
             return new DefaultContent();
  		}else if(ContentProviderFactory::YOU_KU===$providerName ){
  			return new YouKuContent();
  		}else if(ContentProviderFactory::Wl_56===$providerName ){
  			return new WLContent();
  		}else if(ContentProviderFactory::FENG_XING===$providerName ){
  			return new FengXingContent();
  		} else if(ContentProviderFactory::SOHU===$providerName ){
  			return new DefaultContent();
  		}else if(ContentProviderFactory::SINA===$providerName ){
  			return new SinaContent();
  		}else if(ContentProviderFactory::QI_YI===$providerName ){
  			return new IqiyiContent();
  		}else if(ContentProviderFactory::M1905===$providerName ){
  			return new M1905Content();
  		} 
  		else {
  		  return new DefaultContent();
  		}  		
  	}
  	
  	
  public static function checkHtmlCanPlay($providerName,$url){
  	    if(!isset($providerName) || is_null($providerName) || $providerName ==='no' ){
  	    	return true;
  	    }
  	    $content="";
  	    $judgeContent="";
  		if(ContentProviderFactory::YOU_KU===$providerName ){
  			$judgeContent=ContentProviderFactory::YOU_KU_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else if(ContentProviderFactory::TU_DOU===$providerName ){
  			$judgeContent=ContentProviderFactory::TU_DOU_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'gbk');
  		}else if(ContentProviderFactory::PPTV===$providerName ){
  			$judgeContent=ContentProviderFactory::PPTV_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else if(ContentProviderFactory::LETV===$providerName ){
  			$judgeContent=ContentProviderFactory::LETV_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else if(ContentProviderFactory::Wl_56===$providerName ){
  			$judgeContent=ContentProviderFactory::Wl_56_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else if(ContentProviderFactory::FENG_XING===$providerName ){
  			$judgeContent=ContentProviderFactory::FENG_XING_CAN_PLAY_CONTENT;
  			$content=getPageWindow($url, 'utf-8');
  		}else if(ContentProviderFactory::QQ===$providerName ){
  			$judgeContent=ContentProviderFactory::QQ_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else if(ContentProviderFactory::SOHU===$providerName ){
  			$judgeContent=ContentProviderFactory::SOHU_CAN_PLAY_CONTENT; //http://tv.sohu.com/20121126/n3587206433333335.shtml
  			$content=getPage($url, 'GBK');
  		}else if(ContentProviderFactory::SINA===$providerName ){
  			$judgeContent=ContentProviderFactory::SINA_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
	  		if(isset($content) && !is_null($content) &&  strpos($content, "对不起，这个页面已经木有啦") === false){
//	  			return true;
	  		}else {
	  			return false;
	  		}
  		}else if(ContentProviderFactory::QI_YI===$providerName ){
  			$judgeContent=ContentProviderFactory::QI_YI_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}	else if(ContentProviderFactory::TANG_DOU===$providerName ){
  			$judgeContent=ContentProviderFactory::TANG_DOU_CAN_PLAY_CONTENT;
  			$content=getPage($url, 'utf-8');
  		}else {
  			return true;
  		}
//  		var_dump($content);
//  		writetofile("d:\\canhl.txt", $content);
  		if(isset($content) && !is_null($content) &&  strpos($content, $judgeContent) === false){
  			return true;
  		}else {
  			return false;
  		}
  		
  		return false;
  	}
  	
  public static function obj2arr($array) {
	if (is_object ( $array )) {
		$array = ( array ) $array;
	}
	if (is_array ( $array )) {
		foreach ( $array as $key => $value ) {
			$array [$key] = ContentProviderFactory::obj2arr ( $value );
		}
	}
	return $array;
}
  }
  
  abstract class Content{
  	abstract public function checkHtmlCanPlay($url,$p_coding);
  	abstract public function parseAndroidVideoUrlByContent($content,$p_coding,$p_script);
  	abstract public function parseAndroidVideoUrl($url,$p_coding,$p_script);
  	abstract public function parseIOSVideoUrlByContent($content,$p_coding,$p_script);
  	abstract public function parseIOSVideoUrl($url,$p_coding,$p_script);
  }
  
//  var_dump(getPage("http://www.56.com/u42/v_NjY3MTUyOTU.html", "utf-8"));
//echo ContentProviderFactory::getContentProvider(ContentProviderFactory::FENG_XING)->parseAndroidVideoUrl("http://www.funshion.com/subject/play/13376/1", "utf-8", "");
// echo getLocation("http://m.letv.com/playvideo.php?id=1779637&mmsid=19536676");
//  if( ContentProviderFactory::checkHtmlCanPlay(ContentProviderFactory::YOU_KU,"http://v.youku.com/v_show/id_XNDkxMTg4MDg.html")){
//  	echo 'can play';
//  }else {
//  	echo 'can\'t play';
//  }
//  
  
  
?>