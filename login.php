<?php session_start() ?>

	<html>
		<form action="login.php" method="POST">
			Login: <input type="text" name="login">
			Haslo: <input type="text" name="haslo">
			<input type="submit">
		</form>

<?php
		if(isset($_SESSION["autoryzacja"]) && $_SESSION["autoryzacja"])
			echo "zalogowany<br>";
		if(isset($_POST["login"]) && isset($_POST["haslo"])) {
		include_once 'database.php';
		$conn = dbConnect();
		$haslo = hash("sha256", $_POST["haslo"]);
		$query = sprintf("SELECT * FROM konta WHERE login = '%s' AND haslo = '%s'", $_POST["login"], $haslo);
		if(mysqli_num_rows(mysqli_query($conn, $query)) > 0) {
			$_SESSION["autoryzacja"] = true;
			echo "Pomyslnie zalogowano!";
		}
		mysqli_close($conn);
	}
?>
</html>
