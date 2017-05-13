<?php
namespace app\index\controller;
use \think\Controller;
use \think\Db;
use app\index\model\Member as MemberModel;

class Index extends Controller
{

	private $openid    = '';
	private $userinfo  = array('openid'=>'','nickname'=>'','headimgurl'=>'');
	private $mypath    = '';
	private $myapp     = '';
	private $myurl     = '';
	private $oriPath   = '';
	private $cdnPath   = '';
	private $shareData = array(
		'title1'=>'分享给朋友标题',
		'title2'=>'分享到朋友圈标题',
		'desc'=>'分享给朋友描述',
		'link'=>'http://huangxf.com/',
		'icon'=>'http://huangxf.com/favicon.ico'
	);

	/**
	 * 定义控制器初始化方法_initialize，在该控制器的方法调用之前首先执行。
	 * @return [type] [description]
	 */
	public function _initialize(){
		// session(null);// 清除session
		// 环境要求：支持PATHINFO，80端口（最好默认页为index.php，支持伪静态，apache rewrite规则在web目录下）
		$this->oriPath = 'http://wechat.huangxf.com/activity2/public/index.php';//对外入口及分享进入的网址
		$this->myurl   = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";//当前访问页完整网址，同前端location.href
        $domains = array(
            'wechat.huangxf.com'
        );
        $domain = $domains[rand(0,count($domains)-1)];
		// $this->jumpUrl($domain);
		$this->mypath  = "http://{$domain}/activity2/public/";//APP所在目录网址
		$this->myapp   = $this->mypath.'index.php';//主入口APP文件网址
		$this->cdnPath = 'http://wechat.huangxf.com/activity2/public/';//CDN缓存资源路径（不用CDN则填写落地域名）
		//$this->shareData['title1'] = '';
		//$this->shareData['title2'] = '';
		//$this->shareData['desc']   = '';
		$this->shareData['link']     = $this->oriPath;
		//$this->shareData['icon']   = $this->mypath.'icon.jpg';
	}

    /**
     * H5页面诱导分享被举报后被封换域名，配置多个JS安全域名，授权最好用中转模式（用主号授权则跳转的落地域名和授权域名是同一个，若被封要改授权域名，即只有三次机会）
     * 涉及诱导关注或公众号被举报等原因，入口域名也可能被封
     */
	private function jumpUrl($domain){
		if(stripos($this->oriPath, $_SERVER['HTTP_HOST'])!==false){
			$this->redirect( str_ireplace($_SERVER['HTTP_HOST'], $domain, $this->myurl) );
		}
	}

    public function index(){
    	$this->oauth(2);//微信授权
		// $this->openid=input('?get.ls_openid')?input('get.ls_openid'):md5(microtime(true).uniqid().rand(1,1000000));//模拟OPENID存储在本地local storage（网址中db参数为1才要与数据库交互）
        $member = model('Member');// Db::name('member')
		if(!$member->where('openid',$this->openid)->value("id")){//第一次进入:member表里不存在记录
            $data = ['openid'=>$this->openid, 'nickname'=>$this->userinfo['nickname'], 'headimg'=>$this->userinfo['headimgurl'], 'sendtime'=>time(), 'fromip'=>get_client_ip()];
            // $member->insert($data);
            $member->data($data)->save();
        }else{
            $data = [ 'nickname'=>$this->userinfo['nickname'],'headimg'=>$this->userinfo['headimgurl'] ];
            // $member->where('openid',$this->openid)->update($data);
            $member->save($data,['openid'=>$this->openid]);
        }
        $m=Db::name('member')->where('openid',$this->openid)->field('*')->find();

		//分页
		$pagesize=2;
		$this->assign('pagesize',$pagesize);

		$list = Db::name('pic')
					->alias('a')
					->join('__MEMBER__ b','a.openid = b.openid', 'LEFT')
					->where('a.type',input('get.type',1))
					->field('pic,num,a.click,nickname,tel')
					->order('a.sendtime desc')
					// ->fetchSql()
					->paginate($pagesize);

		// trace($list);
		$page = $list->render();
		$this->assign('list',$list);
		$this->assign('page',$page);

		//首屏
		$list2 = Db::name('member')
					->where('status',1)
					->limit($pagesize)
					->order('click desc')
					// ->fetchSql()
					->paginate($pagesize);

		// dump($list2);
		$this->assign('list2',$list2);

		$this->assign('m',$m);
		$this->assign_tpl();
		$this->assign_token(2);//生成哈希会话防止CSRF
		$this->assign('checkstr', md5($this->openid.$this->openid.'hxf') );//生成非会话动态参数防止CSRF

        return $this->fetch();
    }

