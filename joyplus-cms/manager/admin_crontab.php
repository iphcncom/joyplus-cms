<?php
require_once ("admin_conn.php");
	$command = '*  *  *  *  * root wget http://cmsdev.joyplus.tv/manager/collect/auto_collect_vod_cj.php?p_id=7_1-6';
//	replaceCommand($command,'p_id=6_1-6');
	
	
     $action = be("all","action");
     switch($action){		
		default : headAdmin ("定时器管理"); main();break;
     }
    
	function newCommand($command){
		try{
		$f=popen("crontab -e","w");
		fputs($f,"i");
		fputs($f,$command);
		fputs($f,"\n");
		fputs($f,chr(27));
		fputs($f,":wq\r");
		pclose($f);
		}catch (Exception $e){
			var_dump($e);
		}
	}
	function getCrontab(){
		$f=popen("crontab -l","r");
		while(!feof($f)){
		  echo (fgets($f)) .'\n';
		}
	}
	function replaceCommand($command,$id){
		$f=popen("crontab -e","w");
		fputs($f,"/");
		fputs($f,"sourceId=".$id);
		fputs($f,"\n");
		fputs($f,"dd");
		fputs($f,"i");
		fputs($f,$command."");
		fputs($f,"\n");
		fputs($f,chr(27));
		fputs($f,":wq\r");
		pclose($f);
	}
	
	function deleteCommand($command,$id){
		$f=popen("crontab -e","w");
		fputs($f,"/");
		fputs($f,"sourceId=".$id);
		fputs($f,"\n");
		fputs($f,"dd");
		fputs($f,"\n");
		fputs($f,chr(27));
		fputs($f,":wq\r");
		pclose($f);
	}
	
function main()
{
?>
<table class="tb">
# For details see man 4 crontabs  </br>
</br>
# Example of job definition:</br>
# .---------------- minute (0 - 59)</br>
# |  .------------- hour (0 - 23)</br>
# |  |  .---------- day of month (1 - 31)</br>
# |  |  |  .------- month (1 - 12) OR jan,feb,mar,apr ...</br>
# |  |  |  |  .---- day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat</br>
# |  |  |  |  |</br>
# *  *  *  *  * user-name command to be executed</br>

</table>
</body>
</html>
<?php
}
?>