<?php 
header('Content-Type: text/html; charset=utf-8');
require_once "config.php";

$erros = [];

// metodos pseudo-globais
/*
global $_DELETE = array();
global $_PUT = array();

if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE')) {
    parse_str(file_get_contents('php://input'), $_DELETE);
}
if (!strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT')) {
    parse_str(file_get_contents('php://input'), $_PUT);
}
*/

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$fiscal = json_decode($_POST['fiscal']);
	$sql = "SELECT * FROM bens_patrimoniais WHERE ";
	$whereAdd = $fiscal->setor == "TODOS" ? "1=1" : "`setor`='".$fiscal->setor."'";
	// ADD para restringir lista de SEL/SMDU
	if($fiscal->setor == "Gabinete"){
		if($fiscal->rf == "d515438") // Valberlene
			$whereAdd = "`orgao`='SMDU' AND (`setor`='Gabinete' OR `setor`='ATU' OR `setor`='ASCOM')";

		if($fiscal->rf == "d858506") // Thatiane
			$whereAdd .= " AND `orgao`='SEL'";
	}

	$whereAdd .= strlen($fiscal->divisao) > 0 ? (" AND `divisao`='".$fiscal->divisao."'") : "";
	$sql .= $whereAdd.";";
	mysql_query('SET character_set_results=utf8');
	$link->set_charset("utf8");
	$retornoQuery = $link->query($sql);
	$bens = [];
	if($retornoQuery->num_rows > 0){
	    while ($row = $retornoQuery->fetch_assoc()) {
	    	array_push($bens, $row);
	    }
	}
	echo json_encode($bens);
	// echo json_last_error();
	        
	$link->close();

	return;
}
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
	$itemConferido = json_decode(file_get_contents('php://input'));
	$sql = "UPDATE bens_patrimoniais SET ";
	foreach ($itemConferido as $key => $value) {
		$sql .= "`".$key."`='".utf8_decode($value)."',";
	}
	$sql = rtrim($sql,',');
	$sql .= " WHERE id=".$itemConferido->id.";";
	if(!mysqli_query($link, $sql)){
		printf("Errormessage: %s\n", mysqli_error($link));
	}
	else
		echo 1;
	// echo $sql;
	return;
}
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
	$rawInfo = file_get_contents('php://input');
	$rawInfo = explode(".-.",$rawInfo);
	$itemRemovido = $rawInfo[0];
	$usuario = $rawInfo[1];
	$wholeItem = json_encode($rawInfo[2]);

	$sql = "DELETE FROM bens_patrimoniais WHERE `id`=".$itemRemovido.";";

	if(!mysqli_query($link, $sql))
		printf("Errormessage: %s\n", mysqli_error($link));
	else
		echo 1;
	
	$sql = "INSERT INTO log_delete (`rf`,`item`) VALUES ('".$usuario."','".$wholeItem."');";
	if(!mysqli_query($link, $sql))
		printf("Errormessage: %s\n", mysqli_error($link));
	else
		echo 1;

	return;
}

// 	$preSql = "INSERT INTO bens_patrimoniais (";
// 	// Nomeia colunas, e quantidade de interrogações depois remove última vírgula
// 	$colunas = "";
	
// 	foreach ($listaDeBens[0] as $key => $item) {
// 		$colunas .= $key.',';		
// 	}
// 	$colunas = rtrim($colunas,',');
// 	$preSql .= $colunas.') VALUES (';

// 	foreach ($listaDeBens as $itemKey => $item) {
// 		$valores = "";
// 		foreach ($item as $key => $value) {
// 			$valores .= "'".utf8_decode($value)."',";
// 		}
// 		$valores = rtrim($valores,',');
// 		$sql = $preSql.$valores.');';
// 		if(!mysqli_query($link, $sql)){
// 			printf("Errormessage: %s\n", mysqli_error($link));
// 		}
// 		else
// 			$cadastrados+=1;

// 	}
	
// 	echo $cadastrados;
// 	return;
// }
 ?>
