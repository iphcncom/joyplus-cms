<?php
session_start();
header("Content-Type:text/html;Charset=utf-8");
require_once ("config.php");
require_once ("ftp.php");
require_once ("class.php");
require_once ("function.php");
require_once ("template.php");
initObj();
$db = new AppDataBase(app_dbserver,app_dbuser,app_dbpass,app_dbname);
$channels = array('sss');   
$template = new AppTemplate();
$mac = array("vodid"=>-1,"vodnum"=>-1,"vodsrc"=>-1,"vodtypeid"=>-1,"vodtypepid"=>-1,"vodtopicid"=>-1,"arttypeid"=>-1,"arttypepid"=>-1,"arttopicid"=>-1,"flag"=>"","type"=>"","key"=>"","keytype"=>"","ids"=>"","pinyin"=>"","starring"=>"","directed"=>"","area"=>"","language"=>"","year"=>"","des"=>"","keyword"=>"","letter"=>"","where"=>"","order"=>"","by"=>"","listorder"=>false,"page"=>1,"curviewtype"=>1);
$cache=getGlobalCache("cache","php");//视频分类0，文章分类1，视频专题2，文章专题3，地区4，语言5，用户组6,//直播国家7，//直播地区 8，//直播节目类别 9
//var_dump($cache);
?>