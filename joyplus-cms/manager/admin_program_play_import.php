<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "import" : import();break;	
	case "info" :headAdmin ("节目类别管理"); info();break;
	default : headAdmin ("节目类别管理");main();break;
}
dispseObj();

function import(){
	global $db;
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }
else
  {
   $file= $_FILES["file"]["tmp_name"];
   $tempFile=$_FILES["file"]["name"];   
   if(!isN($tempFile) && strpos($tempFile, ".csv") !==false && (strpos($tempFile, ".csv") ===(strlen($tempFile)-4))){
   	try{
		$str = file_get_contents($file);
		$str = replaceStr($str,chr(10),"");
		$tvArrays = explode(chr(13),$str);
		//setGlobalCache("cache_vodlang",$cachearea,1,'php');
	}
	catch(Exception $e){ 
		$tvArrays=array();
	}
	foreach ($tvArrays as $tv){
		$items = explode(",", $tv);
		if(is_array($items)){
					
			$count = count($items);
			
			if($count>0){
				$id=$items[0];
			}else {
				$id='';
				$url='';
			}
			if($count>1){
				$url=$items[1];
			}else {
				$url='';
			}
			if($count>2){
				$definition=$items[2];
			}else {
				$definition='';
				$playfrom='';
			}
			if($count>3){
				$playfrom=$items[3];
			}else {
				$playfrom='';
			}
			
			if(!isN($id) && !isN($url)){
				$row=$db->getRow("select * from mac_tv_play where tv_playurl='".$url ."' and tv_id=".$id);
				if(!$row){
					$sql = "insert into mac_tv_play(tv_id,tv_playurl,status,tv_definition,tv_playfrom) values('".$id ."','".$url ."','1','".$definition ."','".$playfrom ."')";
				}else {
					$sql="update mac_tv_play set status='1', tv_definition='".$definition ."',tv_playfrom='".$playfrom ."' where tv_playurl='".$url ."' and tv_id=".$id;
				}
				$row=$db->query($sql);
			}
		}
	}
   echo "修改完毕";
  }else {
  	echo "文件格式不对";
  }
    
}
}

function main(){
	
	
?>
<script language="javascript">

$(document).ready(function(){
	
	
//	$('#form3').form({		
//	    success:function(data){
//	        $.messager.alert('系统提示', data, 'info');
//	    }
//	});

	
});
</script>
<form name="form3" id="form3" method="post" action="?action=import" enctype="multipart/form-data">
<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	
	<td colspan="2">
	请选择导入直播源，文件后缀为csv，每行数据的格式为 ：格式为频道id,直播源地址,清晰度,来源 ,文件必须按照utf-8来保存<br>
	<input type="file" name="file" id="file" /> 
	<input class="input" type="submit" value="导入" id="btnsearch" onClick="filter();">	| <a href="admin_program.php">返回电视直播</a>
	</td> 
	</tr>
	
	</table>
	</td>
	</tr>
</table>
 </form>

</body>
</html>
<?php

}

?>

