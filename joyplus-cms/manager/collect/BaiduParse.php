<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
class BaiduParse{
	const contentparmStart="var aldJson = ["; //
	const contentparaend="];";
	const contentparmListStart="({"; //
	const contentparaListend="})";
	const cookieStart="Set-Cookie: "; //
	const cookieend=";";
	const BASE_EPISODE="http://video.baidu.com/htvplaysingles/?id={ID}&site={SITE_URL}";
	const BASE_SHOW_EPISODE="http://video.baidu.com/htvshowsingles/?id={ID}&site={SITE_URL}&year={YEAR}";
	//http://video.baidu.com/hcomicsingles/?id=2758&site=pptv.com&callback=bd__cbs__bbtz3q
	const BASE_COMIC_EPISODE="http://video.baidu.com/hcomicsingles/?id={ID}&site={SITE_URL}";

	static function parseMovieListByUrl($url,$p_code,$type){
		$content = getPage($url, $p_code);
		return BaiduParse::parseMovieListByContent($content, $p_code,$type);
	}
	static function parseMovieListByContent($content,$p_code,$type){
		$contentSt= "{".getBody($content, BaiduParse::contentparmListStart, BaiduParse::contentparaListend).'}';
		//  	 	var_dump($contentSt);
			
		$content=json_decode($contentSt);//var_dump($contentSt);
		if(is_object($content)&& property_exists($content, 'videoshow')&& property_exists($content->videoshow, 'videos')){
			//var_dump($content->videoshow->videos);
			$videos=$content->videoshow->videos;
			if(is_array($videos)){
				$linkarr= array();
				$starringarrcode= array();
				$titlearrcode= array();
				$picarrcode= array();
				$areaarrcode= array();
				$typearrcode= array();
				$yeararrcode= array();
				foreach ($videos as $video){
					//  	 			  	var_dump($video);

					if(property_exists($video, 'url')){
						$linkarr[]=$video->url;
					}else {
						continue;
					}

					if(property_exists($video, 'title')){
						$titlearrcode[]=$video->title;
					}else {
						$titlearrcode[]='';
					}

					if(property_exists($video, 'date')){
						$yeararrcode[]=$video->date;
					}else {
						$yeararrcode[]='';
					}

					if(property_exists($video, 'actor')){
						$starringarrcode[]=BaiduParse::parseArrayObjectToString($video->actor,'name');
					}else {
						$starringarrcode[]='';
					}

					if(property_exists($video, 'area')){
						$areaarrcode[]=BaiduParse::parseArrayObjectToString($video->area,'name');
					}else {
						$areaarrcode[]='';
					}

					if(property_exists($video, 'type')){
						$typearrcode[]=BaiduParse::parseArrayObjectToString($video->type,'name');
					}else {
						$typearrcode[]='';
					}


				}
					
				return array(
  	 			   'linkarr'=>$linkarr,
  	 			   'typearr'=>$typearrcode,
  	 			   'areaarr'=>$areaarrcode,
  	 			   'starringarr'=>$starringarrcode,
  	 			   'yeararr'=>$yeararrcode,
  	 			   'titlearr'=>$titlearrcode,
  	 			   'picarr'=>$picarrcode,
				);
			}
		}
	}

	static function parseArrayObjectToString($array,$key){
		$temp= array();
		if(is_array($array)){
			foreach ($array as $item){
				//	  			var_dump($item);
				if(property_exists($item, $key)){
					$temp[]=$item->$key;
				}
			}
			return implode(" ", $temp);
		}
		return "";
	}

	static function parseMovieInfoByUrl($url,$p_code,$type){
		$content = getPage($url, $p_code);
		return BaiduParse::parseMovieInfoByContent($content, $p_code,$type);
	}
	static function slang($num){
		if($num ==='1'){
			return '一';
		}
		if($num ==='2'){
			return '二';
		}
		if($num ==='3'){
			return '三';
		}
		if($num ==='4'){
			return '四';
		}
		if($num ==='5'){
			return '五';
		}
		if($num ==='6'){
			return '六';
		}
		if($num ==='7'){
			return '七';
		}
		if($num ==='8'){
			return '八';
		}
		if($num ==='9'){
			return '九';
		}
		return "";
	}

