<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "updateInFlow" : updateInFlow();break;
	case "AllInflowProject" :headAdmin ("用户上传应用"); AllInflowProject();break;
	case "IDInflow" :headAdmin ("用户上传应用"); IDInflow();break;
	case "info" :headAdmin ("用户上传应用"); info();break;
	default : headAdmin ("用户上传应用");main();break;
}
dispseObj();

function IDInflow()
{
	global $db;
	$ids = be("arr","t_id");
	if (!isN($ids)){
		$count = $db->getOne("Select count(id) as cc from apk_master_temp where id in (".$ids.") ");
		$sql="select *   from apk_master_temp where id in (".$ids.")  ";
		MovieInflow($sql,$count);
	}
	else{
		showmsg ("请选择入库数据！",$backurl);
	}
}


function AllInflowProject()
{
	global $db;
	
    $keyword = be("get","keyword");
//	$from= be("get","playfrom");
//	$project = be("get","cj_vod_projects");
	
	$where =" 1=1 ";
    if(!isN($keyword)){
    	$keyword=trim($keyword); 
      $where .= " AND (package_name like '%".$keyword."%' or  app_name like '%".$keyword."%') ";
    }
    
   $status = be("all", "status");
    if(!isNum($status)) {
    	$status = 0;
    } else {
    	$status = intval($status);
    }   

    if($status !=0){
    	$where .=" and status=".$status;
    }
    
    $state = be("all", "state");
    if(!isNum($state)) {
    	$state = 0;
    } else {
    	$state = intval($state);
    }   

    if($state !=0){
    	$where .=" and state=".$state;
    }
    
    
	//var_dump($where);
	$count = $db->getOne("Select count(id) as cc from apk_master_temp where ".$where);
	$sql="select * from apk_master_temp where ".$where;
	MovieInflow($sql,$count);
}

