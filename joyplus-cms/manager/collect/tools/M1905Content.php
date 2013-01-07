<?php
/**
 * Description of M1905Content
 *
 * @author gaven
 */
require_once dirname(__FILE__).'\ContentManager.php';
class M1905Content {
    public function parseAndroidVideoUrl($url, $p_coding, $p_script) {
        $content = getPage($url, $p_coding);
//        var_dump($content);
        return $this->parseAndroidVideoUrlByContent($content, $p_coding, $p_script);
    }

    public function parseAndroidVideoUrlByContent($content, $p_coding, $p_script) {
        $iosurl = base64_decode(getBody($content, '["iosurl"] '."= '","'"));
        $num = strpos($iosurl, '.');
        $iosurl = substr($iosurl,0,$num);
        $type = trim(getBody($content, "videotype='", "'"));
        if($type = 'vod' OR $type = 'video')
        {
            $videosrc = MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.'http://mp4i.vodfile.m1905.com/movie'.$iosurl.'.mp4';
            $videosrc .= MovieType::VIDEO_SEP_VERSION.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.'http://m3u8i.vodfile.m1905.com/movie'.$iosurl.'.m3u8';
        }
        return $videosrc;
    }
    private function getAndroidVideoUrl($obj, $p_coding, $p_script) {
        return "";
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
