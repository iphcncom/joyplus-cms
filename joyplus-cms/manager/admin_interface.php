<?php
require_once ("admin_conn.php");
require_once ("../inc/pinyin.php");

$action = be("all","action");
$pass = be("all","pass");
$safepass="111222";

switch($action)
{
    case "retype":  chkLogin();headAdmin ("分类转换配置"); retype();break;
    case "retypesave": retypesave();break;
    case "vod": vod();break;
    case "art": art();break;
    default: chkLogin();headAdmin ("接口配置"); main();break;
}
dispseObj();

function gettypere($flag,$tName)
{
	global $cache;
    $res = 0;
    if ($flag=="art"){
        $typearr = $cache[1];
        $file = "../inc/dim_retype_art.txt";
    }
    else{
        $typearr = $cache[0];
        $file = "../inc/dim_retype_vod.txt";
	}
    
    $str = file_get_contents($file);
    if (!isN($str)){
        $str = replaceStr($str, Chr(10), Chr(13));
        $arr1 = explode(Chr(13),$str);
        
        for ($i=0;$i<count($arr1);$i++){
            if (!isN($arr1[$i])){
                $str1 = $arr1[$i];
                $arr2 = explode("=",$str1);
                
                if (trim($tName) == trim($arr2[1])){
                    foreach($typearr as $t){
                        if (trim($t["t_name"]) == trim($arr2[0])){
                            return $t["t_id"];
                            break;
                    	}
                    }
					break;
                }
            }
        }
    }
}

