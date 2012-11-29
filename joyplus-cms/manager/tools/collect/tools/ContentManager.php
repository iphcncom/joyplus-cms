<?php
require_once (dirname(__FILE__)."/YouKuContent.php");
require_once (dirname(__FILE__)."/LetvContent.php");
require_once (dirname(__FILE__)."/PPTVContent.php");
require_once (dirname(__FILE__)."/TudouContent.php");
require_once (dirname(__FILE__)."/../MovieType.php");
require_once (dirname(__FILE__)."/../../admin_conn.php");
  class ContentProviderFactory{
  	const YOU_KU="youku";
  	const TU_DOU="tudou";
  	const PPTV="pptv";
  	const LETV="letv";
  	
  	public static function getContentProvider($providerName){
  		if(ContentProviderFactory::LETV===$providerName ){
  			return new LetvContent();
  		}
  	    if(ContentProviderFactory::TU_DOU===$providerName ){
  			return new TudouContent();
  		}
  	    if(ContentProviderFactory::PPTV===$providerName ){
  			return new PPTVContent();
  		}
  	    if(ContentProviderFactory::YOU_KU===$providerName ){
  			return new YouKuContent();
  		}
  		
  	}
  	
    
  }
  
  abstract class Content{
  	abstract public function checkHtmlCanPlay($url,$p_coding);
  	abstract public function parseAndroidVideoUrlByContent($content,$p_coding,$p_script);
  	abstract public function parseAndroidVideoUrl($url,$p_coding,$p_script);
  	abstract public function parseIOSVideoUrlByContent($content,$p_coding,$p_script);
  	abstract public function parseIOSVideoUrl($url,$p_coding,$p_script);
  }
  
  
  
// echo getLocation("http://m.letv.com/playvideo.php?id=1779637&mmsid=19536676");
  echo ContentProviderFactory::getContentProvider(ContentProviderFactory::TU_DOU)->parseAndroidVideoUrl("http://www.tudou.com/albumplay/dk2-gn5vLVI/1nyapURxDvY.html", "utf-8", "");
  
  
  
?>