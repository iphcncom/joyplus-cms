<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "sqlexe" : headAdmin ("执行sql语句"); sqlexe();break;
	default :  headAdmin ("执行sql语句"); main();break;
}
dispseObj();

function sqlexe()
{
	global $db;
	$sql = be("post","sql");

	if (!isN($sql)){
		$sql= stripslashes($sql);
		if (strtolower(substr($sql,0,6))=="select"){
			$isselect=true;
		}
		else{
			$isselect=false;
		}
		$rs=$db->query($sql);
	}

	if(!isN($_FILES['import_file']['tmp_name'])) {
		$str=iconv("UTF-8","UTF-8", file_get_contents($_FILES['import_file']['tmp_name']));
		$str=preg_replace("/--.*\n/iU","",$str);//去掉注释
		//$str=str_replace("ct_",TABLE_PRE,$str);//替换前缀
       
		$carr=array();
		$iarr=array();
		//提取create
		preg_match_all("/Create table .*\(.*\).*\;/iUs",$str,$carr);
		$carr=$carr[0];
		foreach($carr as $c)
		{
			$isselect=false;
			$rss=$db->query($c);
		}

		//提取insert
		preg_match_all("/INSERT INTO .*\(.*\)\;/iUs",$str,$iarr);
		$iarr=$iarr[0];
		//插入数据
		foreach($iarr as $c)
		{
			$isselect=false;
			$rs2=$db->query($c);

		}
	}
	?>
<table class="tb">
<?php if ($isselect){
	$i=0;
	while($row=$db->fetch_array($rs)){
		if($i==0){
			foreach($row as $k=>$v){
				echo "<td><strong>$k</strong></td>";
			}
		}
		?>
	<tr>
	<?php foreach( $row as $k=>$v){?>
		<td><?php echo $v?></td>
		<?php }?>
	</tr>
	<?php
	$i++;
	}
}
else{
	$nums=mysql_affected_rows();
	?>
	<tr>
		<td><strong>执行结果</strong></td>
	</tr>
	<tr>
		<td><?php echo $nums ."条纪录被影响"?></td>
	</tr>
	<?php }
	?>
</table>
	<?php
}

function main()
{
	?>
<script language="javascript">
	$(document).ready(function(){
		$("#form1").validate({
			rules:{
//				sql:{
//					required:true
//				}
			}
		});
	});
</script>
<form action="?action=sqlexe" method="post" name="form1" id="form1"
	enctype="multipart/form-data">
<table class="admin_sql tb">
	<tr>
		<td>如采用手动输入方式，请在下面输入框内输入</td>
	</tr>
	<tr>
		<td colspan="2"><textarea name="sql" type="text" id="sql" rows="10"
			style="width: 90%"></textarea></td>
	</tr>
	<tr>
		<td colspan="2">批量导入，请从计算机中上传文件： <input id="import_file" type="file" value="浏览"
			name="import_file"><input class="input" type="submit" value="执行"
			name="Submit"></td>
	</tr>
	<tr>
		<td>（注：若使用批量导入的方式，请保证手动输入框为空）</td>
	</tr>
	<tr>
		<td valign="top" colspan="2"><strong><br>
		常用语句对照<br>
		</strong><br>
		<strong>1.查询数据</strong><br>
		SELECT * FROM {pre}vod&nbsp;&nbsp; 查询所有数据<br>
		SELECT * FROM {pre}vod WHERE d_id=1000&nbsp;&nbsp; 查询指定ID数据<br>
		<strong>2.删除数据</strong><br>
		DELETE FROM {pre}vod&nbsp;&nbsp; 删除所有数<br>
		DELETE FROM {pre}vod WHERE d_id=1000 &nbsp; 删除指定的第几条数据<br>
		DELETE FROM {pre}vod WHERE d_starring LIKE '%刘德华%'&nbsp;&nbsp;
		删除d_starring字段里有"刘德华"的数据</br>
		<strong>&nbsp;3.修改数据</strong><br>
		UPDATE {pre}vod SET d_hits=1&nbsp;&nbsp;
		将所有d_hits字段里的值修改成&quot;1&quot;<br>
		UPDATE {pre}vod SET d_hits=1 WHERE d_id=1000&nbsp;
		指定的第几条数据把d_hits字段里的值修改成&quot;1&quot; <br>
		</td>
	</tr>
</table>
</form>
	<?php
}
?>
</body>
</html>
