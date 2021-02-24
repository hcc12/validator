<?php
function dbConnect() {
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "praktyki1";
	$con = mysqli_connect($host, $user, $pass, $db);
	if(!$con) die("Nie można połączyć z bazą danych!");
	return $con;
}
?>
