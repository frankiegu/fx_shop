/**
 * @type Boolean|Boolean
 * 用于统计页面的访问深度
 */
var flagVisit=true;
var flagMid=true;
var flagBtm=true;
//页面访问统计
if(flagVisit){
    flagVisit=false;
    //var postData={aid:tjAid,position:1};
    //send(postData);
}
$(window).scroll(function()
{
        var scrollPosition = window.pageYOffset;  //Netscape属性，指的是滚动条顶部到网页顶部的距离
        var windowSize     = window.innerHeight;   //窗口高度
        //var windowSize     = document.body.clientHeight;   //窗口高度
        //var bodyHeight     = document.body.offsetHeight;
        var bodyHeight     = document.body.scrollHeight;
        //var bottomDistance = Math.max(bodyHeight - (scrollPosition + windowSize), 0);//滚动时，滚动条到底部的距离
        var midDistance    = parseInt(bodyHeight/2);   //页面滚动到一半高度
        var hSize = parseInt(windowSize / 2);
        
        if(scrollPosition >= bodyHeight - windowSize - hSize)
        {
            //到达页面底的位置
            if(flagBtm){
                if(flagMid){
                flagMid=false;
                    var postData={moid:tjMoid,position:2};
                    send(postData);
                }
                flagBtm=false;
                var postData={moid:tjMoid,position:3};
                send(postData);
            }
        } else if(midDistance - hSize  <= scrollPosition && scrollPosition < midDistance + hSize) {
            //到达接近中部的位置
            if(flagMid){
                flagMid=false;
                var postData={moid:tjMoid,position:2};
                send(postData);
            }
        }//console.log(scrollPosition+'-'+windowSize+'-'+bodyHeight+'-'+midDistance+'-'+hSize);
});
//发送数据
function send(data){
    $.post(acUrl, data);
}