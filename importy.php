<?php
session_start();
	if(!(isset($_SESSION["autoryzacja"]) && $_SESSION["autoryzacja"]))
		die("Brak autoryzacji!");

	include_once 'database.php';
	$conn = dbConnect();
	$res = mysqli_query($conn, "SELECT * FROM log_import");
	while($row = mysqli_fetch_row($res)) 
		printf("<a href=\"raport.php?imp=%d\">ID: %d Nazwa: %d Rekordy: %d Data: %s</a><br>", $row[0], $row[0], $row[1], $row[3], $row[2]);
	mysqli_close($conn);
?>
