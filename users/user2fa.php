<?php
if (isset($_GET["ids"])) {
    //-----------------------------------------------------
    // Check "id" to avoid path traversal type of attack
    if (preg_match("/(\.|\/)/",$_GET["ids"]) === 0) {

        //-----------------------------------------------------
        // Sending no-cache headers
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        header( 'Content-Type: image/png' );


        $imgFileName = "/tmp/".$_GET["ids"].".png";
        $image = imagecreatefrompng ($imgFileName);
        ImagePng($image);

        imagedestroy($image);

        // Delete the temporary image file
        unlink($imgFileName);
    } else {
        echo "What you think you're doing ??";
    }
exit;
}

define('QRCODE_TITLE',$_SERVER['SERVER_NAME']);
$userName = $_SESSION['username'];
$mod['userName'] = $userName;

if (file_exists("/home/{$userName}/.f2akey")) {
        $authKey = file_get_contents("/home/{$userName}/.f2akey");
	$mod['onoff'] = "On";
	$notset = false;
	$file = "/home/{$userName}/.f2akey";
} elseif(file_exists("/home/{$userName}/.f2akeyoff")) {
	$authKey = file_get_contents("/home/{$userName}/.f2akeyoff");
	$mod['onoff'] = "Off";
	$notset = false;
	$file = "/home/{$userName}/.f2akeyoff";
} else {
	$notset = true;
	$file = "/home/{$userName}/.f2akey";
	$mod['onoff'] = "On";
}

function updateGauthSecret($user, $secret) {
        global $file;
        $input = file_put_contents($file,$secret);
        if ($input == 0) return true;
}

if(isset($_POST['action'])) {
switch($_POST['action']) {
case "toggleAuthOn":
	rename($file,rtrim($file,"off"));
        $mod['onoff'] = "On";
        break;
case "toggleAuthOff":
	rename($file,$file."off");
        $mod['onoff'] = "Off";
        break;
case "showQRCode":
        if ($userName) {
                require_once("/home/google/googleAuthenticator.php");
                                // Create GoogleAuth object
                        $gauth = new GoogleAuthenticator();

                        if (($secret = $authKey)) {
                                // Create the QRCode as PNG image
                                $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                                $qrcodeimg = "/tmp/".$randomString.".png";
                                $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
				$overlay = true;
                }
        }
break;

        // Renew the GAuth secret  for selected user and show the corresponding QRCode
case "createGAuthSecret":
        if ($userName) {

                 require_once("/home/google/googleAuthenticator.php");
                                // Create GoogleAuth object
                    $gauth = new GoogleAuthenticator();
                    $secret = $gauth->createSecret();
                if (file_put_contents($file, $secret)) {
                                // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = "/tmp/".$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
			$overlay = true;
                                }
                                }
                                $notset = false;
				$mod['onoff'] = "On";
                            break;
case "renewGAuthSecret":
        if ($userName) {
                 require_once("/home/google/googleAuthenticator.php");
                                // Create GoogleAuth object
                    $gauth = new GoogleAuthenticator();
                    $secret = $gauth->createSecret();

                if ((updateGauthSecret($userName,$secret))) {
                                // Create the QRCode as PNG image
                        $randomString = bin2hex(openssl_random_pseudo_bytes (15));
                        $qrcodeimg = "/tmp/".$randomString.".png";
                        $gauth->getQRCode($userName,$secret,$qrcodeimg,QRCODE_TITLE);
			$overlay = true;
                       // $mod['onoff'] = "On";
                            break;
                                }
                                }

}
}
$mod['oo'] = ($mod['onoff'] == "On")?"Off":"On";
$mod['notset'] = $notset;
if (isset($overlay)) {
        echo "<div id=\"overlay\" class=\"blackOut\"><span class=\"boxWrapper\"><div class=\"box\">";
	//$_GET['module'] = "user2fa";
	//$_GET['showQR'] = 1;
        //include_once($overlay);
echo <<<OUT
    <a href="#"><span onclick="$('#overlay').fadeOut()" class="fa fa-close pull-right"></span></a>
    <br>
    Scan the following QR Code with your Google Authenticator app:<br>
    <img src="{$_SERVER['REQUEST_URI']}&ids={$randomString}">
OUT;

        echo"</div></span></div>";
}
?>
