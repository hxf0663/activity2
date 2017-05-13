var loader;
function initLoad() {
    var manifest = [
        {id:"mysrc", src:"addon/img/ewm.jpg"},
        {id:"mysrc", src:"addon/img/share.png"},
        {id:"mysrc", src:"addon/img/close.png"},
        {id:"mysrc", src:"addon/img/tx.jpg"},
        // {id:"myPath", src:"addon/music.mp3"},

    ];

    loader = new createjs.LoadQueue(false);
    // loader.installPlugin(createjs.Sound);
    loader.addEventListener("fileload", handleFileLoad);
    loader.addEventListener("complete", handleComplete);
    loader.addEventListener("progress", handleFileProgress);
    loader.loadManifest(manifest);
    // loader.loadFile({id:"bgmusic", src:"addon/music.mp3"});
}

function handleFileProgress(event) {
    var percent = loader.progress*100|0;
    //console.log(percent+'% loaded.');
    $('#loadingText').html('玩命加载中...('+percent+'%)');
}

function handleFileLoad(evt) {
    //console.log(evt);
}

function handleComplete() {
    // console.log( loader.getResult("mysrc") );
    $('#mask, #loadingText').hide();//隐藏加载层

    // var ppc = new createjs.PlayPropsConfig().set({interrupt: createjs.Sound.INTERRUPT_ANY, loop: -1, volume: 1});
    // createjs.Sound.play("bgmusic", ppc);//播放背景音乐

}

$(document).ready(function(){
    $('#mask').show();
    initLoad();
});