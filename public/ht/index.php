<?php
require("common.php");
if(@$_GET['act']=='begintask'){
	ignore_user_abort(true);
	set_time_limit(0);
	while(1) {
		if(file_exists('stop.cmd')){logger('事务停止');exit;}
		logger('working pulse');
		//轮询事务
		$row=db("select * from {$prefix}_member where status=1");
		if($row!='notexist'){
			$cnt=count($row);
			for($i=1;$i<=$cnt;$i++){
				//后台执行事务，如发送邮件：
				$pushStr = '<b>test mail.</>';
				$data=array('fromName'=>'Administrator','toAddress'=>'659327515@qq.com','toName'=>'hxf','subject'=>'系统通知','body'=>$pushStr);
				logger( curl_post('http://wechat.huangxf.com/activity2/ht/PHPMailer/send_mail.php',$data) );
			}

		}
		sleep(1);//休眠喘口气
		if(time()>1880000000){logger('事务到期停止');exit;}
	}
}
require("nani.php");

if(@$_GET['act']=='cls'){
	//setconf('click','0');
	// db("DROP TABLE IF EXISTS {$prefix}_member_".date("Ymd"));
	// db("CREATE TABLE {$prefix}_member_".date("Ymd")." SELECT * FROM {$prefix}_member");
	db("truncate {$prefix}_member");
	db("truncate {$prefix}_click");
	db("truncate {$prefix}_pic");
	db("update {$prefix}_coupon set status=0");
	deldir('upload');
	deldir('../upload');
	deldir('download');
	deldir('../download');
	jump('?');
}
if(@$_GET['act']=='input'){
	if($_FILES['file']['size']!=0){//upload excel
		include_once("excel/reader.php");
		$tmp=$_FILES['file']['tmp_name'];
		$fn=$_FILES["file"]["name"];
		$save_path="excel/xls/";
		$file_name=$save_path.date('YmdHis').rand(1,100).".xls";
		move_uploaded_file($tmp,$file_name);

		//db("delete from {$prefix}_sell"); //不清除，上传数据追加累积
		$xls=new Spreadsheet_Excel_Reader();
		$xls->setOutputEncoding('utf-8');
		$xls->read($file_name);
		$repeat_cnt=0;
		$repeat_str='';
		$cnt=$xls->sheets[0]['numRows'];
		for($i=1;$i<=$cnt;$i++){
			$p1=$xls->sheets[0]['cells'][$i][1];
			$p2=$xls->sheets[0]['cells'][$i][2];
			$p3=$xls->sheets[0]['cells'][$i][3];
			$p4=$xls->sheets[0]['cells'][$i][4];
			$p5=$xls->sheets[0]['cells'][$i][5];
			list($s1, $s2) = explode(' ', microtime());
			$fakeid=(float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 10000).rand(10000,99999);
			if(db("select id from {$prefix}_member where tel='{$p4}' limit 1")=='notexist'){//与数据库不重复
				db("insert into {$prefix}_member(openid,nickname,headimg,name,tel,click,sendtime) values('{$fakeid}','{$p1}','{$p2}','{$p3}','{$p4}','{$p5}','".time()."')");
			}else{
				$repeat_cnt+=1;
				$repeat_str.=$p4.' ';
			}
		}
		logger("成功导入数据".($cnt-$repeat_cnt)."条，与总数据库重复{$repeat_cnt}条，重复手机号为： $repeat_str");
		go("成功导入数据".($cnt-$repeat_cnt)."条，与总数据库重复{$repeat_cnt}条，重复手机号为： $repeat_str",'index.php');
	}
}
if(@$_GET['act']=='export'){
	$row=db("select * from {$prefix}_member order by id desc");
	if($row=='notexist'){
		go('没有记录');
	}else{
		$str="OPENID\t昵称\t头像\t姓名\t手机\t点赞数\t时间\t状态\t\n";
		for($i=1;$i<=count($row);$i++){
			$str.="{$row[$i]['openid']}\t".fiterChar($row[$i]['nickname'])."\t{$row[$i]['headimg']}\t".fiterChar($row[$i]['name'])."\t".fiterChar($row[$i]['tel'])."\t{$row[$i]['click']}\t".date("Y-m-d h:i:s",$row[$i]['sendtime'])."\t{$row[$i]['status']}\t\n";
		}
		$str=iconv("UTF-8","GB2312//IGNORE",$str);
		$filename=date('YmdHis').'_'.rand(100,999).'.xls';
		exportExcel($filename,$str);
	}
	exit;
}
if(@$_GET['act']=='del'){
	db("delete from {$prefix}_member where id=".$_GET['id']);
	exit('{"errcode":0}');
}
if(@$_GET['act']=='massdel'){
	$chebox_arr=$_POST['ids'];
	foreach($chebox_arr as $c){
		db("delete from {$prefix}_member where id=".$c);
	}
	exit('{"errcode":0}');
}
if(@$_GET['act']=='submit'){
	if($_POST['id']==""){//添加
		foreach($_POST as $key => $value){
			if($key!='id'){
				$key_str.=$key.',';
				$val_str.="'$value',";
			}
		}
		$key_str=rtrim($key_str,',');
		$val_str=rtrim($val_str,',');
		$key_str.=',sendtime';
		$val_str.=",'".time()."'";
		db("insert into {$prefix}_member($key_str) values($val_str)");
	}else{//编辑
		foreach($_POST as $key => $value){
			if($key!='id'){
				$edit_str.="$key='$value',";
			}
		}
		$edit_str=rtrim($edit_str,',');
		db("update {$prefix}_member set {$edit_str} where id={$_POST['id']}");
	}
	go('提交成功！',$_SERVER["HTTP_REFERER"]);
}
if(@$_GET['act']=='change_status'){
	$cur_status=db("select status from {$prefix}_member where id={$_GET['id']}",1);
	db("update {$prefix}_member set status=".($cur_status==1?0:1)." where id={$_GET['id']}");
	go('操作成功！','?');
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
    <h1></h1>
  </div>
  <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a>list</a></div>
  <div class="container-fluid">
  <?php
  if(!isset($_GET['act'])){//列表页
  ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="widget-box widget-plain">
				<div class="widget-content center">
					<ul class="stats-plain">
						<li>
							<h4><?php echo db("select count(id) from {$prefix}_member",1); ?></h4>
							<span>参与人数</span>
						</li>
						<li>
							<h4><?php echo db("select count(id) from {$prefix}_click",1); ?></h4>
							<span>点赞数</span>
						</li>

					</ul>
				</div>
			</div>
		</div>
	</div>
  </div>
  <div class="container-fluid">
	<div class="row-fluid">
      <div class="span12">
        <div class="widget-box">

          <div class="widget-title"> <span class="icon">
            <input type="checkbox" id="title-checkbox" name="title-checkbox" />
            </span>
            <h5></h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th><i class="icon-resize-vertical"></i></th>
				  <th>OPENID</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>姓名</th>
				  <th>手机</th>
				  <th>点赞数</th>
				  <th>状态</th>
				  <th>参与时间</th>
				  <!--<th>操作</th>-->
                </tr>
              </thead>
              <tbody>
			  	<?php
				if(isset($_GET['kw'])&&$_GET['kw']!=''){
					$where="where nickname like '%{$_GET['kw']}%' or name like '%{$_GET['kw']}%' or tel like '%{$_GET['kw']}%'";
				}else{
					$where='';
				}
				$sql="select * from {$prefix}_member {$where} order by id desc";
				//分页
				$pagesize=10;
				$numrows=db(str_replace("select *","select count(*)",$sql),1);
				$pages=intval($numrows/$pagesize);
				if($numrows%$pagesize)$pages++;
				if(isset($_GET['page'])&&is_numeric($_GET['page'])){
					$page=intval($_GET['page']);
				}
				else{
					$page=1;
				}
				$offset=$pagesize*($page - 1);
				$sql=$sql." limit $offset,$pagesize";
				///////
				$row=db($sql);
				if($row!='notexist'){
					$cnt=count($row);
					for($i=1;$i<=$cnt;$i++){
						echo '<tr class="gradeA">';
						echo '<td><input type="checkbox" value="'.$row[$i]['id'].'" class="massdel" /></td>';
						echo "<td>{$row[$i]['openid']}</td>";
						echo "<td><img src='{$row[$i]['headimg']}' width=50 /></td>";
						echo "<td>{$row[$i]['nickname']}</td>";
						echo "<td>{$row[$i]['name']}</td>";
						echo "<td>{$row[$i]['tel']}</td>";
						echo "<td>{$row[$i]['click']}</td>";
						echo "<td>".($row[$i]['status']==1?'':'')."</td>";
						echo "<td>".date("Y-m-d H:i:s",$row[$i]['sendtime'])."</td>";
						//echo "<td><a href=\"?act=change_status&id={$row[$i]['id']}\" class=\"btn".($row[$i]['status']==1?'':' btn-primary')."\">".($row[$i]['status']==1?'已发奖':'发奖')."</a> <a href=\"?act=edit&id={$row[$i]['id']}\" class=\"btn btn-primary\"><i class=\"icon-pencil icon-white\"></i> 修改</a> <a href=\"javascript:void(null)\" class=\"btn btn-danger del\" delid=\"{$row[$i]['id']}\"><i class=\"icon-remove icon-white\"></i> 删除</a> </td></tr>";
					}

				}
				?>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
	<?php
		if($row!='notexist'){
	?>
	<div style=" text-align:right; float:right">
		<div class="pagination alternate" style=" margin:0; padding:0;">
			<ul>
				<?php
				$pageurl="?";
				if($page==1){
					echo '<li class="disabled"><a>〈 上一页</a></li>';
				}elseif($page>1){
					echo '<li><a href="'.$pageurl.'page='.($page-1).'">〈 上一页</a></li>';
				}
				if($pages<=5){
					for($i=1;$i<=$pages;$i++){
						if($page==$i){
							echo '<li class="active"><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}else{
							echo '<li><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}
					}
				}else{
					for($i=1;$i<=2;$i++){
						if($page==$i){
							echo '<li class="active"><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}else{
							echo '<li><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}
					}
					if($page>=6){
						echo '<li><a href="javascript:void(null)">...</a></li>';
					}
					$page_begin=$page-2;
					$page_begin=$page_begin<=2?3:$page_begin;
					$page_end=$page+2;
					$page_end=$page_end>=$pages?$pages:$page_end;
					for($i=$page_begin;$i<=$page_end;$i++){
						if($page==$i){
							echo '<li class="active"><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}else{
							echo '<li><a href="'.$pageurl.'page='.$i.'">'.$i.'</a></li>';
						}
					}
					if($page_end<$pages){
						if($page_end+1<$pages){
							echo '<li><a href="javascript:void(null)">...</a></li>';
						}
						echo '<li><a href="'.$pageurl.'page='.$pages.'">'.$pages.'</a></li>';
					}
				}
				if($page==$pages){
					echo '<li class="disabled"><a>下一页 〉</a></li>';
				}elseif($page<$pages){
					echo '<li><a href="'.$pageurl.'page='.($page+1).'">下一页 〉</a></li>';
				}
				?>

			</ul>
		</div>
		<div style=" font-size:12px; color:gray; float:right">
			<?php echo $numrows; ?>条记录，共 <?php echo $pages; ?> 页，到第 <input id="topage" class="numeric" step="any" type="number" value="<?php echo $page<$pages?($page+1):$pages; ?>" max="<?php echo $pages; ?>" min="1" style=" margin:0; padding:1px; width:40px;" /> 页 <a class="btn btn-mini" style=" margin:0;" onClick="if(document.getElementById('topage').value>=1&&document.getElementById('topage').value<=<?php echo $pages; ?>)location.href='<?php echo $pageurl; ?>page='+document.getElementById('topage').value">确定</a>
		</div>
	</div>
	<?php
	}
	?>
	<button class="btn" id="massdel" style="display:none"><i class="icon-remove"></i> 批量删除</button>
	<button class="btn" onClick="location.replace('?act=export')" style="display:none"><i class="icon-download"></i> 导出数据</button>
	<button class="btn btn-primary" onClick="location.href='?act=add'" style="display:none"><i class="icon-plus icon-white"></i> 添加</button> <br><br>

	<div class="row-fluid" style="display:none">
		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
					<span class="icon">
						<i class="icon-align-justify"></i>
					</span>
					<h5>导入数据</h5>
				</div>
				<div class="widget-content nopadding">
					<form action="?act=input" method="post" class="form-horizontal" enctype="multipart/form-data" />
						<div class="control-group">
							<label class="control-label">上传Excel数据源：</label>
							<div class="controls">
								<input type="file" name="file" id="file" />
								<span class="help-block">
								<a href="excel/xls/demo.xls">下载Excel表格示例</a>
								</span>
							</div>
						</div>
						<div class="form-actions">
							<button type="submit" class="btn btn-primary" />导入</button>（多次导入会累加）
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

  <?php
  }else{//非列表页

	if(isset($_GET['id'])){//编辑
		$row=db("select * from {$prefix}_member where id={$_GET['id']}");
		$row=$row[1];
	}else{//添加
		$row=array();
	}

  ?>

	<div class="row-fluid">
		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
					<span class="icon">
						<i class="icon-align-justify"></i>
					</span>
					<h5><?php echo $_GET['act']=='add'?'新增':'编辑'; ?>记录</h5>
				</div>
				<div class="widget-content nopadding">
					<form id="submitform" action="?act=submit" method="post" class="form-horizontal" enctype="multipart/form-data" />
						<div class="control-group">
							<label class="control-label">昵称</label>
							<div class="controls">
								<input type="text" name="nickname" require="1" value="<?php echo $row['nickname']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">头像</label>
							<div class="controls">
								<input type="text" id="mediasrc" name="headimg" require="1" value="<?php echo $row['headimg']; ?>" style="width:40%;" />
								<a onClick="attach('mediasrc','litpic')" href="javascript:void(0)" class="btn"><i class="icon icon-eye-open"></i> 选择</a>
								<a onClick="upload('mediasrc','litpic')" href="javascript:void(0)" class="btn"><i class="icon icon-upload"></i> 上传</a><br><br>
								<?php
								if($row['img']){
									echo '<img id="litpic" src="'.$row['headimg'].'" style="width:120px" />';
								}else{
									echo '<img id="litpic" src="" style="width:120px;display:none;" />';
								}
								?>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">姓名</label>
							<div class="controls">
								<input type="text" name="name" require="1" value="<?php echo $row['name']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">手机</label>
							<div class="controls">
								<input type="text" name="tel" require="1" value="<?php echo $row['tel']; ?>" />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">点赞数</label>
							<div class="controls">
								<input type="text" name="click" require="0" value="<?php echo $row['click']; ?>" />
							</div>
						</div>
						<input type="hidden" id="id" name="id" value="<?php echo @$_GET['id']; ?>">
						<div class="form-actions">
							<button type="submit" id="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> 确认<?php echo $_GET['act']=='add'?'添加':'修改'; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

  <?php
  }
  ?>

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
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/unicorn.js"></script>
<script src="js/unicorn.tables.js"></script>
<script>
$(function(){
	$('#content-header').find('h1').text($('#sidebar').find('.active').find('span').text());
	$(".del").click(function(){
		if(confirm('删除数据不可恢复，是否确定删除？')){
			$.post("?act=del&id="+$(this).attr('delid'), {},
			function (data, textStatus){
				if(data.errcode==0){
					alert('删除成功！');
					location.replace(location.href);
				}else{
					alert('操作出错，请重试！');
				}
			}, "json");
		}
	});
	$("#massdel").click(function(){
		var ids=new Array(),i=0;
		$(".massdel").each(function(){
			if($(this).attr("checked")){
				ids[i]=$(this).val();
				i++;
			}
		});
		if(ids.length==0){
			alert('请选择要批量删除的记录！');
			return;
		}
		if(confirm('删除数据不可恢复，是否确定删除？')){
			var submitData={
				ids: ids
			};
			$.post("?act=massdel", submitData,
			function (data, textStatus){
				if(data.errcode==0){
					alert('批量删除成功！');
					location.replace(location.href);
				}else{
					alert('操作出错，请重试！');
				}
			}, "json");
		}
	});
	$("#submit").click(function(){
		$("#submitform").find("input").each(function(){
			if($(this).attr("require")==1){
				if($(this).val()==''){
					alert('请填写'+$(this).parents('.control-group').find('label').text());
					$(this).focus();
					event.preventDefault();
					return false;
				}
			}
		});
	});
});
</script>
<link rel="stylesheet" href="artDialog/skins/black.css">
<script src="artDialog/jquery.artDialog.js"></script>
<script src="artDialog/plugins/iframeTools.js"></script>
<script>
function upload(domid,picid){
	art.dialog.data('domid', domid);
	art.dialog.data('picid', picid);
	art.dialog.open('upload.php?type=image',{lock:true,title:'',width:600,height:500,yesText:'关闭',background: '#000',opacity: 0.45});
}
function attach(domid,picid){
	art.dialog.data('domid', domid);
	art.dialog.data('picid', picid);
	art.dialog.open('attach.php?type=image',{lock:true,title:'',width:600,height:500,yesText:'关闭',background: '#000',opacity: 0.45});
}
</script>
</body>
</html>