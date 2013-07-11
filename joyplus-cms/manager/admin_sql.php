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

function execImport(){
	global $db;
	$buffer = '';
	// Defaults for parser
	$sql = '';
	$start_pos = 0;
	$i = 0;
	$len= 0;
	$big_value = 2147483647;
	$delimiter_keyword = 'DELIMITER '; // include the space because it's mandatory
	$length_of_delimiter_keyword = strlen($delimiter_keyword);
	$buffer=iconv("UTF-8","UTF-8", file_get_contents($_FILES['import_file']['tmp_name']));
	$len = strlen($buffer);
	$GLOBALS['finished']=true;
	$sql_delimiter = ';';
	while ($i < $len) {
		$found_delimiter = false;
		// Find first interesting character
		$old_i = $i;
		// this is about 7 times faster that looking for each sequence i
		// one by one with strpos()
		if (preg_match('/(\'|"|#|-- |\/\*|`|(?i)(?<![A-Z0-9_])' . $delimiter_keyword . ')/', $buffer, $matches, PREG_OFFSET_CAPTURE, $i)) {
			// in $matches, index 0 contains the match for the complete
			// expression but we don't use it
			$first_position = $matches[1][1];
		} else {
			$first_position = $big_value;
		}
		/**
		 * @todo we should not look for a delimiter that might be
		 *       inside quotes (or even double-quotes)
		 */

		// the cost of doing this one with preg_match() would be too high
		$first_sql_delimiter = strpos($buffer, $sql_delimiter, $i);
		if ($first_sql_delimiter === false) {
			$first_sql_delimiter = $big_value;
		} else {
			$found_delimiter = true;
		}

		// set $i to the position of the first quote, comment.start or delimiter found
		$i = min($first_position, $first_sql_delimiter);

		if ($i == $big_value) {
			// none of the above was found in the string

			$i = $old_i;
			if (!$GLOBALS['finished']) {
				break;
			}
			// at the end there might be some whitespace...
			if (trim($buffer) == '') {
				$buffer = '';
				$len = 0;
				break;
			}
			// We hit end of query, go there!
			$i = strlen($buffer) - 1;
		}

		// Grab current character
		$ch = $buffer[$i];

		// Quotes
		if (strpos('\'"`', $ch) !== false) {
			$quote = $ch;
			$endq = false;
			while (!$endq) {
				// Find next quote
				$pos = strpos($buffer, $quote, $i + 1);
				/*
				 * Behave same as MySQL and accept end of query as end of backtick.
				 * I know this is sick, but MySQL behaves like this:
				 *
				 * SELECT * FROM `table
				 *
				 * is treated like
				 *
				 * SELECT * FROM `table`
				 */
				if ($pos === false && $quote == '`' && $found_delimiter) {
					$pos = $first_sql_delimiter - 1;
					// No quote? Too short string
				} elseif ($pos === false) {
					// We hit end of string => unclosed quote, but we handle it as end of query
					if ($GLOBALS['finished']) {
						$endq = true;
						$i = $len - 1;
					}
					$found_delimiter = false;
					break;
				}
				// Was not the quote escaped?
				$j = $pos - 1;
				while ($buffer[$j] == '\\') $j--;
				// Even count means it was not escaped
				$endq = (((($pos - 1) - $j) % 2) == 0);
				// Skip the string
				$i = $pos;

				if ($first_sql_delimiter < $pos) {
					$found_delimiter = false;
				}
			}
			if (!$endq) {
				break;
			}
			$i++;
			// Aren't we at the end?
			if ($GLOBALS['finished'] && $i == $len) {
				$i--;
			} else {
				continue;
			}
		}

		// Not enough data to decide
		if ((($i == ($len - 1) && ($ch == '-' || $ch == '/'))
		|| ($i == ($len - 2) && (($ch == '-' && $buffer[$i + 1] == '-')
		|| ($ch == '/' && $buffer[$i + 1] == '*')))) && !$GLOBALS['finished']) {
			break;
		}

		// Comments
		if ($ch == '#'
		|| ($i < ($len - 1) && $ch == '-' && $buffer[$i + 1] == '-'
		&& (($i < ($len - 2) && $buffer[$i + 2] <= ' ')
		|| ($i == ($len - 1)  && $GLOBALS['finished'])))
		|| ($i < ($len - 1) && $ch == '/' && $buffer[$i + 1] == '*')
		) {
			// Copy current string to SQL
			if ($start_pos != $i) {
				$sql .= substr($buffer, $start_pos, $i - $start_pos);
			}
			// Skip the rest
			$start_of_comment = $i;
			// do not use PHP_EOL here instead of "\n", because the export
			// file might have been produced on a different system
			$i = strpos($buffer, $ch == '/' ? '*/' : "\n", $i);
			// didn't we hit end of string?
			if ($i === false) {
				if ($GLOBALS['finished']) {
					$i = $len - 1;
				} else {
					break;
				}
			}
			// Skip *
			if ($ch == '/') {
				$i++;
			}
			// Skip last char
			$i++;
			// We need to send the comment part in case we are defining
			// a procedure or function and comments in it are valuable
			$sql .= substr($buffer, $start_of_comment, $i - $start_of_comment);
			// Next query part will start here
			$start_pos = $i;
			// Aren't we at the end?
			if ($i == $len) {
				$i--;
			} else {
				continue;
			}
		}
		// Change delimiter, if redefined, and skip it (don't send to server!)
		if (strtoupper(substr($buffer, $i, $length_of_delimiter_keyword)) == $delimiter_keyword
		&& ($i + $length_of_delimiter_keyword < $len)) {
			// look for EOL on the character immediately after 'DELIMITER '
			// (see previous comment about PHP_EOL)
			$new_line_pos = strpos($buffer, "\n", $i + $length_of_delimiter_keyword);
			// it might happen that there is no EOL
			if (false === $new_line_pos) {
				$new_line_pos = $len;
			}
			$sql_delimiter = substr($buffer, $i + $length_of_delimiter_keyword, $new_line_pos - $i - $length_of_delimiter_keyword);
			$i = $new_line_pos + 1;
			// Next query part will start here
			$start_pos = $i;
			continue;
		}

		// End of SQL
		if ($found_delimiter || ($GLOBALS['finished'] && ($i == $len - 1))) {
			$tmp_sql = $sql;
			if ($start_pos < $len) {
				$length_to_grab = $i - $start_pos;

				if (! $found_delimiter) {
					$length_to_grab++;
				}
				$tmp_sql .= substr($buffer, $start_pos, $length_to_grab);
				unset($length_to_grab);
			}
			// Do not try to execute empty SQL
			if (! preg_match('/^([\s]*;)*$/', trim($tmp_sql))) {
				$sql = $tmp_sql;
				$sql=preg_replace("/--.*\n/iU","",$sql);//去掉注释
				$sql = trim($sql);
				if((stripos($sql, "CREATE") !==false && stripos($sql, "CREATE") ==0 )|| (stripos($sql, "INSERT") !==false && strpos($sql, "INSERT")==0)
				    || (stripos($sql, "UPDATE") !==false && strpos($sql, "UPDATE")==0)|| (stripos($sql, "DELETE") !==false && strpos($sql, "DELETE")==0)){
					 
					echo "执行sql: " . $sql;
					echo '</br>';
					$rs2=$db->query($sql);
					echo "执行结果: " . mysql_affected_rows();
					echo '</br>';
				}else{
					$sql = str_replace("/*!40101", "", $sql);
					$sql = str_replace("*/", "", $sql);
					$rs2=$db->query($sql);
				}
				//                $iarr=array();
				//                 preg_match_all("/INSERT INTO .*\(.*\)\;/iUs",$sql,$iarr);
				//                 foreach($iarr as $c)
				//		      {
				//		      	var_dump($c);
				//                $rs2=$db->query($c);
				//              }
				// PMA_importRunQuery($sql, substr($buffer, 0, $i + strlen($sql_delimiter)));
				$buffer = substr($buffer, $i + strlen($sql_delimiter));
				// Reset parser:
				$len = strlen($buffer);
				$sql = '';
				$i = 0;
				$start_pos = 0;
				// Any chance we will get a complete query?
				//if ((strpos($buffer, ';') === false) && !$GLOBALS['finished']) {
				if ((strpos($buffer, $sql_delimiter) === false) && !$GLOBALS['finished']) {
					break;
				}
			} else {
				$i++;
				$start_pos = $i;
			}
		}
	}

}


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
		echo "执行sql: " . $sql;
					echo '</br>';
					$rs=$db->query($sql);
					echo "执行结果: " . mysql_affected_rows();
					echo '</br>';
		
	}
	 
	if(!isN($_FILES['import_file']['tmp_name'])) {

		execImport();
		
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
//	$nums=mysql_affected_rows();
	?>
	
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
		<td colspan="2">批量导入，请从计算机中上传文件： <input id="import_file" type="file"
			value="浏览" name="import_file"><input class="input" type="submit"
			value="执行" name="Submit"></td>
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
