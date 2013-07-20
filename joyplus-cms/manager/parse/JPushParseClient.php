<?php
class JPushParseClient {
	
	static $myParse = null;
	
	//yuebaoceshi
	private $appid = '8c12e780e3843ae00a7f9127';
	private $restkey ='8323f85bdca0b89b48dd9b42';
	
	
	//allinone
//	 private $appid = '4pTBrAFZFPXUVnFCkMjRy3nZQDBTdfhj0HfgDme1';
//	private $restkey = 'byKREBb2K6ZBXbVtdAK1RckmOBlS0o2QVPmDuuLa';
	
	private $parseUrl = 'https://api.parse.com/1/classes/';
	private $notificationUrl = 'http://api.jpush.cn:8800/sendmsg/v2/sendmsg';
	
//	private $notificationUrl = 'http://localhost:8080/1/push';
//private $parseUrl = 'http://localhost:8080/1/classes/';
	private $parseLogin = 'https://api.parse.com/1/login';
	
	// Single Instance
	static function getInstance() {
		if (is_null ( self::$myParse )) {
			self::$myParse = new JPushParseClient ();
		}
		return self::$myParse;
	}
	
	public function setAppID($app_id){
		$this->appid=$app_id;
	}
	
	public function setRestKey($restKey){
		$this->restkey=$restKey;
	}
	/*
	 * All requests go through this function There are functions that filter all
	 * the different request types No need to use this function directly
	 */
	private function request($args, $type = 'Object') {
		$c = curl_init ();
		curl_setopt ( $c, CURLOPT_TIMEOUT, 10 );
		curl_setopt ( $c, CURLOPT_USERAGENT, 'parseRestClient/1.0' );
		curl_setopt ( $c, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $c, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $c, CURLOPT_RETURNTRANSFER, true );
//		curl_setopt ( $c, CURLOPT_HTTPHEADER, array (
//				'Content-Type: application/json',
//				'X-Parse-Application-Id: ' . $args ['appid'],
//				'X-Parse-REST-API-Key: ' .  $args ['restkey'] 
//		) );
		curl_setopt ( $c, CURLOPT_CUSTOMREQUEST, $args ['method'] );
		curl_setopt ( $c, CURLOPT_URL, $this->parseUrl . $args ['url'] );
		
	    if ('Notifications' == $type) {
				curl_setopt ( $c, CURLOPT_URL, $this->notificationUrl );
				$postData = json_encode ( $args ['payload'] );
//				var_dump($postData);
			    curl_setopt ( $c, CURLOPT_POSTFIELDS, $postData );
				
		} else if ($args ['method'] == 'PUT' || $args ['method'] == "POST") {
			$postData = json_encode ( $args ['payload'] );
			curl_setopt ( $c, CURLOPT_POSTFIELDS, $postData );
		} else {
			$postData = array ();
			if (isset ( $args ['query'] )) {
				$postData ['where'] = json_encode ( $args ['query'] );
			}
			if (isset ( $args ['order'] )) {
				$postData ['order'] = $args ['order'];
			}
			if (isset ( $args ['limit'] )) {
				$postData ['limit'] = $args ['limit'];
			}
			if (isset ( $args ['skip'] )) {
				$postData ['skip'] = $args ['skip'];
			}
		if (isset ( $args ['count'] )) {
			$postData ['count'] = $args ['count'];
		}
			if (count ( $postData ) > 0) {
				$query = http_build_query ( $postData, '', '&' );
				// dump($this->parseUrl . $args ['url'] . '?' . $query);
				curl_setopt ( $c, CURLOPT_URL, $this->parseUrl . $args ['url'] . '?' . $query );
			} else if ('Login' == $type) {
				$postData ['username'] = $args ['username'];
				$postData ['password'] = $args ['password'];
				$query = http_build_query ( $postData, '', '&' );
				curl_setopt ( $c, CURLOPT_URL, $this->parseLogin . '?' . $query );
			}
		}
		
		$response = curl_exec ( $c );
//		var_dump($response);
		$httpCode = curl_getinfo ( $c, CURLINFO_HTTP_CODE );
		return array (
				'code' => $httpCode,
				'response' => $response 
		);
	}
	
