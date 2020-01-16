

	$(function () {
			var tabContainers = $('div.tabs > div');
			tabContainers.hide().filter(':first').show();
			
			$('div.tabs ul.tabNavigation a').click(function () {
				tabContainers.hide();
				tabContainers.filter(this.hash).show();
				$('div.tabs ul.tabNavigation a').removeClass('selected');
				$(this).addClass('selected');
				return false;
			}).filter(':first').click();
			
			
			
			
		});
		
		


// $(document).ready(function(){
//        $(".tab li").click(function(){
//        $(".tab li").eq($(this).index()).addClass("cur").siblings().removeClass('cur');
//$(".tabb").hide().eq($(this).index()).show();
//       //另一种方法: $("div").eq($(".tab li").index(this)).addClass("on").siblings().removeClass('on'); 
//
//        });
//    });