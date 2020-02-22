<?php
define("index","abcdefg.php");
$c = "constant";
if (substr($_SERVER['REQUEST_URI'],1,5) != "login") header("Location: /login/");
if (isset($_GET['acc'])) {
$userName = !empty($_POST['username'])?trim(htmlentities($_POST['username']), ENT_QUOTES):"";
$authCode = !empty($_POST['code'])?htmlentities($_POST['code'], ENT_QUOTES):"";

if ($need = file_exists("/home/{$userName}/.f2akey")) {
	$authKey = file_get_contents("/home/{$userName}/.f2akey");
}
$auths=file('/etc/twofactor');
switch($_GET['acc']) {
case "f2acode":
		require_once("/home/google/googleAuthenticator.php");
		$gauth = new GoogleAuthenticator();
		list(,$authInDB)=explode(':',array_pop($auth));
		if ($gauth->verifyCode(trim($authKey),$authCode) === true) {
	        	echo <<< EOL
$('#login').fadeOut('fast',function(){
	$('.logged-message-wrapper').fadeIn('fast');
});
$( "#formloginon" ).submit();
EOL;
		} else {
			echo <<< EOL
$("#btn-f2acode").removeClass('disabled');
$("#btn-f2acode").attr('disabled',false);
$("#btn-f2acode").html(msjbtn);
$("#f2acode").val("");
noti_bubble('incorrect access..!','','error',false,false,'3000',true);
EOL;
		}
exit;
  break;
case "f2aneed":
if ($need == 1){
echo <<< EOL
$("#btnsubmit").hide();
$('#formlogin').fadeOut('fast',function(){
	$('#form2fa').fadeIn('fast');
});
setTimeout( function() { document.getElementById("f2acode").focus()},800);

EOL;

	} else {
		echo <<< EOL
$('#login').fadeOut('fast',function(){
	$('.logged-message-wrapper').fadeIn('fast');
});
$( "#formloginon" ).submit();
EOL;
	}
exit;
break;
}
} 
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CWP | User</title>

    <link href="/login/cwp_theme/original/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/login/cwp_theme/original/font-awesome/css/fontawesome-all.css">
    <link href="/login/cwp_theme/original/css/plugins/iCheck/custom.css" rel="stylesheet">
    <!-- Toastr style -->
    <link href="/login/cwp_theme/original/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <!-- Gritter -->
    <link href="/login/cwp_theme/original/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="/login/cwp_theme/original/css/animate.css" rel="stylesheet">
    <link href="/login/cwp_theme/original/css/style.css" rel="stylesheet">
    <link rel="icon" href="/login/cwp_theme/original/img/ico/favicon.ico" type="image/png">
    <style media="screen">
      #btn_icon{
        display: none;
      }
      body, html{
        margin: 0px;
        padding: 0px;
      }
      .logged-message{
        /* display: flex;
        height: 100vh;
        justify-content: center;
        align-items: center;
        flex-direction: column; */
        display: flex;
        align-items: center;
      }
      .logged-message a{
        margin-bottom: 2rem;
      }
      .logged-message img{
        width: 35rem;
      }
      .logged-message-alert{
        border-radius: 5px;
        color: #676a6c;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
      }
      .logged-message-alert .fa{
        font-size: 6rem;
      }
      .logged-message-alert h5{
        font-size: 2rem;
      }
      .logged-message-alert h2{
        font-size: 2.5rem;
        font-weight: bold;
      }
      .logged-message-wrapper{
        display: none;
        max-width: 800px;
        margin: 0 auto;
        padding: 100px 20px 20px 20px;
      }
    </style>
</head>

