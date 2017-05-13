//弹出
$("#popup").click(function() {
    $('#mask').show();
    $("#pop1").show();
});

//关闭按钮
$(".close").click(function() {
    $('#mask').hide();
    $(this).parent().hide();
    //$(".box").hide();
});

//分享
$("#share").click(function() {
    shareData.link = share_click_link;
    share_act();
    $('#sharemask').show();
    console.log(shareData);
});
$("#sharemask").click(function() {
    $(this).hide();
});


//保存
var saving1 = 0;
var status1 = '保存中，请稍候...';
$("#saveinfo").click(function() {
    if ($("#name").val() === '') {
        alert('请填写姓名');
        $("#name").focus();
        return;
    }
    if (/^1[345789][0-9]{9}$/.test($("#tel").val()) === false) {
        alert('请填写正确的手机号码');
        $("#tel").focus();
        return;
    }
    if (saving1 === 0) {
        saving1 = 1;
        var submitData = {
            token: token1,
            openid: openid,
            name: $("#name").val(),
            tel: $("#tel").val(),
        };
        $.post(myapp + '/ajax/saveInfo', submitData,
            function(data, textStatus) {
                if (data.errcode === 0) {
                    //saving1=0;//可重复操作
                    alert('保存成功！');
                    status1 = '已保存';
                }
            }, "json");
    } else {
        alert(status1);
    }

});

//点赞
var saving2 = 0;
var status2 = '操作中，请稍候...';
$("#click").click(function() {
    if (saving2 === 0) {
        saving2 = 1;
        var _self = $(this);
        // $.get(myapp + '/ajax/tellFans?openid=' + openid,
        //     function(data, textStatus) {
        //         if (data.errcode == 1) {
        //             status2 = '关注后才能点赞哦~';
        //             alert(status2);
        //             return false;
        //         } else {
        //             //...
        //         }
        //     }, "json");
        var my_openid = getQueryString('my_openid')==null?openid:getQueryString('my_openid');
        var by_openid = openid;
        $.get(myapp + '/ajax/click?my_openid=' + my_openid + '&by_openid=' + by_openid + '&token=' + token2 + '&checkstr=' + checkstr,
            function(data, textStatus) {
                if (data.errcode === 0) {
                    //saving2=0;//可重复操作
                    alert('点赞成功！');
                    status2 = '已点赞';
                } else if (data.errcode == 1) {
                    //saving2=0;//可重复操作
                    status2 = '你已经给TA点过赞了';
                    alert(status2);
                }else{
                    alert(data.errmsg);
                }
            }, "json");

    } else {
        alert(status2);
    }

});

//上传图片
$("#upload").click(function() {
    $("#upload").unbind('click');
    var submitData = {
        openid: openid,
        pic: 'data:image/x-icon;base64,AAABAAEAEBACAAEAAQCwAAAAFgAAACgAAAAQAAAAIAAAAAEAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD//wAA888AAPPPAADzzwAA888AAPPPAADzzwAA8A8AAPAPAADzzwAA888AAPPPAADzzwAA888AAPPPAAD//wAA',
    };
    $.post(myapp + '/ajax/uploadPic', submitData,
        function(data, textStatus) {
            if (data.errcode === 0) {
                alert('保存图片成功！');
            }
        }, "json");
});

//加载更多
var loading = 0;
var curPage = 1;
$("#loadMore").on('click', function() {
    if (loading === 0) {
        loading = 1;
        var _this = $(this);
        _this.html('加载中...');
        if (curPage == rearPage) {
            _this.html('');
        } else {
            curPage += 1;
            $.get(myapp + '/ajax/loadMore?page=' + curPage,
                function(data, textStatus) {
                    if (data.indexOf('<li') !== -1) {
                        if (curPage == rearPage) {
                            _this.html('');
                        } else {
                            loading = 0;
                            _this.html('加载更多……');
                        }
                        console.log(data);
                        $('#rankList').append(data);
                    } else {
                        _this.html('');
                    }
                }, "html");
        }
    }
});

//抽奖
var saving3 = 0;
var status3 = '抽奖中，请稍候...';
$('#lottery').click(function(){
    if (saving3 === 0) {
        saving3 = 1;
        var submitData = {
            token: token1,
            openid: openid,
        };
        $.post(myapp + '/ajax/lottery', submitData,
            function(data, textStatus) {
                saving3 = 0;
                console.log(data);
                if(data.win==1){
                    swal('已经中'+data.level+'等奖：'+data.text+'，是否填写信息：'+data.needinfo);
                    return;
                }
                if(data.res===1){
                    if(data.level===0){
                        swal('很遗憾，没有中奖，今天剩余抽奖次数:'+data.left);
                    }else{
                        swal('恭喜你中得'+data.level+'等奖：'+data.text+'，今天剩余抽奖次数:'+data.left);
                    }
                }else if(data.res===0){
                    swal(data.msg);
                }
            }, "json");
    } else {
        swal(status3);
    }
});

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r !== null) return unescape(r[2]);
    return null;
}
