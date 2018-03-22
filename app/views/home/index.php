<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>慕尚天街</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <?php echo css('main.css') ?>
</head>

<body>
    <div class="container">
        <div class="home">
            <div class="img"><img src="<?php echo image('p_01.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_02.jpg') ?>" alt=""></div>
            <div class="form">
                <div class="title">
                    <p>马上预定</p>
                </div>
                <div class="content">
                    <form id="form_contenct">
                        <div class="entry clear">
                            <label>姓名<span>*</span></label>
                            <input type="text" id="username" name="username" placeholder="">
                        </div>
                        <div class="entry clear">
                            <label>电话<span>*</span></label>
                            <input type="text" id="phone" name="phone" placeholder="">
                        </div>
                        <div class="entry clear">
                            <label>验证码<span>*</span></label>
                            <input type="text" id="code" name="code" placeholder="" class="code">
                            <a class="btn btn-small get-code" onclick="getCode(this,1)" id="J_getCode">获取验证码</a>
                            <a class="btn btn-small reset-code" id="J_resetCode" style="display:none;"><span id="J_second">60</span>秒后重发</a>
                        </div>
                        <input type="hidden" name="c" value="<?php echo $c ?>">
                    </form>
                    <a class="btn" href="javascript:;" id="submit_contect" onclick="submit_contect()">提交</a>
                </div>
            </div>
            <div class="img"><img src="<?php echo image('p_04.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_05.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_06.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_07.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_08.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_09.jpg') ?>" alt=""></div>
            <div class="img"><img src="<?php echo image('p_10.jpg') ?>" alt=""></div>
            <div class="form">
                <div class="title">
                    <p>马上预定</p>
                </div>
                <div class="content">
                    <form id="form_contenct_2">
                        <div class="entry clear">
                            <label>姓名<span>*</span></label>
                            <input type="text" id="username" name="username" placeholder="">
                        </div>
                        <div class="entry clear">
                            <label>电话<span>*</span></label>
                            <input type="text" id="phone" name="phone" placeholder="">
                        </div>
                        <div class="entry clear">
                            <label>验证码<span>*</span></label>
                            <input type="text" id="code" name="code" placeholder="" class="code">
                            <a class="btn btn-small get-code" onclick="getCode(this,2)" id="J_getCode">获取验证码</a>
                            <a class="btn btn-small reset-code" id="J_resetCode" style="display:none;"><span id="J_second">60</span>秒后重发</a>
                        </div>
                        <input type="hidden" name="c" value="<?php echo $c ?>">
                    </form>
                    <a class="btn" href="javascript:;" id="submit_contect" onclick="submit_contect_2()">提交</a>
                </div>
            </div>

            <div class="bottom">
                <div class="img"><img src="<?php echo image('p_11.jpg') ?>" alt=""></div>
                <div class="content">
                    <p><a href="tel:400-772-0111">咨询热线：400 - 772 - 0111</a></p>
                    <p>深圳市罗湖区人民北路永通大厦15楼</p>
                </div>
            </div>
            <footer>
                <p>深圳市慕尚天街文化发展有限公司</p>
                <p>京ICP备17006876号-3</p>
            </footer>
        </div>
    </div>
    <?php echo js('jquery.min.js') ?>
	<script>
		function submit_contect() {
            $.ajax({
                url: '<?php echo base_url('index/submitContect') ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form_contenct').serialize(),
                success:function(data) {
                    if(data.status == 200) {
                        alert('申请成功');
                        location.reload();
                    }else {
                        alert(data.msg);
                    }
                }
            })
		}
        function submit_contect_2() {
            $.ajax({
                url: '<?php echo base_url('index/submitContect') ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form_contenct_2').serialize(),
                success:function(data) {
                    if(data.status == 200) {
                        alert('申请成功');
                        location.reload();
                    }else {
                        alert(data.msg);
                    }
                }
            })
        }

        /*获取验证码*/
        var isPhone = 1;
        function getCode(e,i){
            checkPhone(i); //验证手机号码
            if(isPhone){
                if(i == 1){
                    var phone = $('#form_contenct #phone').val();                
                }else{
                    var phone = $('#form_contenct_2 #phone').val(); 
                }
                $.post('<?php echo base_url('index/sendSms') ?>', {phone: phone}, function(data) {
                    alert(data.msg);
                    if (data.status == 200) {
                        resetCode(i); //倒计时
                    }
                }, 'JSON');
            }else{
                if(i == 1){
                    $('#form_contenct #phone').focus();                
                }else{
                    $('#form_contenct_2 #phone').focus(); 
                }
            }
        }

        //验证手机号码
        function checkPhone(i){
            if(i == 1){
                var phone = $('#form_contenct #phone').val();                
            }else{
                var phone = $('#form_contenct_2 #phone').val(); 
            }
            var pattern = /^1[0-9]{10}$/;
            isPhone = 1;
            if(phone == '') {
                alert('请输入手机号码');
                isPhone = 0;
                return;
            }
            if(!pattern.test(phone)){
                alert('请输入正确的手机号码');
                isPhone = 0;
                return;
            }
        }
        //倒计时
        function resetCode(i){
            var flag;
            if(i == 1){
                flag = '#form_contenct ';          
            }else{
                flag = '#form_contenct_2 ';  
            }
            $(flag + '#J_getCode').hide();
            $(flag + '#J_second').html('60');
            $(flag + '#J_resetCode').show();
            var second = 60;
            var timer = null;
            timer = setInterval(function(){
                second -= 1;
                if(second >0 ){
                    $(flag + '#J_second').html(second);
                }else{
                    clearInterval(timer);
                    $(flag + '#J_getCode').show();
                    $(flag + '#J_resetCode').hide();
                }
            },1000);
        }
    </script>
</body>
</html>