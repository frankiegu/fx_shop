/*用于表单验证*/
$(function(){
    //限制输入字符长度
    $('input[name=cname]').attr('maxlength', '16');
    $('input[name=telno]').attr('maxlength', '11');
    $('input[name=address]').attr('maxlength', '200');
    $('input[name=qq]').attr('maxlength', '12');
    //统计总价
    $('form').each(function(){
        var obj = this;
        $(this).find('.pid_1').click(function(){
            totalPrice(obj);
        });
        totalPrice(obj);
    });
    
    function totalPrice(obj){
        var tt = 0;
        $(obj).find('.pid_1').each(function(){
            if($(this).is(':checked')){
                tt = tt + parseInt($(this).attr('price'));
            }
        });
        $(obj).find('#ttprice_1').html(tt);
    }
    //验证表单
    $('form').submit(function(e){
        if($(this).find('.pid_1').length > 0){
            var pids = $(this).find('.pid_1').is(':checked');
            if($(pids).length < 1){
                alert("请选择想要购买的商品");
                return false;
            }
        }
        
        var input = $(this).children().find('input');
        for(i = 0; i < input.length; i++){
            if($(input[i]).attr('name') === 'cname'){
                var cname = $.trim($(input[i]).val());
                var reg = /^[\u4E00-\u9FA5- ]+$/;
                if(!reg.test(cname) || cname === ''){
                    alert("请输入中文姓名!");
                    return false;
                }
            }else if($(input[i]).attr('name') === 'telno'){
                var telno = $.trim($(input[i]).val());
                var patrn=/^(1)\d{10}$/;
                if (!patrn.exec(telno) || telno === ''){
                    alert('请输入正确的手机号码!');
                    return false;
                }
            }else if($(input[i]).attr('name') === 'telno1'){
                var telno1 = $.trim($(input[i]).val());
                var patrn=/^(1)\d{10}$/;
                if (telno !== telno1 || telno1 === ''){
                    alert('两次输入的手机号码不一致!');
                    return false;
                }
            }else if($(input[i]).attr('name') === 'address'){
                var city = $.trim($(input[i]).val());
                if(city === ''){
                    alert('请输入地址!');
                    return false; 
                }
            }
        }
        var dizhi = $(this).children().find('[name=province]').val() + $(this).children().find('[name=city]').val() + $(this).children().find('[name=district]').val() + $(this).children().find('[name=address]').val()
        $(this).children().find('[name=address]').val(dizhi)
        return true;
    });
 });

/**倒计时**/   
var $afterTime="";
function timer($afterTime) {
    setInterval(function(){
        var d=new Date();
        var $now_s=d.getSeconds();//当前秒数
        var $now_i=d.getMinutes();//当前分钟数
        var $now_h=d.getHours();//当前小时数
        var $af_s=60-$now_s;
        var $af_i=60-$now_i-1;
        var $af_h=24-$now_h-1;
        //var day_show=$af_h;
        $afterTime=($af_h)+"小时"+($af_i)+"分"+($af_s)+"秒";
        $(".time_01").html($afterTime);
        $afterTime--;
    }, 1000);
}
$(function(){
        timer($afterTime);
 });	