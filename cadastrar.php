<?php 

require_once "config.php";

$cadastrados = 0;
$erros = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$listaDeDoacoes = json_decode(str_replace("CODREPEAMP","&", $_POST['insertList']));

	$preSql = "INSERT INTO doacoes (";
	// Nomeia colunas, e quantidade de interrogações depois remove última vírgula
	$colunas = "";
	
	foreach ($listaDeDoacoes[0] as $key => $item) {
		$colunas .= $key.',';		
	}
	$colunas = rtrim($colunas,',');
	$preSql .= $colunas.') VALUES (';

	foreach ($listaDeDoacoes as $itemKey => $item) {
		$valores = "";		
		
		foreach ($item as $key => $value) {
			$valores .= "'".str_replace(["'", "&"], ["\'", "\&"], utf8_decode($value))."',"; // str replace usado para resolver nomes com apóstrofe (encerrava a string prematuramente)			
		}
		$valores = rtrim($valores,',');
		$sql = $preSql.$valores.');';
		if(!mysqli_query($link, $sql)){
			printf("Errormessage: %s\n", mysqli_error($link));
		}
		else
			$cadastrados+=1;

	}
	
	echo $cadastrados;
	return;	
}
 ?>
