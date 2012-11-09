<?php

function obj2arr($array) {
	if (is_object ( $array )) {
		$array = ( array ) $array;
	}
	if (is_array ( $array )) {
		foreach ( $array as $key => $value ) {
			$array [$key] = obj2arr ( $value );
		}
	}
	return $array;
}

function parseDate($date) {
	return array (
			'__type' => 'Date',
			'iso' => $date . 'T00:00:00.000Z' 
	);
}

function parsePointer($className, $objectId) {
	return array (
			'__type' => 'Pointer',
			'className' => $className,
			'objectId' => $objectId 
	);
}

// Json简单模型返回
function jResp($sts=false,$desc='unknown'){
	return json_encode ( array (
			'sts' => $sts,
			'des' => $desc
	) );
}
// 页面标题位置
function outputTitle($moduleName) {
	$list = C ( 'PAGE_SUB_TITLE' );
	return $list [$moduleName];
}

// 登录权限检测
function checkAuth() {
	if (! session ( '?user' )) {
		redirect(__APP__ . '/Login/Index');
	}
}

// 根据Merchant获取（缓存）列表
function getListByMerchant($className, $merchant) {
	$list = S ( 'List_' . $className );
	if (! $list || S ( 'List_' . $className . '_Refresh' )) {
		$args = array (
				'className' => $className,
				'query' => array (
						'merchant' => $merchant,
						'delFlg' => false 
				) 
		);
		$result = ParseClient::getInstance ()->query ( $args );
		$list = obj2arr ( $result->results );
		S ( 'List_' . $className, $list, C ( 'CACHE_TIMEOUT' ) );
		S ( 'List_' . $className . '_Refresh', false );
	}
	return $list;
}

// 根据Merchant和另外一个指针字段获取（缓存）列表
function getListByPointer($className, $merchant, $pointType, $pointId) {
	$listStr = 'List_' . $className . '_' . $pointType . '_' . $pointId;
	$refreshStr = $listStr . '_Refresh';
	$list = S ( $listStr );
	var_dump($pointId);
	var_dump($list);
	var_dump(S ( $refreshStr ));
	if (! $list || S ( $refreshStr )) {
		$args = array (
				'className' => $className,
				'query' => array (
						strtolower ( $pointType ) => parsePointer ( $pointType, $pointId ),
						'merchant' => $merchant,
						'delFlg' => false 
				) 
		);
		$result = ParseClient::getInstance ()->query ( $args );
		$list = obj2arr ( $result->results );
		S ( $listStr, $list, C ( 'CACHE_TIMEOUT' ) );
		S ( $refreshStr, false );
	}else{
		dump('cache mode');
	}
	return $list;
}
// 新增记录
function addRecordByClass($className, $data) {
	$args = array (
			'className' => $className,
			'object' => $data 
	);
	if (ParseClient::getInstance ()->create ( $args )) {
		S ( 'List_' . $className . '_Refresh', true );
		return true;
	}
	return false;
}
// 编辑记录
function updateRecordByClass($className, $objectId, $data) {
	$args = array (
			'className' => $className,
			'objectId' => $objectId,
			'object' => $data 
	);
	if (ParseClient::getInstance ()->update ( $args )) {
		S ( 'List_' . $className . '_Refresh', true );
		return true;
	}
	return false;
}
?>