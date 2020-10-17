<?php
	$nChars = "1234567890-";
	$fChars = "1234567890.-";
	
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
					if(!$depth)
						$ret[$lines][++$col] = array();
				break;
				
				case "\n":
					if(!$depth) {
						if(count($ret[$lines]) != count($types))
							return sprintf("Błędna liczba kolumn w linii %d (%d z %d)", $lines+1, count($ret[$lines]), count($types));
						if ($types[$col] == 1)
							$ret[$lines][$col] = intval(implode($ret[$lines][$col]));
						else if ($types[$col] == 2)
							$ret[$lines][$col] = floatval(implode($ret[$lines][$col]));
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
		
?>