    public function share(){
		$this->oauth();//微信授权获取访问者openid
		if( !input('?get.my_openid') ){
			$this->redirect('index');
		}
		$m=Db::name('member')->where('openid',input('get.my_openid'))->field('*')->find();

		//计算当前排名第几名（忽略与自己相等的）
		$up_me=Db::name('member')
			->where('click','GT',$m['click'])
			->group('click')
			->field('id')
			->select();
		$rank1=count($up_me)+1;
		//计算当前排名第几位（与自己相等的按入库前后排名）
		$up_me_cnt=Db::name('member')
			->where('click','GT',$m['click'])
			->count('id');
		$equal_me=Db::name('member')
			->where('click','EQ',$m['click'])
			->order('id','asc')
			->field('id')
			->select();
		$equal_me_cnt=count($equal_me);
		if($equal_me_cnt==1){
			$rank2=$up_me_cnt+1;
		}else{
			for($e=0;$e<$equal_me_cnt;$e++){
				if($equal_me[$e]['id']==$m['id']){
					$rank2=$up_me_cnt+$e+1;
				}
			}
		}
		//计算与前一名差多少
		$last_up_me=Db::name('member')
			->where('click','GT',$m['click'])
			->order('click')
			->value('click');
		$differ=!$last_up_me?0:($last_up_me-$m['click']);

		$this->assign('m',$m);
		$this->assign_tpl();
		$this->assign_token(1);//生成哈希会话防止CSRF
		$this->assign('checkstr', md5(input('get.my_openid').$this->openid.'hxf') );

		return $this->fetch('index');

	}

