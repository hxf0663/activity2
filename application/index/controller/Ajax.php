<?php
namespace app\index\controller;

use \think\Controller;
use \think\Db;

// // 七牛CDN
// require_once __DIR__ . '/../../../public/qiniu/autoload.php';
// // 引入鉴权类
// use Qiniu\Auth;
// // 引入上传类
// use Qiniu\Storage\UploadManager;
// use Qiniu\Processing\PersistentFop;

// //阿里云OSS
// use OSS\OssClient;
// use OSS\Core\OssException;
// require_once __DIR__ . '/../../../public/aliyun/autoload.php';

// //腾讯云COS
// require_once __DIR__ . '/../../../public/cos/include.php';
// use qcloudcos\Cosapi;

class Ajax extends Controller
{
	private $ajaxArr = array('errcode'=>-1, 'errmsg'=>'');

	public function saveInfo(){
		if ( !session('?token1') || session('token1')!=input('post.token') ) {
			$this->ajaxArr['errmsg']='invalid token!';
		}else{
			Db::name('member')
				->where('openid',input('post.openid'))
				->update([ 'name'=>input('post.name'),'tel'=>input('post.tel'),'status'=>1 ]);
			$this->ajaxArr['errcode']=0;
			session('token1', null);
		}
		return json($this->ajaxArr);
	}

	public function click(){
		if ( !session('?token2') || session('token2')!=input('get.token') ) {
			$this->ajaxArr['errmsg']='invalid token!';
		}else{
			if( md5(input('get.my_openid').input('get.by_openid').'hxf')!=input('get.checkstr') ){
				$this->ajaxArr['errmsg']='invalid request!';
			}else{
				if( !Db::name('click')->where(['my_openid'=>input('get.my_openid'),'by_openid'=>input('get.by_openid')])->count('id') ){
					Db::transaction(function(){
						Db::name('click')->insert(['my_openid'=>input('get.my_openid'),'by_openid'=>input('get.by_openid'),'click_time'=>time()]);
						Db::name('member')->where('openid',input('get.my_openid'))->setInc('click');
					});
					$this->ajaxArr['errcode']=0;
					session('token2', null);
				}else{
					$this->ajaxArr['errcode']=1;
				}
			}
		}
		return json($this->ajaxArr);
	}

