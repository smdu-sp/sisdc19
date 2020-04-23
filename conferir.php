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
	$sql = "SELECT * FROM doacoes;";
	
	mysqli_query($link, 'SET character_set_results=utf8');
	$link->set_charset("utf8");
	$retornoQuery = $link->query($sql);
	$doacoes = [];
	if($retornoQuery->num_rows > 0){
	    while ($row = $retornoQuery->fetch_assoc()) {
	    	// BUSCA RECEBIMENTOS E DISTRIBUICOES DE CADA DOAÇÃO
	    	$sqlDistri = "SELECT * FROM `distribuicoes` WHERE `id_doacao`=".$row['id'].";";
	    	$distribuicoes = [];
	    	$queryInterna = $link->query($sqlDistri);
	    	if ($queryInterna->num_rows > 0) {
	    		while ($distri = $queryInterna->fetch_assoc()) {
	    			array_push($distribuicoes, $distri);
	    		}
	    	}
	    	$sqlReceb = "SELECT * FROM `recebimentos` WHERE `id_doacao`=".$row['id'].";";
	    	$recebimentos = [];
	    	$queryInterna = $link->query($sqlReceb);
	    	if ($queryInterna->num_rows > 0) {
	    		while ($receb = $queryInterna->fetch_assoc()) {
	    			array_push($recebimentos, $receb);
	    		}
	    	}
	    	$row['distribuicoes'] = $distribuicoes;
	    	$row['recebimentos'] = $recebimentos;

	    	array_push($doacoes, $row);
	    }
	}
	echo json_encode($doacoes);
	// echo json_last_error();
	        
	$link->close();

	return;
}
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
	$itemConferido = json_decode(file_get_contents('php://input'));
	$sql = "UPDATE doacoes SET ";
	$distribuicoes = [];
	$recebimentos = [];
	foreach ($itemConferido as $key => $value) {
		// INSERE TODOS OS CAMPOS, MENOS OS DE DISTRIBUIÇÃO E RECEBIMENTO
		if ($key === 'recebimentos') {
			foreach ($value as $vKey => $vValue) {
				array_push($recebimentos, $vValue);					
			}
		}
		else if ($key === 'distribuicoes') {
			foreach ($value as $vKey => $vValue) {
				array_push($distribuicoes, $vValue);
			}
		}
		else {
			$sql .= "`".$key."`='".utf8_decode($value)."',";			
		}		
	}
	// ATUALIZA, ADICIONA OU REMOVE DISTRIBUIÇÕES E RECEBIMENTOS DA DOAÇÃO
	// DISTRIBUIÇÕES
	foreach ($distribuicoes as $key => $distribuicao) {
		$sqlLista = '';
		if(property_exists($distribuicao, 'id') && $distribuicao->id > 0) {
			$sqlLista = "UPDATE `distribuicoes` SET `data_distribuicao`='".$distribuicao->data_distribuicao."', `qtde_distribuicao`='".$distribuicao->qtde_distribuicao."' WHERE id=".$distribuicao->id.";";
		}
		else {
			$sqlLista = "INSERT INTO `distribuicoes` (`id_doacao`, `data_distribuicao`, `qtde_distribuicao`) VALUES ('".$itemConferido->id."', '".$distribuicao->data_distribuicao."', '".$distribuicao->qtde_distribuicao."');";
		}
		if (!mysqli_query($link, $sqlLista)) {
			printf("Errormessage: %s\n", mysqli_error($link));
		}
	}
	// RECEBIMENTOS
	foreach ($recebimentos as $key => $recebimento) {
		$sqlLista = '';
		if(property_exists($recebimento, 'id') && $recebimento->id > 0) {
			$sqlLista = "UPDATE `recebimentos` SET `data_recebimento`='".$recebimento->data_recebimento."', `qtde_recebida`='".$recebimento->qtde_recebida."' WHERE id=".$recebimento->id.";";
		}
		else {
			$sqlLista = "INSERT INTO `recebimentos` (`id_doacao`, `data_recebimento`, `qtde_recebida`) VALUES ('".$itemConferido->id."', '".$recebimento->data_recebimento."', '".$recebimento->qtde_recebida."');";
		}
		if (!mysqli_query($link, $sqlLista)) {
			printf("Errormessage: %s\n", mysqli_error($link));
		}
	}
	// DISTRIBUIÇÕES E RECIMENTOS END

	$sql = rtrim($sql,',');
	$sql .= " WHERE id=".$itemConferido->id.";";
	if(!mysqli_query($link, $sql)){
		printf("Errormessage: %s\n", mysqli_error($link));
	}
	else {
		session_start();
		$sqlLog = "INSERT INTO `log_geral` (`rf`, `registro`) VALUES ('".strtolower($_SESSION['IDUsuario'])."', 'alteracao_doacao');";
		if(!mysqli_query($link, $sqlLog))
		    printf("Errormessage: %s\n", mysqli_error($link));
		echo 1;
	}
	// echo $sql;
	return;
}
if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
	$rawInfo = file_get_contents('php://input');
	$rawInfo = explode(".-.",$rawInfo);
	$itemRemovido = $rawInfo[0];
	$usuario = $rawInfo[1];
	$wholeItem = json_encode($rawInfo[2]);
	// REMOVE ENTREGA OU DISTRIBUIÇÃO
	$itemObj = json_decode($rawInfo[2]);
	if (property_exists($itemObj, 'id_doacao')) {
		// SE OBJETO POSSUI PROP id_doacao, É UMA ENTREGA OU DISTRIBUICAO. REMOVE DA RESPECTIVA TABELA
		$tabela = '';
		if (property_exists($itemObj, 'data_recebimento')) {
			$tabela = "recebimentos";
		}
		else if (property_exists($itemObj, 'data_distribuicao')) {
			$tabela = "distribuicoes";
		}
		else {
			printf("Tipo de item não identificado. Esperado tipo DISTRIBUICAO ou RECEBIMENTO.\n");
			return;
		}
		$sqlLista = "DELETE FROM `$tabela` WHERE `id`='$itemRemovido';";

		if(!mysqli_query($link, $sqlLista))
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

	$sql = "DELETE FROM doacoes WHERE `id`=".$itemRemovido.";";

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
