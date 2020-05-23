<?php
class gitupdate {
	public $url;
	public $script;
	public function __construct($user,$script) {
		global $mysql_conn;
		$this->url =  "https://api.github.com/repos/$user/$script/commits";
		$this->script = $script;
		if( mysqli_num_rows(mysqli_query($mysql_conn,"SHOW TABLES LIKE '{$this->script}_settings' ")) == 0 ){		
			$mi_table3= "CREATE TABLE {$this->script}_settings(
			varname VARCHAR(65) NOT NULL UNIQUE,
			varval VARCHAR(65) NOT NULL,
			PRIMARY KEY(`varname`)
			)ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			mysqli_query($mysql_conn, $mi_table3);
		}
	}
	private function checkgit() {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->script);
		//curl_setopt($curl, CONNECTTIMEOUT, 1);
		$content = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($content,true);
		if (isset($data[0]['sha'])) {
			return $data[0]['sha'];
		} else {
			var_dump($data);
			return false;
		}
	}
    public function checkupdate($force = "N") {
		global $mysql_conn;
		// need to check date last checked (varname, varval) $this->script_settings
		$resp = mysqli_query($mysql_conn,"select varval from {$this->script}_settings where varname='lastcheck'");
		$resp2 = mysqli_query($mysql_conn,"select varval from {$this->script}_settings where varname='sha'");
		if (mysqli_num_rows($resp) > 0 && mysqli_num_rows($resp2) > 0) {
			list($lastcheck) = mysqli_fetch_row($resp);
			list($sha) = mysqli_fetch_row($resp2);
			if ($force != "N") {
				$newsha = $this->checkgit();
				if ($newsha === false) return false;
				$date = date("Y-m-d H:i:s");
				mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('sha','$newsha') on duplicate key update varval='$newsha'");
				mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('lastcheck','$date') on duplicate key update varval='$date'");
				if ($sha != $newsha) {
					echo "<div style='position:absolute; top:80px;' class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>";
					echo "<h3>A New Version is available</h3><p>Please follow the directions:<br><code>cd /usr/local/src/$this->script<br>git update && ./install</code>";
					echo "</p></div>";
				}
				return true;
			}
			$start_date = new DateTime($lastcheck);
			$since_start = $start_date->diff(new DateTime(date("Y-m-d H:i:s")));
			if ($since_start->d >= 1) {
				$newsha = $this->checkgit();
				if ($newsha === false) return false;
				$date = date("Y-m-d H:i:s");
				mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('sha','$newsha') on duplicate key update varval='$newsha'") or die(mysqli_error($mysql_conn));
				mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('lastcheck','$date') on duplicate key update varval='$date'") or die(mysqli_error($mysql_conn));;
				if ($sha != $newsha) {
					echo "<div style='position:absolute; top:80px;' class='alert alert-info'><button type='button' class='close' data-dismiss='alert'>×</button>";
					echo "<h3>A New Version is available</h3><p>Please do the following:<br><code>cd /usr/local/src/$script<br>git update && ./install</code>";
					echo "</p></div>";
				}
			}
			/*echo $since_start->days.' days total<br>';
			echo $since_start->y.' years<br>';
			echo $since_start->m.' months<br>';
			echo $since_start->d.' days<br>';
			echo $since_start->h.' hours<br>';
			echo $since_start->i.' minutes<br>';
			echo $since_start->s.' seconds<br>';
			echo "LC: " . $lastcheck . "<br>";
			echo "Sha: " .$sha;*/
			
		} 
		else {
			// No Response or table not created.  We do the first check and create it.
			$sha = $this->checkgit();
			if ($sha === false) return false;
			$date = date("Y-m-d H:i:s");
			mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('sha','$sha') on duplicate key update varval='$sha'");
			mysqli_query($mysql_conn,"insert into {$this->script}_settings (varname, varval) values ('lastcheck','$date') on duplicate key update varval='$date'");
		}
		/**/
	}
}
/*To Call
include_once "gitupdate.php";
$update = new gitupdate('rcschaff82','cwp_2fa');
$force = (isset($_GET['forceupdate']))?'Y':'N';
$update->checkupdate($force);
*/
?>
