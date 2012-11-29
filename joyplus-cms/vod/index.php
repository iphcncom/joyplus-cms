<?php
	require_once ("../inc/conn.php");
    $query = $_SERVER['QUERY_STRING'];
    
    if (app_vodcontentviewtype == 0 || app_vodcontentviewtype == 3){
        $ID1 = replaceStr($query, "." . app_vodsuffix . "", "");
        $ID2 = explode( "-",$ID1);
        $id = $ID2[0];
    }
    else if (app_vodcontentviewtype == 1){
        $id = be("get", "id");
    }
	else{
	}
    if (!isNum($id)){ showMsg ("请勿传递非法参数！", "../"); }
    $sql = "SELECT d_id,d_name,d_subname,d_enname,d_type,d_state,d_color,d_pic,d_starring,d_directed,d_area,";
    $sql = $sql . "d_year,d_language,d_level,d_stint,d_hits,d_dayhits,d_weekhits,d_monthhits,d_topic,d_content,";
    $sql = $sql . "d_remarks,d_good,d_bad,d_score,d_scorecount,d_addtime,d_time,d_playfrom,d_playserver,d_playurl,d_downurl FROM {pre}vod WHERE d_hide=0 and d_id=" . $id;
    $row = $db->getRow($sql);
    if (!$row){ showMsg ("找不到此数据", "../"); }
    $mac["vodid"] = intval($id);
    $mac["vodtypeid"] = $row["d_type"];
    if (app_user == 1){
        if (!getUserPopedom($mac["vodtypeid"], "vod")){ showMsg ("您没有权限浏览此内容!", "../user/"); }
    }
    $typearr = getValueByArray($cache[0], "t_id", $mac["vodtypeid"]);
    $template->loadvod ($row,$typearr, "view");
    unset($row);
    echo $template->html;
    dispseObj();
?>