<body class="gray-bg">
<noscript><h1>You must enable Javascript to login</h1></noscript>
<div class="loginColumns animated fadeInDown" id="login">
    <div class="row">
        <div class="col-md-6">
            <!--p><img src="/login/cwp_theme/design/img/new_logo_small.png"></p-->
            <div class="col-md-12 text-center" style="margin-top: -20px">
                <a href="https://www.control-webpanel.com" target="_blank"><img width="330px" src="/login/cwp_theme/original/img/new_logo_small.png"></a>
            </div>

            <p style="margin-top: 45px">
                Welcome to Webhosting control panel. Login to your account to manage your websites, files, databases, emails and many other services
            </p>

            <p>
                Domains, Emails and forwarding can all be configured here
            </p>

        </div>
        <div class="col-md-6">
            <div class="ibox-content" id="formlogin">
                <form class="m-t" role="form" action="#" id="formloginon" method="post">
                    <div class="form-group">
                        <input type="text" name="username" max="16" id="username" class="form-control" placeholder="Username" required="" maxlength="16" autofocus>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="">
                    </div>
                    <button type="submit"  id="btnsubmit"  class="btn btn-primary block full-width m-b" onclick="return valite()">
                      <i id="btn_icon" class="fa fa-spinner fa-spin"></i>
                      <span id="btn_title">Login</span>
                    </button>
                    <p class="text-muted text-center" style="display: none">
                        <i class="fa fa-lock"></i>  <small>Please use SSL login <a href="https://192.168.0.178:2083">Click here for SSL login</a></small>
                    </p>
                    <a class="btn btn-sm btn-white btn-block" href="#" onclick="return forgout(0)">Recover password</a>
                    <input type="hidden" id="token" name="token" value="">
                    <input type="hidden" id="intended" name="intended" value="">
                </form>

            </div>
            <div class="ibox-content" id="form2fa" style="display: none">

                    <h2 class="text-center">Two-factor authentication</h2>
                    <h3 class="text-center">Authentication code</h3>
                    <div class="row">
                        <div class="form-group">
                            <form><input type="text" autofocus name="
f2acode" max="6" id="f2acode" class="form-control" placeholder="******" required="" maxlength="6" style="text-align: center;font-size:25px">
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-primary block full-width m-b" id="btn-f2acode">Validate</button></form>
                    </div>
                    <p class="text-muted text-center"><small><a href="https://docs.control-webpanel.com/docs/user-guide/login/two-factor-authentication" target="_blank">Do you have problems with the authentication code?</a></small></p>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            <a href="https://www.control-webpanel.com" target="_blank">CWP Control WebPanel.</a>   All rights reserved
        </div>
        <div class="col-md-6 text-right">
            <small>© 2013 -  2020</small>
        </div>
    </div>
</div>

<div class="logged-message-wrapper">
  <div class="logged-message row">
    <div class="col-md-6" style="text-align: center;">
      <a href="https://www.control-webpanel.com" target="_blank"><img src="/login/cwp_theme/original/img/new_logo_small.png"></a>
    </div>
    <div class="col-md-6" style="border-left: 1px solid #ddd;">
      <div class="logged-message-alert">
        <i class="fa fa-spinner text-success fa-spin"></i>
          <h2 class="text-success">
              Successfully logged in
          </h2>
          <h5>You&#039;re being redirected</h5>
          <h5>Please Wait...</h5>
      </div>
    </div>
  </div>
</div>
<div class="middle-box animated fadeInDown" id="lost-pass" style="display: none">
    <div class="text-center">
        <a href="http://centos-webpanel.com/" target="_blank"><img src="/login/cwp_theme/original/img/cwp_logo.png" width="300"></a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="ibox-content">

                <h2 class="font-bold">Forgot password</h2>

                <p>
                    Enter your username and your email address and your new access will be sent to you by email.
                </p>

                <div class="row">

                    <div class="col-lg-12">
                        <form class="m-t" role="form" action="">
                            <div class="form-group">
                                <input type="text" class="form-control" maxlength="8" name="lost-user" id="lost-user" placeholder="Username" required="">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Email address" name="lost-email" id="lost-email" required="">
                            </div>

                            <button type="submit" class="btn btn-primary block full-width m-b" onclick="return lostpass()">Send new password</button>
                            <p></p>
                            <a class="btn btn-sm btn-white btn-block" href="#" onclick="return forgout(1)">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mainly scripts -->
<script src="/login/cwp_theme/original/js/jquery-3.1.1.min.js"></script>
<script src="/login/cwp_theme/original/js/popper.min.js"></script>
<script src="/login/cwp_theme/original/js/bootstrap.js"></script>
<!-- iCheck -->
<script src="/login/cwp_theme/original/js/plugins/iCheck/icheck.min.js"></script>
<script src="/login/cwp_theme/original/js/plugins/toastr/toastr.min.js"></script>
<script src="/login/cwp_theme/original/js/plugins/gritter/jquery.gritter.min.js"></script>

