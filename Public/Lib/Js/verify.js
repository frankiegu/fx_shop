/*用于表单验证*/
$(function(){
    //限制输入字符长度
    $('input[name=cname]').attr('maxlength', '16');
    $('input[name=telno]').attr('maxlength', '11');
    $('input[name=address]').attr('maxlength', '200');
    $('input[name=qq]').attr('maxlength', '20');
    //统计总价
    $('form').each(function(){
        var obj = this;
        if($(obj).find('.pid_1').length > 0 && $(obj).find('.pid_2').length < 1){
            $(obj).find('.pid_1').click(function(){
                totalPrice(obj);
            });
            totalPrice(obj);
        }else if($(obj).find('.pid_2').length > 0){
            $(obj).find('.pid_2').change(function(){
                totalPrice_2(obj);
            });
            totalPrice_2(obj);
        }
    });
    //点选按钮计算总价
    function totalPrice(obj){
        var tt = 0;
        $(obj).find('.pid_1').each(function(){
            if($(this).is(':checked')){
                tt = tt + parseInt($(this).attr('price'));
            }
        });
        $(obj).find('.ttprice_1').val(tt);
    }
    //下拉选择计算总价
    function totalPrice_2(obj){
        var tt = $(obj).find('.pid_2').children('option:selected').attr('price');
        $(obj).find('.ttprice_1').val(parseInt(tt));
    }
    //地区初始化
    $('form').each(function(x){
        var obj = this;
        $(obj).find('.wfdqxl').each(function(y){
            $(this).attr('name', 'name_'+x+'_'+y);
        });
        try{
            new PCAS('name_'+x+'_0', 'name_'+x+'_1', 'name_'+x+'_2');
        }catch(e){}
    });
    //验证表单
    $('form').unbind().submit(function(e){
        if($(this).find('.pid_1').length > 0){
            var pids = $(this).find('.pid_1').is(':checked');
            if($(pids).length < 1){
                alert("请选择想要购买的商品");
                return false;
            }
        }
        //
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
            }else if($(input[i]).attr('name') === 'city'){
                var province = $(this).find('.province_1').val();
                var city = $(this).find('.city_1').val();
                var area = $(this).find('.area_1').val();
                if($.trim(area) === ''){
                    alert('请选择地区!');
                    return false;
                }
                $(input[i]).val(province+' '+city+' '+area);
            }else if($(input[i]).attr('name') === 'address'){
                var address = $.trim($(input[i]).val());
                if(address === ''){
                    alert('请输入详细地址!');
                    return false; 
                }
            }else if($(input[i]).attr('name') === 'qq'){
                var qq = $(input[i]).val();
                if(qq !== '' && qq.search(/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/) == -1){
                    //alert('请输入正确的qq/微信号码!');
                    //return false;
                }
            }
        }
        
        return true;
    });
 });