<?php
	require_once ("../inc/conn.php");
    $page = be("get", "page");
    $query = $_SERVER['QUERY_STRING'];
    
    if (app_arttopicviewtype == 0 || app_arttopicviewtype == 3){
        $ID1 = replaceStr($query, "." . app_artsuffix . "", "");
        $page = $ID1;
    }
    else if (app_arttopicviewtype == 1){
        $page = be("get", "page");
    }
    else{
    }
    if (!isNum($page)){ $page = 1;} else { $page = intval($page); }
    if ($page < 1){ $page = 1;}
    $mac["page"] = $page;
    $t1 = root . "template/" . app_templatedir . "/" . app_htmldir . "/arttopic.html";
    $template->html = getFileByCache("template_arttopic", $t1);
    $template->topicpagelist();
    $template->pageshow();
    $template->mark();
    $template->ifEx();
    $template->run();
    echo $template->html;
    dispseObj();
?>