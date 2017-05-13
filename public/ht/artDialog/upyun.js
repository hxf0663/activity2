function upyunPicUpload(domid,width,height,token){
	art.dialog.data('width', width);
	art.dialog.data('height', height);
	art.dialog.data('domid', domid);
	art.dialog.data('lastpic', $('#'+domid).val());
	art.dialog.open('?g=User&m=Upyun&a=upload&token='+token+'&width='+width,{lock:true,title:'上传图片',width:600,height:400,yesText:'关闭',background: '#000',opacity: 0.45});
}
function upyunWapPicUpload(domid,width,height,token){
	art.dialog.data('width', width);
	art.dialog.data('height', height);
	art.dialog.data('domid', domid);
	art.dialog.data('lastpic', $('#'+domid).val());
	art.dialog.open('?g=User&m=Upyun&a=upload&from=Wap&token='+token+'&width='+width,{lock:true,title:'上传图片',width:260,height:150,top:100,yesText:'关闭',background: '#000',opacity: 0.65});
}
function viewImg(domid){
	if($('#'+domid).val()){
		var html='<img src="'+$('#'+domid).val()+'" />';
	}else{
		var html='没有图片';
	}
	art.dialog({title:'图片预览',content:html,lock:true,background: '#000',opacity: 0.45});
}

function editClass(domid,domid2,cid){
	art.dialog.data('domid', domid);
	art.dialog.data('domid2', domid2);
	art.dialog.data('cid', cid);
	art.dialog.open('http://www.baidu.com?id='+cid,{lock:true,title:'选择分类',width:600,height:500,yesText:'关闭',background: '#000',opacity: 0.45});
}