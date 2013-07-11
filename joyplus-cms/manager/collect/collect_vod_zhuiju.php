<?php
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
chkLogin();
$action = be("get","action");
headAdminCollect ("视频采集入库管理");

switch($action)
{	case "del" : del();break;
    case "delpl" : delpl();break;
	case "delall" : delall();break;
	
case "editall" : editall();break;
	default : main();
}


function editall()
{
	global $db;
	$t_id = be("arr","m_id");
	$ids = explode(",",$t_id);
	
	foreach( $ids as $id){
		$tv_playfrom = be("arr","crontab_desc" .$id);

		$db->Update ("{pre}cj_zhuiju",array("crontab_desc"),array($tv_playfrom),"m_id=".$id);
	}
	echo  "更新成功";
}

function del()
{
	global $db;
	$m_id=be("get","m_id");
	$db->query("delete from {pre}cj_zhuiju WHERE m_id =".$m_id);
	
	echo  "<script language=\"javascript\">setTimeout(\"makeNextUrl();\",1);function makeNextUrl(){location.href='".getReferer()."';}</script>";
}

function delpl()
{
	global $db;
	$ids = be("arr","m_id");
	if (!isN($ids)){
		$db->query("delete from {pre}cj_zhuiju WHERE m_id in (".$ids.")");
		echo  "删除成功";
	 }
	 else{
	  echo  "删除成功";
	 }
}

function delall()
{
	global $db;
	$db->query("delete from {pre}cj_zhuiju");
	echo  "删除成功";
}

function main()
{
	global $db,$cache;
	$pagenum = be("get","page");
	if (isN($pagenum) || !isNum($pagenum)){ $pagenum = 1; }
	if ($pagenum < 1 ){ $pagenum = 1;}
	$pagenum = intval($pagenum);
	
	$keyword = be("get","keyword");
	$from= be("get","playfrom");
	$project = be("get","cj_vod_projects");
	$zt = be("get","zt");
	
	$sql="Select a.crontab_desc, a.m_id,a.m_name,a.m_typeid,a.m_urltest,m_playfrom ,b.p_name,status as p_name from {pre}cj_zhuiju a,{pre}cj_vod_projects b where a.m_pid=b.p_id  ";
	
	if ($keyword != "") {
		$keyword= trim($keyword);
		$sql = $sql . " and m_name like '%" . $keyword . "%' ";
	}
	if ($project!= "") {
		$sql = $sql . " and a.m_pid = " . $project;
	}
	
    if ($from!= "") {
		$sql = $sql . " and a.m_playfrom ='" . $from."' ";
	}
	
	//$sql = $sql . " group by m_urltest " ;
	
	$rscount = $db->query($sql);
	$nums= $db -> num_rows($rscount);//总记录数
	$pagecount = ceil($nums/app_pagenum);//总页数
	$sql = $sql ." limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	//var_dump($sql);
	$rs = $db->query($sql);
?>
<script language="javascript">
$(document).ready(function(){	
	$("#btnDelall").click(function(){
		if(confirm('确定要删除所有的数据 吗')){
			$("#form1").attr("action","?action=delall");
			$("#form1").submit();
		}
	});
	$("#btnDel").click(function(){
		if(confirm('确定要删除所选择的数据吗')){
			$("#form1").attr("action","?action=delpl");
			$("#form1").submit();
		}
	});
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
	$('#form1').form({
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        	location.href=location.href;
	        });
	    }
	});
	
});
</script>
<TABLE width="96%" border=0 align=center cellpadding=0 cellSpacing=0 class=tbtitle >
  <TBODY>
    <tr>
		<td>
		<form action="collect_vod_zhuiju.php" method="get" > 
			<strong>搜索影片：</strong>
			<input id=keyword size=40 name=keyword value="<?php echo $keyword;?>">
			
			<select id="playfrom" name=playfrom>
	<option value="">视频播放器</option>
	<?php echo makeSelectPlayer($from)?>
	</select>
			
			<select id="cj_vod_projects" name="cj_vod_projects">
				<option value="">全部采集项目</option>
				<?php echo makeSelect("{pre}cj_vod_projects","p_id","p_name","","","&nbsp;|&nbsp;&nbsp;",$project)?>
			</select>	
			<INPUT class=inputbut type=submit value=搜索 name=submit>
			    
		</form>
		</td>
    </tr>
  </TBODY>
</TABLE>

<form action="" method="post" name="form1" id="form1">
<table class=tb >
	<tr>
	  <td width="4%" >&nbsp;</td>
      <td>影片名称</td>
      <td>定时采集时间</td>
      <td>栏目分类</td>
      <td width="15%">所属采集项目</td> 
       <td width="7%">播放器</td>
     
       <td width="8%">操作</td>
    </tr>
	<?php
	if (!$rs){
	?>
    <tr><td align="center" colspan="9" >没有任何记录!</td></tr>
    <?php
	}
	else{
		$i=0;
	  	while ($row = $db ->fetch_array($rs))
	  	{
	  		$t_id= $row["m_id"];
	  		
	?> 
    <tr>
	<td><input name="m_id[]" type="checkbox" id="m_id" value="<?php echo $t_id;?>" /></td>
	<td><?php echo $row["m_name"]?>  (连载：<?php echo $row["status"]?>)</td>
	
	<td>
		
		<input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="1" <?php if(strpos($row["crontab_desc"], "1") !==false){echo "checked";}?> />星期一
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="2" <?php if(strpos($row["crontab_desc"], "2") !==false){echo "checked";}?> />星期二
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="3" <?php if(strpos($row["crontab_desc"], "3") !==false){echo "checked";}?>/>星期三
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="4" <?php if(strpos($row["crontab_desc"], "4") !==false){echo "checked";}?> />星期四
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="5" <?php if(strpos($row["crontab_desc"], "5") !==false){echo "checked";}?>/>星期五
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="6" <?php if(strpos($row["crontab_desc"], "6") !==false){echo "checked";}?>/>星期六
	    <input type="checkbox" name="crontab_desc<?php echo $t_id?>[]" value="0" <?php if(strpos($row["crontab_desc"], "0") !==false){echo "checked";}?>/>星期天
	
     </td>
	
	<td>
	<?php
	if ($row["m_typeid"]==0){
	?>
		<font color="#FF0000">没找到对应分类请配置</font>
	<?php
	}
	else{
		$typearr = getValueByArray($cache[0], "t_id" , $row["m_typeid"] );
		echo $typearr["t_name"];
	}
	?>
	</td><td><a href="<?php echo $row["m_urltest"]?>" target='_blank'><?php echo $row["p_name"]?></a></td>
	<td><?php echo $row["m_playfrom"]?></td>
	
   
      <td><A href="?action=del&m_id=<?php echo $row["m_id"]?>">删除</A></td>
    </tr>
	<?php
		}
	}
	?>
	<tr class="formlast">
	<td colspan="4">
    全选<input name="chkall" type="checkbox" id="chkall" value="1" onClick="checkAll(this.checked,'m_id[]');"/>&nbsp;
     &nbsp;<input type="button" id="btnDel" value="批量删除" class="btn"  />
     &nbsp;<input type="button" id="btnDelall" value="删除所有" class="btn"  />
     &nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
	</td>
	</tr>
	
    <tr align="center" class="formlast">
	<td colspan="4">
	<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"collect_vod_zhuiju.php?page={p}&cj_vod_projects=".$project."&keyword=".$keyword."&playfrom=".$from) ?></td>
    </tr>
</table>
</form>
<?php
}

?>