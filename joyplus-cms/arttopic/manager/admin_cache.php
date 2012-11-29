<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");
$stype = be("get","type");
if (isN($stype)){ $stype= 0;} else { $stype = intval($stype);}

switch($action)
{
	case "cls" : cls();break;
	case "upto": update();break;
	case "uptodata": updatedata();break;
	case "uptofile": updatefile();break;
	case "uptoindex": updateindex();break;
	default : headAdmin ("缓存管理"); main();break;
}
dispseObj();

function update()
{
	$cachePath= root.'upload/cache';
	if ($handle = opendir($cachePath)){
	   while (false !== ($item = readdir($handle))){
		   if ($item != "." && $item != ".." ){
			   if (is_dir("$cachePath/$item")){
			   } 
			   else{
			   	  unlink("$cachePath/$item");
			   }
		   }
	   }
	   closedir( $handle );
	}
	unset($handle);
	echo "";
}

function updatedata()
{
	updateCacheFile();
	echo "";
}

function updatefile()
{
	$cachePath= root.'upload/cache';
	if ($handle = opendir($cachePath)){
		if (is_dir("$cachePath/app")){
			delFileUnderDir("$cachePath/app");
		}
		if (is_dir("$cachePath/vodlist")){
			delFileUnderDir("$cachePath/vodlist");
		}
		if (is_dir("$cachePath/artlist")){
			delFileUnderDir("$cachePath/artlist");
		}
		if (is_dir("$cachePath/search")){
			delFileUnderDir("$cachePath/search");
		}
		if (is_dir("$cachePath/client")){
			delFileUnderDir("$cachePath/client");
		}
	}
	closedir( $handle );
	unset($handle);
	echo "";
}
function updateindex()
{
	if(file_exists("../index.html")){
		unlink("../index.html");
	}
	if(file_exists("../index.htm")){
		unlink("../index.htm");
	}
	echo "";
}

function delDirAndFile( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delDirAndFile( "$dirName/$item" );
				} else {
					if( unlink( "$dirName/$item" ) )echo "成功删除文件： $dirName/$item<br />\n";
				}
	   		}
	   }
	   closedir( $handle );
	   if( rmdir( $dirName ) )echo "成功删除目录： $dirName<br />\n";
	}
	unset($handle);
}
function delFileUnderDir( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delFileUnderDir( "$dirName/$item" );
				} else {
					if( unlink( "$dirName/$item" ) )echo "成功删除文件： $dirName/$item<br />\n";
				}
			}
   		}
	closedir( $handle );
	}
	unset($handle);
}

function main()
{
?>
<table class="tb">
<?php
	$i=0;
	$cachePath= root.'upload/cache';
	if ($handle = opendir($cachePath)){
	   while (false !== ($item = readdir($handle))){
		   if ($item != "." && $item != ".." ){
			   if (is_file("$cachePath/$item")){
			   	   $i++;
			   	echo $item."<br>";
			   }
		   }
	   }
	   closedir( $handle );
	}
	unset($handle);
	if ($i==0){echo "<tr><td align=center> 当前系统中没有缓存数据 </td></tr>";}
?>
</table>
</body>
</html>
<?php
}
?>