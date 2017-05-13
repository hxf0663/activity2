<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use \think\Db;

/**
 * 获取微信OAUTH授权引导网页
 * @param  string  $appid        认证服务号APPID
 * @param  string  $redirect_uri 回调地址
 * @param  integer $scope        静默授权为1，非静默授权为2
 * @return string                授权网址
 */
function auth_url($appid,$redirect_uri,$scope=1){
	if($scope==1){
		return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_base&state=wx#wechat_redirect";
	}elseif($scope==2){
		return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_userinfo&state=wx#wechat_redirect";
	}
}

/**
 * 读取配置
 * @param  string $item 配置项
 * @return string       配置值
 */
function conf( $item ) {
	return Db::name('config')->where('item', $item)->value('conf');
}
/**
 * 修改配置
 * @param  string $item 配置项
 * @param  string $conf 配置值
 * @return boolean      是否修改成功
 */
function setconf($item, $conf) {
	$res = Db::name('config')->where('item', $item)->update(['conf'=>$conf]);
	return $res ? true : false;
}

function getAT($refresh=0){//获取access_token
	//$json=httpGet("http://campaign.ugomedia.net/wxsdk.php");
	$json=httpGet("http://wechat.huangxf.com/single/wxapi.php?method=getat");
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
	//$json=httpGet("http://campaign.ugomedia.net/wxsdk.php");
	$json=httpGet("http://wechat.huangxf.com/single/wxapi.php?method=getjt");
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
	//$json=httpGet("http://campaign.ugomedia.net/wxsdk.php?url=".$url);
	$json=httpGet("http://wechat.huangxf.com/single/wxapi.php?&method=wxsdk&url=".$url);
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

/**
 * 调用客服接口48小时内发文本消息
 * @param  [type] $openid [description]
 * @param  [type] $text   [description]
 * @return [type]         [description]
 */
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
		trace($json['errcode'].$json['errmsg']);
		return false;
	}
}
/**
 * 调用客服接口48小时内发图文消息
 * @param  [type] $openid [description]
 * @param  array  $tw     图文消息数组：array( "1"=>array( "title"=>"","description"=>"","url"=>"","picurl"=>"") ) );
 * @return [type]         [description]
 */
function sendnews($openid,$tw=array()){
	$cnt=count($tw);
	$cnt=$cnt>8?8:$cnt;
	$articles='';
	for($i=1;$i<=$cnt;$i++){
		$articles.='{
				 "title":"'.$tw[$i]['title'].'",
				 "description":"'.$tw[$i]['description'].'",
				 "url":"'.$tw[$i]['url'].'",
				 "picurl":"'.$tw[$i]['picurl'].'"
			 },';
	}
	$articles=rtrim($articles,",");
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
		trace($json['errcode'].$json['errmsg']);
		return false;
	}
}

//发送消息模板
/*
$data=array(
	'touser'=>'o7WwIt1N5UNnVVukDZ0ns3pG46S4',
	'template_id'=>'X6Vvo98Iqh3AaPPQ4XhXJhysylZ3AJVxqXjqUzePQTM',
	'url'=>'http://www.baidu.com',
	'data'=>array(
		'first'=>array(
			'value'=>urlencode('收到门店通知'),
			'color'=>'#FF0000',
		),
		'keyword1'=>array(
			'value'=>urlencode('李店长'),
			'color'=>'#000000',
		),
		'keyword2'=>array(
			'value'=>urlencode('2014年7月21日 18:36'),
			'color'=>'#000000',
		),
		'remark'=>array(
			'value'=>urlencode('从下周一起门店关门时间推迟到晚上21：00，所有人不得早退'),
			'color'=>'#0000FF',
		),
	)
);
调用：send_template_message(json_encode($data));
*/
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

function randstr($len){
  $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  return substr(str_shuffle($str),0,$len);
}
function randnum($len){
  $str = '0123456789';
  return substr(str_shuffle($str),0,$len);
}

function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = ip2long($ip);
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
