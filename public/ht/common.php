<?php
require('config.php');

$flag='微信营销活动管理后台';

$copyright='2016 &copy; UGO Media.';

if(isset($_GET['url'])){
	if(isset($_GET['cdns'])){//跨域
		echo $_GET['callback'].'('.json_encode(wxsdk($_GET['url'])).')';
	}else{
		echo json_encode(wxsdk($_GET['url']));
	}
}

/**
 * 错误调试函数
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
function p($var)
{
	if (is_bool($var)) {
		var_dump($var);
	}elseif (is_null($var)) {
		var_dump($var);
	}else{
		echo "<pre style='position:relative;z-index:1000;padding:10px;border-radius:5px;background:#F5F5F5;border:1px solid #aaa;font-size:14px;line-height:18px;opacity:0.9;'>" . print_r($var, true) . "</pre>";
	}
}

/**
 * mysql数据库操作函数
 * @param  string  $sql   SQL语句
 * @param  integer $isone 是否查询字段值
 * @return [type]         查询不存在返回notexist，存在返回字段值或下标从1开始的二维数组；执行成功返回true
 */
function db0($sql,$isone=0){
	$con=mysql_connect(DBHOST,DBUSER,DBPWD);
	if(!$con){die('could not connect: '.mysql_error());}
	if(!mysql_select_db(DBNAME,$con)){die('select database failed: '.mysql_error());}
	mysql_query("set names 'utf8'");
	if(strpos($sql,"select")!==false){
		$rs=mysql_query($sql);
		if($rs==false){
			die('Error: '.mysql_error().'<br>sql:'.$sql);
			return false;
		}
		if(mysql_num_rows($rs)<1){
			return "notexist";
		}else{
			if($isone==1){//返回第一条记录第一个字段值
				$row=mysql_fetch_array($rs);
				return $row[0];
			}else{//返回二维数组下标从1开始
				$i=0;
				while($row=mysql_fetch_array($rs)){
					$i+=1;
					$data[$i]=array();
					$data[$i]=$row;
				}
				return $data;
			}
		}
	}else{
		if(!mysql_query($sql,$con)){
			die('Error: '.mysql_error().'<br>sql:'.$sql);
			return false;
		}else{
			return true;
		}
	}
	//mysql_close($con);
}

/**
 * mysqli数据库操作函数
 * @param  string  $sql   SQL语句
 * @param  integer $isone 是否查询单条记录
 * @param  integer $fieldone 查询单条记录下是否返回第一个字段值或整条记录以一维数组返回
 * @return [type]         查询不存在返回notexist，存在返回字段值或下标从1开始的二维数组；执行成功返回true
 */
$con=new mysqli(DBHOST,DBUSER,DBPWD,DBNAME,DBPORT);
if($con->connect_errno){die('CONNECT ERROR: '.$con->connect_error);}
function db($sql,$isone=0,$fieldone=1){
	global $con;
	$con->set_charset('utf8');
	if(strpos($sql,"select")!==false){
		$rs=$con->query($sql);
		if($rs===false){
			die('Error: '.$con->errno.':'.$con->error.'<br>sql:'.$sql);
		}
		if($rs->num_rows<1){
			return "notexist";
		}else{
			if($isone==1){
				if($fieldone==1){
					$row=$rs->fetch_row();//取得结果集中一条记录作为索引数组返回
					$rs->free();//释放结果集
					return $row[0];//返回第一条记录第一个字段值
				}else{
					$row=$rs->fetch_assoc();//取得结果集中一条记录作为关联数组返回
					$rs->free();//释放结果集
					return $row;//返回第一条记录的一维数组
				}

			}else{//返回二维数组下标从1开始
				$i=0;
				while($row=$rs->fetch_array(MYSQLI_BOTH)){//MYSQLI_NUM(下标为索引)、MYSQLI_ASSOC(下标为字段名)、MYSQLI_BOTH(默认)
					$i+=1;
					$data[$i]=array();
					$data[$i]=$row;
				}
				$rs->free();//释放结果集
				return $data;
			}
		}
	}else{
		$rs=$con->query($sql);
		if($rs){
			return true;
		}else{
			die('Error: '.$con->errno.':'.$con->error.'<br>sql:'.$sql);
		}
	}
	//$con->close();
}

function db_filter($str){
	$str2=htmlspecialchars($str);
	if(!get_magic_quotes_gpc()){
		$str2=addslashes($str);
	}
	return $str2;
}

function go($alert,$jump='',$exit=1){
	echo "<script>alert('{$alert}');</script>";
	if($jump!=''){
		jump($jump);
	}else{
		echo "<script>window.history.back();</script>";
	}
	if($exit==1)exit;
}

function jump($url,$mode=1,$exit=1){
	if($mode==1){
		echo "<script>location.replace('{$url}');</script>";
	}else{
		echo "<script>location.href='{$url}';</script>";
	}
	if($exit==1)exit;
}

