<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "editall" : editall();break;
	case "info" :headAdmin ("视频榜单管理"); info();break;
	default : headAdmin ("视频榜单管理");main();break;
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
		$t_toptype = be("post","t_toptype" .$id);
		$t_template = be("post","t_template" .$id);
		$t_pic = be("post","t_pic" .$id);
		if (isN($t_name)) { echo "信息填写不完整!";exit;}
		if (isN($t_sort)) { $t_sort= $db->getOne("SELECT MAX(t_sort) FROM {pre}vod_topic")+1; }
		if (!isNum($t_sort)) { echo "信息填写不完整!";exit;}
		$db->Update ("{pre}vod_topic",array("t_name", "t_toptype","t_type","t_sort","t_pic","t_flag","t_bdtype"),array($t_name,$t_toptype,$t_type,$t_sort,$t_pic,$t_flag,$t_bdtype),"t_id=".$id);
	}
	updateCacheFile();
	echo "修改完毕";
}

function main()
{
	global $db,$cache;

	$keyword = be("all", "keyword"); $t_flag = be("all", "t_flag");
	$t_userid = be("all", "t_userid");   $t_type = be("all", "t_type");
	$t_bdtype = be("all", "t_bdtype");
	$can_search_device = be("all", "can_search_device");
	if(!isNum($t_userid)) { $t_userid = -1; } else { $t_userid = intval($t_userid);}

	if(!isNum($t_bdtype)) { $t_bdtype = -1; } else { $t_bdtype = intval($t_bdtype);}

	if(!isNum($t_flag)) { $t_flag = -1;} else { $t_flag = intval($t_flag);}
	if(!isNum($t_type)) { $t_type = -1;} else { $t_type = intval($t_type);}

	$where = " t_id>4 ";
	if (!isN($keyword)) { $where .= " AND t_name LIKE '%" . $keyword . "%' ";}
	if ($t_type > 0) {
		$typearr = getValueByArray($cache[0], "t_id" ,$t_type );
		if(is_array($typearr)){
			$where = $where . " and t_type in (" . $typearr["childids"] . ")";
		}
		else{
			$where .= " AND t_type=" . $t_type . " ";
		}
	}

	if(!isN($can_search_device)){
		//if($can_search_device ==='TV'){
		//	$where .= " AND can_search_device like '%TV%' ";
		//}else {
		$where .= " AND (can_search_device like '".$can_search_device."' or can_search_device is null or can_search_device ='' ) ";
		//}
	}

	if($t_flag==1){
		$where .= " AND t_flag =1 ";
	}

	if($t_bdtype!=-1){
		$where .= " AND t_bdtype=".$t_bdtype;
	}


	if($t_flag==0){
		$where .= " AND t_flag =0 ";
	}

	if($t_userid==1){
		$where .= " AND t_userid >0 ";
	}

	if($t_userid==0){
		$where .= " AND t_userid =0 ";
	}

	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM {pre}vod_topic"." where ".$where;;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM {pre}vod_topic  where ".$where." ORDER BY t_sort desc,t_id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
	?>
<script language="javascript">
function filter(){
	var t_userid=$("#t_userid").val();
	var t_flag=$("#t_flag").val();
	var t_type=$("#t_type").val();
	var keyword=$("#keyword").val();
	var t_bdtype=$("#t_bdtype").val();
	 
	var can_search_device=$("#can_search_device").val();
	var url = "admin_vod_topic.php?can_search_device="+can_search_device+"&keyword="+encodeURI(keyword)+"&t_userid="+t_userid+"&t_flag="+t_flag+"&t_type="+t_type+"&t_bdtype="+t_bdtype;
	window.location.href=url;
}

$(document).ready(function(){
	$("#form2").validate({
		rules:{
			t_name:{
				required:true,
//				stringCheck:true,
				maxlength:64
			},
//			t_enname:{
//				required:true,
////				stringCheck:true,
//				maxlength:128
//			},
//			t_template:{
//				required:true,
//				maxlength:128
//			},
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
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab={pre}vod_topic");
				$("#form1").submit();
			}
			else{return false}
	});

	$("#btnAddLunBo").click(function(){
		var ids = "",tid="";
		tid= "t_id[]";
		$("input[name='"+tid+"']").each(function() {
			if(this.checked){ ids =  ids + this.value + ","; }
		});
		if (ids != ""){
			if(confirm('确定要添加到轮播图吗')){
				$("#form1").attr("action","admin_ajax.php?action=lunboForTopic&flag=batch&tab={pre}vod_popular");
				$("#form1").submit();
			}else{return false;}
			}else{
				alert("请至少选择一个数据!");
				}
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
function edit(id)
{
	$('#form2').form('clear');
	$("#flag").val("edit");
	$('#win1').window('open');
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab={pre}vod_topic&col=t_id&val='+id);
}
</script>

<table class="admin_vod_topic_1 tb">
	<tr>
		<td>
		<table width="100%" border="0" align="center" cellpadding="3"
			cellspacing="1">
			<tr>
				<td colspan="2">过滤条件：<select id="t_type" name="t_type">
					<option value="0">视频栏目</option>
					<option value="-1" <?php if($t_type==-1){ echo "selected";} ?>>没有栏目</option>
					<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$t_type)?>
				</select> <select id="t_bdtype" name="t_bdtype">
					<option value="-1">榜单类型</option>
					<option value="1" <?php if ($t_bdtype==1){ echo "selected";} ?>>悦单</option>
					<option value="2" <?php if ($t_bdtype==2){ echo "selected";} ?>>悦榜</option>
				</select> &nbsp; <select id="t_flag" name="t_flag">
					<option value="-1">显示到APP</option>
					<option value="1" <?php if ($t_flag==1){ echo "selected";} ?>>显示</option>
					<option value="0" <?php if ($t_flag==0){ echo "selected";} ?>>隐藏</option>
				</select> <select id="t_userid" name="t_userid">
					<option value="-1">系统/用户创建</option>
					<?php echo $t_userid?>
					<option value="0" <?php if ($t_userid==0){ echo "selected";} ?>>系统创建</option>
					<option value="1" <?php if ($t_userid>0){ echo "selected";} ?>>用户创建</option>
				</select> <select id="can_search_device" name="can_search_device">
					<option value="">投放设备</option>
					<option value="TV"
					<?php if ($can_search_device==='TV'){ echo "selected";} ?>>TV版</option>
					<option value="iPad"
					<?php if ($can_search_device==='iPad'){ echo "selected";} ?>>iPad版</option>
					<option value="iphone"
					<?php if ($can_search_device==='iphone'){ echo "selected";} ?>>iphone版</option>
					<option value="apad"
					<?php if ($can_search_device==='apad'){ echo "selected";} ?>>Android-Pad版</option>
					<option value="aphone"
					<?php if ($can_search_device==='aphone'){ echo "selected";} ?>>Android-Phone版</option>
					<option value="web"
					<?php if ($can_search_device==='web'){ echo "selected";} ?>>网站版</option>
				</select></td>
			</tr>
			<tr>
				<td colspan="6">关键字：<input id="keyword" size="40" name="keyword"
					value="<?php echo $keyword?>"> <input class="input" type="button"
					value="搜索" id="btnsearch" onClick="filter();"></td>
				<td width="150px"></td>
			</tr>
		</table>
		</td>
	</tr>
</table>

<table class="admin_vod_topic tb">
	<form action="" method="post" id="form1" name="form1">
	<tr>
		<td width="5%">&nbsp;</td>
		<td width="5%">编号</td>
		<td>名称</td>
		<td>关注度</td>
		<td width="3%">类别</td>
		<td width="10%">视频栏目</td>
		<td width="8%">投放设备</td>
		<td width="5%">图片</td>
		<td width="5%">排序</td>
		<td width="10%">显示到App</td>
		<td width="30%">操作</td>
	</tr>
	<?php
	if($nums==0){
		?>
	<tr>
		<td align="center" colspan="7">没有任何记录!</td>
	</tr>
	<?php
	}
	else{
		while ($row = $db ->fetch_array($rs))
		{
			$t_id=$row["t_id"];
			?>
	<tr>
		<td><input name="t_id[]" type="checkbox" id="t_id"
			value="<?php echo $t_id?>" /></td>
		<td><?php echo $t_id?></td>
		<td><input type="text" name="t_name<?php echo $t_id?>"
			value="<?php echo $row["t_name"]?>" size="20" /></td>
		<td><select id="t_toptype<?php echo $t_id?>"
			name="t_toptype<?php echo $t_id?>">
			<option value="-1">关注度</option>
			<option value="1"
			<?php if ($row["t_toptype"]==1){ echo "selected";} ?>>热门</option>
			<option value="0"
			<?php if ($row["t_toptype"]==0){ echo "selected";} ?>>非热门</option>
		</select></td>
		<td><select id="t_type<?php echo $t_id?>"
			name="t_type<?php echo $t_id?>">
			<option value="0" <?php if($row["t_type"]==0){ echo "selected";} ?>>没有栏目</option>
			<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$row["t_type"])?>
		</select></td>
		<td><select id="t_bdtype<?php echo $t_id?>"
			name="t_bdtype<?php echo $t_id?>">
			<option value="-1">榜单类型</option>
			<option value="1"
			<?php if ($row["t_bdtype"]==1){ echo "selected";} ?>>悦单</option>
			<option value="2"
			<?php if ($row["t_bdtype"]==2){ echo "selected";} ?>>悦榜</option>
		</select></td>
		<td width="20%"><?php  if(isN($row["can_search_device"])){ echo 'TV,iPad,iphone,apad,aphone,web';} else { echo $row["can_search_device"]; }?>
		</td>
		<td><input type="text" name="t_pic<?php echo $t_id?>"
			value="<?php echo $row["t_pic"]?>" size="20" /></td>
		<td><input name="t_sort<?php echo $t_id?>" type="text"
			value="<?php echo $row["t_sort"]?>" size="5" /></td>
		<td><select id="t_flag<?php echo $t_id?>"
			name="t_flag<?php echo $t_id?>">
			<option value="0" <?php if($row["t_flag"]==0){ echo "selected";} ?>>不显示</option>
			<option value="1" <?php if($row["t_flag"]==1){ echo "selected";} ?>>显示</option>
		</select></td>



		<td><a href="javascript:void(0)"
			onclick="edit('<?php echo $t_id?>');return false;">修改</a> | <a
			href="admin_vod_topic_items.php?topic_id=<?php echo $t_id?>">显示视频列表</a>
		| <!--	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> |-->
		<a
			href="admin_ajax.php?action=lunboForTopic&tab={pre}vod_popular&t_id=<?php echo $t_id?>"
			onClick="return confirm('确定要添加到轮播图吗?');">添加到轮播图</a></td>
	</tr>
	<?php
		}
	}
	?>
	<tr class="formlast">
		<td colspan="12">全选<input type="checkbox" name="chkall" id="chkall"
			class="checkbox" onClick="checkAll(this.checked,'t_id[]')" /> <!--	<input type="button" value="批量删除" id="btnDel" class="input"  />-->
		&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />
		&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />
		&nbsp;<input type="button" value="添加到轮播图" id="btnAddLunBo"
			class="input" /></td>
	</tr>
	<tr align="center" class="formlast">
		<td colspan="12"><?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"admin_vod_topic.php?page={p}&can_search_device=" . $can_search_device . "&keyword=" . urlencode($keyword) . "&t_type=" . $t_type . "&t_flag=".$t_flag."&t_userid=".$t_userid ."&t_bdtype=".$t_bdtype)?>
		</td>
	</tr>

</table>
</form>

<div id="win1" class="easyui-window" title="窗口"
	style="padding: 5px; width: 450px;" closed="true" closable="false"
	minimizable="false" maximizable="false">
<form action="admin_ajax.php?action=save&tab={pre}vod_topic"
	method="post" name="form2" id="form2">
<table class="tb">
	<input id="t_id" name="t_id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<tr>
		<td width="30%">专题名称：</td>
		<td><input id="t_name" size=40 value="" name="t_name"></td>
	</tr>
	<tr>
		<td>关注度：</td>
		<td><select id="t_toptype" name="t_toptype">
			<option value="0">非热门</option>
			<option value="1">热门</option>
		</select></td>
	</tr>
	<tr>
		<td>节目类别：</td>
		<td><select id="t_type" name="t_type">
			<option value="0">没有栏目</option>
			<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",0)?>
		</select></td>
	</tr>
	<tr>
		<td>专题图片：</td>
		<td><input id="t_pic" size=40 value="" name="t_pic"></td>
	</tr>

	<tr>
		<td>显示到App：</td>
		<td><select id="t_flag" name="t_flag">
			<option value="0" selected>不显示</option>
			<option value="1">显示</option>
		</select></td>
	</tr>

	<tr>
		<td>榜单类型</td>
		<td><select id="t_bdtype" name="t_bdtype">
			<option value="1" selected>悦单</option>
			<option value="2">悦榜</option>
		</select></td>
	</tr>

	<tr id="can_search_device">
		<td>投放设备（可以多选）</td>
		<td><input type="checkbox" name="can_search_device[]" value="TV"
		<?php if(strpos($can_search_device, "TV") !=false){echo "checked";}?> />TV版
		<input type="checkbox" name="can_search_device[]" value="iPad"
		<?php if(strpos($can_search_device, "iPad") !=false){echo "checked";}?> />iPad版
		<input type="checkbox" name="can_search_device[]" value="iphone"
		<?php if(strpos($can_search_device, "iphone") !=false){echo "checked";}?> />iphone版
		<input type="checkbox" name="can_search_device[]" value="apad"
		<?php if(strpos($can_search_device, "apad") !=false){echo "checked";}?> />Android-Pad版
		<input type="checkbox" name="can_search_device[]" value="aphone"
		<?php if(strpos($can_search_device, "aphone") !=false){echo "checked";}?> />Android-phone版
		<input type="checkbox" name="can_search_device[]" value="web"
		<?php if(strpos($can_search_device, "web") !=false){echo "checked";}?> />网站版
		<!--<input type="checkbox" id="can_search_device[]" name="can_search_device[]" value="TV"  />TV版-->
		<!--    <input type="checkbox" id="can_search_device[]" name="can_search_device[]" value="Pad"  />Pad版-->
		<!--    <input type="checkbox"  id="can_search_device[]" name="can_search_device[]" value="Mobile" />Mobile版-->
		<!--    <input type="checkbox"  name="can_search_device[]" value="Web" />网站版-->
		</td>
	</tr>



	<!--    <tr>-->
	<!--     <td>榜单归档，Tag：</td>-->
	<!--      <td>-->
	<!--      <TEXTAREA id="t_tag_name" NAME="t_tag_name" ROWS="1" style="width:300px;table-layout:fixed; word-wrap:break-word;"></TEXTAREA>-->
	<!--	  </td>-->
	<!--    </tr>-->
	<!--   -->

	<tr>
		<td>排序：</td>
		<td><input id="t_sort" size=10 value="" name="t_sort"></td>
	</tr>
	<tr>
		<td>描述信息：</td>
		<td><TEXTAREA id="t_des" NAME="t_des" ROWS="2"
			style="width: 300px; table-layout: fixed; word-wrap: break-word;"></TEXTAREA>
		</td>
	</tr>
	<tr align="center">
		<td colspan="2"><input class="input" type="submit" value="保存"
			id="btnSave"> <input class="input" type="button" value="返回"
			id="btnCancel"></td>
	</tr>
</table>
</form>
</div>
</body>
</html>
		<?php
		unset($rs);
}



function info()
{
	global $db,$cache;

	$prod_id = be("all", "id");
	$sql = "SELECT * FROM {pre}vod_topic ,{pre}vod_topic_items as items where t_id = topic_id and t_userid=0 and t_id>4 and  vod_id=".$prod_id." ORDER BY t_sort,t_id ASC ";
	//var_dump($sql)
	$rs = $db->query($sql);
	$nums=1;
	?>


<table class="tb">
	<form action="" method="post" id="form1" name="form1">
	<tr>
		<td width="5%">&nbsp;</td>
		<td width="10%">编号</td>
		<td>名称</td>
		<td>关注度</td>
		<td width="15%">类别</td>
		<td width="15%">视频栏目</td>
		<td width="10%">显示到App</td>
		<td width="20%">操作</td>
	</tr>
	<?php
	if($nums==0){
		?>
	<tr>
		<td align="center" colspan="7">没有任何记录!</td>
	</tr>
	<?php
	}
	else{
		while ($row = $db ->fetch_array($rs))
		{
			$t_id=$row["t_id"];
			?>
	<tr>
		<td></td>
		<td><?php echo $t_id?></td>
		<td><?php echo $row["t_name"]?></td>
		<td><select disabled id="t_toptype<?php echo $t_id?>"
			name="t_toptype<?php echo $t_id?>">
			<option value="-1">关注度</option>
			<option value="1"
			<?php if ($row["t_toptype"]==1){ echo "selected";} ?>>热门</option>
			<option value="0"
			<?php if ($row["t_toptype"]==0){ echo "selected";} ?>>非热门</option>
		</select></td>
		<td><select disabled id="t_type<?php echo $t_id?>"
			name="t_type<?php echo $t_id?>">
			<option value="0" <?php if($row["t_type"]==0){ echo "selected";} ?>>没有栏目</option>
			<?php echo makeSelectAll("{pre}vod_type","t_id","t_name","t_pid","t_sort",0,"","&nbsp;|&nbsp;&nbsp;",$row["t_type"])?>
		</select></td>
		<td><select disabled id="t_bdtype<?php echo $t_id?>"
			name="t_bdtype<?php echo $t_id?>">
			<option value="-1">榜单类型</option>
			<option value="1"
			<?php if ($row["t_bdtype"]==1){ echo "selected";} ?>>悦单</option>
			<option value="2"
			<?php if ($row["t_bdtype"]==2){ echo "selected";} ?>>悦榜</option>
		</select></td>

		<td><select disabled id="t_flag<?php echo $t_id?>"
			name="t_flag<?php echo $t_id?>">
			<option value="0" <?php if($row["t_flag"]==0){ echo "selected";} ?>>不显示</option>
			<option value="1" <?php if($row["t_flag"]==1){ echo "selected";} ?>>显示</option>
		</select></td>



		<td><a href="admin_vod_topic_items.php?topic_id=<?php echo $t_id?>">显示视频列表</a>
		| <!--	  <a href="admin_ajax.php?action=del&tab={pre}vod_topic&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a>-->
		</td>
	</tr>
	<?php
		}
	}
	?>

</table>
</form>

</body>
</html>
	<?php
	unset($rs);
}
?>