	static function lang($num){
		if(strlen($num) ===1){
			return BaiduParse::slang($num);
		}
		if(strlen($num) ===2){
			return BaiduParse::slang(substr($num, 0,1)).'十'.BaiduParse::slang(substr($num, 1,1));
		}
	}


	static function parseMovieInfoByContent($content,$p_code,$type){
		$contentSt= getBody($content, BaiduParse::contentparmStart, BaiduParse::contentparaend);
		$content=json_decode($contentSt);
		$info= new VideoInfo();
		if(is_object($content)){
			$info->max_episode=property_exists($content, 'max_episode')?$content->max_episode:"";
			$info->curr_episode=property_exists($content, 'cur_episodes')?$content->cur_episodes:"";
			if(isN($info->curr_episode)){
				$info->curr_episode=property_exists($content, 'episode')?$content->episode:"";
			}//
			$info->title=property_exists($content, 'title')?$content->title:"";
			if (property_exists($content, 'season_num') ){
				$info->season_num=$content->season_num;
			}
			if (!isN($content->season_num)){
				$content->season_num= BaiduParse::lang($num);
					
				if (!isN($content->season_num)){
					$info->title=$info->title.' 第' .$content->season_num.'季';
				}
			}
			$info->language=property_exists($content, 'language')?$content->language:"";

			$info->update_freq=property_exists($content, 'update_freq')?$content->update_freq:"";
			$info->duration=property_exists($content, 'duration')?$content->duration:"";

			$info->big_poster="";//property_exists($content, 'big_poster')?$content->big_poster:""; //
			$info->director=property_exists($content, 'director')?BaiduParse::parseArrayToString($content->director):"";
			$info->area=property_exists($content, 'area')?BaiduParse::parseArrayToString($content->area):"";
			$info->type=property_exists($content, 'type')?BaiduParse::parseArrayToString($content->type):"";
			$info->actor=property_exists($content, 'actor')?BaiduParse::parseArrayToString($content->actor):"";
			if(isN($info->actor)){
				$info->actor=property_exists($content, 'host')?BaiduParse::parseArrayToString($content->host):"";
			}
			if(isN($info->director)){
				$info->director=property_exists($content, 'station')?BaiduParse::parseArrayToString($content->station):"";
			}
			if(isN($info->actor)){//角色
				$info->actor=property_exists($content, 'leader')?BaiduParse::parseArrayToString($content->leader):"";
			}
			if(isN($info->director)){//声优
				$info->director=property_exists($content, 'dub')?BaiduParse::parseArrayToString($content->dub):"";
			}
			$info->alias=property_exists($content, 'alias')?BaiduParse::parseArrayToString($content->alias):"";
			$info->brief=property_exists($content, 'brief')?$content->brief:"";
			if(!isN($info->alias)){
				$info->brief=$info->brief .'  ' .$info->alias;
			}
			if(!isN($info->update_freq)){
				$info->brief=$info->brief.'  更新频率：' .$info->update_freq;
			}
			$info->id=property_exists($content, 'id')?$content->id:"";
			$info->pubdate=property_exists($content, 'al_date')?$content->al_date:"";
			if(isN($info->pubdate)){
				$info->pubdate=property_exists($content, 'pubtime')?$content->pubtime:"";
			}
			if(isN($info->pubdate)){
				$info->pubdate=property_exists($content, 'years')?BaiduParse::parseArrayToString($content->years):"";
			}

			$info->sites=property_exists($content, 'sites')?BaiduParse::parseSitesUrl($content->sites,$info->id,$type,property_exists($content, 'years')?BaiduParse::parseArrayToString($content->years):$info->pubdate,$p_code):"";
		}else {
			$vedio_id=getBody($contentSt, "id: '", "'");
			$info->id=$vedio_id;
			if($type ===1 || $type ==='1') {
				$url='http://video.baidu.com/movie_intro/?dtype=playUrl&service=json&id='.$vedio_id;
			}else if($type ===2 || $type ==='2'){
				$url='http://video.baidu.com/movie_intro/?dtype=tvPlayUrl&service=json&id='.$vedio_id;
			}else if($type ===3 || $type ==='3'){
				$url='http://video.baidu.com/show_intro/?dtype=tvshowPlayUrl&service=json&id='.$vedio_id;
			}else if($type ===131 || $type ==='131'){
				$url='http://video.baidu.com/comic_intro/?dtype=comicPlayUrl&service=json&id='.$vedio_id;
			}

			writetofile("baiducontent.log","request url:".$url);

			$playUrlsContent = getPage($url, 'gb2312');
			writetofile("baiducontent.log","request content:".$playUrlsContent);

			$content=json_decode($playUrlsContent);

			if($type ===1 || $type ==='1') {
				$info->sites=BaiduParse::parseArrayMovie($content);
			}else if($type ===2 || $type ==='2'){
				$info->sites=BaiduParse::parseArrayTV($content);
			}else if($type ===3 || $type ==='3'){
				$info->sites =BaiduParse::parseArrayShow($content);
			}else if($type ===131 || $type ==='131'){
				$info->sites=BaiduParse::parseArrayTV($content);
			}

		}
		//	 	var_dump($info->sites[0]);
		//  	 	var_dump('------');
		return $info;
	}

