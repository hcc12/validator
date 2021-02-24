<?php
session_start();
	if(!(isset($_SESSION["autoryzacja"]) && $_SESSION["autoryzacja"]))
		die("Brak autoryzacji!");
	
	function chDate($data) {
		$num = 1;
		foreach(str_split($data) as $c)
			if($c == '-')
				$num++;
		return $num;
	}

	function datePart($date, $part){
			$sIndex = 0;
			$endIndex = 0;
			$num = 0;
			foreach(str_split($date) as $k=>$c) {
					if($c == '-') {
							$num++;
							if($part == $num) {
									$endIndex = $k;
									return substr($date, $sIndex, $endIndex-$sIndex);
							}
							if($part == $num+1)
									$sIndex = $k+1;
					}
			}
			return substr($date, $sIndex);
	}

	function zrobWarunek() {
		$arr = func_get_args();
		$i = 0;
		$str = "";
		$datearr = array("YEAR", "MONTH", "DAY");
		foreach($arr as $k => $v) {
			if(!$v[1]) continue;
			if($i) $str .= " AND ";
			if($k == 0) {
				for($x = 1; $x <= chDate($v[1]); $x++) {
					if($i) $str .= " AND ";
					$str .= $datearr[$x-1] . '(p.' . $v[0] . ')' . " = " . datePart($v[1], $x);
					$i++;
				}
			} else
				$str .= "p." . $v[0] . " = " . $v[1];
			$i++;
		}
		return $str;
	}

	$src = <<<EOD
	<form action="raport.php" method="GET">
		Dzien:<input type="text" name="dm" size="2">
		Miesiac:<input type="month" name="ym">
		Ze statusem:<input type="text" name="sta" size="1">
		<a href="importy.php">Z importu</a>
		<input type="submit">
	</form>
	<br>
	EOD;
	echo $src;
	$date = false;
	$status = false;
	$import = false;

	if(isset($_GET["ym"]) && $_GET["ym"])
		$date = $_GET["ym"];
	if(isset($_GET["dm"]) && $_GET["dm"])
		if($date)
			$date .= '-' . $_GET["dm"];
	if(isset($_GET["sta"]) && $_GET["sta"])
		$status = $_GET["sta"];
	if(isset($_GET["imp"]))
		$import = $_GET["imp"];
	
	$warunek = zrobWarunek(array("Data", $date), array("Status", $status), array("import", $import));
	if($warunek === '') die;
	
	include_once 'database.php';
	$conn = dbConnect();

	$query = "SELECT * FROM polaczenia AS p INNER JOIN klienci AS k ON p.Nr=k.ID WHERE ";
	$query .= $warunek . ';';
	echo $query."<br><br>";
	$result = mysqli_query($conn, $query);
	echo "<table border=\"1\" style=\"width:100%;text-align:center;\">";
	echo "<tr><th>ID</th><th>Telefon</th><th>Imie</th><th>Nazwisko</th><th>Status</th><th>Data</th><th>Import</th>";
	while($row = mysqli_fetch_row($result)) {
		echo "<tr>";
		echo "<td>$row[0]</td><td>$row[1]</td><td>$row[8]</td><td>$row[9]</td><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td>";
		echo "</tr>";
	}
	echo "</table>";


	mysqli_close($conn);
?>
