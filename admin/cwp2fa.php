<style>
.blackOut {
    position: absolute;
    top:0px;
    left:0px;
    z-index: 5;
    width:100%;
    height:100%;
    background-color: rgba(0,0,0,.8);
    text-align:center;
									 
											


												   
												   
						 

							 

										  
							 
			
											  
	 
	 
}
span.boxWrapper{
        border: solid;
        border-width: 1px;
        border-radius: 3px;
        padding: 10px;
        background-color: #ffffff;
        display:block;
    width:640px;
    height:480px;
        margin:0 auto;
     z-index:8;
}
.box {
    margin-left:auto;
    margin-right:auto;
    vertical-align:center;
    z-index:8;
}
</style>
<?php
define('QRCODE_TITLE',$_SERVER['SERVER_NAME']);
$userName = $_SESSION['username'];

if (file_exists("/{$userName}/.f2akey")) {
        $authKey = file_get_contents("/{$userName}/.f2akey");
        $onoff = "On";
        $notset = false;
        $file = "/{$userName}/.f2akey";
} elseif(file_exists("/{$userName}/.f2akeyoff")) {
        $authKey = file_get_contents("/{$userName}/.f2akeyoff");
        $onoff = "Off";
        $notset = false;
        $file = "/{$userName}/.f2akeyoff";
} else {
        $notset = true;
        $file = "/{$userName}/.f2akey";
}

function updateGauthSecret($user, $secret) {
        global $file;
	$input = file_put_contents($file,$secret);								 
        if ($input == 0) return true;
}

if(isset($_POST['action'])) {


switch($_POST['action']) {
case "toggleAuthOff":
	rename($file,rtrim($file,"off"));
        $onoff = "On";
        break;
case "toggleAuthOn":
        rename($file,$file."off");
        $onoff = "Off";
        break;
case "showQRCode":
        if ($userName) {
                require_once("design/googleAuthenticator.php");
                                // Create GoogleAuth object
                        $gauth = new GoogleAuthenticator();

                        if (($secret = $authKey)) {
                                // Create the QRCode as PNG image
                                $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                                $qrcodeimg = "/tmp/".$randomString.".png";
                                $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);

                                $overlay = "design/showQRCode.php";

                }
        }
break;

        // Renew the GAuth secret  for selected user and show the corresponding QRCode
case "createGAuthSecret":
        if ($userName) {

                 require_once("design/googleAuthenticator.php");
                                // Create GoogleAuth object
                    $gauth = new GoogleAuthenticator();
                    $secret = $gauth->createSecret();
                if (file_put_contents($file, $secret)) {
                                // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = "/tmp/".$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);

                        $overlay = "design/showQRCode.php";
                                }
                                }
                                $notset = false;
								$onoff = "On";
                            break;
case "renewGAuthSecret":
        if ($userName) {
                 require_once("design/googleAuthenticator.php");
                                // Create GoogleAuth object
                    $gauth = new GoogleAuthenticator();
                    $secret = $gauth->createSecret();

                if ((updateGauthSecret($userName,$secret))) {
                                // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = "/tmp/".$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);

                        $overlay = "design/showQRCode.php";
                                
                                
                        $onoff = "On";
                            break;
}
}
}
}
?>
<form id="userAction" action="index.php?module=cwp2fa" method="post">
<input type="hidden" name="username" value="root">
<?php if($notset != true) { ?>
<button type="submit" name="action" value="showQRCode" class="btn btn-primary"><span style="color:white" class="fa fa-qrcode"></span> Show QR code</button>
<button type="submit" name="action" value="toggleAuth<?php echo $onoff; ?>" class="btn btn-primary"><span style="color:white" class="fa fa-lock"></span> Use Auth [<?php echo $onoff;?>]</button>
<button onclick="return confirmGAScrt('cards');" type="submit" name="action" value="renewGAuthSecret" class="btn btn-primary "><span style="color:white" class="fa fa-refresh"></span> <span style="color:white" class="fa fa-key"></span> Renew Secret</button>
<?php } else { ?>
<button type="submit" name="action" value="createGAuthSecret" class="btn btn-primary "><span style="color:white" class="fa fa-refresh"></span> <span style="color:white" class="fa fa-key"></span>Create Secret</button>
<?php } ?>
</form>

<script>
function confirmDelete(username) {
        return (confirm("You're about to delete user "+username+".\nAre you SURE ?"));
}

function confirmGAScrt(username) {
        return (confirm("You're about to renew the secret for user "+username+".\nAre you SURE ?"));
}

$(document).ready(function(){

    $("#deleteDatabase").submit(function(event) {
        if(!confirm("DELETE ALL USERS FROM DATABASE - ARE YOU SURE ?")) {
                event.preventDefault();
        }
    });
});
</script>
<?php
if (isset($overlay)) {
        echo "<div id=\"overlay\" class=\"blackOut\"><span class=\"boxWrapper\"><div class=\"box\">";
        require_once($overlay);
																								  
		
																	  
															 
	

        echo"</div></span></div>";
}
include_once "gitupdate.php";
$update = new gitupdate('rcschaff82','cwp_2fa');
$force = (isset($_GET['forceupdate']))?'Y':'N';
$update->checkupdate($force);
?>

<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>