	/*
	 * Login
	 */
	public function login($args) {
		$params = array (
				'username' => $args ['username'],
				'password' => $args ['password'],
				'method' => 'GET' 
		);
		
		$return = $this->request ( $params, 'Login' );
		if ($this->checkResponse ( $return, '200' )) {
			$user = json_decode ( $return ['response'] );
			$merchant = obj2arr ( $user->merchant );
			session ( 'user', $user );
			session ( 'merchant', $merchant );
		} else {
			return false;
		}
		return true;
	}
	
	/*
	 * Used to get a parse.com object @param array $args - argument hash:
	 * className: string of className objectId: (optional) the objectId of the
	 * object you want to update. If none, will return multiple objects from
	 * className @return string $return
	 */
	public function get($args) {
		$params = array (
				'url' => $args ['className'] . '/' . $args ['objectId'],
				'method' => 'GET' 
		);
		
		$return = $this->request ( $params );
		
		return $this->checkResponse ( $return, '200' );
	}
	
	public function create($args) {
		$params = array (
				'url' => $args ['className'],
				'method' => 'POST',
				'payload' => $args ['object'] 
		);
		$return = $this->request ( $params );
		
		return $this->checkResponse ( $return, '201' );
	
	}
	
	/*
	 * Used to update a parse.com object @param array $args - argument hash:
	 * className: string of className objectId: the objectId of the object you
	 * want to update object: object to update in place of old one @return
	 * string $return
	 */
	public function update($args) {
		$params = array (
				'url' => $args ['className'] . '/' . $args ['objectId'],
				'method' => 'PUT',
				'payload' => $args ['object'] 
		);
		
		$return = $this->request ( $params );
		
		return $this->checkResponse ( $return, '200' );
	}
	
   public function push($args,$appid,$restkey) {
		$params = array (
				'url' => '',
				'method' => 'POST',
				'payload' => $args ,
				'appid' => $appid ,
				'restkey' => $restkey , 
		);
		
		$return = $this->request ( $params ,'Notifications');
//		var_dump($return);
		return $return;
	}
	
	/*
	 * Used to query parse.com. @param array $args - argument hash: className:
	 * string of className query: array containing query. See:
	 * https://www.parse.com/docs/rest#data-querying order: (optional) used to
	 * sort by the field name. use a minus (-) before field name to reverse sort
	 * limit: (optional) limit number of results skip: (optional) used to
	 * paginate results @return string $return
	 */
	
	public function query($args) {
		$params = array (
				'url' => $args ['className'],
				'method' => 'GET' 
		);
		
		if (isset ( $args ['query'] )) {
			$params ['query'] = $args ['query'];
		}
		if (isset ( $args ['order'] )) {
			$params ['order'] = $args ['order'];
		}
		if (isset ( $args ['limit'] )) {
			$params ['limit'] = $args ['limit'];
		}
		if (isset ( $args ['skip'] )) {
			$params ['skip'] = $args ['skip'];
		}
	if (isset ( $args ['count'] )) {
			$params ['count'] = $args ['count'];
		}
		$return = $this->request ( $params );
		if ($this->checkResponse ( $return, '200' )) {
			return json_decode ( $return ['response'] );
		} else {
			return null;
		}
	
	}
	
	/*
	 * Used to get a parse.com object @param array $args - argument hash:
	 * className: string of className objectId: (optional) the objectId of the
	 * object you want to update. If none, will return multiple objects from
	 * className @return string $return
	 */
	public function delete($args) {
		$params = array (
				'url' => $args ['className'] . '/' . $args ['objectId'],
				'method' => 'DELETE' 
		);
		
		$return = $this->request ( $params );
		
		return $this->checkResponse ( $return, '200' );
	}
	
	/*
	 * Checks for correct/expected response code. @param array $return, string
	 * $code @return string $return['response]
	 */
	private function checkResponse($return, $code) {
		// TODO: Need to also check for response for a correct result from
		// parse.com
		if ($return ['code'] != $code) {
			$error = json_decode ( $return ['response'] );
			// die ( 'ERROR: response code was ' . $return ['code'] . ' with
			// message: ' . $error->error );
			return false;
		} else {
			return true;
		}
	}
}

?>
