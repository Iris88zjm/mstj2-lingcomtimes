(function($){
	$(function(){
		 
       

        $('body').on('click', '.modify', function(){
            $('.popup').addClass('active');
            return false;
        });
        $('body').on('click', '.add', function(){
            $('.popup').addClass('active');
            return false;
        });
        $('body').on('click', '.popup .close', function(){
            $('.popup').removeClass('active');
            return false;
        });
        $('body').on('click', '.popup .cancle', function(){
            $('.popup').removeClass('active');
            return false;
        });

        $("#chkall").click(function(){//给全选按钮加上点击事件
            var xz = $(this).prop("checked");//判断全选按钮的选中状态
            var ck = $(".chk").prop("checked",xz);  //让class名为chk的选项的选中状态和全选按钮的选中状态一致。  
        });

        var str = window.location.pathname;
        var positions = new Array();
        var pos = str.lastIndexOf("/");
        var ret = str.substring(pos+1, str.length);
        console.log(ret);
        if(ret){
            $('#' + ret).addClass('active');
        }


        // 手机端 菜单
        $('body').on('click', '.nav-toggle .open', function(){
            $('.nav-toggle .close').addClass('active');
            $(this).removeClass('active');
            $('.menu').addClass('active');
            return false;
        });
        $('body').on('click', '.nav-toggle .close', function(){
            $('.nav-toggle .open').addClass('active');
            $(this).removeClass('active');
            $('.menu').removeClass('active');            
            return false;
        });
    });
})(jQuery);