function main(){
	global $db,$cache;
	
	 $keyword = be("all", "keyword"); 	 
	 
	
   $where = " 1=1 ";
    if(!isN($keyword)){
    	$keyword=trim($keyword); 
      $where .= " AND (package_name like '%".$keyword."%' or  app_name like '%".$keyword."%')";
    }
    $status = be("all", "status");
    if(!isNum($status)) {
    	$status = 0;
    } else {
    	$status = intval($status);
    }   

    if($status !=0){
    	$where .=" and status=".$status;
    }
   
    $state = be("all", "state");
    if(!isNum($state)) {
    	$state = 0;
    } else {
    	$state = intval($state);
    }   

    if($state !=0){
    	$where .=" and state=".$state;
    }
    
	$pagenum = be("all","page");
	if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
	if ($pagenum < 1) { $pagenum = 1; }
	$sql = "SELECT count(*) FROM apk_master_temp"." where ".$where;
	$nums = $db->getOne($sql); 
	$pagecount=ceil($nums/app_pagenum);
	$sql = "SELECT * FROM apk_master_temp  where ".$where." ORDER BY create_date desc, upload_count desc,id ASC limit ".(app_pagenum * ($pagenum-1)) .",".app_pagenum;
	$rs = $db->query($sql);
?>
<script language="javascript"><!--
function filter(){
	var keyword=$("#keyword").val();
	var status=$("#status").val();
	var state=$("#state").val();
	var url = "admin_apk_temp.php?keyword="+keyword+"&status="+status+"&state="+state;
	window.location.href=url;
}

$(document).ready(function(){
	$("#form2").validate({
		rules:{
			name:{
				required:true,
//				stringCheck:true,
				maxlength:64
			},
			zipcode:{
				isZipCode:true
			},
			email:{
				email:true
			}
//			t_enname:{
//				required:true,
////				stringCheck:true,
//				maxlength:128
//			},
//			t_template:{
//				required:true,
//				maxlength:128
//			},
			
		}
	});
//	$('#form1').form({
//		onSubmit:function(){
//			if(!$("#form1").valid()) {return false;}
//		},
//	    success:function(data){
//	        $.messager.alert('系统提示', data, 'info',function(){
//	        	location.href=location.href;
//	        });
//	    }
//	});
	$('#form2').form({
		onSubmit:function(){
			if(!$("#form2").valid()) {return false;}
		},	    
	    success:function(data){
        $.messager.alert('系统提示', data, 'info',function(){
        	location.href=location.href;
        });
    }
	});
	$("#btnDel").click(function(){
			if(confirm('确定要删除吗')){
				$("#form1").attr("action","admin_ajax.php?action=del&flag=batch&tab=apk_master_temp");
				$("#form1").submit();
			}
			else{return false}
	});

	$("#btnAddLunBo").click(function(){
		if(confirm('确定要添加到轮播图吗')){
			$("#form1").attr("action","admin_ajax.php?action=lunboForTopic&flag=batch&tab={pre}vod_popular");
			$("#form1").submit();
		}
		else{return false}
});
	$("#btnSelin").click(function(){
		if(confirm('确定入库您所选择的数据吗')){
			$("#form1").attr("action","?action=IDInflow");
			$("#form1").submit();
		}
	});

	$("#AllInflowProject").click(function(){
		var keyword=$("#keyword").val();
		var status=$("#status").val();
		var state=$("#state").val();
		
		//var playfrom=$("#playfrom").val();
		//var project=$("#cj_vod_projects").val(); +"&playfrom="+playfrom+"&cj_vod_projects="+project
		if(confirm('确定所搜视频全部入库吗')){
			$("#form1").attr("action","?action=AllInflowProject&keyword="+keyword+"&status="+status+"&state="+state);
			$("#form1").submit();
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
	$('#form2').form('load','admin_ajax.php?action=getinfo&tab=apk_master_temp&col=id&val='+id);
}
--></script>

<table class="tb">
	<tr>
	<td>
	<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td colspan="2">
	关键字：<input id="keyword" size="40" name="keyword" value="<?php echo $keyword?>">&nbsp;
	
	<select id="status" name="status">
	<option value="0">是否入库</option>
	<option value="1" <?php if($status==1){ echo "selected";} ?>>未入库</option>
	<option value="2" <?php if($status==2){ echo "selected";} ?>>已入库</option>
	</select>&nbsp;
	<select id="state" name="state">
	<option value="0">apk入库处理状态</option>
	<option value="1" <?php if($state==1){ echo "selected";} ?>>正常</option>
	<option value="2" <?php if($state==2){ echo "selected";} ?>>package为空</option>
	<option value="3" <?php if($state==3){ echo "selected";} ?>>应用名称 有冲突</option>
    <option value="4" <?php if($state==4){ echo "selected";} ?>>文件的md5不一样</option>
	</select>&nbsp;
	
	
	<input class="input" type="button" value="搜索" id="btnsearch" onClick="filter();">	
	</td> 
	</tr>
	
	</table>
	</td>
	</tr>
</table>

<table class="tb">
<form action="" method="post" id="form1" name="form1">
	<tr>
	<td width="5%">&nbsp;</td>
	<td width="5%">编号</td>
	<td>应用名称</td>
	<td>包名</td>
	<td>版本号</td>
	<td>版本名</td>
	<td>md5</td>
	<td>文件大小</td>
	<td>上传次数</td>
	<td width="20%">操作</td>
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
	<?php if(isN($row['apk_id'])) {?>
	   <?php echo $row["app_name"];?>
	<?php } else {
		?>
		<a href="admin_apk_items.php?apk_id=<?php echo $row['apk_id'];?>"><?php echo $row["app_name"];?></a>
		<?php 
	}?>
	</td>
	
	<td>
	<?php echo $row["package_name"];?>
	</td>
	
	<td>
	<?php echo $row["version_code"];?>
	</td>
	<td>
	<?php echo $row["version_name"];?>
	</td>
	
	<td>
	<?php echo $row["md5"];?>
	</td>
	
	<td>
	<?php echo $row["file_size"];?>
	</td>
	
	<td>
	<?php echo $row["upload_count"];?>
	</td>
	
	
	  
	    <td>
<!--	  <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改</a> -->
	   <a href="admin_ajax.php?action=del&tab=apk_master_temp&t_id=<?php echo $t_id?>" onClick="return confirm('确定要删除吗?');">删除</a> 
	   <?php if($row['state'] ==='4' && !isN($row['apk_id'])){?> | <a href="javascript:void(0)" onclick="edit('<?php echo $t_id?>');return false;">修改并入库</a> <?php }?>
	   | <a href="http://apk2.qiniudn.com/<?php echo $row["qiniu_file_key"]?>" onClick="return confirm('确定要下载吗?');">下载apk</a>
	  </td>
    </tr>
	<?php
			}
		}
	?>
	<tr>
	<td  colspan="7">全选<input type="checkbox" name="chkall" id="chkall" class="checkbox" onClick="checkAll(this.checked,'t_id[]')" />
	<input type="button" value="批量删除" id="btnDel" class="input"  />
<!--	&nbsp;<input type="button" value="批量修改" id="btnEdit" class="input" />-->
<!--	&nbsp;<input type="button" value="添加" id="btnAdd" class="input" />-->
    &nbsp;<input type="button" id="btnSelin" class="btn" name="Submit" value="入库所选" >
	&nbsp;<input type="button" id="AllInflowProject" class="btn" name="Submit" value="所搜应用全部入库" >
	</td></tr>
    <tr align="center" >
	<td colspan="8">
		<?php echo pagelist_manage($pagecount,$pagenum,$nums,app_pagenum,"apk_master_temp.php?page={p}&keyword=" . urlencode($keyword)."&status=".$status+"&state=".$state )?>
	</td>
    </tr>
</table>
</form>



<div id="win1" class="easyui-window" title="窗口" style="padding:5px;width:450px;" closed="true" closable="false" minimizable="false" maximizable="false">
<form action="admin_apk_temp.php?action=updateInFlow" method="post" name="form2" id="form2">
<table class="tb">
    <tr>
	<td width="20%"></td>
	<td>相同的版本号，但md5不同，是否需要入库
	</td>
	</tr>
	<input id="id" name="id" type="hidden" value="">
	<input id="flag" name="flag" type="hidden" value="">
	<input id="apk_id" name="apk_id" type="hidden" value="">
	<tr>
	<td width="30%">版本号：</td>
	<td><input id="version_code" size=40 value="" name="version_code">
	</td>
	</tr>
	
	<tr>
	<td width="30%">版本名：</td>
	<td><input id="version_name" size=40 value="" name="version_name">
	</td>
	</tr>
	
    <tr align="center" >
      <td colspan="2"><input class="input" type="submit" value="确认入库" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"></td>
    </tr>
</table>
</form>
</div>

</body>
</html>
<?php
unset($rs);
}

function updateInFlow(){
	global $db;
	 $id = be("all","id");
	 $version_code = be("all","version_code");
	 $version_name = be("all","version_name");
	 $apk_id = be("all","apk_id");
	
	 $row = $db->getRow("select * from apk_master_temp where id=".$id);
	 if(!isN($row['id'])){
	 	$package_name = $row["package_name"];
		$upload_count = $row["upload_count"];
		$file_url = $row["file_url"];
		$file_type=$row['file_type'];
		$qiniu_file_key = $row["qiniu_file_key"];
		$file_size = $row["file_size"];
		$status = $row["status"];
		$md5 = $row["md5"];
		
		$min_sdk_version = $row["min_sdk_version"];
		$target_sdk_version = $row["target_sdk_version"];
		$file_name = $row["file_name"];
		$app_name=$row['app_name'];
		$description=$row['description'];
		
		
	   //检查同一版本md5是否相同
    	$sql="select id ,md5 from apk_master_items where md5='".$md5 ."' and apk_id=".$apk_id ." and version_code='".$version_code ."' and version_name='".$version_name."' ";
	    $itemrowvod = $db->getRow($sql);	   
        if(!isN($itemrowvod["id"])){
    		//同一版本
    		$db->Update ("apk_master_temp",array('status','apk_id','state'),array('2',$temp_base_apk_id,'1'),"id=".$itemrowvod["id"]);
    	}else {
    		//新版本,直接入库
    	
	    	$colarr = Array("apk_id", "file_url", "file_type", "qiniu_file_key", "file_size", "md5", "version_code", "version_name", "min_sdk_version", "target_sdk_version", "file_name", "package_name", "create_date",  "upload_count", "download_count", "description");
			$valarr = array($apk_id,$file_url, $file_type, $qiniu_file_key,  $file_size ,  $md5, $version_code,   $version_name, $min_sdk_version,  $target_sdk_version,  $file_name,  $package_name,  date('Y-m-d H:i:s',time()),  $upload_count,  $download_count,  $description);
			$db->Add("apk_master_items", $colarr, $valarr);
			
			$db->query("update apk_master_base set upload_count=upload_count+".$upload_count." where id=".$apk_id);
			
//			$db->Update ("apk_master_base",array('upload_count'),array($temp_base_apk_upload_count+$upload_count),"id=".$apk_id);
			$db->Update ("apk_master_temp",array("version_code",'version_name','status','state'),array($version_code,$version_name,'2','1'),"id=".$id);
    	}
		
		
	 }
	 echo '入库完成';
	 
}

function MovieInflow($sql_collect,$MovieNumW)
{  
	global $db;
?>
<table class=tb>
	<tr>
		<td  colspan="2" align="center"> 入 库 状 态 </td>
		<div id="refreshlentext" style="background:#006600"></div>
		</td>
	</tr>
  	<tr>
		<td  colspan="2" align="center"><span id="storagetext">正 在 入 库...</span></td>
  	</tr>
</table>
<?php
	$iscover= be("iscover","get");
	$rs = $db->query($sql_collect);
	$rscount = $MovieNumW;
	
	if($rscount==0){
		echo "<script>alert('没有可入库的数据!'); location.href='admin_apk_temp.php';</script>";
		exit;
	}
	
	if ($rscount > 10000){
		$rscount = 1000;
	}
	elseif ($rscount > 5000) {
		$rscount = 500;
	}
	elseif ($rscount > 1000){
		$rscount = 100;
	}
	else{
		$rscount = 10;
	}
	
	$MovieInflowNum=0;
	while ($row = $db ->fetch_array($rs))
	{   $MovieInflowNum++;
	    $temp_id=$row["id"];
		$package_name = $row["package_name"];
		$upload_count = $row["upload_count"];
		$file_url = $row["file_url"];
		$file_type=$row['file_type'];
		$qiniu_file_key = $row["qiniu_file_key"];
		$file_size = $row["file_size"];
		$status = $row["status"];
		$md5 = $row["md5"];
		$version_code=$row['version_code'];
		$version_name = $row["version_name"];
		
		$min_sdk_version = $row["min_sdk_version"];
		$target_sdk_version = $row["target_sdk_version"];
		$file_name = $row["file_name"];
		$app_name=$row['app_name'];
		$description=$row['description'];
		
		
		//如果包名为null，不处理
		if(isN($package_name)){
			$db->Update ("apk_master_temp",array("state"),array('2'),"id=".$temp_id);
			continue;
		}
		
		//检查是否为一个新的应用
		$sql = "SELECT id,app_name ,upload_count FROM apk_master_base WHERE package_name = '".$package_name."' ";
		$rowvod = $db->getRow($sql);
		if(isN($rowvod["id"])){
			//新应用
			$colarr = Array("package_name", "create_date",  "upload_count",   "app_name","description");
			$valarr = array($package_name,date('Y-m-d H:i:s',time()),$upload_count,$app_name,$description);
			$db->Add("apk_master_base", $colarr, $valarr);
			$new_apk_id = $db->insert_id();
			
			$colarr = Array("app_id", "language_id",  "display_name",   "description");
			$valarr = array($new_apk_id,'zh_cn',$app_name,$description);
			$db->Add("apk_master_info", $colarr, $valarr);
			
			$colarr = Array("apk_id", "file_url", "file_type", "qiniu_file_key", "file_size","md5", "version_code", "version_name", "min_sdk_version", "target_sdk_version", "file_name", "package_name", "create_date",  "upload_count", "download_count", "description");
			$valarr = array($new_apk_id,$file_url, $file_type, $qiniu_file_key,  $file_size ,  $md5, $version_code,   $version_name, $min_sdk_version,  $target_sdk_version,  $file_name,  $package_name,  date('Y-m-d H:i:s',time()),  $upload_count,  $download_count,  $description);
			$db->Add("apk_master_items", $colarr, $valarr);
			
			$db->Update ("apk_master_temp",array("status"),array('2'),"id=".$temp_id);
			
		}else {//应用已经存在
			$temp_base_apk_id=$rowvod["id"];
			
			$temp_base_apk_upload_count=$rowvod["upload_count"];
			
			//应用名称是否一样
			if($app_name !== $rowvod["app_name"]){ 
				//应用名称不一样
				$db->Update ("apk_master_temp",array("state",'apk_id'),array('3',$temp_base_apk_id),"id=".$temp_id);
			}
			
			
			
			//检查是否存在相同的版本号
			$sql="select id ,md5 from apk_master_items where apk_id=".$temp_base_apk_id ." and version_code='".$version_code ."' and version_name='".$version_name."' ";
			$itemrowvod = $db->getRow($sql);
		    if(isN($itemrowvod["id"])){
		    	//新版本,直接入库
		    	$db->Update ("apk_master_temp",array('apk_id'),array($temp_base_apk_id),"id=".$temp_id);
		    	$colarr = Array("apk_id", "file_url", "file_type", "qiniu_file_key", "file_size",  "md5", "version_code", "version_name", "min_sdk_version", "target_sdk_version", "file_name", "package_name", "create_date",  "upload_count", "download_count", "description");
				$valarr = array($temp_base_apk_id,$file_url, $file_type, $qiniu_file_key,  $file_size ,   $md5, $version_code,   $version_name, $min_sdk_version,  $target_sdk_version,  $file_name,  $package_name,  date('Y-m-d H:i:s',time()),  $upload_count,  $download_count,  $description);
				$db->Add("apk_master_items", $colarr, $valarr);
				
				$db->Update ("apk_master_temp",array('status','apk_id'),array('2',$temp_base_apk_id),"id=".$temp_id);
				
				$db->Update ("apk_master_base",array('upload_count'),array($temp_base_apk_upload_count+$upload_count),"id=".$temp_base_apk_id);
		    }else{		    	
		    	
		    	//检查同一版本md5是否相同
		    	$sql="select id ,md5 from apk_master_items where md5='".$md5 ."' and apk_id=".$temp_base_apk_id ." and version_code='".$version_code ."' and version_name='".$version_name."' ";
			    $itemrowvod = $db->getRow($sql);
		        if(!isN($itemrowvod["id"])){
		    		//同一版本
		    		$db->Update ("apk_master_temp",array('status','apk_id'),array('2',$temp_base_apk_id),"id=".$temp_id);
		    	}else {
		    		//md5 不同
		    		$db->Update ("apk_master_temp",array("state",'apk_id'),array('4',$temp_base_apk_id),"id=".$temp_id);
		    	}
		    }
			
			
			
			
		}
		
		if (  fmod($MovieInflowNum,$rscount) == 0) {
		    echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "document.getElementById(\"refreshlentext\").style.width = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
			echo "document.getElementById(\"refreshlentext\").innerHTML = \"".($MovieInflowNum/$MovieNumW*100)."%\";";
			echo "document.getElementById(\"storagetext\").innerHTML = \"正在入库......\";";
			echo "</script>";
		}
	}
	echo "<script type=\"text/javascript\" language=\"javascript\">";
			echo "document.getElementById(\"refreshlentext\").style.width = \"100%\";";
			echo "document.getElementById(\"refreshlentext\").innerHTML = \"100%\";";
			echo "document.getElementById(\"storagetext\").innerHTML = \"入库完毕 <a href='".getReferer()."'>返回</a>\";";
			echo "alert('入库完毕'); ";
			echo "</script>";
	unset($rs);
}


?>