<?php session_start() ?>

<?php
	$src = <<<EOD
		<form enctype="multipart/form-data" action="import.php" method="POST">
		<input name="plikcsv" type="file" accept=".csv"/>
		<input type="submit"/>
		</form>
	EOD;
	if(isset($_SESSION["autoryzacja"]) && $_SESSION["autoryzacja"])
		echo $src;
	else
		echo "Brak autoryzacji!";

	if(isset($_FILES["plikcsv"])) {
		include_once 'vcsv.php';	
		$plik = $_FILES["plikcsv"]["tmp_name"];
		$tups = csv_validate($plik, array(1, 1, 0, 0, 1), 1);
		if(gettype($tups) == "string")
			printf("%s", $tups);
		else {
			include_once 'database.php';	
			$conn = dbConnect();
			$rownum = count($tups);

			mysqli_begin_transaction($conn);

			$query = sprintf("INSERT INTO log_import(Plik, Rekordy, Data) VALUES ('%s', %d, NOW())",
				$_FILES["plikcsv"]["name"], $rownum);
			mysqli_query($conn, $query);
			
			$impnum = mysqli_insert_id($conn);
			
			$query = "INSERT INTO polaczenia(ID, NRTelefonu, Nr, Status, Data, Import) VALUES ";
			foreach($tups as $row){
				$tqr = sprintf("SELECT * FROM klienci WHERE NRTelefonu = %d AND Imie = %d AND Nazwisko = %d", 
					$row[1], $row[2], $row[3]);
				$result = mysqli_query($conn, $tqr);
				$cnum = 0;		
				if(mysqli_num_rows($result) > 0) {
					$trow = mysqli_fetch_assoc($result);
					$query .= sprintf("(%d, %d, %d, %d, NOW(), %d),", $row[0], $row[1], $trow["ID"], $row[4], $impnum);
					$cnum++;
				} else printf("Pominieto polaczenie z ID %d, poniewaz nie mozna znalezc klienta<br>", $row[0]);
			}
			$query[-1] = ';';
			echo $query;
			if($cnum == 0){
				mysqli_rollback($conn);
				goto cancel;
			}

			if(!mysqli_query($conn, $query)) {
				echo mysqli_error($conn);
				mysqli_rollback($conn);
				goto cancel;
			}
			
			mysqli_commit($conn);
			cancel:
			mysqli_close($conn);
		}
	}
?>
