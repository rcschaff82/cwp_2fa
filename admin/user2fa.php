<h1>Manage Users 2 Factor Authentication</h1>
<h3>You can only turn it on or off.</h3>

<?php
//use API to get list of users
$data = array("key" => "API_KEY","action"=>'list');
$url = "https://{$_SERVER['SERVER_NAME']}:2304/v1/account";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt ($ch, CURLOPT_POST, 1);
$response = curl_exec($ch);
curl_close($ch);
$response = json_decode($response);
$users = array();
foreach ($response->msj as $key) {
           $users[] = $key->username  ."\n";
       }
?>
<table class="table table-striped table-bordered table-condensed table-hover" style="width:300px">
<thead>
                  <tr>
                    <th class="text-center">USERNAME</th>
                    <th class="text-center">2FA Status</th>
                  </tr>
                </thead>
                <tbody>

<?php
// Illiterate through users, and check to see if they have a key file, or a disabled key file
foreach ( $users as $user) {
	$user = trim($user);
	if (file_exists("/home/{$user}/.f2akey")) {
        	$active = "On";
        	$notset = false;
       		$file = "/home/{$user}/.f2akey";
	} elseif(file_exists("/home/{$user}/.f2akeyoff")) {
        	$active = "Off";
        	$notset = false;
        	$file = "/home/{$user}/.f2akeyoff";	
	} else {
		$active = "Not Set";
        	$notset = true;
        	$file = "/home/{$user}/.f2akey";
	}
	$userpost[$user] = array($active, $notset, $file);
}
// Button Click functions to turn 2FA on or off for them
if(isset($_POST['user'])) {
	if ($_POST['onoff'] === "Off") {  // Turn On
		rename($userpost[$_POST['user']][2],rtrim($userpost[$_POST['user']][2],"off"));
	}
	if ($_POST['onoff'] === "On") {  // Turn Off
		 rename($userpost[$_POST['user']][2],$userpost[$_POST['user']][2]."off");
	}
	$userpost[$_POST['user']][0] = ($_POST['onoff'] === 'Off')?'On':'Off';   //Set appropriate button text so we don't have to reload
	unset($_POST);
}
foreach ($userpost as $user=>$var) {  // var[0] = active; var[1] = notset var[2] = $file
$disabled = ($var[1] == true)?"disabled=disabled":"";  // Disable the button if the didn't set up 2fa
$active = $var[0];
echo "<tr><td>".$user . "</td><td><form method='post'><input type=hidden name=onoff value={$active}><input type=hidden name=user value={$user}><button $disabled class='btn btn-primary' type='submit' name='submit' value='{$active}'><span style='color:white' class='fa fa-lock'></span>{$active}</button></form><br></td></tr>\n";
}
?>
</tbody></table>
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
