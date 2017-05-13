<?php
require("common.php");
require("nani.php");

if(@$_GET['act']=='refresh_token'){
	if(getAT(1)){
		exit('{"errcode":0}');
	}
}
if(@$_GET['act']=='refresh_ticket'){
	if(getJT(1)){
		exit('{"errcode":0}');
	}
}
if(@$_GET['act']=='submit'){
	foreach($_POST as $key => $value){
		if($key!='pcdebug'&&$key!='pcopenid'){
			setconf($key,$value);
		}
	}
	if($_POST['pcdebug']==1){
		setcookie("pcdebug", 1, time()+3600*24*3, '/');
		setcookie("pcopenid", $_POST['pcopenid'], time()+3600*24*3, '/');
	}else{
		setcookie('pcdebug','',time()-3600,'/');
		setcookie('pcopenid','',time()-3600,'/');
	}
	unset($_SESSION['openid']);
	unset($_SESSION['userinfo']);
	jump('settings.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $flag; ?></title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="css/bootstrap.min.css" />
<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="css/uniform.css" />
<link rel="stylesheet" href="css/select2.css" />
<link rel="stylesheet" href="css/unicorn.main.css" />
<link rel="stylesheet" href="css/unicorn.grey.css" class="skin-color" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php include('sidebar.php'); ?>
<div id="content">
  <div id="content-header">
    <h1>系统设置</h1>
  </div>
  <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current">系统设置</a> </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-edit"></i> </span>
            <h5>系统参数设置</h5>
          </div>
          <div class="widget-content nopadding">
            <form action="?act=submit" method="post" class="form-horizontal" name="validate" id="validate" novalidate="novalidate" />
			<div class="control-group">
              <label class="control-label">AppId</label>
              <div class="controls">
                <input type="text" name="appid" value="<?php echo conf('appid'); ?>" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">AppSecret</label>
              <div class="controls">
                <input type="text" name="appsecret" value="<?php echo conf('appsecret'); ?>" />
              </div>
            </div>
			<div class="control-group">
              <label class="control-label">access_token缓存<br><span class="label tip-bottom" title="如更换appid和appsecret要刷新否则缓存的access_token无效！" id="refresh_token" style="cursor:pointer">刷新access_token令牌</span></label>
              <div class="controls">
                <?php if(conf('access_token_cache')=='1'){ ?>
					<label><input type="radio" name="access_token_cache" value="1" checked="checked">开启</label>
					<label><input type="radio" name="access_token_cache" value="0">关闭</label>
				<?php }else{ ?>
					<label><input type="radio" name="access_token_cache" value="1">开启</label>
					<label><input type="radio" name="access_token_cache" value="0" checked="checked">关闭</label>
				<?php } ?>
              </div>
            </div>
			<div class="control-group">
              <label class="control-label">jsapi_ticket缓存<br><span class="label tip-bottom" title="如更换appid和appsecret要刷新否则缓存的jsapi_ticket无效！" id="refresh_ticket" style="cursor:pointer">刷新jsapi_ticket令牌</span></label>
              <div class="controls">
                <?php if(conf('jsapi_ticket_cache')=='1'){ ?>
					<label><input type="radio" name="jsapi_ticket_cache" value="1" checked="checked">开启</label>
					<label><input type="radio" name="jsapi_ticket_cache" value="0">关闭</label>
				<?php }else{ ?>
					<label><input type="radio" name="jsapi_ticket_cache" value="1">开启</label>
					<label><input type="radio" name="jsapi_ticket_cache" value="0" checked="checked">关闭</label>
				<?php } ?>
              </div>
            </div>
			<div class="control-group">
              <label class="control-label">是否开启PC调试<br><span class="label tip-bottom" title="">开启后本机可越过微信授权</span></label>
              <div class="controls">
                <?php if($_COOKIE['pcdebug']==1){ ?>
					<label><input type="radio" name="pcdebug" value="1" checked="checked">开启</label>
					<label><input type="radio" name="pcdebug" value="0">关闭</label>
				<?php }else{ ?>
					<label><input type="radio" name="pcdebug" value="1">开启</label>
					<label><input type="radio" name="pcdebug" value="0" checked="checked">关闭</label>
				<?php } ?>
              </div>
            </div>
			<div class="control-group">
              <label class="control-label">设置当前OPENID为<br><span class="label tip-bottom" title="">用于PC模拟调试</span></label>
              <div class="controls">
                <input type="text" name="pcopenid" value="<?php echo $_COOKIE['pcopenid']; ?>" />
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary" name="submit" id="submit"><i class="icon-ok icon-white"></i> 确认</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row-fluid">
      <div id="footer" class="span12"><?php echo $copyright; ?></div>
    </div>
  </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/jquery.ui.custom.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.uniform.js"></script>
<script src="js/select2.min.js"></script>
<script src="js/jquery.validate.js"></script>
<script src="js/unicorn.js"></script>
<script src="js/unicorn.form_validation.js"></script>
<script>
$(document).ready(function(){
	$("#refresh_token").click(function(){
		$.post("?act=refresh_token", {},
		function (data, textStatus){
			if(data.errcode==0){
				alert('刷新access_token成功！');
			}
		}, "json");
	});
	$("#refresh_ticket").click(function(){
		$.post("?act=refresh_ticket", {},
		function (data, textStatus){
			if(data.errcode==0){
				alert('刷新jsapi_tiket成功！');
			}
		}, "json");
	});

});
</script>
</body>
</html>
