<?php
require("common.php");

if(@$_GET['act']=='doUpload'){
	/*
	allowupload——上传文件需为允许的格式和大小
	chktype——不是目前所需的媒体类型不给上传
	*/
	if($_FILES['Filedata']['size']!=0){
		$fn=$_FILES["Filedata"]["name"];
		$fs=$_FILES['Filedata']['size'];
		if(allowupload($fn,$fs)===true){
			if($_GET['type']==''||chktype($fn)==$_GET['type']){
				if(preg_match("/([\x81-\xfe][\x40-\xfe])/",$fn,$match)){//含有汉字
					$ext=end(explode('.',$fn));
					$fileRandName=date('YmdHis',time());
					$fn=$fileRandName.'.'.$ext;
				}
				if(file_exists('source/'.$fn)){
					$i=1;
					$info=pathinfo($fn);
					while(file_exists('source/'.$info['filename']."_".$i.".".$info['extension'])){
						$i++;
					}
					$fn=$info['filename']."_".$i.".".$info['extension'];
				}
				if(move_uploaded_file($_FILES["Filedata"]["tmp_name"],'source/'.$fn)===true){
					jump("?return=source/{$fn}");
				}
			}else{
				go('请上传所需格式的文件');
			}
		}else{
			go(allowupload($fn,$fs));
		}
	}
}

require("nani.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $flag; ?></title>
<script src="artDialog/jquery-1.4.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="artDialog/skins/black.css">
<script src="artDialog/jquery.artDialog.js"></script>
<script src="artDialog/plugins/iframeTools.js"></script>

<link href="uploadify/uploadify.css" rel="stylesheet" type="text/css" />
<script src="uploadify/jquery.uploadify-3.1.js" type="text/javascript"></script>
<script src="uploadify/jquery.uploadify-3.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function () {
	$("#uploadify").uploadify({
		//指定swf文件
		'swf': 'uploadify/uploadify.swf',
		//后台处理的页面
		'uploader': 'upload.php?act=doUpload&type=<?php echo $_GET['type']; ?>',
		//按钮显示的文字
		'buttonText': '批量上传',
		//显示的高度和宽度，默认 height 30；width 120
		//'height': 15,
		//'width': 80,
		//上传文件的类型  默认为所有文件    'All Files'  ;  '*.*'
		//在浏览窗口底部的文件类型下拉菜单中显示的文本
		'fileTypeDesc': 'Upload Files',
		//允许上传的文件后缀
		'fileTypeExts': '*.gif; *.jpg; *.jpeg; *.png; *.bmp; *.mp3; *.amr; *.mp4',
		//发送给后台的其他参数通过formData指定
		//'formData': { 'someKey': 'someValue', 'someOtherKey': 1 },
		//上传文件页面中，你想要用来作为文件队列的元素的id, 默认为false  自动生成,  不带#
		//'queueID': 'fileQueue',
		//选择文件后自动上传
		'auto': true,
		//设置为true将允许多文件上传
		'multi': true,
		//fileDataName ：设置一个名字，在服务器处理程序中根据该名字来取上传文件的数据。默认为Filedata
		//method ： 提交方式Post 或Get 默认为Post
		//sizeLimit ： 上传文件的大小限制
		'fileSizeLimit' : '1000KB',
		//上传成功后执行
		'onUploadSuccess': function (file, data, response) {
			$('#' + file.id).find('.data').html(' 上传完毕');
		},
		 'onQueueComplete' : function(queueData) {
			alert(queueData.uploadsSuccessful + ' 个文件上传成功！');
			location.href='attach.php?type=<?php echo $_GET['type']; ?>';
		}
	});
});

</script>

<style>
body,ul,li{
	margin: 0;
	padding: 0;
}

.tab {height: 35px; margin:10px 0 0 0;background:#eee;border-bottom: 1px solid #d7dde6;}
.tab ul{ height: 35px;}
.tab ul li{ float:left; height:30px; border: 1px solid #d7dde6; background: #f6f6f6; font-size: 14px; line-height: 30px; margin: 4px 0 0 10px; display: inline; cursor: pointer; white-space: nowrap ;  padding:0 18px}
.tab ul li.current{ background:#fff; font-weight:bold; line-height:28px; border-bottom:none; height:31px;padding:0 18px; cursor:default;}
.tab ul li.last{border:none; margin-left:50px; background:none;}
</style>
</head>
<body>
<!--tab start-->
<div class="tab">
  <ul>
    <li onclick="location.href='attach.php?type=<?php echo $_GET['type']; ?>'">我的素材库</li>
	<li class="current">上传新文件</li>
  </ul>
</div>
<!--tab end-->
<?php 
if(!isset($_GET['return'])){
?>
<div style="background:#fefbe4;border:1px solid #f3ecb9;color:#993300;padding:10px;width:90%;margin:20px auto 5px auto;">选中文件后上传或从素材库选择已上传的文件</div>
<form enctype="multipart/form-data" action="?act=doUpload&type=<?php echo $_GET['type']; ?>" method="POST" style="font-size:16px;padding:10px 20px 10px 20px;" onsubmit="if(document.getElementById('Filedata').value==''){alert('请选择本地文件上传！');return false;}">
  <p>
  <div>
  <div style="font-size:14px;">选择本地文件：<br><br>
    <input type="file" id="Filedata" name="Filedata" style="width:90%;border:1px solid #ddd; padding:3px;"></input>
  </div>
  <div style="padding:20px 0;text-align:center;">
    <input name="upload" id="uploadbtn" type="submit" value="上传"></input>
    <input onclick="location.href='attach.php?type=<?php echo $_GET['type']; ?>'" type="button" value="从素材库选择" />
  </div>
  </p>
</form>
<hr />
<div style="font-size:14px;">上传多个文件后到素材库中选择：<br><br>
	<input type="file" name="uploadify" id="uploadify" />
</div>
<?php 
}else{
?>
<div style="width:100%; text-align:center; margin-top:20px;"><img src="images/export.png" />上传成功！
<?php 
if(chktype($_GET['return'])=='image'){
	echo "<br><br><img src='{$_GET['return']}' width='250' />";
}
?>
</div>
<script>
var domid=art.dialog.data('domid');
var picid=art.dialog.data('picid');
// 返回数据到主页面
function returnHomepage(url){
	var origin = artDialog.open.origin;
	var dom = origin.document.getElementById(domid);
	var pic = origin.document.getElementById(picid);
	dom.value=url;
	pic.src=url;
	pic.style.display='block';
	setTimeout("art.dialog.close()", 1500 )
}
returnHomepage('<?php echo $_GET['return']; ?>');
</script>
<?php 
}
?>
</body>
</html>
