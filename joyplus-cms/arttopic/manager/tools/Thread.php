<?php

/**
 
@title:PHP多线程类(Thread)
 
@version:1.0
 
@author:axgle <axgle@126.com>
 
*/ 

$th=new thread(10);//10个线程
 
$th->exec('demo');//执行自定义的函数
 

function demo() {
 
        fopen('data/'.microtime(),'w');
 
}
 

class thread {
 
        var $count;
 
        function thread($count=1) {
 

                $this->count=$count;
 
        }
 
   
 
           function _submit() {
 
                for($i=1;$i<=$this->count;$i++) $this->_thread(); 
                return true;
 
        }
 

        function _thread() {
 
                $fp=fsockopen($_SERVER['HTTP_HOST'],80); 
                fputs($fp,"GET $_SERVER[PHP_SELF]?flag=1\r\n"); 
                fclose($fp); 
        }
 
        
        function exec($func) {
 
                isset($_GET['flag'])?call_user_func($func):$this->_submit(); 
        }
 


}
 


?>