	static function parseArrayMovie($content){
		$sites = array();
		if (is_array($content)){
			foreach ($content as $siteObject){
				$site = array();
				if (property_exists($siteObject, 'site') ){
					$site['site_url']=$siteObject->site;
				}
				if (property_exists($siteObject, 'name') ){
					$site['site_name']=BaiduParse::getSite($siteObject->name);
				}
				$site['max_episode']='false';
				if (property_exists($siteObject, 'link') ){
					$episodes = array();
					$episodes[]=array(
	   				  'name' =>'',
			          'guest' =>'',
			          'episode' => '1',
			          'url' => $siteObject->link,
			          'img_url' => '',
					);
					if(strpos($siteObject->link, "baidu.com") !==false){
						continue;
					}
					$site['episodes']=$episodes;
				}
				$sites[]=$site;
			}
		}
		return $sites;
	}
	static function obj2arr($array) {
		if (is_object ( $array )) {
			$array = ( array ) $array;
		}
		if (is_array ( $array )) {
			foreach ( $array as $key => $value ) {
				$array [$key] = BaiduParse::obj2arr ( $value );
			}
		}
		return $array;
	}
	static function parseArrayTV($sitesObject){
		$sites = array();
		if (is_array($sitesObject)){
			$sitesArray =$sitesObject;var_dump($sitesArray);
			foreach($sitesArray as $siteObject){
				$site = array();
				if(is_object($siteObject)){
					if (property_exists($siteObject, 'site_info') ){
						$site_info = $siteObject->site_info;
						if (is_object($site_info) && property_exists($site_info, 'name') ){
							$site['site_name']=BaiduParse::getSite($site_info->name);
						}
						if (is_object($site_info) && property_exists($site_info, 'site') ){
							$site['site_url']=$site_info->site;
						}
					}

					$site['max_episode']='true';
					if (property_exists($siteObject, 'episodes') ){
						$episodesArray= $siteObject->episodes;
						if(is_array($episodesArray)){
							$episodes = array();
							foreach ($episodesArray as $item) {
								$episode = array(
   							   	    'guest' =>'',
   							   	    'img_url' => '',
								);
								if (property_exists($item, 'single_title') ){
									$episode['name']=$item->single_title;
								}
								if (property_exists($item, 'url') ){
									$episode['url']=$item->url;
								}
								if(strpos($item->url, "baidu.com") !==false){
									continue;
								}
								if (property_exists($item, 'episode') ){
									$episode['episode']=$item->episode;
								}
								$episodes[]=$episode;
							}
							$site['episodes']=$episodes;
						}
					}
					$sites[]=$site;
				}
					
			}
		}
		return $sites;
	}

