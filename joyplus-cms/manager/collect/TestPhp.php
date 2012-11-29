<?php 
  $s = 'a6hh11b';
  
  $temp = str_split( $s);
  $flag=false;
  $setNum="";
  if(isset($temp) && is_array($temp)){
  	foreach ($temp as $num){
  		if(is_numeric($num)){
  			$setNum=$setNum.$num;  
  			$flag=true;			
  		}else {
  			if($flag){
  				break;
  			}
  		}
  	}
  }
//  echo $setNum;
//  $m_playfrom='letv';
//  $d_type='3';
//  if($m_playfrom !== 'letv' && $d_type !=='2'){
//  	var_dump("d");
//  }else {
//  	var_dump("4");
//  }
  $url="http://www.letv.com/ptv/pplay/79075/16.html";
  $urlA= explode("/", $url);
		if(isset($urlA) && is_array($urlA) && count($urlA)>0){
			$num= $urlA[count($urlA)-1];
			$num=replaceStr($num, ".html", "");
			echo $num;
			
		}
		
		function replaceStr($text,$search,$replace)
{
	
	$res=str_replace($search,$replace,$text);
	return $res;
}
//  time();
   echo time(); 
   echo md5('ijoyplus001');
//  $link="http://www.youku.com/show_page/id_z2fb3434a561a11e1b356.html";
//  try {
//						$pos = strpos($link, "href=\"");
//						  if ($pos !== false) {
//							$link=substr($link, $pos+6);
//						  }
//						  $pos = strpos($link, "\"");
//						  if ($pos !== false) {
//							$link=substr($link, 0,$pos);
//						  }	
//						
//					} catch (Exception $e) {
//					}
//					var_dump($link)
//   echo $s;
//   echo (time() + (7 * 24 * 60 * 60)); 
 $UrlTestMoive ='http://www.tudou.com/albumplay/AptLd3ZlHq4/vwkSN1p4ueY.html';
 $UrlTest="sde";
 $urArray = explode("/", $UrlTestMoive);
//					writetofile("d:\\ts.txt","ss:".$UrlTestMoive);
					var_dump($urArray);
					$ur="";
					for($k=0;$k<count($urArray)-1;$k++){
						$ur=$ur.$urArray[$k]."/";
					}
					$UrlTest=$ur.$UrlTest.".html";
					echo $UrlTest;
// $text=str_replace("\n","",$text);
// $text=str_replace("\r","",$text);
// $text=str_replace("\r\n","",$text);
// echo $text;
					
?>