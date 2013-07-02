<?php
require_once (dirname(__FILE__)."/../../inc/conn.php");

//define your token
define("APPID", "wx2b1e0a3a3d8e716f");
define("APPSECRET", "7e9b0aadb7340509ca4cc0cf9b732ab8");
define("ACCESS_TOKEN_URL", "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET);

$mp = new MpWeiXinClass();
$mp->authToken();
var_dump( $mp->acces_token);
class MpWeiXinClass
{
  public $acces_token;
  public function authToken(){
  	   var_dump(ACCESS_TOKEN_URL);
  	    $content = getPageSSL(ACCESS_TOKEN_URL, "utf-8");  	    
  	 	$content=json_decode($content);//property_exists($content, 'max_episode')?$content->max_episode
  	 	if(is_object($content) && property_exists($content, 'access_token')){
  	 	  $this->acces_token= $content->access_token;
  	 	  return $this->acces_token;
  	 	}  	 	
  	 	return false;
  }
}