	static function parseArrayShow($sitesObject){
		$sites = array();
		if (is_array($sitesObject)){
			$sitesArray =$sitesObject;var_dump($sitesArray);
			foreach($sitesArray as $siteObject){
				$site = array();
				if(is_object($siteObject)){
				if (property_exists($siteObject, 'site_info') ){
						$site_info = $siteObject->site_info;
						if (is_object($site_info) && property_exists($site_info, 'name') ){
							$site['site_name']=BaiduParse::getSite($site_info->name);
						}
						if (is_object($site_info) && property_exists($site_info, 'site') ){
							$site['site_url']=$site_info->site;
						}
					}
					/*if (property_exists($siteObject, 'years') ){
						$years = $siteObject->years;
						if (is_array($years)){
							foreach ($years as $item){
								$sites['years']=$item;
							}
						}
					}
					*/
					$site['max_episode']='true';
					if (property_exists($siteObject, 'episodes') ){
						$episodesArray= $siteObject->episodes;
						if(is_array($episodesArray)){
							foreach ($episodesArray as $items) {
							$episodes = array();
								if(is_array($items)){
									foreach ($items as $item){
										$episode = array(
   							   	    'guest' =>'',
   							   	    'img_url' => '',
										);
										if (property_exists($item, 'single_title') ){
											$episode['name']=$item->single_title;
										}
										if (property_exists($item, 'url') ){
											$episode['url']=$item->url;
										}
										if(strpos($item->url, "baidu.com") !==false){
											continue;
										}
										if (property_exists($item, 'episode') ){
											$episode['episode']=$item->episode;
										}
										$episodes[]=$episode;

									}
							$site['episodes']=$episodes;
								}
							}
						}
					}
					$sites[]=$site;
				}
			}
		}
		return $sites;
	}

	static function parseArrayToString($array){
		if(is_array($array)){
			return implode(",", $array);
		}
		return "";
	}
	static function getSite($sitename){

		$PLAY_FROMS= array(
	  	   '爱奇艺'=>'qiyi',
			'搜狐'=>'sohu',
			'优酷'=>'youku',
			'土豆'=>'tudou',
			'风行网'=>'fengxing',
			'酷6'=>'ku6',
			'新浪'=>'sinahd',
			'迅雷看看'=>'kankan',
			'乐视'=>'letv',
			'腾讯'=>'qq',
			'PPS'=>'pps',
			'PPTV'=>'pptv',
			'电影网'=>'m1905',
			'CNTV'=>'cntv',
			'华数TV'=>'wasu',//56网
	  	    '56网'=>'56',//56网
	  	    '56我乐'=>'56',//56我乐
	  	    '华数TV'=>'wasu',
		) ;
		//	    if(array_key_exists($sitename,array_keys($PLAY_FROMS))){
		//	    	var_dump($PLAY_FROMS[$sitename]);
		//             return $PLAY_FROMS[$sitename];
		//     	 }else {
		//     	   return $site_name;
		//     	 }
		try{
			return $PLAY_FROMS[$sitename];
		}catch (Exception $e){
			return $sitename;
		}
	}

	static function parseSitesUrl($sites,$id,$type,$year,$p_code){
		$tempSites= array();
		if(is_array($sites)){
			foreach ($sites as $site){
				$tempSite= array();
				$site_name= property_exists($site, 'site_name')?$site->site_name:"";
				$tempSite['site_name']= BaiduParse::getSite($site_name);
				$site_url= property_exists($site, 'site_url')?$site->site_url:"";
				$tempSite['site_url']=$site_url;
				$max_episode= property_exists($site, 'max_episode')?true:false;
				$tempSite['max_episode']=$max_episode;
				$tempSites[]=$tempSite;
			}
		}
		$sites = array();
		foreach ($tempSites as $tempSite){
			//	  		var
			if($tempSite['max_episode']){
				switch($type){
					case 1 :
					case 2 :
						$url= BaiduParse::BASE_EPISODE;
						break;
					case 3 :
						$url= BaiduParse::BASE_SHOW_EPISODE;
						break;
					case 131 :
						$url= BaiduParse::BASE_COMIC_EPISODE;
						break;
					default :
						$url= BaiduParse::BASE_EPISODE;
						break;
				}
				$url = replaceStr($url, '{ID}', $id);
				$url = replaceStr($url, '{SITE_URL}', $tempSite['site_url']);

				if($type==3){
					//$yearA= explode(",", $year);
					//foreach ($yearA as $tyear){
					$turl = replaceStr($url, '{YEAR}', '2013');
					var_dump($turl);
					$temp = BaiduParse::parseSingleSiteUrls($id, $turl,$p_code);
					if(is_array($tempSite['episodes'])){
						$tempSite['episodes']= array_merge($tempSite['episodes'],$temp) ;
					}else {
						$tempSite['episodes']=$temp;
					}
					//break;
					//}
				}else {
					$tempSite['episodes'] =BaiduParse::parseSingleSiteUrls($id, $url,$p_code);
				}
			}else {
				$tempSite['episodes'] =array( array("episode" =>'1' ,
	  		                                 "url"=> $tempSite['site_url']));
			}
			$sites[]=$tempSite;
		}
		return $sites;
	}

