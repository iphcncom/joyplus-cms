<?php
require_once (dirname(__FILE__)."/YouKuContent.php");
require_once (dirname(__FILE__)."/LetvContent.php");
require_once (dirname(__FILE__)."/PPTVContent.php");
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
  	
  	public static function getContentProvider($providerName){
  		if(ContentProviderFactory::LETV===$providerName ){
  			return new LetvContent();
  		}else if(ContentProviderFactory::TU_DOU===$providerName ){
  			return new TudouContent();
  		}else if(ContentProviderFactory::PPTV===$providerName ){
  			return new PPTVContent();
  		}else if(ContentProviderFactory::YOU_KU===$providerName ){
  			return new YouKuContent();
  		}else if(ContentProviderFactory::Wl_56===$providerName ){
  			return new WLContent();
  		}else if(ContentProviderFactory::FENG_XING===$providerName ){
  			return new FengXingContent();
  		} else {
  		  return new DefaultContent();
  		}
  		
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
  
// echo getLocation("http://m.letv.com/playvideo.php?id=1779637&mmsid=19536676");
//  echo ContentProviderFactory::getContentProvider(ContentProviderFactory::FENG_XING)->parseAndroidVideoUrl("http://www.funshion.com/subject/play/103299/4", "utf-8", "");
  
  
  
?>