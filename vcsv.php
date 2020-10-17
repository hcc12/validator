<?php
	$nChars = "1234567890-";
	$fChars = "1234567890.-";
	
	function cCast(&$ptr, $type) {
		if ($type == 1)
			$ptr = intval(implode($ptr));
		else if ($type == 2)
			$ptr = floatval(implode($ptr));
		else if ($type == 0)
			$ptr = implode($ptr);
		else return false;
		return true;
	}
	
	//typy: 0 - typ tekstowy, 1 - int, 2 - float
	//tryby: 0 - błąd przy nieregularności tablicy, 1 - błąd przy niezgodności danych wejściowych
	function csv_validate ($fname, $types, $mode) {
		global $nChars, $fChars;
		$file = fopen($fname, "r");
		if(!$file)
			return false;
		
		$c;
		$depth = 0;
		$lines = 0;
		$col = 0;
		$ret = array( array( array() ) );
		
		while (false !== ($c = fgetc($file))) {
			switch($c){
				case '"':
					if(!$depth)
						$depth++;
					else
						if(($c = fgetc($file)) == '"')
							array_push($ret[$lines][$col], '"');
						else{
							$depth--;
							fseek($file, -1, SEEK_CUR);
						}
				break;
				case ',':
					if(!$depth){
						if(!cCast($ret[$lines][$col], $types[$col])) return sprintf("Błędny typ dla kolumny %d", $col + 1);
						$ret[$lines][++$col] = array();
					}
				break;
				
				case "\n":
					if(!$depth) {
						if(count($ret[$lines]) != count($types))
							return sprintf("Błędna liczba kolumn w linii %d (%d z %d)", $lines+1, count($ret[$lines]), count($types));
						if(!cCast($ret[$lines][$col], $types[$col])) return sprintf("Błędny typ dla kolumny %d", $col + 1);
						$ret[++$lines] = array();
						$col = 0;
						$ret[$lines][$col] = array();
					} else
				break;
				
				default:
					switch($types[$col]) {
						case 0:
							array_push($ret[$lines][$col], $c);
						break;
						
						case 1:
							if(strchr($nChars, $c)){
								if(!count($ret[$lines][$col])){
									if($c=="0")
										break;
									} else if($c=="-")
										break;
								array_push($ret[$lines][$col], $c);
							} else if($mode==1) 
								return sprintf("Niedozowlony znak w linii %d, kolumnie %d, znaku %d",
											$lines+1, $col+1, count($ret[$lines][$col]));
						break;
						
						case 2:
							if(strchr($fChars, $c)){
								if(!count($ret[$lines][$col])){
									if($c=="0")
										break;
									} else if($c=="-")
										break;
								array_push($ret[$lines][$col], $c);
							} else if($mode==1) 
								return sprintf("Niedozowlony znak w linii %d, kolumnie %d, znaku %d",
											$lines+1, $col+1, count($ret[$lines][$col]));
						break;
						
					}
			}
		}
		if($col == 0){
			array_pop($ret);
		}
		return $ret;
	}
	/*echo "<pre>";
	$arr = csv_validate("plik.csv", array(0, 0, 0, 0, 0, 2), 1);
	if(gettype($arr) == "string")
		printf("%s", $arr);
	else
		for($i = 0; $i < count($arr) ; $i++)
			printf("%s %s %s %s %s %f\n", 
				$arr[$i][0], $arr[$i][1], $arr[$i][2], $arr[$i][3], $arr[$i][4], $arr[$i][5]);*/
	
?>