<?php
require("common.php");
require("nani.php");

if(isset($_GET['del'])){
	@unlink($_GET['del']);
	go('删除素材成功！',"attach.php");
}
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
<style>
body,ul,li{
	margin: 0;
	padding: 0;
}
a {
	text-decoration:none;
	color:#636b89;
}

TABLE.ListProduct {
	BORDER-TOP: #d3d3d3 1px solid;
	MARGIN-TOP: 5px;
	WIDTH: 100%;
	MARGIN-BOTTOM: 5px;_border-collapse: collapse;
}
TABLE.ListProduct THEAD TH {
	BORDER-BOTTOM: #d3d3d3 1px solid; PADDING-BOTTOM: 5px; BACKGROUND-COLOR: #f1f1f1; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; COLOR: #666; FONT-SIZE: 14px; BORDER-TOP: #e3e3e3 1px solid; FONT-WEIGHT: normal; BORDER-RIGHT: #ddd 1px solid; PADDING-TOP: 5px; color:#000000; font-weight:bold
}
TABLE.ListProduct TBODY TD {
	BORDER-BOTTOM: #eee 1px solid; PADDING-BOTTOM: 10px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; BORDER-RIGHT: #eee 1px solid; PADDING-TOP: 10px;
	font-size:12px;_empty-cells:show;word-break: break-all;
}
TABLE.ListProduct TBODY TR:nth-child(2n+1) {
    background-color:#FCFCFC;
}
TABLE.ListProduct TBODY TR:hover {
    background-color:#F1FCEA;
}
TABLE.ListProduct TBODY TD p{
	PADDING: 0;font-size:12px;_empty-cells:show;word-break: break-all;
}
TABLE.ListProduct Tfoot TD {
	BORDER-BOTTOM: #eee 1px solid; PADDING-BOTTOM: 10px; PADDING-LEFT: 5px; PADDING-RIGHT: 5px; BORDER-RIGHT: #eee 1px solid; PADDING-TOP: 10px; background-color:#f9f9f9;
	font-size:12px;_empty-cells:show;word-break: break-all;
}
TABLE.ListProduct THEAD TH.norightborder {
	BORDER-RIGHT: 0;
}
TABLE.ListProduct TBODY TD.norightborder {
	BORDER-RIGHT: 0;
}
TABLE.ListProduct .select{
	width: 30px;
}
TABLE.ListProduct .keywords{width: 150px;
}
TABLE.ListProduct .answer{
	width: 375px;
}
TABLE.ListProduct .answer_text{
	 width: 360px; overflow:hidden;white-space:nowrap;text-overflow:ellipsis; height:16px
}
.answer_text img{
	margin-right: 5px; float:left;
}
TABLE.ListProduct .category{
	width: 70px;
}
TABLE.ListProduct .time{
	width: 70px;
}
TABLE.ListProduct .edit{
	width: 120px;
}