function conf($item){
	return db("select conf from ".PREFIX."_config where item='{$item}' limit 1",1);
}
function setconf($item,$conf){
	db("update ".PREFIX."_config set conf='{$conf}' where item='{$item}'");
}

function getAT($refresh=0){//获取access_token
	//$json=file_get_contents("http://campaign.ugomedia.net/wxsdk.php");
	// $json=file_get_contents("http://fotilewechat.fotile.com/oauth/token?platId=c2a1809168b6d3fdcb101ff2bfd30f7c");
	$json=file_get_contents("http://wechat.huangxf.com/single/wxapi.php?method=getat");
	$signPackage=json_decode($json,true);
	return $signPackage["access_token"];

	$appid=conf('appid');
	$appsecret=conf('appsecret');
	if($refresh==1||conf('access_token_cache')=='0'||conf('access_token_expire')<time()){
		$res=httpGet("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}");
		$json=json_decode($res);
		$access_token=$json->access_token;
		setconf('access_token',$access_token);
		setconf('access_token_expire',time()+7000);
		return $access_token;
	}else{
		return conf('access_token');
	}
}

function getJT($refresh=0){//获取jsapi_ticket
	//$json=file_get_contents("http://campaign.ugomedia.net/wxsdk.php");
	// $json=file_get_contents("http://fotilewechat.fotile.com/oauth/ticket?platId=c2a1809168b6d3fdcb101ff2bfd30f7c");
	$json=file_get_contents("http://wechat.huangxf.com/single/wxapi.php?method=getjt");
	$signPackage=json_decode($json,true);
	return $signPackage["jsapi_ticket"];

	if($refresh==1||conf('jsapi_ticket_cache')=='0'||conf('jsapi_ticket_expire')<time()){
		$res=httpGet("https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".getAT());
		$json=json_decode($res);
		$jsapi_ticket=$json->ticket;
		setconf('jsapi_ticket',$jsapi_ticket);
		setconf('jsapi_ticket_expire',time()+7000);
		return $jsapi_ticket;
	}else{
		return conf('jsapi_ticket');
	}
}

function wxsdk($url=''){//令牌与网页接口JSON数据
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url0 = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url=$url==''?$url0:$url;

	//urlencode传输
	$url=urlencode($url);
	//$json=file_get_contents("http://campaign.ugomedia.net/wxsdk.php?url=".$url);
	$json=file_get_contents("http://wechat.huangxf.com/single/wxapi.php?&method=wxsdk&url=".$url);
	$signPackage=json_decode($json,true);
	return $signPackage;

	$appid=conf('appid');
	$access_token = getAT();
    $jsapiTicket = getJT();
    $timestamp = time();

	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$length=16;
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
	$nonceStr = $str;

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $appid,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string,
	  "access_token" => $access_token,
	  "jsapi_ticket" => $jsapiTicket
    );
    return $signPackage;
}

function getinfo($openid){
	$access_token=getAT();
	$res=json_decode(httpGet("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}"),true);
	if(!isset($res['errcode'])){
		return array(
			"subscribe"=>$res['subscribe'],
			"openid"=>$res['openid'],
			"nickname"=>$res['nickname'],
			"sex"=>$res['sex'],
			"language"=>$res['language'],
			"city"=>$res['city'],
			"province"=>$res['province'],
			"country"=>$res['country'],
			"headimgurl"=>$res['headimgurl'],
			"subscribe_time"=>$res['subscribe_time'],
			"unionid"=>$res['unionid'],
			"remark"=>$res['remark'],
			"groupid"=>$res['groupid']
		);
	}else{
		return false;
	}
}

function sendtext($openid,$text){
	$access_token=getAT();
	$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	$post='{
		"touser":"'.$openid.'",
		"msgtype":"text",
		"text":
		{
			 "content":"'.$text.'"
		}
	}';
	$json=json_decode(httpPost($url,$post),true);
	if($json['errcode']==0){
		return true;
	}else{
		logger($json['errcode'].$json['errmsg']);
		return false;
	}
}

function sendnews($openid,$tw=array()){
	for($i=1;$i<=count($tw);$i++){
		$articles.='{
				 "title":"'.$tw[$i]['title'].'",
				 "description":"'.$tw[$i]['description'].'",
				 "url":"'.$tw[$i]['url'].'",
				 "picurl":"'.$tw[$i]['picurl'].'"
			 },';
		$articles=rtrim($articles,",");
	}
	$access_token=getAT();
	$url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
	$post='{
		"touser":"'.$openid.'",
		"msgtype":"news",
		"news":{
			"articles": ['.$articles.']
		}
	}';
	$json=json_decode(httpPost($url,$post),true);
	if($json['errcode']==0){
		return true;
	}else{
		logger($json['errcode'].$json['errmsg']);
		return false;
	}
}

