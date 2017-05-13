<div id="header">
  <h1><a>Unicorn Admin</a></h1>
</div>
<?php
function is_open($sub){
	$myname=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1,strlen($_SERVER['PHP_SELF'])-strrpos($_SERVER['PHP_SELF'],"/"));
	/*if($_SERVER['QUERY_STRING']!=''){
		$myname.='?'.$_SERVER['QUERY_STRING'];
	}*/
	return in_array($myname,$sub)?'open active':'';
}
function is_active($name){
	$myname=substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1,strlen($_SERVER['PHP_SELF'])-strrpos($_SERVER['PHP_SELF'],"/"));
	/*if($_SERVER['QUERY_STRING']!=''){
		$myname.='?'.$_SERVER['QUERY_STRING'];
	}*/
	return $myname==$name?'class="active"':'';
}
?>
<?php
$myname=str_replace('ht/','',stristr($_SERVER['PHP_SELF'],'ht/'));
?>
<div id="search">
  <input type="text" placeholder="Search here..." id="kw" />
  <button type="submit" class="tip-right" title="Search" onclick="location.href='<?php echo $myname; ?>?kw='+document.getElementById('kw').value"><i class="icon-search icon-white"></i></button>
</div>
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav btn-group">
    <li class="btn btn-inverse"><a title="" href="admin.php"><i class="icon icon-user"></i> <span class="text">管理员</span></a></li>
    <li class="btn btn-inverse"><a title="" href="settings.php"><i class="icon icon-cog"></i> <span class="text">系统设置</span></a></li>
    <li class="btn btn-inverse"><a title="" href="logout.php"><i class="icon icon-share-alt"></i> <span class="text">Logout</span></a></li>
  </ul>
</div>
<div id="sidebar"> <a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
  <ul>
    <li <?php echo is_active('index.php'); ?>><a href="index.php"><i class="icon icon-th-list"></i> <span>Dashboard</span></a></li>
	<!--<li <?php echo is_active('index2.php'); ?>><a href="index2.php"><i class="icon icon-th-list"></i> <span>Dashboard2</span></a></li>-->
	<?php
	if($debug==1){
	if(@$_GET['cls']==1)logger('cleared logs.',1);
	?>
	<li class="submenu"> <a href="#"><i class="icon icon-wrench"></i> <span>调试模式</span></a>
      <ul>
	  	<li><a href="index.php?act=cls" onclick="if(!confirm('确定?'))return false;">清零数据</a></li>
		<li><a href="../" target="_blank">PC调试</a></li>
		<li><a href="log.txt" target="_blank">后台日志</a></li>
        <li><a href="?cls=1">清空日志</a></li>
      </ul>
    </li>
	<?php
	}
	?>

  </ul>
</div>
<div id="style-switcher"> <i class="icon-arrow-left icon-white"></i> <span>Style:</span> <a href="#grey" style="background-color: #555555;border-color: #aaaaaa;"></a> <a href="#blue" style="background-color: #2D2F57;"></a> <a href="#red" style="background-color: #673232;"></a> </div>