	public function uploadPic(){
		$pic=str_replace(' ','+',input('post.pic'));
		$pic=substr(strstr($pic,','),1);
		$pic=base64_decode($pic);
		list($s1, $s2) = explode(' ', microtime());
		$name1=(float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 10000).rand(10000,99999);
		file_put_contents("upload/{$name1}.jpg",$pic);
		// // 上传到七牛
		// // 需要填写你的 Access Key 和 Secret Key
		// $accessKey = 'U4H27Ine8KGJOMnkH1jLDVGm-AiRgcjssos08COo';
		// $secretKey = 'lPGazd4JPIauS6FSkR6NtGL3g4KT_2LkZccxuEMV';
		// // 构建鉴权对象
		// $auth = new Auth($accessKey, $secretKey);
		// // 要上传的空间
		// $bucket = 'buffer';
		// //上传图片/////////////////////////////////////////////////////////////////////////
		// // 生成上传 Token
		// $token = $auth->uploadToken($bucket);
		// // 要上传文件的本地路径
		// $filePath = "upload/{$name1}.jpg";//也可以不用保存到本地，直接上传base64编码：input('post.pic')
		// // 上传到七牛后保存的文件名
		// $key = "{$name1}.jpg";
		// // 初始化 UploadManager 对象并进行文件的上传。
		// $uploadMgr = new UploadManager();
		// // 调用 UploadManager 的 putFile 方法进行文件的上传。
		// list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
		// echo "\n====> putFile result: \n";
		// if ($err !== null) {
		//     var_dump($err);
		// } else {
		//     var_dump($ret);
		// }
		//微信amr格式音频上传与转码处理////////////////////////////////////////////////////
		// //转码时使用的队列名称（七牛后台创建多媒体处理队列）
		// $pipeline = 'amr2mp3';
		// //要进行转码的转码操作
		// $fops = "avthumb/mp3";
		// //可以对转码后的文件进行使用saveas参数自定义命名，当然也可以不指定文件会默认命名并保存在当间
		// $savekey = \Qiniu\base64_urlSafeEncode($bucket.':'.$name1.'.mp3');
		// $fops = $fops.'|saveas/'.$savekey;
		// // 上传后立即触发转码，在上传策略中设置persistentOps、persistentPipeline和persistentNotifyUrl
		// // 上传文件到七牛后， 七牛将文件名和文件大小回调给业务服务器
		// $policy = array(
		// 	'persistentOps' => $fops,
		// 	'persistentPipeline' => $pipeline,
		// 	'callbackUrl' => 'http://wechat.huangxf.com/activity2/public/index.php/ajax/callbackHandler',
		// 	'callbackBody' => 'filename=$(fname)&filesize=$(fsize)'
		// );
		// // 生成上传 Token
		// $token = $auth->uploadToken($bucket, null, 3600, $policy);
		// // 要上传文件的本地路径
		// // $file = 'upload/wx.amr';
		// // 要上传文件的远程路径
		// $file = file_get_contents('http://wechat.huangxf.com/activity2/public/upload/wx.amr');
		// // 上传到七牛后保存的文件名
		// $key = $name1.'.amr';
		// // 初始化 UploadManager 对象并进行文件的上传
		// $uploadMgr = new UploadManager();
		// // 调用 UploadManager 的 putFile(本地)或put(远程) 方法进行文件的上传
		// list($ret, $err) = $uploadMgr->put($token, $key, $file);//上传到七牛云存储的时候，key 是可选的。如果你不指定 key(设为null)，则自动以 hash 值作为 key，此时自动带了消重能力。两个用户上传相同的文件，最终的 key 是一样的。
		// echo "\n====> putFile result: \n";
		// if ($err !== null) {
		// 	var_dump($err);
		// } else {
		// 	var_dump($ret);
		// }
		//对已经上传到七牛的视频发起异步转码操作////////////////////////////////////////////
		// $key = '1492351597605678314.amr';
		// //转码是使用的队列名称
		// $pipeline = 'amr2mp3';
		// $pfop = new PersistentFop($auth, $bucket, $pipeline);
		// //要进行转码的转码操作
		// $fops = "avthumb/mp3";
		// list($id, $err) = $pfop->execute($key, $fops);
		// echo "\n====> pfop avthumb result: \n";
		// if ($err != null) {
		//   var_dump($err);
		// } else {
		//   echo "PersistentFop Id: $id\n";
		// }
		// //查询转码的进度和状态(http://api.qiniu.com/status/get/prefop?id={PersistentFop Id})
		// list($ret, $err) = $pfop->status($id);
		// echo "\n====> pfop avthumb status: \n";
		// if ($err != null) {
		//   var_dump($err);
		// } else {
		//   var_dump($ret);
		// }

		// //上传到阿里云OSS
		// $accessKeyId = "ytDERP6CXu4gmoSe"; ;
		// $accessKeySecret = "hXdIG0FN0qn7al08HGCsNW29kH2chh";
		// $endpoint = "oss-cn-qingdao.aliyuncs.com";
		// try {
		// 	$ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
		// 	$ossClient->uploadFile('huangxf',"test/upload/{$name1}.jpg","upload/{$name1}.jpg");
		// } catch (OssException $e) {
		// 	print $e->getMessage();
		// }

		// //上传到腾讯云COS
		// $arr = Cosapi::upload('huangxf',"upload/{$name1}.jpg","test/{$name1}.jpg");
		// if($arr['code']=='0'){//上传成功了
		//     echo $arr['data']['access_url'];
		//     echo '<br>';
		//     echo $arr['data']['resource_path'];
		// }else{
		//     exit('上传失败,'.$arr['message']);
		// }

		$member_id=Db::name('member')
			->where('openid',input('post.openid'))
			->value('id');
		if(strlen($member_id)<5){
			$num=$member_id.randnum(5-strlen($member_id));
		}else{
			$num=$member_id;
		}
		$coupons=Db::name('coupon')
			->where('status',0)
			->limit(1)
			->field('id,code')
			->find();
		if(!$coupons){
			$code=randstr(16);
		}else{
			Db::name('coupon')
				->where('id',$coupons['id'])
				->update(['status'=>1]);
			$code=$coupons['code'];
		}
		$newid=Db::name('pic')
			->insertGetId(['openid'=>input('post.openid'),'pic'=>"{$name1}.jpg",'num'=>$num,'coupon'=>$code,'sendtime'=>time()]);
		$this->ajaxArr['errcode']=0;
		//服务器角度解决跨域（如果图片不传至云存储可独立一台图片服务器用其它域名读写图片，适合SLB）
		header('content-type:application:json;charset=utf8');
		header('Access-Control-Allow-Origin:*');
		header('Access-Control-Allow-Methods:POST');
		header('Access-Control-Allow-Headers:x-requested-with,content-type');
		if( isset($_GET['jsoncallback']) ){//前端jsonp解决跨域，不太可行，jsonp是动态模拟script标签实现跨域，故只能GET不能POST，base64图片编码拼接到URL会导致URL太大
			echo $_GET['jsoncallback'] . "(".json_encode($this->ajaxArr).")";
			exit;
		}else{
			return json($this->ajaxArr);
		}
	}

	public function callbackHandler(){//微信amr格式音频上传与转码处理完成回调
		trace(input(''));
	}

	public function afterShare($type){//分享成功
		//do something...
		return json(['errcode'=>0]);
	}

	public function tellFans(){//判断是否粉丝
		$info=getinfo( input('get.openid') );
		$this->ajaxArr['errcode']=@$info['subscribe']==1?0:1;
		return json($this->ajaxArr);
	}

