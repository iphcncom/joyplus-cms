<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	default : headAdmin ("视频轮播管理");main();break;
}
dispseObj();

function editall()
{
	global $db;
	$t_id = be("arr","t_id");
	$ids = explode(",",$t_id);
	foreach( $ids as $id){
		$t_name = be("post","t_name" .$id);
		$t_type= be("post","t_type" .$id);//var_dump($t_type);
		$t_bdtype= be("post","t_bdtype" .$id);
		$t_sort = be("post","t_sort" .$id);
		$t_flag = be("post","t_flag" .$id);
		$t_enname = be("post","t_enname" .$id);
		$t_template = be("post","t_template" .$id);
		$t_pic = be("post","t_pic" .$id);
		if (isN($t_name)) { echo "信息填写不完整!";exit;}
		if (isN($t_enname))  { echo "信息填写不完整!";exit;}
		if (isN($t_sort)) { $t_sort= $db->getOne("SELECT MAX(t_sort) FROM {pre}vod_topic")+1; }
		if (!isNum($t_sort)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}vod_topic",array("t_name", "t_enname","t_type","t_sort","t_pic","t_flag","t_bdtype"),array($t_name,$t_enname,$t_type,$t_sort,$t_pic,$t_flag,$t_bdtype),"t_id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main()
{
	global $db,$cache;
	
	 $keyword = be("all", "keyword"); $status = be("all", "status");
   

    if(!isNum($status)) { $status = -1;} else { $status = intval($status);}
    
$where = " 1=1 ";
    if (!isN($keyword)) { $where .= " AND vod.d_name LIKE '%" . $keyword . "%' ";}
   
    
    
    if($status==1){
    	$where .= " AND a.status =1 ";
    }
    
    if($status==0){
    	$where .= " AND a.status =0 ";
    }
    
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
//	$sql = "SELECT count(*) FROM {pre}vod_popular as a, {pre}vod as vod"." where ".$where ." and a.vod_id=vod.d_id";
	$nums =1;
//	var_dump($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = 'SELECT *
FROM (

SELECT 0 as type, a.id AS id, a.status AS
STATUS , a.disp_order AS disp_order, vod.d_name AS vod_name, vod.d_id AS vod_id, a.ipad_pic_url AS ipad_pic_url, a.iphone_pic_url AS iphone_pic_url, a.info_desc AS info_desc
FROM mac_vod_popular a, mac_vod vod
WHERE a.type =0
AND a.vod_id = vod.d_id
UNION
SELECT 1 as type, a.id AS id, a.status AS
STATUS , a.disp_order AS disp_order, vod.t_name AS vod_name, vod.t_id AS vod_id, a.ipad_pic_url AS ipad_pic_url, a.iphone_pic_url AS iphone_pic_url, a.info_desc AS info_desc
FROM mac_vod_popular a, mac_vod_topic vod
WHERE a.type =1
AND a.vod_id = vod.t_id

UNION
SELECT a.type as type, a.id AS id, a.status AS
STATUS , a.disp_order AS disp_order, "" AS vod_name, "" AS vod_id, a.ipad_pic_url AS ipad_pic_url, a.iphone_pic_url AS iphone_pic_url, a.info_desc AS info_desc
FROM mac_vod_popular a
WHERE a.type != 1 and a.type != 0

) AS b
ORDER BY b.disp_order ASC , b.id DESC ';
//	var_dum/p($sql);
	$rs = $db->query($sql);
	
//	$sql2 = "SELECT a.id as id, a.status as status, a.disp_order as disp_order, vod.t_name as vod_name,vod.t_id as vod_id ,a.ipad_pic_url as ipad_pic_url, a.iphone_pic_url as iphone_pic_url,a.info_desc as info_desc FROM {pre}vod_popular a,{pre}vod_topic vod  where  a.type=1 and a.vod_id=vod.t_id ORDER BY a.disp_order asc,a.id desc ";
////	var_dum/p($sql);
//	$rs2 = $db->query($sql2);
?>
<script language="javascript">
function filter(){
	var status=$("#status").val();
	var keyword=$("#keyword").val();
	var url = "admin_vod_topic.php?keyword="+encodeURI(keyword)+"&status="+status;
	window.location.href=url;
}

$(document).ready(function(){
	$("#form2").validate({
		rules:{
			t_name:{
				required:true,
				stringCheck:true,
				maxlength:64
			},
			t_enname:{
				required:true,
				stringCheck:true,
				maxlength:128
			},
			t_template:{
				required:true,
				maxlength:128
			},
			t_pic:{
				maxlength:254
			},
			t_sort:{
				number:true
			},
			t_des:{
				required:true,
				maxlength:254
			}
		}
	});
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
	$('#form2').form({
		onSubmit:function(){
			if(!$("#form2").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info');
	    }
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_popular");
				$("#form1").submit();
			}
			else{return false}
	});
	$("#btnEdit").click(function(){
		$("#form1").attr("action","?action=editall");
		$("#form1").submit();
	});
	$("#btnAdd").click(function(){
		$('#form2').form('clear');
		$("#flag").val("add");
		$('#win1').window('open');
	});
	$("#btnCancel").click(function(){
		location.href= location.href;
	});
});
function edit(id,type)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}vod_popular&col=id&val='+id+'&type='+type);
}
</script>

<!--<table class="tb">-->
<!--	<tr>-->
<!--	<td>-->
<!--	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">-->
<!--	<tr>-->
<!--	<td colspan="2">-->
<!--	&nbsp;-->
<!--	<select id="status" name="status">-->
<!--	<option value="-1"  <?php if ($status==-1){ echo "selected";} ?>>是否出现在轮播图</option>-->
<!--	<option value="1" <?php if ($status==1){ echo "selected";} ?>>显示</option>-->
<!--	<option value="0" <?php if ($status==0){ echo "selected";} ?>>不显示</option>-->
<!--	</select>-->
<!--	-->
<!--	</td>-->
<!---->
<!--	<td>-->
<!--	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">-->
<!--	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	-->
<!--	</td>-->
<!--	<td width="150px">-->
<!--		-->
<!--	</td>-->
<!--	</tr>-->
<!--	</table>-->
<!--	</td>-->
<!--	</tr>-->
<!--</table>-->

<table class="admin_vod_popular tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="2%">&nbsp;</td>
	<td width="5%">编号</td>
	<td >名称</td>
	<td width="20%">Iphone图片</td>
	<td width="20%">Ipad图片</td>
	<td width="10%">简介</td>
	<td width="10%">权重（越小排前）</td>
	<td width="8%">操作</td>
	</tr>
	<?php
		if($nums==0){
	?>
    <tr><td align="center" colspan="7">没有任何记录!</td></tr>
    <?php
		}
		else{
			while ($row = $db ->fetch_array($rs))
		  	{
		  		$t_id=$row["id"];
		  		
	?>
    <tr>
	  <td>
	  <input name="t_id[]" type="checkbox" id="t_id" value="<?php echo $t_id?>" /></td>
      <td><?php echo $t_id?></td>
      <td>

    <a href=<?php if ($row['type']==='0'){ echo 'admin_vod.php?action=edit&id=';}
    else{
    echo 'admin_vod_topic_items.php?topic_id=';}?><?php echo $row["vod_id"];?>>
      <?php echo $row["vod_name"]?></a> <?php if($row['type'] ==='0') { echo '[视频]';} else if($row['type'] ==='1') {echo '[榜单]';} else if($row['type'] ==='3') {echo '[二维码图片]';}?> </td>
	  <td>
	 <?php echo $row["iphone_pic_url"]?></td>
	 
      <td>
     <?php echo $row["ipad_pic_url"]?></td>
       <td>
     <?php echo $row["info_desc"]?></td>
	  <td>
	  <?php echo $row["disp_order"]?></td>
<!--	  <td>-->
<!--	 -->
<!--	  <select id="status<?php echo $t_id?>" name="status<?php echo $t_id?>">-->
<!--	<option value="0" <?php if($row["status"]==0){ echo "selected";} ?>>不显示</option>-->
<!--	<option value="1" <?php if($row["status"]==1){ echo "selected";} ?>>显示</option>-->
<!--	</select>-->
<!--	  </td>-->
	  
	  
	  
      <td>
	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>','<?php echo $row['type'];?>');return false;">修改</a> |
<!--	  <a href="admin_vod_topic_items.php?topic_id=<?php echo $t_id?>">显示视频列表</a> |-->
	  <a href="admin_ajax.php?action=del&tab={pre}vod_popular&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
			}

 
		}
	?>
	<tr class="formlast">
	<td  colspan="8"><input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" /> 全选
	<input type="button" value="批量删除" id="btnDel" class="input"  />
	
	&nbsp;<input type="button" value="添加视频" id="btnAddvideo" class="input" onclick="javascript:window.location.href='admin_vod.php?action=addVodPopular'" />
	&nbsp;<input type="button" value="添加榜单" id="btnAddbang" class="input" onclick="javascript:window.location.href='admin_vod_topic.php?keyword=&t_userid=0&t_flag=1&t_bdtype=-1'" />
	</td></tr>
    <tr align="center" class="formlast">
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod_popular.php?page={p}&keyword=" . urlencode($keyword) . "&status=" . $status )?>
	</td>
    </tr>
</table>
</form>

<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}vod_popular" method="post" name="form2" id="form2">
<table class="tb">
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
	<td width="30%">视频名称：</td>
	<td><input id="vod_name" size=40 value="" name="vod_name" disabled>
	</td>
	</tr>
	<tr>
	<td>Iphone图片：</td>
	<td><input id="iphone_pic_url" size=40 value="" name="iphone_pic_url" >
	</td>
    </tr>
	
	<tr>
     <td>Ipad图片</td>
      <td><input id="ipad_pic_url" size=40 value="" name="ipad_pic_url" >
	  </td>
    </tr>
    
    <tr>
     <td>类别</td>
      <td><select id="type" name="type">
	   
	   <option value="0" selected>视频</option>
	   <option value="1" >榜单</option>
	</select>
	  </td>
    </tr>
    
  
    
   
    
	<tr>
     <td>排序：</td>
      <td><input id="disp_order" size=10 value="" name="disp_order" >
	  </td>
    </tr>
    
	<tr>
     <td>描述信息：</td>
      <td>
      <TEXTAREA id="info_desc" NAME="info_desc" ROWS="8" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>
	  </td>
    </tr>
    <tr align="center" >
      <td colspan="2"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"></td>
    </tr>
</table>
</form>
</div>
</body>
</html>
<?php
unset($rs);
}
?>