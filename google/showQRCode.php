<?php

/**
 * TwoFactorAuth 
 * This script can either be called directly or included in another script
 * 
 * 1. If it's called directly, it is supposed to be passed a uniq id corresponding to
 * a temporary QRCode image. It will return a PNG image.
 * 
 * 2. If it's included from another script, it will prepare the display for the QRCode image
 *    and use the $randomString variable prepared in the calling script
 * 
 * @author Arno0x0x - https://twitter.com/Arno0x0x
 * @license GPLv3 - licence available here: http://www.gnu.org/copyleft/gpl.html
 * @link https://github.com/Arno0x/
 */
 
//------------------------------------------------------
// Include config file
//require_once("../config.php");

    //------------------------------------------------------
    // Application base url
    $baseUrl = "";//dirname(dirname($PHP_SELF));
    echo <<<OUT
    <a href="#"><span onclick="$('#overlay').fadeOut()" class="fa fa-close pull-right"></span></a>
    <br>
    Scan the following QR Code with your Google Authenticator app:<br>
    <img src="{$_SERVER['REQUEST_URI']}&imgid={$randomString}">
OUT;
?>