<script>
    $("#btn-f2acode").click(function (){
        var msjbtn =$("#btn-f2acode").text();
        $.ajaxSetup({ headers: { 'csrftoken' : 'fbbcfe5567cfce4080d774ce9b03ba64' } });
        $("#btn-f2acode").addClass('disabled');
        $("#btn-f2acode").attr('disabled',true);
        $("#btn-f2acode").html('<i class="fa fa-spinner fa-spin"></i>'+msjbtn);
        $.ajax({
            type: "POST",
            url: "index.php?acc=f2acode",
            data:"code="+$("#f2acode").val()+"&username="+$("#username").val(),
            complete: function(datos){
                eval(datos.responseText);
	    return false;
            }
        });
    });
    function noti_bubble(title,msj,type,bar,button,timer,repeat) {
        toastr.options = {
            closeButton: button,
            progressBar: bar,
            showMethod: 'slideDown',
            preventDuplicates:repeat,
            timeOut: timer
        };
        if(type =='success'){ toastr.success(title, msj); }
        if(type =='error'){ toastr.error(title, msj); }
        if(type =='info'){ toastr.info(title, msj); }
        if(type =='warning'){ toastr.warning(title, msj); }
    }
    function cookie() {
        $.ajaxSetup({ headers: { 'csrftoken' : 'fbbcfe5567cfce4080d774ce9b03ba64' } });
        $.ajax({
            type: "POST",
            url: "/login/<?php echo index;?>?acc=cookie",
            complete: function(datos){
                if(datos.responseText!=''){
                    //window.location = datos.responseText;
                }
            }
        });
    }
    function valite(){
        if($("#username").val()=='root'){
            noti_bubble('User root Invalid..!','','error',false,false,'3000',true);
            return false;
        }
        $.ajaxSetup({ headers: { 'csrftoken' : 'fbbcfe5567cfce4080d774ce9b03ba64' } });


        if(($("#username").val()=='')||($("#password").val()=='')){
            noti_bubble('All data is required','','error',false,false,'3000',true);
            // $("#btnsubmit").attr('disabled',false);
            // $("#btnsubmit").removeClass('disabled');
            // $("#btnsubmit").html('Login');
            return false;
        }else{
          $("#btnsubmit").prop('disabled',true);
          $("#btnsubmit").addClass('disabled');
          $('#btn_icon').css('display','inline-block');
          $("#btn_title").html('Please wait!');
            var pass=$("#password").val();
            var pass= Base64.encode(pass);
            var userN =$("#username").val();
            userN =userN.trim();
            $.ajax({
                type: "POST",
                url: "/login/<?php echo index;?>?acc=validate",
                data:"username="+userN+"&password="+pass,
                complete: function(datos){
                    var obj = JSON.parse(datos.responseText);
                    if(obj.error){
                        if(obj.error!='locked'){
                            noti_bubble(obj.error,'','error',false,false,'3000',true);
                            $("#btnsubmit").prop('disabled',false);
                            $("#btnsubmit").removeClass('disabled');
                            $('#btn_icon').css('display','none');
                            $("#btn_title").html('Login');
                        } else if(obj.error=='locked'){
                            noti_bubble('User locked','','error',false,false,'3000',true);
                            window.location = datos2.responseText;
                            }
                        return false;
                    }else if(obj.token){
  			$.ajax({
		        	type: "POST",
		        	url: "index.php?acc=f2aneed",
		        	data:"username="+$("#username").val(),
       				complete: function(datoss){
            				eval(datoss.responseText);
     			    
				
				}
//here
                      });

                        $("#token").val(obj.token);//
                        $("#password").val('');
                        $('#formloginon').attr("action", "/"+$("#username").val()+"/");
                        return  false;
                    }
                }
            });
        }
        return false;
    }
    function forgout(sw){
        if(sw==0){
            $("#login").hide();
            $("#lost-pass").show();
        }else  if(sw==1){
            $("#lost-pass").hide();
            $("#login").show();
        }
        return false;
    }
    function lostpass(){
        $.ajaxSetup({ headers: { 'csrftoken' : 'fbbcfe5567cfce4080d774ce9b03ba64' } });
        $("#error").hide('');
        if(($("#lost-user").val()!='')&&($("#lost-email").val()!='')){
            $.ajax({
                type: "POST",
                url: "/login/<?php echo index;?>?acc=lostpass",
                data:"username="+$("#lost-user").val()+"&email="+$("#lost-email").val(),
                complete: function(datos){
                    noti_bubble(datos.responseText,'','info',false,false,'3000',true);
                    return false;
                }
            });
            return false;
        }else{
            return false;
        }
    }
    var Base64 = {

        // private property
        _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        // public method for encoding
        encode : function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return output;
        },

        // public method for decoding
        decode : function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = Base64._utf8_decode(output);

            return output;

        },

        // private method for UTF-8 encoding
        _utf8_encode : function (string) {
            string = string.replace(/\r\n/g,"\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

        // private method for UTF-8 decoding
        _utf8_decode : function (utftext) {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while ( i < utftext.length ) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i+1);
                    c3 = utftext.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }

            return string;
        }

    }
    cookie();
</script>
</body>
</html>

<?php
?>
