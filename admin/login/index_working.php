<?php 
define("DO_LOGIN","");
$ct = "constant";
// Function for our template
function hd($param) {
    // just return whatever has been passed to us
    return $param;
}
$hd = "hd";
function fastlogin() {
global $ct;
$test = $ct('DO_LOGIN');
	header("Location: /login/$test" ,TRUE,307);
}
// Preset Variables / set to nothing if they don't exists (Prevent errors)
$fa2="";
$loginError="";
$userName = !empty($_POST['username'])?htmlentities($_POST['username'], ENT_QUOTES):"";
$userPasswd = !empty($_POST['password'])?htmlentities($_POST['password'], ENT_QUOTES):"";
$authCode = !empty($_POST['authCode'])?htmlentities($_POST['authCode'], ENT_QUOTES):"";
$commit = !empty($_POST['commit'])?htmlentities($_POST['commit'], ENT_QUOTES):"";
$passwdFile='/etc/shadow';
$users=file('/etc/shadow');

//Do the work ///
// Logout?  Redirect to old index page //
if ($_GET['logout'] == 'yes') header("Location: {$ct('DO_LOGIN')}?logout=yes");
//   Set our errors using switch //
switch($_GET['login']) {
 case "nousername":
	$loginError = "*Empty Username*";
	break;
 case "userlogin":
	$loginError = "This is admin area!!!<br>*<a href=\"https://".$_SERVER['SERVER_NAME'].":2083\">&gt; Login to NEW User Panel &lt;</a>*";
	break;
 case "nopassword":
	$loginError = "*Empty Password*";
	break;
 case "failed":
	$loginError = "*Login Failed*";
	break;
 case "logout":
	$loginError = "*You have logged out.*";
	break;
 case "invalidauth":
	$loginError = "*Invalid Auth Token*";
	break;
 default:
	$loginError = "";
}
//  Check our Post Data //
if (!empty($_POST)) {
//  No username, redirect //
if ($userName=="") {header("Location: index.php?login=nousername"); exit();}
// No Password Redirect //
if ($userPasswd==""){ header("Location: index.php?login=nopassword"); exit();}
// Check if username exists, if yes continue, otherwise show user login link //
if (!$user=preg_grep("/\b$userName\b/",$users)){header("Location: index.php?login=userlogin");}
// Check Password for User
list(,$passwdInDB)=explode(':',array_pop($user));
if (crypt($userPasswd,$passwdInDB) != $passwdInDB) {header("Location: index.php?login=failed");exit();}
//  Check Auth Code
if (file_exists("/root/.f2akey")) {
        $authKey = file_get_contents("/{$userName}/.f2akey");
        $fa2 = true;
} else {
	fastlogin();
	$fa2 = false;
}
require_once("design/googleAuthenticator.php");
$gauth = new GoogleAuthenticator();
if ($gauth->verifyCode(trim($authKey),$authCode) === true) {
	fastlogin();
	$fa2 = false;
} else {
	$loginError = ($authCode != "")?"*Invaid Auth* ":"";
}



}
echo <<< EOL
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Login | CentOS WebPanel</title>
  <link rel="icon" href="design/img/ico/favicon.ico" type="image/png">
  <link rel="stylesheet" href="design/img/login.css">
  <style>
  .hide {
  visibility:hidden;
  position:absolute;
  }
  .show {
  visibility:show;
  position:relative;
  }
  </style>
  <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--script src="design/js/jquery.min.js" type="text/javascript"></script><script src="design/js/login.js" type="text/javascript"></script-->
</head>
<body>
  <section class="container">
    <div class="login">
      <img src="design/images/cwp_small.png">
      <h1>Login to CentOS WebPanel
	  <br>{$loginError}</h1>
      <form method="post" action="{$hd(($fa2 === false)?"{$ct('DO_LOGIN')}":"?")}">
	<fieldset id="main" class="{$hd(($userName != "")?"hide":"show")}">
        <p><input type="text" name="username" value="{$userName}" placeholder="Admin Username" autofocus></p>
        <p><input type="password" name="password" value="{$userPasswd}" placeholder="Admin Password"></p>
        <p class="remember_me">
          <label>
            <input type="checkbox" name="fast_login" id="fast_login" {$hd(($_POST['fast_login'] == 'on')?"checked=\"checked\"":"")}>
            Fast Login (no stats and checks)
          </label>
        </p>
	</fieldset>
	<fieldset id="2fa" class="{$hd(($fa2 == true)?"show":"hide")}">
	<p><input type="text" name="authCode" value="" placeholder="Auth Code" autofocus autocomplete="off"></p>
	<input type="hidden" name="commit" value="Login">
	</fieldset>
        <p class="submit"><input id="mesubmit" type="submit" name="commit" value="Login"></p>
      </form>
    </div>

    <div class="login-help">
       <p>You are using SSL login.</p>	  
	 
    </div>
  </section>

  <section class="about">
    <p class="about-links">
      <a href="http://centos-webpanel.com" target="_parent">Visit Website</a>
      <a href="http://centos-webpanel.com/installation-instructions" target="_parent">How to Install</a>
    </p>
    <p class="about-author">
      &copy; 2020 <a href="http://centos-webpanel.com" target="_blank">CentOS WebPanel</a> 
	  control panel for linux
{$hd(($fa2 === false)?"<script>document.getElementById('mesubmit').style.visibility = \"hidden\"; document.forms[0].submit();</script>":"")}

</body>
</html>
EOL;
unset($_POST);
?>
