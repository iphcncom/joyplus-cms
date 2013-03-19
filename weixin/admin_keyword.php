<?php
require_once (dirname(__FILE__)."/inc/conn.php");
$weixin_config=require(dirname(__FILE__).'/weixin_config.php');
$action = be("all","action");

switch($action){
	
	case "save" : save();break;	
	default : headAdmin ("微信关键词配置管理"); main();break;
}

function save(){
	$keyword = be("all","keyword");
	$keyword=replaceStr($keyword, "\'", "'");
$backurl = getReferer();
	if($backurl === null || $backurl ==='' ){
		$backurl='admin_keyword.php';
	}
	$keyword='<?php return array('.$keyword.');?>';
//	var_dump($keyword);
	newfile(dirname(__FILE__).'/weixin_config.php',$keyword);
	echo '<script language="javascript">alert("采集完成");</br></script><a href="'.$backurl.'"><font color="red">返回</font></a>';	
}
function main()
{
	global $db,$action;
	$backurl = getReferer();
	if($backurl === null || $backurl ==='' ){
		$backurl='admin_keyword.php';
	}
	$content=  @file_get_contents(dirname(__FILE__).'/weixin_config.php');
	$content=replaceStr($content, "<?php", "");
	$content=replaceStr($content, "return array(", "");
	$content=replaceStr($content, ");", "");
	$content=replaceStr($content, "?>", "");
?>
<script language="javascript">
$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
			location.href = $("#backurl").val();
	    }
	});
	$("#btnCancel").click(function(){
		location.href = $("#backurl").val();
	});
});
</script>
 <form name="form1" id="form1" method="post" action="?action=save">
<table class="tb">	
	<input id="backurl" name="backurl" type="hidden" value="<?php echo $backurl?>">	
	
	<tr> 
    <td>微信配置参数：每个榜单关键字配置形式为 'topicid'=>',keyword1,keyword2,',</td>
    </tr>
    <tr> 
    <td>&nbsp;
    <textarea id="keyword" name="keyword" style="width:1000px;height:450px;"><?php echo $content?></textarea>
  
    </td>
	</tr>
	<tr align="center">
	<td><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"> </td>
    </tr>
 </table>
 </form>
<?php 
}


?>