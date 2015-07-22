<?
error_reporting(0);

//Set the default timezone to NY
date_default_timezone_set("America/New_York");

$hostname = "bmnleasing.db.6174036.hostedresource.com";
$username = "bmnleasing";
$dbname = "bmnleasing";
$password = "BrendanIsADingus15!";

  //Old style non-PDO DB connection
mysql_connect($hostname, $username, $password) OR DIE ("Unable to connect to database! Please try again later.");
mysql_select_db($dbname);

  //New style PDO DB connection
try {
  $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
  echo $e->getMessage();
}
?>