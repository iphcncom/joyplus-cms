<?php 
/**
  * wechat php test
  */
   require_once (dirname(__FILE__)."/inc/conn.php");
   $prod_id = be("all","prod_id");
    global $db;
    //$sql = 'select d_id as prod_id, d_name as prod_name, d_type as prod_type,d_pic as prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area where mac_vod where d_hide=0  and d_type in (1,2,3,131) ';
   // var_dump($prod_id);
    $row = $db->getRow("SELECT d_pic_ipad, d_pic,d_name,d_directed,d_content,d_starring,d_year,d_area,d_video_desc_url FROM mac_vod WHERE d_id=".$prod_id);
 
    //echo getReferer();
	if (!$row){
?>
     <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>
       <head>
         <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
       </head>
       <body>
         <script>
         history.go(-1);
//            window.location ="<?php echo getReferer();?>";
         </script>
      </body>
    </html>
  <?php 
	}else{		   		
    	if(is_null($row['d_pic_ipad']) || $row['d_pic_ipad'] ===''){
    		$prod_pic=$row['d_pic'];
    	}else {
    		$prod_pic=parsePadPost($row['d_pic_ipad']);
    	}
    	
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="apple-itunes-app" content="app-id=587246114">
	<meta name="format-detection" content="telephone=no">
	<meta name="description" content="悦视频">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" />
	<title><?php echo $row['d_name'];?></title>
	 <script>
	function play() {
		var hasVideo = !!(document.createElement('video').canPlayType);
		if(hasVideo){
			document.getElementById("videoClicker").style.display = "none";
	        var player = document.getElementById("demoVideoPlayer");
	        player.style.display = "block";
	        player.style.zIndex = "2";
	        player.play();
//	        if(player.webkitEnterFullscreen){
//	        	player.webkitEnterFullscreen();
//	        }
	        play = function () {};
        }
       
    };
    </script>
	<style type="text/css">
	*{
		padding: 0;
		margin: 0;
		text-decoration: none;
	}
	body{
		background:#4a4f51;
		color:#ccc;
		font-family: 12px/150% Helvetica,Arial,'Microsoft yahei',simsun,sans-serif;
		text-shadow: 0 1px 1px rgba(0, 0, 0, .5);
	}
	h1{
		color: #FFF;
		float: left;
		line-height: 55px;
		font-size: 16px;
		font-weight: normal;
	}
	h2{
		font-size: 24px;
		margin:0 0 10px 0;
		padding: 5px 0;
		color: #FFF;
		clear: both;
		text-align: center;
	}
	.topbar {
		height:54px;
		padding: 5px 10px;
		background: #222;
		box-shadow: 0 -1px 3px rgba(0,0,0,1) inset;
	}
	.android{
		width: 100%;
		text-align: center;
	}
	.content{
		padding: 10px;
	}
	.small{
		color: #888;
		font-size: 12px;
		text-align: center;
		text-shadow: none;
	}
	.btn{
		color: #fff;
		display:block;
		vertical-align: center;
		outline: none;
		cursor: pointer;
		text-align:center;
		text-decoration:none;
		margin:10px 0;
		text-shadow: 0 1px 1px rgba(0, 0, 0, .7);
		border-radius: 6px;
		line-height: 30px;
		box-shadow: 0 1px 2px rgba(0,0,0,.5);
	}
	.green {
		background: -webkit-linear-gradient(-90deg, #487611,#336816);
		background: -webkit-gradient(linear, 0 0,0 100%, color-stop(0, #487611), color-stop(1, #336816));
		border-top: 1px solid #62853a;
		float:right;
		font-size: 14px;
		padding:2px 10px;
	}
	.black{
		background: -webkit-linear-gradient(-90deg, #5f6470,#4b4f58);
		background: -webkit-gradient(linear, 0 0,0 100%, color-stop(0, #5f6470), color-stop(1, #4b4f58));
		*background: #5f6470;
		*display:none;
		border-top: 1px solid #6f747f;
		width:100%;
		font-size: 24px;
		padding:10px 0;
	}
	.images{
		border-radius: 6px;
		box-shadow: 0 1px 2px rgba(0,0,0,.5);
		*margin-bottom:10px;
		overflow: hidden;
	}
	.center{
		text-align:center;
		position: relative;
	}
	p{
		font-size: 16px;
		line-height: 140%;
		margin: 0 0 6px 0;
	}
	.title{
		color: #FFF;
	}
	ul {
		overflow: hidden;
	}
	ul li{
		width: 32%;
		margin-right: 2%;
		float: left;
		position: relative;
	}
	ul li img{
		width: 100%;
		border-radius: 3px;
		box-shadow: 0 1px 2px rgba(0,0,0,.5);
		overflow: hidden;
	}
	ul li:last-child{
		margin-right:0;
	}
	.mask{
		position: absolute;
		display: block;
		width: 100%;
		height: 100%;
		left: 0;
		top: 0;
		text-indent: -99999em;
		background-image: url('play.png');
		background-position: 50% 45%;
		background-size:32px;
		background-repeat: no-repeat;
	}
	.center .mask {
		background-position: 50% 50%;
		background-size:64px;
	}
	h3{
		text-align: center;
		padding: 10px 0;
		color: #fff;
		font-weight: normal;
		font-size: 14px;
		clear: both;
	}
	#demoVideoPlayer{
		height: 180px;
	}
	#movie_player{
		height: 180px;
	}
	</style>
</head>
<body>
<?php
   $userAgent =  isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:""; //iPod
   if(strpos($userAgent, "iPhone") !==false || strpos($userAgent, "iPod") !==false || strpos($userAgent, "iPad") !==false){
?>
    <div class="topbar">
		<h1>微信号：悦视频</h1>
		<a class="btn green" href="http://weixin.qq.com/r/1HW4t1PERm05h0LUnyDw">关注我们的微信</a>
	 </div>
<?php 
   }else {
?>
	<div class="topbar">
		<h1 class="android">关注微信号：悦视频，观看最新影片</h1>
	 </div>
<?php
}
?>
	
	<div class="content">
		<h2><?php echo $row['d_name'];?></h2>
	  <div>
		<div class="center images img" id="videoClicker">
		<?php 
		    $d_video_desc_url= $row['d_video_desc_url'];
//		    $d_video_desc_url="http://static.video.qq.com/TPout.swf?vid=k0012fcrx27{Array}http://115.238.173.134:80/play/35C6F31DB4D756BA4349191C46BD180CDE735C75.mp4";
		    $canPlay=false;
		    $displayPlayPic=false;
		    if(!(is_null($d_video_desc_url) || $d_video_desc_url ==='')){
		    	$videoUrls=split("{Array}", $d_video_desc_url)	;
		    	$videoUrlIos="";
		    	$videoUrlAndroid="";
		    	if(count($videoUrls) ===2){
		    		if(strpos($videoUrls[0], ".swf") !==false){
		    			$videoUrlAndroid=$videoUrls[0];
		    			if(strpos($videoUrls[1], ".swf") ===false){
		    				$videoUrlIos=$videoUrls[1];
		    			}		    			
		    		}else {
		    			$videoUrlIos=$videoUrls[0];
		    		    if(strpos($videoUrls[1], ".swf") !==false){
		    				$videoUrlAndroid=$videoUrls[1];
		    			}
		    		}
		    	}
		    		
		    	if(count($videoUrls) ===1){
		    		if(strpos($videoUrls[0], ".swf") !==false){
		    			$videoUrlAndroid=$videoUrls[0];
		    		}else {
		    			$videoUrlAndroid=$videoUrls[0];
		    			$videoUrlIos=$videoUrls[0];
		    		}
		    	}
		    	
		    	if(strpos($userAgent, "iPhone") !==false || strpos($userAgent, "iPod") !==false || strpos($userAgent, "iPad") !==false ){
		    		if(!(is_null($videoUrlIos) || $videoUrlIos ==='')){
		    		  $canPlay=true;
		    		  $displayPlayPic=true;
		    		}
		    	}else {
		    		if((!(is_null($videoUrlAndroid) || $videoUrlAndroid ==='')) && strpos($videoUrlAndroid, "m3u") ===false){
		    			$canPlay=true;
		    			if(strpos($videoUrlAndroid, ".swf") ===false){
		    				$displayPlayPic=true;
		    			}
		    		}
		    	}
		    }
		    if(!(is_null($prod_pic) || $prod_pic ==='')){
		      if(strpos($prod_pic, "douban") !==false){
		      	$prod_pic='image.php?imgurl='.urlencode($prod_pic);
		      }
		      if($canPlay ){
		      	if($displayPlayPic){
		      	
	    ?>
		        <a href="javascript: void(0);" onclick="play();">
		        <img  width="100%" src="<?php echo $prod_pic; ?>" />
		        <i class="mask"></i>
		        </a>
	        
	    <?php  }
		      }else {
		?>
		    <img  width="100%" src="<?php echo $prod_pic; ?>" />
		<?php       	
		      }             
		    }
		    
		    
		?>	    
		</div>
		<?php  if($canPlay){?>
		  <a href="javascript: void(0);" onclick="play();">
		  <?php if(strpos($userAgent, "iPhone") !==false || strpos($userAgent, "iPod") !==false || strpos($userAgent, "iPad") !==false ){?>
		     <video id="demoVideoPlayer" src="<?php echo $videoUrlIos;?>" controls="controls" style="display: none;" width="100%"></video>		
		  <?php }else {
		  	      if(strpos($videoUrlAndroid, ".swf") !==false){
		  ?>
			      <object type="application/x-shockwave-flash" data="<?php echo $videoUrlAndroid;?>"
							width="100%" height="100%" id="movie_player">
							<param name="allowFullScreen" value="true">
							<param name="allowscriptaccess" value="always">
							<div class="player_html5">
								<span style="font-size: 18px">您需要<a
									href="http://www.adobe.com/go/getflash" target="_blank">安装flash播放器</a></span>
							</div>
						</object>
		  <?php 	
		  	      }else {
		  ?>
		                 <video id="demoVideoPlayer" src="<?php echo $videoUrlAndroid;?>" controls="controls" style="display: none;" width="100%"></video>		
		  <?php 	      	
		  	      } 
		  	   }
		        
		  ?>  
		  </a>
		<?php }?>
		</div>
		<?php
		   //$userAgent =  isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:""; //iPod
		   if(strpos($userAgent, "iPhone") !==false || strpos($userAgent, "iPod") !==false || strpos($userAgent, "iPad") !==false){
		?>
		    <a class="btn black" href="http://ums.bz/GGISox/">下载悦视频观看</a>
		<?php 
		   }else {
		?>
			<a class="btn black" href="http://ums.bz/7J5Q4k/">下载悦视频观看</a>
			<p class="small">安卓版支持Android4.0及以上系统</p>
		<?php
		}
		?>
		<p><span class="title">导演</span>：<?php echo $row['d_directed'];?></p>
		<p><span class="title">主演</span>：<?php echo $row['d_starring'];?></p>
		<p><span class="title">上映</span>：<?php echo $row['d_year'];?></p>
		<p><span class="title">地区</span>：<?php echo $row['d_area'];?></p>
		<p><span class="title">简介</span>：</p>
		<p><?php echo $row['d_content'];?></p>
		<p><span class="title">猜你喜欢</span>：</p>
		<ul>		 
			<?php 
			  unset($row); 
			  $movies= getRelateMovies($prod_id);
			  foreach ($movies as $movie){			  	
			    $prod_pic=$movie['prod_pic'];
			?>
			    <li>
			    <?php 
				    if(!(is_null($prod_pic) || $prod_pic ==='')){
				      if(strpos($prod_pic, "douban") !==false){
				      	$prod_pic='image.php?imgurl='.urlencode($prod_pic);
				      }
		              echo '<img height="140px" src="'.$prod_pic.'" />';
				    }
				?>
				<h3><?php echo $movie['prod_name']; ?></h3>
				<a href="http://weixin.joyplus.tv/info.php?prod_id=<?php echo $movie['prod_id']; ?>" class="mask">PLAY</a>
			</li>
			<?php  	
			  }
			?>
			
		</ul>
	</div>
</body>
</html>


<?php 
   
    	   	  		
    } 
     
    function getRelateMovies($prodid){
    	global $db;
//    	$rs = $db->query("SELECT topic_id FROM mac_vod_topic_items WHERE vod_id =".$prodid);
    	$rs = $db->query("SELECT topic_id FROM mac_vod_topic_items,mac_vod_topic WHERE  topic_id=t_id and t_bdtype=1 and vod_id =".$prodid);
		$movie=array();
    	while ($row = $db ->fetch_array($rs)){    		
    		$movie[]=$row['topic_id'];
	    }
	    unset($rs);
	    if(count($movie) ===0){
	    	$topicid='140';
	    }else {
		   $topicid=implode(",", $movie);
	    }
    	
		writetofilelog("info-log.log", $topicid);
		$sql='SELECT  DISTINCT d_id AS prod_id, d_name AS prod_name, d_pic AS prod_pic_url, d_pic_ipad FROM mac_vod, mac_vod_topic_items WHERE d_hide =0 AND vod_id = d_id AND topic_id in ('.$topicid.') and d_id !='.$prodid.' ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC LIMIT 0, 3';
    	writetofilelog("info-log.log", $sql);
		$rs = $db->query($sql);
    	$movies=array();
    	while ($row = $db ->fetch_array($rs)){
    		$movie=array();
    		$movie['prod_id']=$row['prod_id'];
    		$movie['prod_name']=$row['prod_name'];		
    		if(is_null($row['d_pic_ipad']) || $row['d_pic_ipad'] ===''){
    			$movie['prod_pic']=$row['prod_pic_url'];
    		}else {
    			$movie['prod_pic']=parsePadPost($row['d_pic_ipad']);
    		}
	  		$movies[]=$movie;
	    }
	    unset($rs);
	    return $movies;
		//
    }
    
    function parsePadPost($pic_url){
	  if(isset($pic_url) && !is_null($pic_url)){
	      $prodPicArray = explode("{Array}", $pic_url);	  
	      if(count($prodPicArray)>0){
		      return $prodPicArray[0];
		  }
	  }
	  return $pic_url;
	}
    function writetofilelog($file_name,$text) {
	     $date_time = date("Y-m-d H:i:s");
	     $text = "$date_time: ".$text;
		 $date = date("Y-m-d");
		 $fileArray = explode(".", $file_name);
		 if(count($fileArray)==2){
		 	$file_name =$fileArray[0].'_'.$date.'.'.$fileArray[1];
		 } 
		 $file_name = dirname(__FILE__).'/logs/'.$file_name;
		// var_dump($file_name);
		if (!file_exists($file_name)) {
	      touch($file_name);
	      chmod($file_name,"744");
	    }
	
	   $fd = @fopen($file_name, "a");
	   @fwrite($fd, $text."\r\n");
	   @fclose($fd);
	
	}
    
?>


