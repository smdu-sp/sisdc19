<?php 

require_once "config.php";

$erros = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$fiscal = json_decode($_POST['fiscal']);
	var_dump($fiscal);
	$sql = "SELECT * FROM bens_patrimoniais WHERE ";
	$whereAdd = "`setor`='".$fiscal->setor."'";
	$whereAdd .= strlen($fiscal->divisao) > 0 ? (" AND `divisao`='".$fiscal->divisao."'") : "";
	$sql .= $whereAdd.";";
	echo $sql;
	return;

	$preSql = "INSERT INTO bens_patrimoniais (";
	// Nomeia colunas, e quantidade de interrogações depois remove última vírgula
	$colunas = "";
	
	foreach ($listaDeBens[0] as $key => $item) {
		$colunas .= $key.',';		
	}
	$colunas = rtrim($colunas,',');
	$preSql .= $colunas.') VALUES (';

	foreach ($listaDeBens as $itemKey => $item) {
		$valores = "";
		foreach ($item as $key => $value) {
			$valores .= "'".utf8_decode($value)."',";
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
