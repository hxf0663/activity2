<?php
$path = '/home/wwwroot/sc_t_ugomedia_net/public_html/images/';
function getfiles($path){
   if(!is_dir($path)) return;
   $handle  = opendir($path);
   $files = array();
   while(false !== ($file = readdir($handle))){
    if($file != '.' && $file!='..'){
     $path2= $path.'/'.$file;
     if(is_dir($path2)){
     getfiles($path2);
     }else{
        if(preg_match("/\.(gif|jpeg|jpg|png|bmp)$/i", $file)){
        $files[] = $path.'/'.$file;
        echo '{id:"mysrc", src:"images/'.$file.'"},<br>';
     }
     }
    }
   }
   return $files;
}
getfiles($path);