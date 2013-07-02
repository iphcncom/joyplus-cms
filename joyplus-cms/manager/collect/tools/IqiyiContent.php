<?php

/**
 * Description of Iqiyi
 *
 * @author gaven
 */
require_once dirname(__FILE__).'/ContentManager.php';
class IqiyiContent {
    const API = 'http://cache.m.iqiyi.com/mt/{aid}/';//621409
    private $p_code = 'utf-8';
    public function parseAndroidVideoUrl($url, $p_coding, $p_script) {
        $content = getPageWindow($url, $this->p_code);
        writetofile("iqiyi.log", "page url:".$url);
        return $this->parseAndroidVideoUrlByContent($content, $this->p_code, $p_script);
    }

    public function parseAndroidVideoUrlByContent($content, $p_coding, $p_script) {
        $aid = getBody($content, '"tvId":"', '",');
        if(isN($aid)){
        	$aid = getBody($content, 'data-player-tvid="', '"');
        }
        $api = $url = replaceStr(IqiyiContent::API,"{aid}",$aid);
        $json = getPageWindow($api, $p_coding);
      //  writetofile("iqiyi.log", $json);
        return $this->getAndroidVideoUrl(json_decode($json), $p_coding, $p_script);
    }

    private function getAndroidVideoUrl($obj, $p_coding, $p_script) {
    	//var_dump($obj);
        $videoAddressUrl="";
        if(!is_object($obj) || !is_object($obj->data))
        {
            return true;
        }
        /* mp4 && m3u8  */
        if(property_exists($obj->data, 'mpl'))
        {
            foreach ($obj->data->mpl as $mpl)
            {
                /* mp4 */
                switch($mpl->vd)
                {
                    case 1:
                        $strBody = getPageWindow($mpl->m4u,'utf-8');
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::Liu_Chang.MovieType::VIDEO_NAME_URL_SEP.getBody($strBody, 'data:{"l":"', '"');
                        break;
                    case 2:
                        $strBody = getPageWindow($mpl->m4u,'utf-8');
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.getBody($strBody, 'data:{"l":"', '"');
                        break;
                }
            }
            /*  m3u8 */
            foreach ($obj->data->mpl as $mpl)
            {
                 switch($mpl->vd)
                {
                    case 1:
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::Liu_Chang.MovieType::VIDEO_NAME_URL_SEP.$mpl->m3u;
                        break;
                    case 2:
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$mpl->m3u;
                        break;
                }
            }
        }
        /* m3u8 */
        if(property_exists($obj->data, 'mtl'))
        {
            foreach ($obj->data->mtl as $mtl) {
                switch ($mtl->vd)
                {
                    case 1:
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::Liu_Chang.MovieType::VIDEO_NAME_URL_SEP.$mtl->m3u;
                        break;
                    case 2:
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$mtl->m3u;
                        break;
                    case 3:
                        $videoAddressUrl .= MovieType::VIDEO_SEP_VERSION.MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$mtl->m3u;
                        break;
                }
            }
        }
        if(strpos($videoAddressUrl, "{mType}") !==false && strpos($videoAddressUrl, "{mType}")===0){
           $videoAddressUrl= substr($videoAddressUrl, 7);
        }
        writetofile("iqiyi.log", "down url:".$videoAddressUrl);
        return $videoAddressUrl;
    }

    public function checkHtmlCanPlay($url, $p_coding) {
        return true;
    }

    public function parseIOSVideoUrl($url, $p_coding, $p_script) {
        return "";
    }

    public function parseIOSVideoUrlByContent($content, $p_coding, $p_script) {
        return "";
    }

}
?>
