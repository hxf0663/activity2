<?php
require("common.php");
if(isset($_POST['user'])&&isset($_POST['pass'])){
  if($_POST['user']==conf('adminid')&&$_POST['pass']==conf('adminpwd')){
    $_SESSION['hxf']=1;
    exit('{"errcode":0}');
  }else{
    exit('{"errcode":1}');
  }
}
if($_SESSION['hxf']==1){
	header('Location: index.php');
	exit;
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
<link rel="stylesheet" href="css/unicorn.login.css" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div id="logo">  </div>
<div id="loginbox">
  <form id="loginform" class="form-vertical" action="#" method="post" onSubmit="return false;">
  <p>Enter username and password to continue.</p>
  <div class="control-group">
    <div class="controls">
      <div class="input-prepend"> <span class="add-on"><i class="icon-user"></i></span>
        <input type="text" placeholder="Username" name="user" id="user" value="admin" />
      </div>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <div class="input-prepend"> <span class="add-on"><i class="icon-lock"></i></span>
        <input type="password" placeholder="Password" name="pass" id="pass" value="admin" />
      </div>
    </div>
  </div>
  <div class="form-actions"> <span class="pull-left" style="display:none"><a href="#" class="flip-link" id="to-recover">Lost password?</a></span> <span class="pull-right">
    <input type="submit" class="btn btn-inverse" value="Login" id="login" />
    </span> </div>
  </form>
  <form id="recoverform" action="#" class="form-vertical" />
  <p>Enter your e-mail address below and we will send you instructions how to recover a password.</p>
  <div class="control-group">
    <div class="controls">
      <div class="input-prepend"> <span class="add-on"><i class="icon-envelope"></i></span>
        <input type="text" placeholder="E-mail address" />
      </div>
    </div>
  </div>
  <div class="form-actions"> <span class="pull-left"><a href="#" class="flip-link" id="to-login">&lt; Back to login</a></span> <span class="pull-right">
    <input type="submit" class="btn btn-inverse" value="Recover" />
    </span> </div>
  </form>
</div>
<script src="js/jquery.min.js"></script>
<script>
$(function(){
	$("#login").click(function(){
		if($("#user").val()==''){
			alert('请填写账户名');
			$("#user").focus();
			return;
		}
		if($("#pass").val()==''){
			alert('请填写密码');
			$("#pass").focus();
			return;
		}
		var submitData={
			user: $("#user").val(),
			pass: $("#pass").val(),
		};
		$.post("#", submitData,
		function (data, textStatus){
			if(data.errcode==0){
				location.replace('index.php');
			}else if(data.errcode==1){
				alert('账户名或密码错误！');
			}
		}, "json");
	});
});
</script>
<script src="js/unicorn.login.js"></script>
</body>
</html>