    /**
     * oauth 授权获取用户信息
     * @param  integer $scope 微信授权类型，1为暗授权，2为明授权，默认为1
     * @return [type]         [description]
     */
    private function oauth($scope=1){
    	//授权前先判断用户信息是否仍然保存在服务器会话中
    	if( $scope==1 && session('?openid') ){
    		$this->openid = session('openid');
    		return;
    	}
    	if( $scope==2 && session('?openid') && session('?userinfo') ){
    		$this->openid   = session('openid');
    		$this->userinfo = session('userinfo');
    		return;
    	}
    	//主号授权
    	$appid     = conf('appid');
    	$appsecret = conf('appsecret');
    	if(input('get.state')=='wx'){
    		if( input('?get.code') ){
    			$code = input('get.code');
    			$res  = httpGet("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code");
    			$arr  = json_decode($res, true);
    			if( isset($arr['errcode']) ){
    				trace("errcode:{$arr['errcode']}, errmsg:{$arr['errmsg']}");
    				if( config('app_debug') ){
    					$this->error($arr['errmsg']);
    				}else{
    					$this->redirect('index.php');
    				}
    			}else{
    				$this->openid = $arr['openid'];//snsapi_base式的网页授权流程即到此为止
    				if(empty($this->openid)){
    					if( config('app_debug') ){
    						$this->error("OPENID为空，请重新授权");
    					}else{
    						$this->redirect('index.php');
    					}
    				}
    				session('openid', $this->openid);
    				if($scope==2){//snsapi_userinfo式授权
    					$oauth_token = $arr['access_token'];
    					$res = httpGet("https://api.weixin.qq.com/sns/userinfo?access_token={$oauth_token}&openid={$this->openid}&lang=zh_CN");
    					$this->userinfo = json_decode($res, true);
    					session('userinfo', $this->userinfo);
    				}
    			}
    		}else{
    			$this->error('请同意授权才能继续');
    		}
    	}else{
    		if( cookie('?pcdebug') ){//调试模式下PC调试
    			$this->openid                 = cookie('pcopenid');
    			$this->userinfo['nickname']   = 'huangxf';
    			$this->userinfo['headimgurl'] = $this->mypath.'addon/img/tx.jpg';
    			// session('openid', $this->openid);
    			// session('userinfo', $this->userinfo);
    		}else{
    			$this->redirect( auth_url($appid, $this->myurl, $scope) );
    		}
    	}
    	return;
    	//授权中转（方太：跳转授权网址?platId=c2a1809168b6d3fdcb101ff2bfd30f7c&scope=[snsapi_base|snsapi_userinfo]&state=justcode&url=urlencode(url)，返回url?code=(用此CODE拉取信息)）
    	if( input('?get.code') ){
    		$code = input('get.code');
    		$res  = httpGet("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code");
    		$arr  = json_decode($res, true);
    		if( isset($arr['errcode']) ){
    			trace("errcode:{$arr['errcode']}, errmsg:{$arr['errmsg']}");
    			if( config('app_debug') ){
    				$this->error($arr['errmsg']);
    			}else{
    				$this->redirect('index.php');
    			}
    		}else{
    			$this->openid = $arr['openid'];//snsapi_base式的网页授权流程即到此为止
    			if(empty($this->openid)){
    				if( config('app_debug') ){
    					$this->error("OPENID为空，请重新授权");
    				}else{
    					$this->redirect('index.php');
    				}
    			}
    			session('openid', $this->openid);
    			if($scope==2){//snsapi_userinfo式授权
    				$oauth_token = $arr['access_token'];
    				$res = httpGet("https://api.weixin.qq.com/sns/userinfo?access_token={$oauth_token}&openid={$this->openid}&lang=zh_CN");
    				$this->userinfo = json_decode($res, true);
    				session('userinfo', $this->userinfo);
    			}
    		}
    	}else{
    		if( cookie('?pcdebug') ){//调试模式下PC调试
    			$this->openid                 = cookie('pcopenid');
    			$this->userinfo['nickname']   = 'huangxf';
    			$this->userinfo['headimgurl'] = $this->mypath.'addon/img/tx.jpg';
    			// session('openid', $this->openid);
    			// session('userinfo', $this->userinfo);
    		}else{
    			$this->redirect( 'http://fotilewechat.fotile.com/oauth/authorize?platId=c2a1809168b6d3fdcb101ff2bfd30f7c&scope='.($scope==1?'snsapi_base':'snsapi_userinfo').'&state=justcode&url='.urlencode($this->myurl) );
    		}
    	}
    	return;
    	//授权中转（kidea：跳转授权网址?redirect_uri=urlencode(url)&scope=[snsapi_base|snsapi_userinfo]，返回url?wxUser=urlencode(json_encode(用户信息数组))）
    	if( input('?get.wxUser') ){
    		$arr=json_decode(urldecode( input('get.wxUser') ), true);
    		$this->openid=$arr['openid'];//snsapi_base式的网页授权流程即到此为止
    		session('openid', $this->openid);
    		if($scope==2){//snsapi_userinfo式授权
    			$this->userinfo=$arr;
    			session('userinfo', $this->userinfo);
    		}
    	}else{
    		if( cookie('?pcdebug') ){//调试模式下PC调试
    			$this->openid                 = cookie('pcopenid');
    			$this->userinfo['nickname']   = 'huangxf';
    			$this->userinfo['headimgurl'] = $this->mypath.'addon/img/tx.jpg';
    			// session('openid', $this->openid);
    			// session('userinfo', $this->userinfo);
    		}else{
    			$this->redirect( 'http://kidea.h5.babyjs.cn/wechat/auth?redirect='.urlencode($this->myurl).'&scope='.($scope==1?'snsapi_base':'snsapi_userinfo') );
    		}
    	}
        //授权中转（wxsdk：跳转授权网址?redirect_uri=urlencode(url)&scope=[1|2]，返回url?FromOpenid=...[&NickName=urlencode(...)&HeadImgUrl=urlencode(...)]）
        if( input('?get.FromOpenid') ){
            $this->openid=input('get.FromOpenid');//snsapi_base式的网页授权流程即到此为止
            session('openid', $this->openid);
            if($scope==2){//snsapi_userinfo式授权
                $this->userinfo['nickname']   = urldecode(input('get.NickName'));
                $this->userinfo['headimgurl'] = urldecode(input('get.HeadImgUrl'));
                session('userinfo', $this->userinfo);
            }
        }else{
            if( cookie('?pcdebug') ){//调试模式下PC调试
                $this->openid                 = cookie('pcopenid');
                $this->userinfo['nickname']   = 'huangxf';
                $this->userinfo['headimgurl'] = $this->mypath.'addon/img/tx.jpg';
                // session('openid', $this->openid);
                // session('userinfo', $this->userinfo);
            }else{
				//访问域名为授权域名
                $this->redirect( 'http://wechat.huangxf.com/single/ht/wxsdk.php?redirect_uri='.urlencode($this->myurl).'&scope='.$scope );
            }
        }
    }

    private function assign_tpl(){//模板公共变量
    	$this->assign('openid',$this->openid);
    	$this->assign('userinfo',$this->userinfo);
    	$this->assign('mypath',$this->mypath);
    	$this->assign('myapp',$this->myapp);
    	$this->assign('myurl',$this->myurl);
    	$this->assign('oriPath',$this->oriPath);
    	$this->assign('cdnPath',$this->cdnPath);
    	$this->assign('shareData',$this->shareData);
    	$this->assign('signPackage',wxsdk());
    	$this->assign('tongji','<div style="display:none"></div>');
    }

    private function assign_token($n){//模板会话变量(用于AJAX交互)
    	for($i=1;$i<=$n;$i++){
    		if(!session('?token'.$i)){
	    		$hash = md5( $this->openid.time().rand(1,10000) );
	    		session('token'.$i, $hash);
	    		$this->assign('token'.$i, $hash);
    		}else{
    			$this->assign('token'.$i, session('token'.$i));
    		}
    	}
    }

}
