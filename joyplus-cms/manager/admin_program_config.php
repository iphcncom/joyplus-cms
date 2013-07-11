<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "save" : save();break;
	default : headAdmin ("电视直播相关配置管理");main();break;
}
dispseObj();

function save()
{
	$program_country = be("post","program_country");
	$program_area = be("post","program_area");
	$program_type = be("post","program_type");
	$tv_playfrom = be("post","tv_playfrom");
	fwrite(fopen("../inc/program_country.txt","wb"),$program_country);
	fwrite(fopen("../inc/program_area.txt","wb"),$program_area);
	fwrite(fopen("../inc/program_type.txt","wb"),$program_type);	
	fwrite(fopen("../inc/tv_playfrom.txt","wb"),$tv_playfrom);
	updateCacheFile();
	echo "修改完毕";
}

function main()
{   if (!file_exists("../inc/program_country.txt")){
	  writetofileNoAppend("../inc/program_country.txt", '');
     }
     if (!file_exists("../inc/program_area.txt")){
	  writetofileNoAppend("../inc/program_area.txt", '');
     }
     if (!file_exists("../inc/program_type.txt")){
	  writetofileNoAppend("../inc/program_type.txt", '');
     }
     if (!file_exists("../inc/tv_playfrom.txt")){
	  writetofileNoAppend("../inc/tv_playfrom.txt", '');
     }
	$fc1 = file_get_contents("../inc/program_country.txt");
	$fc2 = file_get_contents("../inc/program_area.txt");
	$fc3 = file_get_contents("../inc/program_type.txt");
	$fc4 = file_get_contents("../inc/tv_playfrom.txt");
?>
<script type="text/javascript">
	$(document).ready(function(){
    	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
		});
	});
</script>
<form action="?action=save" method="post" id="form1" name="form1">
<table class="admin_program_config tb">
<tr class="thead"><th colspan="2"> 1.每个各占一行;2.不要有多余的空行</th></tr>
<tr><td>国家</td>
<td>地区</td>
<td >节目类别</td>
<td >直播地址来源</td>
</tr>
<tr>
	<td>
	<textarea id="program_country" name="program_country" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc1?></textarea>
	</td>
	<td>
	<textarea id="program_area" name="program_area" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc2?></textarea>
	</td>
	<td>
	<textarea id="program_type" name="program_type" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc3?></textarea>
	</td>
	<td>
	<textarea id="tv_playfrom" name="tv_playfrom" style="width:100%;font-family: Arial, Helvetica, sans-serif;font-size: 14px;" rows="25"><?php echo $fc4?></textarea>
	</td>
	</tr>
	<tr class="formlast">
	<td align="center" colspan="4"> <input type="submit" id="btnSave" name="btnSave" value="保存" class="input" /> </td>
	</tr>
</table>
</form>
</body>
</html>
<?php
}
?>