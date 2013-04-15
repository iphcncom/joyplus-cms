<?php 
/**
  * wechat php test
  */
   require_once (dirname(__FILE__)."/../inc/conn.php");
   $rule=array(
    	 '140'=>array(
    	        '7280'=>array(
			    	      '1'=>'10',
			    	      '2'=>'9',
		    	        )
    	        ),
    	 '141'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'8'
		    	        ) 
    	        ),
    	 '3472'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'7'
		    	        ) 
    	        ),
    	 '142'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'6'
		    	        ) 
    	        ),
    	 '143'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'5'
		    	        ) 
    	        ),
    	 '2704'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'4'
		    	        ) 
    	        ),
    	 '145'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'3'
		    	        ) 
    	        ),
    	 '146'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'2'
		    	        ) 
    	        ),
    	 '144'=>array(
    	        '7280'=>array(
		    	       	  '1'=>'1'
		    	        ) 
    	        ),
    	        
    	        
    	        
    	 '147'=>array(
    	        '7282'=>array(
			    	      '1'=>'10',
			    	      '2'=>'9',
		    	        )
    	        ),
    	  '2250'=>array(
    	        '7282'=>array(
			    	      '1'=>'8',
			    	      '2'=>'7',
		    	        )
    	        ),
    	  '150'=>array(
    	        '7282'=>array(
			    	      '1'=>'6',
			    	      '2'=>'5',
		    	        )
    	        ),
    	  '918'=>array(
    	        '7282'=>array(
			    	      '1'=>'4',
			    	      '2'=>'3',
		    	        )
    	        ),
    	 '148'=>array(
    	        '7282'=>array(
		    	       	  '1'=>'2'
		    	        ) 
    	        ),
    	 '149'=>array(
    	        '7282'=>array(
		    	       	  '1'=>'1'
		    	        ) 
    	        ),
    	        
    	  '5429'=>array(
    	        '7284'=>array(
			    	      '1'=>'10',
			    	      '2'=>'9',
    	                  '3'=>'8',
		    	        )
    	        ),
    	  '7112'=>array(
    	        '7284'=>array(
			    	      '1'=>'7',
			    	      '2'=>'6',
		    	        )
    	        ),
    	  '7176'=>array(
    	        '7284'=>array(
			    	      '1'=>'5',
			    	      '2'=>'4',
		    	        )
    	        ),
    	  '7188'=>array(
    	        '7284'=>array(
			    	      '1'=>'3',
		    	        )
    	        ),
    	  '7193'=>array(
    	        '7284'=>array(
			    	      '1'=>'2',
		    	        )
    	        ),
    	  '7265'=>array(
    	        '7284'=>array(
			    	      '1'=>'1',
		    	        )
    	        ),
    	);
   
    function replaceTop($topic_id,$rec_topic_id,$rec_topic_rule){
    	global $db;    	
//    	var_dump($rec_topic_id);
    	
        $rs = $db->query("SELECT vod_id  FROM mac_vod_topic_items WHERE flag=1 and topic_id =".$topic_id." ORDER BY disp_order DESC LIMIT 0 , 10");
		$movie=array();
    	while ($row = $db ->fetch_array($rs)){    		
    		$movie[]=$row['vod_id'];
	    }
        foreach (array_keys($rec_topic_rule) as $key){
    		$position=$key;
    		$position=$position-1;
    		$displayOrder=$rec_topic_rule[$key];
    		$movieid=$movie[$position];
    		$d_addtime= date('Y-m-d H:i:s',time());
//  		    var_dump($movieid);
//  		    var_dump($position);
//  		    var_dump($displayOrder);
    		$id=$db->getRow('select id from mac_vod_topic_items where flag=1 and topic_id ='.$rec_topic_id .' and disp_order='.$displayOrder);
    		$id=$id['id'];
    		if(!isN($id)){
//    		   var_dump($id);
    		   $rs = $db->query("update mac_vod_topic_items set vod_id='".$movieid."' WHERE id =".$id);
    		}else {
    			$rs = $db->query("insert into mac_vod_topic_items (topic_id,vod_id,flag,disp_order,create_date) values('".$rec_topic_id."','".$movieid."',1,'".$displayOrder."','".$d_addtime."')");
    		}
        }
	    

    }
    
    
    
    //replaceTopRecommend()
    
    function replaceTopRecommend($topic_id){  
        global $rule;   	
    	if(array_key_exists($topic_id, $rule)){
    		$topicRules= $rule[$topic_id];
    		
    		foreach (array_keys($topicRules) as $key){
    			$rec_topic_id=$key;
    			$rec_topic_rule=$topicRules[$key];
    			replaceTop($topic_id,$rec_topic_id,$rec_topic_rule);
    		}
    		
    		
    	}
        
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