.tab {height: 35px; margin:10px 0 0 0;background:#eee;border-bottom: 1px solid #d7dde6;}
.tab ul{ height: 35px;}
.tab ul li{ float:left; height:30px; border: 1px solid #d7dde6; background: #f6f6f6; font-size: 14px; line-height: 30px; margin: 4px 0 0 10px; display: inline; cursor: pointer; white-space: nowrap ;  padding:0 18px}
.tab ul li.current{ background:#fff; font-weight:bold; line-height:28px; border-bottom:none; height:31px;padding:0 18px; cursor:default;}
.tab ul li.last{border:none; margin-left:50px; background:none;}

/* pages style */
.pages{padding:3px;margin:3px;text-align:center;}
.pages a{border:#bbb 1px solid;padding:2px 5px;margin:2px;color:#333;text-decoration:none;}
.pages a:hover{border:#999 1px solid;color:#666;}
.pages a:active{border:#999 1px solid;color:#666;}
.pages .current{border:#000 1px solid;padding:2px 5px;font-weight:bold;margin:2px;color:#fff;background-color:#333;}
.pages .disabled{border:#eee 1px solid;padding:2px 5px;margin:2px;color:#ddd;}
</style>
</head>
<body style="background:#fff">
<!--tab start-->
<div class="tab">
  <ul>
    <li class="current">我的素材库</li>
	<li onclick="location.href='upload.php?type=<?php echo $_GET['type']; ?>'">上传新文件</li>
  </ul>
</div>
<!--tab end-->
<div style="margin:10px 20px;">
  <div>
    <table class="ListProduct" border="0" cellSpacing="0" cellPadding="0" width="100%">
      <thead>
        <tr>
          <th>文件</th>
          <th>时间</th>
          <th>操作</th>
        </tr>
      </thead>
      <?php 
	  	$files=array();
		$cnt=0;
		function traverse($path='.',$type=''){
			global $files;
			global $cnt;
			$current_dir=opendir($path);//opendir()返回一个目录句柄,失败返回false
			while(($file=readdir($current_dir))!==false){//readdir()返回打开目录句柄中的一个条目
				$sub_dir=$path.DIRECTORY_SEPARATOR.$file;//构建子目录路径
				if($file=='.'||$file=='..'){
					continue;
				}else if(is_dir($sub_dir)){//如果是目录,进行递归
					//echo 'Directory '.$file.':<br>';
					traverse($sub_dir);
				}else{//如果是文件,直接输出
					//echo 'File in Directory '.$path.': '.$file.' '.date("Y-m-d H:i:s",filemtime($sub_dir)).'<br>';
					if($type==''||chktype($file)==$type){
						$cnt++;
						$files[$cnt]['name']=$file;
						$files[$cnt]['type']=strtolower(end(explode('.',$file)));
						$files[$cnt]['time']=filemtime($sub_dir);
						$files[$cnt]['path']=$sub_dir;
						$files[$cnt]['url']=str_replace("\\","/",$sub_dir);
					}
				}
			}
			return array_sort($files,'time','desc');//按修改日期排序
		}
		
		$row=traverse('source',isset($_GET['type'])?$_GET['type']:'');//按类型筛选文件并写入数组
		if(count($row)==0){
			echo "<tr><td colspan=3>没有上传的素材记录</td></tr>";
		}else{
			//分页
			$pagesize=10;
			$numrows=count($row);
			$pages=intval($numrows/$pagesize);
			if($numrows%$pagesize)$pages++;
			if(isset($_GET['page'])&&is_numeric($_GET['page'])){
				$page=intval($_GET['page']);
			}
			else{
				$page=1;
			}
			$offset=$pagesize*($page - 1)+1;
			$end=$offset+$pagesize-1;
			$end=$end>$numrows?$numrows:$end;
			for($i=$offset;$i<=$end;$i++){
				if(chktype($row[$i]['type'])=='image'){
					echo "<tr><td><img title='{$row[$i]['name']}' src='{$row[$i]['url']}' width='120' /></td><td>".date("Y-m-d H:i:s",$row[$i]['time'])."</td><td>&nbsp;&nbsp;<a href='javascript:void(0)' onclick=\"returnHomepage('{$row[$i]['url']}')\">选择</a>&nbsp;<a href=\"?del={$row[$i]['path']}\" onclick=\"if(!confirm('确定删除？'))return false;\">删除</a></td></tr>";
				}else{
					echo "<tr><td><a href='{$row[$i]['url']}'>{$row[$i]['name']}</a></td><td>".date("Y-m-d H:i:s",$row[$i]['time'])."</td><td>&nbsp;&nbsp;<a href='javascript:void(0)' onclick=\"returnHomepage('{$row[$i]['url']}')\">选择</a>&nbsp;<a href=\"?del={$row[$i]['path']}\" onclick=\"if(!confirm('确定删除？'))return false;\">删除</a></td></tr>";
				}
			}
		}
	?>
    </table>
    <div style="padding-left:10px; margin-top:10px; font-size:13px;">
      <div class="pages">
	  <?php echo $numrows; ?> 条记录 <?php echo $page."/".$pages; ?> 页  
	  <?php
		if($page==1){
			echo '<a class="current">首页</a> ';
			echo '<a class="disabled">上一页</a> ';
		}elseif($page>1){
			echo '<a href="?type='.$_GET['type'].'&page=1">首页</a> ';
			echo '<a href="?type='.$_GET['type'].'&page='.($page-1).'">上一页</a> ';
		}
		echo "<select onchange=\"location.href='?type={$_GET['type']}&page='+this.value\">";
		for($i=1;$i<=$pages;$i++){
			if($i==$page){
				echo "<option selected=\"selected\" value={$i}>第{$i}页</option>";
			}else{
				echo "<option value={$i}>第{$i}页</option>";
			}
		}
		echo "</select>";
		if($page==$pages){
			echo '<a class="disabled">下一页</a> ';
			echo '<a class="disabled">尾页</a> ';
		}elseif($page<$pages){
			echo '<a href="?type='.$_GET['type'].'&page='.($page+1).'">下一页</a> ';
			echo '<a href="?type='.$_GET['type'].'&page='.$pages.'">尾页</a> ';
		}
	  ?>
    </div>
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
	setTimeout("art.dialog.close()", 100 )
}
</script>
</body>
</html>
