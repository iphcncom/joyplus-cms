<?php
  $url=$_GET['imgurl'];
  
  $img= imagecreatefromjpeg($url);
  header("Content-type: image/jpeg");
  imagejpeg($img);
?>


