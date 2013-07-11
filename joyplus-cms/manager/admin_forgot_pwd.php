<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
switch($action)
{
	case "chgpwd" :headAdmin ("修改密码"); chgpwd();break;
	default : headAdmin ("修改密码");main();break;
}


dispseObj();

function chgpwd(){
	global $db;
	$oldP=be('all','old_pwds');
	$newP=be('all','new_pwd');$m_name = getCookie("adminname");
	 $row = $db->getRow("select m_id from {pre}manager where m_password='".md5($oldP)."' and m_name='".$m_name."'");
	 if(!isN($row['m_id'])){
	 	$db->Update ("{pre}manager",array('m_password'),array(md5($newP)),"m_name='".$m_name."'");
	 	echo '更新密码成功';
	 }else {
	 	echo '输入的旧密码不对';
	 }
}

function main()
{
	
?>
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{			
		     old_pwds:{
				required:true
			},
			new_pwd:{
				required:true,
				maxlength:32,
				minlength:6,
				CharAndDigst:true
			},
			new_pwd_conf:{
				required:true,
				maxlength:32,
				minlength:6,
				equalTo:new_pwd
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
	
});
</script>
<form action="admin_forgot_pwd.php?action=chgpwd" method="post" name="form1" id="form1">
<table class="admin_forgot_pwd tb">
	<tr>
	<td colspan='2'>
      <h3>更改用户密码</h3></td>
	</tr>
	<tr style="display:none">
	  <td align="right">旧密码</td> 
	  <td align="left"><input type='password' name='x' id="x" value="" ></td>
	</tr>
	
	<tr>
	  <td align="right">旧密码</td> 
	  <td align="left"><input type='password' name='old_pwds' id="old_pwds" value="" ></td>
	</tr>
	
	<tr>
	  <td align="right">新密码</td> 
	  <td align="left"><input type='password' name='new_pwd' id="new_pwd" value="" >必须大于6位,并且包含数字和字母
	  </td>
	</tr>
	
	<tr>
	  <td align="right">确认密码</td> 
	  <td align="left"><input type='password' name='new_pwd_conf' id="new_pwd_conf" value="" ></td>
	</tr>
	
    <tr><td align="right"></td> 
      <td align="left"><input class="input" type="submit" value="修改" id="btnSaves"> <input class="input" type="reset" value="返回" id="btnCancel"> </td>
    </tr>
</table>
</form>

</body>
</html>
<?php
}
?>