	public function loadMore(){
		$pagesize=2;
		$list = Db::name('member')
			->where('status',1)
			->limit($pagesize)
			->order('click desc')
			// ->fetchSql()
			->paginate($pagesize);

		$str='';
		$i=0;
		foreach ($list as $vo) {
			$str.='<li>
	                <a href="#"><img src="'.$vo['headimg'].'" width="150" /></a>
	                <p>排行榜：'.(((input('get.page')-1)*$pagesize)+$i+1).'位</p>
	                <p>微信昵称：'.$vo['nickname'].'</p>
	                <p>手机：'.(substr($vo['tel'],0,3).'****'.substr($vo['tel'],-3)).'</p>
	                <p>总票数：'.$vo['click'].'</p>
	              </li>';
	        $i++;

		}
		echo $str;

	}

	public function lottery(){
		$lotArr=array();//奖项数据
		$lotArr[0]=array('level'=>0,'text'=>'未中奖','angle'=>0);
		for($p=1;$p<=conf('pcnt');$p++){
			$lotArr[$p]=array(
				'level'=>$p,
				'text'=>conf('prize_name'.$p),
				'angle'=>intval(conf('prize_angle'.$p)),
				'chance'=>intval(conf('prize_chance'.$p)),
				'count'=>intval(conf('prize_cnt'.$p)),
				'daycount'=>intval(conf('prize_daycnt'.$p))
			);
		}

		//抽奖
		if ( !session('?token1') || session('token1')!=input('post.token') ) {
			$this->ajaxArr['errmsg']='invalid token!';
		}else{
			$m=Db::name('member')->where('openid',input('post.openid'))->field('win_prize,lottery_times,lottery_time,name')->find();
			if(!$m)exit('invalid openid.');

			if($m['win_prize']!=0){//每人只能中奖一次的，多次中奖机制另建win表
				if(empty($m['name'])){//中奖了但没填信息
					exit('{"win":1,"needinfo":1,"level":'.$m['win_prize'].',"text":"'.$lotArr[ $m['win_prize'] ]['text'].'"}');
				}else{
					exit('{"win":1,"needinfo":0,"level":'.$m['win_prize'].',"text":"'.$lotArr[ $m['win_prize'] ]['text'].'"}');//也可以不用这句住下走，不会重复中奖
				}
			}

			if(date('Y-m-d',$m['lottery_time'])==date('Y-m-d')){//今天已经抽过
				if($m['lottery_times']>=conf('lottery_times_per_day')){//今天抽的次数已经达到设定值
					exit('{"res":0,"msg":"每人每天只能抽奖'.conf('lottery_times_per_day').'次~"}');
				}else{
					Db::name('member')->where('openid',input('post.openid'))->setInc('lottery_times');
					$this->ajaxArr['left']=conf('lottery_times_per_day')-$m['lottery_times']-1;//今天剩余多少次抽奖次数
				}
			}else{//今天第一次抽
				Db::name('member')->where('openid',input('post.openid'))->update(['lottery_time'=>time(),'lottery_times'=>1]);
				$this->ajaxArr['left']=conf('lottery_times_per_day')-1;//今天剩余多少次抽奖次数
			}

			$lotCnt=count($lotArr);
			for($i=1;$i<$lotCnt;$i++){//总奖品数量限制
				$win_count=Db::name('member')->where('win_prize',$i)->count('id');
				// trace($win_count);
				if($win_count>=$lotArr[$i]['count']){
					$lotArr[$i]['chance']=0;
				}
			}
			for($i=1;$i<$lotCnt;$i++){//每日奖品数量限制
				$today_win_count=Db::name('member')->where("TO_DAYS(FROM_UNIXTIME(win_time,'%Y-%m-%d'))=TO_DAYS(current_date) and win_prize={$i}")->count('id');
				// trace($today_win_count);
				if($today_win_count>=$lotArr[$i]['daycount']){
					$lotArr[$i]['chance']=0;
				}
			}
			if($m['win_prize']!=0){//不能重复中奖
				for($i=1;$i<$lotCnt;$i++){
					$lotArr[$i]['chance']=0;
				}
			}

			$allChance=0;
			for($i=1;$i<$lotCnt;$i++){
				$allChance+=$lotArr[$i]['chance'];
			}
			if($allChance>1000){
				$max=$allChance;
			}else{
				$max=1000;
			}
			$n=rand(1,$max);
			$r=0;
			$beginPos=0;
			for($i=1;$i<$lotCnt;$i++){
				if($n>$beginPos&&$n<=$beginPos+$lotArr[$i]['chance']){
					$r=$i;
				}
				$beginPos+=$lotArr[$i]['chance'];
			}
			if($r!=0){
				Db::name('member')->where('openid',input('post.openid'))->update(['win_prize'=>$r,'win_time'=>time(),'win_date'=>date('Y-m-d')]);
			}
			//$r=0;
			$this->ajaxArr['res']=1;
			$this->ajaxArr['level']=$lotArr[$r]['level'];
			$this->ajaxArr['angle']=$lotArr[$r]['angle'];
			$this->ajaxArr['text']=$lotArr[$r]['text'];
		}
		return json($this->ajaxArr);
	}

}