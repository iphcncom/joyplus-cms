<?php
/**
 * Description of M1905Content
 *
 * @author gaven
 */
require_once dirname(__FILE__).'/ContentManager.php';
class M1905Content {
    private $p_code='utf-8';
    public function parseAndroidVideoUrl($url, $p_coding, $p_script) {
        $content = getPage($url, $this->p_code);
        return $this->parseAndroidVideoUrlByContent($content, $this->p_code, $p_script);
    }

    public function parseAndroidVideoUrlByContent($content, $p_coding, $p_script) {
        $iosurl = base64_decode(getBody($content, '["iosurl"] '."= '","'"));
        $iosurl = strstr($iosurl, '.', true);
        $type = trim(getBody($content, "videotype='", "'"));
        if($type = 'vod' || $type = 'video')
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