	static function parseSingleSiteUrls($id,$site_url,$p_code){
		$content=getPage($site_url, $p_code);
		//	  	 var_dump($content);
		$content=json_decode($content);
		$playUrls = array();
		if(is_object($content)){
			$videos=property_exists($content, 'videos')?($content->videos):"";
			if(is_array($videos)){
				foreach ($videos as $item){
					$title=property_exists($item, 'title')?$item->title:"";
					$guest=property_exists($item, 'guest')?BaiduParse::parseArrayToString($item->guest):"";
					$episode=property_exists($item, 'episode')?$item->episode:"";
					$url=property_exists($item, 'url')?$item->url:"";
					//		  	     $img_url=property_exists($item, 'img_url')?$item->img_url:"";
					//		  	     if(isN($img_url)){
					//		  	        //thumbnail
					//		  	        $img_url=property_exists($item, 'thumbnail')?$item->thumbnail:"";
					//		  	     }
					$playUrl = array();
					$playUrl['name']=$title;
					$playUrl['guest']=$guest;
					$playUrl['episode']=$episode;
					$playUrl['url']=$url;
					if(strpos($url, "baidu.com") !==false){
						continue;
					}
					$playUrl['img_url']="";
					$playUrls[]=$playUrl;
				}
			}
		}
		return $playUrls;
	}

}



$find = array("+","/");
$replace = array("-", "_");
$str= base64_encode('apk2:11111');
$str='ddfafdfew+wweeweee/wweew/ewfff+eeee';
$duration = '7740';
if(is_numeric($duration)){
	$duration= intval($duration);
	$h=intval($duration/3600);
	$m=intval($duration%3600/60);
	$s=intval($duration%3600%60);
	$durations='';
	if($h>0){
		$durations=$h.':';
	}else {
		$durations='00:';
	}
	if($m<10){
		$durations=$durations.'0'.$m.':';
	}else {
		$durations=$durations.$m.':';
	}
	if($s<10){
		$durations=$durations.'0'.$s;
	}else {
		$durations=$durations.$s;
	}
}

$duration = '';
if(is_numeric($duration)){
	var_dump($duration);
	$duration= intval(intval);
}
$duration = '';

if(is_numeric($duration)){
	var_dump($duration);
	$duration= intval(intval);
}

if(is_numeric($duration2)){
	var_dump($duration);
	$duration= intval(intval);
}

//       var_dump($str);
//        var_dump(str_replace($find, $replace,$str ));
//var_dump(BaiduParse::parseMovieListByUrl("http://video.baidu.com/commonapi/tvplay2level/?callback=jQuery19105037843275615373_1368593570491&filter=true&type=&area=&actor=&start=&complete=&order=pubtime&pn=5&rating=", "gb2312",1));
//  var_dump(BaiduParse::parseMovieInfoByUrl("http://video.baidu.com/tv_intro/?id=18667&page=1&frp=browse", "gb2312",2));
//  BaiduParse::parseMovieInfoByUrl("http://video.baidu.com/show_intro/?id=386&page=1&frp=browse", "gb2312",'3');  //
// var_dump(BaiduParse::lang('0'));
//  var_dump(BaiduParse::parseMovieInfoByUrl("http://video.baidu.com/comic_intro/?id=2879&page=1&frp=browse", "gb2312",131));  //
class VideoInfo{
	public $max_episode;
	public $curr_episode;
	public $title;
	public $language;
	public $director;
	public $area;
	public $type;
	public $actor;
	public $brief;
	public $pubdate;
	public $id; //al_date
	public $sites; // site_url"/site_name
	public $playfrom;
	public $alias;
	public $update_freq;
	public $big_poster;
	public $duration;
	public $season_num;

}

?>