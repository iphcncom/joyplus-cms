<?php
class Crontab{
	
  public static function newCommand($command){
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
	
	public static function getCrontab(){
		$f=popen("crontab -l","r");
		while(!feof($f)){
		  echo (fgets($f)) .'\n';
		}
	}
	
	public static function replaceCommand($command,$id){
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
	
	public static function deleteCommand($command,$id){
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
}

?>