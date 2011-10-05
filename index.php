<html>
<body>
<form method="post" action="">
<table border="0">
<tr><td> Steam Community Path:</td><td> <input type="text" name="path" /></td></tr>
<tr><td> <input type="submit" name="submit" value="Submit" /></td><td><input type="reset" value="Clear" /></td></tr>
</table>
</form>
</body>
</html>

<?php

function strip_cdata($string) 
{ 
    preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches); 
    return str_replace($matches[0], $matches[1], $string); 
}

if(isset($_POST['submit'])) {

$apiKey = "4025BCF7889FDAE9DC651ECE0EC4022E";

$path = $_REQUEST['path'];

$xml = simplexml_load_file("http://steamcommunity.com/id/".$path."/?xml=1");

  $steamID = $xml->steamID64;
  
  $xmldeep = simplexml_load_file("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".$apiKey."&steamids=".$steamID."&format=xml");
  
  $dbh = new PDO("mysql:host=mysql.kylepeatt.com;dbname=progressbar", "kpeatt", "fux0r123");
  
  echo "Connected to database.<br>";
  
  $stmt = $dbh->prepare("SELECT `steam_id` FROM `users` WHERE `steam_id` = :steamID");
  $stmt->bindValue(":steamID", $steamID);
  $stmt->execute();
  
	print_r($stmt->rowCount());

  
  if($stmt->rowCount() == 0) {
    	
  	echo "<br><br>This is a new user! <br><br>";
  	
  	$steam_name = strip_cdata($xml->steamID);
  	$steam_realname = $xmldeep->players->player->realname;
  	$steam_avatar = strip_cdata($xml->avatarIcon);
  	$steam_avatar_med = strip_cdata($xml->avatarMedium);
  	$steam_avatar_full = strip_cdata($xml->avatarFull);
  	$steam_customURL = strip_cdata($xml->customURL);
  	$steam_membersince = $xmldeep->players->player->timecreated;
  	$steam_lastlogoff = $xmldeep->players->player->lastlogoff;
  	$steam_loc_country = $xmldeep->players->player->loccountrycode;
  	$steam_loc_state = $xmldeep->players->player->locstatecode;
  	$steam_loc_cityid = $xmldeep->players->player->loccityid;
  	
  	echo "Name: ".$steam_name." <br>";
  	echo "Steam ID: ".$steamID." <br>";
  	echo "Real Name: ".$steam_realname." <br>";
  	echo "Last Log Off: ".$steam_lastlogoff." <br><br>";
  	
  	try {
  	
  	$qry = "INSERT INTO `users` (`name`,`steam_id`,`steam_name`,`steam_realname`,`steam_customurl`,`steam_avatar`,`steam_avatar_med`,`steam_avatar_full`,`steam_loc_country`, `steam_loc_state`,`steam_loc_cityid`,`steam_lastlogoff`,`steam_membersince`) VALUES (:name,:steam_id,:steam_name,:steam_realname,:steam_customurl,:steam_avatar,:steam_avatar_med,:steam_avatar_full,:steam_loc_country, :steam_loc_state,:steam_loc_cityid,:steam_lastlogoff,:steam_membersince)";
  	
  	$sql = $dbh->prepare($qry);
  	
  	$sql->bindValue(':name', $steam_name);
  	$sql->bindValue(':steam_id', $steamID);
  	$sql->bindValue(':steam_name', $steam_name);
  	$sql->bindValue(':steam_realname', $steam_realname);
  	$sql->bindValue(':steam_customurl', $steam_customURL);
  	$sql->bindValue(':steam_avatar', $steam_avatar);
  	$sql->bindValue(':steam_avatar_med', $steam_avatar_med);
  	$sql->bindValue(':steam_avatar_full', $steam_avatar_full);
  	$sql->bindValue(':steam_loc_country', $steam_loc_country);
  	$sql->bindValue(':steam_loc_state', $steam_loc_state);
  	$sql->bindValue(':steam_loc_cityid', $steam_loc_cityid);
  	$sql->bindValue(':steam_lastlogoff', $steam_lastlogoff);
  	$sql->bindValue(':steam_membersince', $steam_membersince);
  	
	$sql->execute();
	
	echo "Inserted into DB!<br>";
	
	print_r($sql);
	
	$dhb = null;
	
	}
	catch(PDOException $e)    {
	    echo $e->getMessage();
    }
	
  	
  } else {
  	echo "Existing user!";
  }

}

?>