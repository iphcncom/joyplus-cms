<?php
class FilterUtils{
	private static function isN($str){
		if (is_null($str) || $str==''){ return true; }else{ return false;}
	}
  public static function keyWordValid($keyword){
  	  if(SystemConfig::getBooleanSystemConf(Constants::SYS_CONF_OPEN_FILTER_KEYWORD, false)){
  	  	 $keywords = SystemConfig::getStringSystemConf(Constants::SYS_CONF_FILTER_KEYWORD_KEY, '');
  	  	 if(!FilterUtils::isN($keywords)){  	  	 	
  	  	 	$keywordArray = explode("{Array}", str_replace(Chr(13), "{Array}", str_replace(Chr(10), "", trim($keywords))));
  	  	 	foreach ($keywordArray as $keywordS){
  	  	 		$keywordS= trim($keywordS);
  	  	 		if(strpos($keyword, $keywordS) !==false || strpos($keywordS, $keyword) !==false){
  	  	 			return false;
  	  	 		}
  	  	 	}
  	  	 }
  	  }
  	  return true;
  }
}
?>