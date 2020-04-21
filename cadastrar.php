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
		// Remove Entregas e Distribuições do objeto principal e prepara para inserir nas respectivas tabelas
		if ($key !== 'entregas' && $key !== 'distribuicoes') {
			$colunas .= $key.',';
		}
	}

	$colunas = rtrim($colunas,',');
	$preSql .= $colunas.') VALUES (';

	foreach ($listaDeDoacoes as $itemKey => $item) {
		$valores = "";		
		$entregas = [];
		$distribuicoes = [];
		
		foreach ($item as $key => $value) {
			// Sendo uma entrega ou distribuição, insere valores correspondentes à tabela
			if ($key === 'entregas') {
				foreach ($value as $vKey => $vValue) {
					array_push($entregas, $vValue);					
				}
			}
			else if ($key === 'distribuicoes') {
				foreach ($value as $vKey => $vValue) {
					array_push($distribuicoes, $vValue);
				}
			}
			else {
				$valores .= "'".str_replace(["'", "&"], ["\'", "\&"], utf8_decode($value))."',"; // str replace usado para resolver nomes com apóstrofe (encerrava a string prematuramente)
			}
		}
		$valores = rtrim($valores,',');
		$sql = $preSql.$valores.');';
		if(!mysqli_query($link, $sql)){
			printf("Errormessage: %s\n", mysqli_error($link));
		}
		else {
			$id = mysqli_insert_id($link);
			// ENTREGAS
			foreach ($entregas as $entregaKey => $entrega) {
				$sqlEntrega = "INSERT INTO `recebimentos` (`id_doacao`, `data_recebimento`, `qtde_recebida`) VALUES ('$id', '$entrega->data_recebimento', '$entrega->qtde_recebida');";
				if (!mysqli_query($link, $sqlEntrega)) {
					printf("Errormessage: %s\n", mysqli_error($link));
				}
			}
			// DISTRIBUICOES
			foreach ($distribuicoes as $distribuicaoKey => $distribuicao) {
				$sqlDist = "INSERT INTO `distribuicoes` (`id_doacao`, `data_distribuicao`, `qtde_distribuicao`) VALUES ('$id', '$distribuicao->data_distribuicao', '$distribuicao->qtde_distribuicao');";
				if (!mysqli_query($link, $sqlDist)) {
					printf("Errormessage: %s\n", mysqli_error($link));
				}
			}
			$cadastrados+=1;
		}

	}
	// LOG de cadastro
	session_start();
	$sqlLog = "INSERT INTO `log_geral` (`rf`, `registro`) VALUES ('".strtolower($_SESSION['IDUsuario'])."', 'cadastro_doacao');";
	if(!mysqli_query($link, $sqlLog))
	    printf("Errormessage: %s\n", mysqli_error($link));

	echo $cadastrados;
	return;	
}
 ?>
