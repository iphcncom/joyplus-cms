<?php
class Session
{
	public function qq_token($token = '')
	{
		if($token){
			$_SESSION['QQTOKEN'] = $token;
		}
		else{
			return $_SESSION['QQTOKEN'];
		}
	}
}

class QqConnect
{
	private $sess;
	public $token = array();
	public $qqid;
	
	public function __construct()
	{
		global $sess;
		$this->sess = $sess;
		$this->token = $this->get_url_token();
	}
	
	private function get_url_token(){
		parse_str(array_pop(explode('?', $_SERVER['REQUEST_URI'], 2)), $data);
		$result = array();
		$keys = array('oauth_token', 'openid', 'oauth_signature', 'timestamp', 'oauth_vericode');
		foreach($keys as $key){
			$result[$key] = trim($data[$key]);
		}
		return $result;
	}
	
	private function make_sign($method, $url, $params, $secret = '')
	{
		$str = $method . '&' . urlencode($url). '&' . urlencode(http_build_query($params));
		return base64_encode(hash_hmac('sha1', $str, QQ_OAUTH_CONSUMER_SECRET . '&' . $secret, true));
	}


	private function get_temp_token()
	{
		$url = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token';
		$params = array(
				'oauth_consumer_key' => QQ_OAUTH_CONSUMER_KEY,
				'oauth_nonce' => QQ_OAUTH_NONCE,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp' => QQ_TIMESTAMP,
				'oauth_version' => '1.0',
			);
		$params['oauth_signature'] = $this->make_sign('GET', $url, $params);
		$url .= '?' . http_build_query($params);
		$str = @file_get_contents($url);
		$result = false;
		is_string($str) && parse_str($str, $result);
		return $result;
	}
	
	public function create_login_url(){
		$token = $this->get_temp_token();
		$url = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize';
		$params = array(
				'oauth_consumer_key' => QQ_OAUTH_CONSUMER_KEY,
				'oauth_token' => $token['oauth_token'],
				'oauth_callback' => QQ_CALLBACK_URL,
			);
		ksort($params); #按照字典排序
		$url .= '?' . http_build_query($params);
		$this->sess->qq_token($token['oauth_token_secret']);
		return $url;
	}
	
	public function check_oauth_token(){
		$token = $this->token;
		$str = $token['openid'] . $token['timestamp'];
		return $token['oauth_signature'] == base64_encode(hash_hmac('sha1', $str, QQ_OAUTH_CONSUMER_SECRET, true));
	}
	
	public function get_access_token(){
		$token = $this->token;
		$url = 'http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token';
		$params = array(
				'oauth_consumer_key' => QQ_OAUTH_CONSUMER_KEY,
				'oauth_nonce' => QQ_OAUTH_NONCE,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp' => QQ_TIMESTAMP,
				'oauth_token' => $token['oauth_token'],
				'oauth_vericode' => $token['oauth_vericode'],
				'oauth_version' => '1.0',
			);
		ksort($params); #按照字典排序
		$params['oauth_signature'] = $this->make_sign('GET', $url, $params, $this->sess->qq_token());
		$url .= '?' . http_build_query($params);
		$str = @file_get_contents($url);
		$result = false;
		is_string($str) && parse_str($str, $result);
		return $result;
	}
	
	public function get_user_info(){
		$token = $this->get_access_token();
		if(isset($token['error_code'])){
			//var_dump($token);
			exit;
		}
		$this->qqid = $token['openid'];
		$url = 'http://openapi.qzone.qq.com/user/get_user_info';
		$params = array(
				'oauth_consumer_key' => QQ_OAUTH_CONSUMER_KEY,
				'oauth_nonce' => QQ_OAUTH_NONCE,
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp' => QQ_TIMESTAMP,
				'oauth_token' => $token['oauth_token'],
				'oauth_version' => '1.0',
			);
		$params['openid'] = $token['openid'];
		ksort($params); #按照字典排序
		$params['oauth_signature'] = $this->make_sign('GET', $url, $params, $token['oauth_token_secret']);
		$url .= '?' . http_build_query($params);
		$str = file_get_contents($url);
		$result = false;		
		is_string($str) && $result = json_decode($str, true);
		return $result;
	}
}
?>