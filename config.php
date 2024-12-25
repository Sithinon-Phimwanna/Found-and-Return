<?php
//ติดต่อฐานข้อมูล
 $host="localhost";
$username="6412231023";
$password="P@ss1023";
$database="6412231023_Lostitem";
$mysqli = new mysqli ( $host, $username, $password, $database );
if ($mysqli->connect_errno) {
	echo $mysqli->connect_error;
	exit;
}
$mysqli->set_charset('utf8');
?>
	
	