function vod()
{
	global $db,$safepass,$pass;
	if ($safepass != $pass){ echo "非法使用err";exit; }
	
    $d_id = be("all", "d_id");
    $d_name = be("all", "d_name"); $d_subname = be("all", "d_subname");
    $d_enname = be("all", "d_enname"); $d_type = be("all", "d_type");
    $d_state = be("all", "d_state"); $d_color = be("all", "d_color");
    $d_pic = be("all", "d_pic"); $d_starring = be("all", "d_starring");
    $d_directed = be("all", "d_directed"); $d_area = be("all", "d_area");
    $d_language = be("all", "d_language"); $d_level = be("all", "d_level");
    $d_stint = be("all", "d_stint"); $d_hits = be("all", "d_hits");
    $d_dayhits = be("all", "d_dayhits"); $d_weekhits = be("all", "d_weekhits");
    $d_monthhits = be("all", "d_monthhits"); $d_topic = be("all", "d_topic");
    $d_content = be("all", "d_content"); $d_remarks = be("all", "d_remarks");
    $d_hide = be("all", "d_hide"); $d_good = be("all", "d_good");
    $d_bad = be("all", "d_bad"); $d_usergroup = be("all", "d_usergroup");
    $d_year = be("all", "d_year"); $d_addtime = be("all", "d_addtime");
    $d_time = be("all", "d_time"); $d_playurl = be("all", "d_playurl");
    $d_playfrom = be("all", "d_playfrom"); $d_playserver = be("all", "d_playserver");
    $d_addtime = date('Y-m-d H:i:s',time()); $d_downurl = be("all","d_downurl");
    $d_time = date('Y-m-d H:i:s',time());
    
    $d_content = stripslashes($d_content);
    if (!isNum($d_usergroup)) { $d_usergroup = 0;}
    if (isN($d_hide)) { $d_hide = 0;} else {$d_hide = 1;}
    if (isN($d_name)) { echo "视频名称不能为空err"; exit;}
    if (isN($d_type)) { echo "视频分类不能为空err"; exit;}
    if (isN($d_playfrom)) { echo "视频播放器类型不能为空err";exit;}
    if (!isNum($d_level)) { $d_level = 0;}
    if (!isNum($d_hits)) { $d_hits = 0;}
    if (!isNum($d_topic)) { $d_topic = 0;}
    if (!isNum($d_stint)) { $d_stint = 0;}
    if (!isNum($d_state)) { $d_state = 0;}
    if (!isNum($d_score)) { $d_score=0;}
    if (!isNum($d_scorecount)) { $d_scorecount=0;}
    //if (isN($d_playserver)) { $d_playserver = "0";}
    if (isN($d_enname)) { $d_enname = Hanzi2PinYin($d_name); }
    if (strpos($d_enname, "*")>0 || strpos($d_enname, ":")>0 || strpos($d_enname, "?")>0 || strpos($d_enname, "\"")>0 || strpos($d_enname, "<")>0 || strpos($d_enname, ">")>0 || strpos($d_enname, "|")>0 || strpos($d_enname, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err";exit;
    }
    $d_letter = strtoupper(substring($d_enname,1));
    $rc = false;
    
    $playurlarr = explode("$$$",$d_playurl) ; $playfromarr = explode("$$$",$d_playfrom) ; $playserverarr = explode("$$$",$d_playserve);
    if (count($playurlarr) != count($playfromarr)){
    	echo "播放器类型、播放地址数量不一致,多组数据请用$$$连接err" ; exit;
    }
    
    
    $tmptype = "";
    $tmptypeid = 0;
    $tmptype = $d_type;
    if (!isNum($d_type)) { $tmptypeid = gettypere("vod", $d_type);} else{ $tmptypeid = intval($d_type);}
    if ($tmptypeid ==0) { echo $d_name . " " . $tmptype . " 没有找到转换的分类err";exit; }
    $d_type = $tmptypeid;
    
    if (!isN($d_playurl)){
        $d_playurl = replaceStr($d_playurl, chr(13), "#");
        $d_playurl = replaceStr($d_playurl, chr(10), "#");
        $d_playurl = replaceStr($d_playurl, "###", "#");
        $d_playurl = replaceStr($d_playurl, "##", "#");
    }
    if (!isN($d_downurl)){
        $d_downurl = replaceStr($d_downurl, chr(13), "#");
        $d_downurl = replaceStr($d_downurl, chr(10), "#");
        $d_downurl = replaceStr($d_downurl, "###", "#");
        $d_downurl = replaceStr($d_downurl, "##", "#");
    }
    
    
    $sql = "SELECT * FROM {pre}vod WHERE d_name ='" .$d_name. "' ";
    $row = $db->getRow($sql);
    if (!$row){
        $resultdes = "新增数据ok";
        $db->Add ("{pre}vod", array("d_name", "d_subname", "d_enname", "d_type", "d_state", "d_letter", "d_color", "d_pic", "d_starring", "d_directed", "d_area", "d_year", "d_language", "d_level", "d_stint", "d_hits", "d_topic", "d_content", "d_remarks", "d_usergroup", "d_score", "d_scorecount", "d_addtime", "d_time", "d_playurl", "d_downurl", "d_playfrom", "d_playserver"), array($d_name, $d_subname, $d_enname, $d_type, $d_state, $d_letter, $d_color, $d_pic, $d_starring, $d_directed, $d_area, $d_year, $d_language, $d_level, $d_stint, $d_hits, $d_topic, $d_content, $d_remarks, $d_usergroup,$d_score, $d_scorecount, $d_addtime, $d_time, $d_playurl, $d_downurl, $d_playfrom, $d_playserver));
    }
    else{
    	
        if(isN($d_downurl)){
        	$d_downurl = $row["d_downurl"];
        }
        
        if ($row["d_playurl"] ==$d_playurl){
            $resultdes = "无需更新播放地址ok";
        }
        else{
            $resultdes = "更新播放地址ok";
            $arr1 = explode("$$$",$row["d_playurl"]); $arr2 = explode("$$$",$row["d_playfrom"]);$arr3 = explode("$$$",$row["d_playserver"]);
            $rc = false;
            $tmpplayurl="";  $tmpplayfrom=""; $tmpplayserver="";
            
            for ($k=0;$k<count($playfromarr);$k++){
            	if ($rc){
            		$tmpplayurl = $tmpplayurl . "$$$";
            		$tmpplayfrom = $tmpplayfrom . "$$$";
            		$tmpplayserver = $tmpplayserver ."$$$";
            	}
            	if (strpos(",".$row["d_playfrom"], $playfromarr[$k]) > 0){
	            	for ($j=0;$j<count($arr2);$j++){
	            		if ($arr2[$j] == $playfromarr[$k]){
	            			$arr1[$j] = $playurlarr[$k];
	                		break;
	            		}
	            	}
	            	$tmpplayurl = $tmpplayurl . $arr1[$j];
	            	$tmpplayfrom = $tmpplayfrom . $arr2[$j];
	            	$tmpplayserver = $tmpplayserver . $arr3[$j];
	            }
            	else{
            		$tmpplayfrom = $tmpplayfrom . $playfromarr[$k];
            		$tmpplayurl = $tmpplayurl . $playurlarr[$k];
            		if (count($playserverarr) > $k){
            			$tmpplayserver = $tmpplayserver . $playserverarr[$k];
            		}
            		else{
            			$tmpplayserver = $tmpplayserver . "0";
            		}
            		$resultdes = "新增播放地址ok";
            	}
            	$rc=true;
            }
            unset($arr1);
            unset($arr2);
            unset($arr3);
		}
        $tmpplayurl = replaceStr($tmpplayurl, Chr(13), "#");
        if (strpos(",".$row["d_pic"], "http:") > 0) { } else { $d_pic= $row["d_pic"];}
        $db->Update ("{pre}vod",array("d_state","d_time","d_pic","d_playfrom","d_playurl","d_playserver","d_downurl"),array($d_state,date('Y-m-d H:i:s',time()),$d_pic,$tmpplayfrom,$tmpplayurl,$tmpplayserver,$d_downurl),"d_id=".$row["d_id"]);
    }
    unset($row);
    echo $resultdes;
}


function art()
{
    global $db,$safepass,$pass;
	if ($safepass != $pass){ echo "非法使用";exit; }
    
    $a_id = be("all", "a_id"); $a_title = be("all", "a_title");
    $a_subtitle = be("all", "a_subtitle"); $a_entitle = be("all", "a_entitle");
    $a_type = be("all", "a_type");$a_content = be("all", "a_content");
    $a_author = be("all", "a_author"); $a_color = be("all", "a_color");
    $a_hits = be("all", "a_hits"); $a_dayhits = be("all", "a_dayhits");
    $a_weekhits = be("all", "a_weekhits");$a_monthhits = be("all", "a_monthhits");
    $a_from = be("all", "a_from"); $a_hide = be("all", "a_hide");
    $a_addtime = be("all", "a_addtime"); $a_time = be("all", "a_time"); $a_hitstime = be("all", "a_hitstime");
    
    $a_addtime = date('Y-m-d H:i:s',time());
    $a_time = date('Y-m-d H:i:s',time());
    
    if (isN($a_title)) { echo "文章标题不能为空err"; exit;}
    if (isN($a_type)) { echo "文章分类不能为空err"; exit;}
    if (!isNum($a_hits)) { $a_hits = 0;}
    if (isN($a_hide)) { $a_hide = 0;} else{ $a_hide = 1;}
    if (isN($a_entitle)) { $a_entitle = Hanzi2PinYin($a_title); }
    
    if (strpos($a_entitle, "*")>0 || strpos($a_entitle, ":")>0 || strpos($a_entitle, "?")>0 || strpos($a_entitle, "\"")>0 || strpos($a_entitle, "<")>0 || strpos($a_entitle, ">")>0 || strpos($a_entitle, "|")>0 || strpos($a_entitle, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err"; exit;
    }
    $a_letter = strtoupper(substring($a_entitle,1));
    if (!isNum($a_type)) { $a_type = gettypere("art", $a_type);}
    if ($a_type== 0) { echo "没有找到转换的分类err";exit;}
    
    $sql = "SELECT * FROM {pre}art WHERE a_title ='" . $a_title . "' ";
    $row = $db->getRow($sql);
    if(!$row){
        $db->Add ("{pre}art", array("a_title", "a_subtitle", "a_entitle", "a_type","a_letter" ,"a_content", "a_author", "a_color", "a_from", "a_hits", "a_addtime", "a_time"), array($a_title, $a_subtitle, $a_entitle, $a_type, $a_letter, $a_content, $a_author, $a_color, $a_from, $a_hits, $a_addtime, $a_time));
    }
    else{
        $db->Update ("{pre}art", array("a_content"),array($a_content),"a_id=".$row["a_id"]);
    }
    unset($row);
    echo "ok";
}

function retypesave()
{
    $vodtype = be("post", "vodtype");
    $arttype = be("post", "arttype");
    $oldpass = be("post", "oldpass");
    $temppass = be("post", "temppass");
    
    fwrite(fopen("../inc/dim_retype_vod.txt","wb"),$vodtype);
    fwrite(fopen("../inc/dim_retype_art.txt","wb"),$arttype);
    
    $fc = file_get_contents("admin_interface.php");
    $fc = replaceStr($fc,"safepass=\"".$oldpass."\"","safepass=\"".$temppass."\"");
    fwrite(fopen( "admin_interface.php" ,"wb"),$fc);
    showMsg ("修改完毕", "admin_interface.php?action=retype");
}

function retype()
{
    $fc1 = file_get_contents("../inc/dim_retype_vod.txt");
    $fc2 = file_get_contents("../inc/dim_retype_art.txt");
	$fc3 = file_get_contents("admin_interface.php");
	$temppass= regMatch($fc3,"safepass=\"(\S*?)\"");
?>
<form action="?action=retypesave" method="post">
<table class="tb">
<tr class="thead"><th colspan="2">此功能主要用于第三方工具（火车头、ET等）入库接口转换。>>> 1.每个各占一行; 2.本地分类在前,采集分类在后(动作片=动作).;3.不要有多余的空行</th></tr>
<tr><td width="50%">视频分类转换</td>
<td width="50%">文章分类转换</td>
</tr>
<tr>
    <td>
    <textarea id="vodtype" name="vodtype" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc1?></textarea>
    </td>
    <td>
    <textarea id="arttype" name="arttype" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc2?></textarea>
    </td>
    </tr>
    <tr>
    <td align="center" colspan="2">入库免登录安全验证密码:<input id="oldpass" name="oldpass" type="hidden" value="<?php echo $temppass?>"><input id="temppass" name="temppass" size="20" value="<?php echo $temppass?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="btnSave" name="btnSave" value="保存" class="input" /> </td>
    </tr>
</table>
</form>
</body>
</html>
<?php
}

function main()
{}
?>