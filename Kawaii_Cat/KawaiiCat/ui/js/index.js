$(function(){
    $(".l-sort1 ul li").click(function(){
	    $(this).addClass("l_select").siblings().removeClass("l_select");
		var index = $(this).index();
      
		$("#contentop ul").eq(index).css("display","block").siblings().css("display","none");
	});
});