function send_template_message($data){
	$url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.getAT();
	$res=httpPost($url,$data);
	return json_decode($res,true);
}

function httpGet($url='', $options=array()){
	$ch = curl_init($url);
	//https请求，防止验证证书出错
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	if (!empty($options)){
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function httpPost($url='', $postdata, $options=array()){
	$ch = curl_init($url);
	// $headers = array("Content-type: application/json");
	// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//https请求，防止验证证书出错
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	if (!empty($options)){
		curl_setopt_array($ch, $options);
	}
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function allowupload($filename,$filesize){
	$subfix=array("jpg","png","mp3","amr","mp4");
	$size=array(200000,200000,1000000,1000000,5000000);
	if(!in_array(str_replace(".","",strstr($filename,".")),$subfix)){
		return '不允许上传此类型的文件！';
	}
	if($filesize>$size[array_search(substr($filename,strrpos($filename,".")+1,strlen($filename)-strrpos($filename,".")),$subfix)]){
		return '上传文件大小超出限制！';
	}
	return true;
}
function chktype($file){
	if(strpos($file,".")!=false){
		$subfix=end(explode('.',$file));
	}else{
		$subfix=$file;
	}
	$image=array("jpg","png");
	$audio=array("mp3","amr");
	$video=array("mp4");
	if(in_array($subfix,$image)){
		return 'image';
	}else if(in_array($subfix,$audio)){
		return 'voice';
	}else if(in_array($subfix,$video)){
		return 'video';
	}else{
		return 'others';
	}
}

function logger($log_content,$cls=0){//日志
	$max_size = 1000000000;
	$log_filename = "log.txt";
	if (!file_exists($log_filename)) file_put_contents($log_filename,"\xEF\xBB\xBF".'log file create at '.date('Y-m-d H:i:s',time())."\r\n\r\n");
	if(file_exists($log_filename) and ( (abs(filesize($log_filename)) > $max_size) or $cls==1 )){unlink($log_filename);return;}//清空日志
	if(!is_array($log_content)){
		file_put_contents($log_filename, "\r\n".date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
	}else{
		file_put_contents($log_filename, "\r\n".date('Y-m-d H:i:s')." Array\r\n", FILE_APPEND);
		output_arr($log_filename,$log_content);
	}
}
function output_arr($log_filename,$arr,$indent=0){
    if (!is_array ($arr)){
        return false;
    }
    foreach ($arr as $key => $val ){
        if (is_array ($val)){
			if($indent!=0){
				for($i=1;$i<=$indent;$i++){
					file_put_contents($log_filename, "\t", FILE_APPEND);
				}
			}
			file_put_contents($log_filename, "[{$key}] => {$val}\r\n", FILE_APPEND);
            output_arr($log_filename,$val,$indent+1);
        }else{
			if($indent!=0){
				for($i=1;$i<=$indent;$i++){
					file_put_contents($log_filename, "\t", FILE_APPEND);
				}
			}
            file_put_contents($log_filename, "[{$key}] => {$val}\r\n", FILE_APPEND);
        }
    }
}

function fiterChar($str){
	return str_replace(array("\r\n", "\r", "\n", "\t", "·"), "", $str);
}
function exportExcel($filename,$content){
 	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/vnd.ms-execl");
	header("Content-Type: application/force-download");
	header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=".$filename);
    header("Content-Transfer-Encoding: binary");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $content;
}

function download($url,$filename){
	if($url==""):return false;endif;
	ob_start();
	readfile($url);
	$file=ob_get_contents();
	ob_end_clean();
	$size=strlen($file);
	$local_path="download/";
	$fp=@fopen($local_path.$filename,"a");
	fwrite($fp,$file);
	fclose($fp);
	return true;
}

function tellphone($phoneNum){
	if(preg_match("/^1[345789][0-9]{9}$/",$phoneNum)){
		$c=substr($phoneNum,0,3);
		if($c=='134'||$c=='135'||$c=='136'||$c=='137'||$c=='138'||$c=='139'||$c=='147'||$c=='150'||$c=='151'||$c=='152'||$c=='157'||$c=='158'||$c=='159'||$c=='178'||$c=='182'||$c=='183'||$c=='184'||$c=='187'||$c=='188'){
			return 1;//中国移动
		}elseif($c=='130'||$c=='131'||$c=='132'||$c=='155'||$c=='156'||$c=='185'||$c=='186'||$c=='145'||$c=='176'){
			return 2;//中国联通
		}elseif($c=='133'||$c=='153'||$c=='177'||$c=='180'||$c=='181'||$c=='189'){
			return 3;//中国电信
		}else{
			return 0;
		}
	}else{
		return -1;
	}
}

function deldir($dir) {
  //删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }

  closedir($dh);
  //删除当前文件夹：
  /*if(rmdir($dir)) {
    return true;
  } else {
    return false;
  }*/
}
