// $.getJSON('http://wechat.huangxf.com/single/wxapi.php?callback=?',{"method":"wxsdk","cdns":1,"url":location.href.split('#')[0]},function(json){
//     //自动encodeURIComponent
//     wx.config({
//         debug: true,
//         appId: json.appId,
//         timestamp: json.timestamp,
//         nonceStr: json.nonceStr,
//         signature: json.signature,
//         jsApiList: [
//             'onMenuShareTimeline',
//             'onMenuShareAppMessage',
//         ]
//     });
// });
wx.config({
    debug: false,
    appId: signPackage.appId,
    timestamp: signPackage.timestamp,
    nonceStr: signPackage.nonceStr,
    signature: signPackage.signature,
    jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'hideOptionMenu',
        'showOptionMenu',
        'hideMenuItems',
        'showMenuItems',
        'hideAllNonBaseMenuItem',
        'showAllNonBaseMenuItem',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
        'chooseImage',
        'previewImage',
        'uploadImage',
        'downloadImage',
        'closeWindow'
    ]
});

function share_success() {
    $.get(myapp+'/ajax/afterShare',
        function(data, textStatus) {
            if (data.errcode === 0) {

            } else {

            }
        }, "json");
}

function share_act() {
    // 监听“分享给朋友”
    wx.onMenuShareAppMessage({
        title: shareData.title1,
        desc: shareData.desc,
        link: shareData.link,
        imgUrl: shareData.imgUrl,
        success: function(res) {
            // share_success();
        },
    });
    // 监听“分享到朋友圈”
    wx.onMenuShareTimeline({
        title: shareData.title2,
        link: shareData.link,
        imgUrl: shareData.imgUrl,
        success: function(res) {
            // share_success();
        },
    });
}

wx.ready(function() {
    share_act();
    // alert('活动已经结束了哦~');
    // wx.closeWindow();
});
