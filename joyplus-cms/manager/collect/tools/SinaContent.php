<?php

/**
 * Description of SinaContent
 *
 * @author gaven
 */

require_once (dirname(__FILE__) . "/ContentManager.php");

class SinaContent extends Content {
    const BASE_URL="http://v.iask.com/v_play_ipad.php?vid={vid}";
    private $contentparmStart = "ipad_vid:'"; //
    private $contentparaend = "',";

    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
        $content = getPage($url, $p_coding);
//        var_dump($content);
        return $this->parseAndroidVideoUrlByContent($content, $p_coding, $p_script);
    }

    public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script) {
        $vid = getBody($content, $this->contentparmStart, $this->contentparaend);
        return $this->getAndroidVideoUrl($vid);
    }

    public function getAndroidVideoUrl($vid) {
        $videoAddressUrl = "";
        if (isset($vid) && !is_null($vid)) {
            $videoAddressUrl = replaceStr(SinaContent::BASE_URL, "{vid}", $vid);            
             $videoAddressUrl =  MovieType::HIGH_CLEAR . MovieType::VIDEO_NAME_URL_SEP.$videoAddressUrl ;
        }
         var_dump($videoAddressUrl);
        return $videoAddressUrl;
    }

    public function checkHtmlCanPlay($url,$p_coding){
        $content = getPage($url, $p_coding);
        return false;
    }

    private $p_videourlstart = "ipad_vid:'";
    private $p_videourlend = "',";  

    public function parseIOSVideoUrl($url,$p_coding,$p_script){
       return "";
    }

    public function parseIOSVideoUrlByContent($url,$p_coding,$p_script){
        return "";
    }

}

?>
