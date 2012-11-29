<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("collect_vod_cjVideoUrl.php");
//chkLogin();

//$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest, m.m_type as m_type,
//    	        m.m_typeid as m_typeid 
//    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
//                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id  AND (url.iso_video_url IS NUaL OR url.iso_video_url ='') and m.m_id=13079";
//
////parseVideoUrlsByMovieId('13079');
$p_ids = be("all","p_id");
//parseVideoTypes($p_ids);
//echo $p_ids;
writetofile("parseVideo.txt", $p_ids);
parseVideoUrlsByProjectIdNullUrls($p_ids);


?>