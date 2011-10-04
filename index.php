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

if(isset($_POST['submit'])) {

$path = $_REQUEST['path'];

$xml = simplexml_load_file("http://steamcommunity.com/id/."$path."/?xml=1");


echo $xml->getName() . "<br />";

foreach($xml->children() as $child)
  {
  echo $child->getName() . ": " . $child . "<br />";
  }

}

?>