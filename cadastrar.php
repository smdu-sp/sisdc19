<?php 

require_once "config.php";

$cadastrados = 0;
$erros = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$listaDeDoacoes = json_decode(str_replace("CODREPEAMP","&", $_POST['insertList']));

	$preSql = "INSERT INTO doacoes (";
	// Nomeia colunas, e quantidade de interrogações depois remove última vírgula
	$colunasDoacao = "";
	
	foreach ($listaDeDoacoes[0] as $key => $item) {
		// Remove Entregas e Distribuições do objeto principal e prepara para inserir nas respectivas tabelas
		if ($key !== 'itens_doacao') {
			$colunasDoacao .= $key.',';
		}
	}

	$colunasDoacao = rtrim($colunasDoacao,',');
	$preSql .= $colunasDoacao.') VALUES (';

	foreach ($listaDeDoacoes as $itemKey => $item) {
		$valores = "";		
		$itensDoacao = [];
		
		foreach ($item as $key => $value) {
			// Sendo um item_doacao, insere valores correspondentes à tabela
			if ($key === 'itens_doacao') {
				foreach ($value as $vKey => $vValue) {
					array_push($itensDoacao, $vValue);					
				}
			}
			else {
				$valores .= "'".str_replace(["'", "&"], ["\'", "\&"], utf8_decode($value))."',"; // str replace usado para resolver nomes com apóstrofe (encerrava a string prematuramente)
			}
		}
		$valores = rtrim($valores,',');
		$sql = $preSql.$valores.');';
		// $sql = "select * from doacoes;"; // TODO: REMOVER
		if(!mysqli_query($link, $sql)){
			printf("Errormessage: %s\n", mysqli_error($link));
		}
		else {
			// DOAÇÃO INSERIDA COM SUCESSO NO BANCO. PROSSEGUE COM INSERÇÃO DOS ITENS
			$idDoacao = mysqli_insert_id($link);

			// NOMEIA COLUNAS 
			$sqlItens = "INSERT INTO `doacao_itens` (`id_doacao`, ";
			$colunasItens = "";

			foreach ($itensDoacao[0] as $key => $item) {
				if ($key !== 'entregas' && $key !== 'distribuicoes') {
					$colunasItens .= $key.',';
				}
			}

			$colunasItens = rtrim($colunasItens,',');
			$sqlItens .= $colunasItens.") VALUES ('$idDoacao', ";
			
			// ITENS
			foreach ($itensDoacao as $itemKey => $itemDoacao) {
				$entregas = [];
				$distribuicoes = [];
				$valoresItens = "";

				foreach ($itemDoacao as $itemKey => $itemValue) {
					if ($itemKey === 'entregas') {
						foreach ($itemValue as $vKey => $vValue) {
							array_push($entregas, $vValue);					
						}
					}
					else if ($itemKey === 'distribuicoes') {
						foreach ($itemValue as $vKey => $vValue) {
							array_push($distribuicoes, $vValue);
						}
					}
					else {
						$valoresItens .= "'".str_replace(["'", "&"], ["\'", "\&"], utf8_decode($itemValue))."',"; // str replace usado para resolver nomes com apóstrofe
					}
				}
				
				$valoresItens = rtrim($valoresItens,',');
				$sqlInsertItens = $sqlItens.$valoresItens.');';

				if (!mysqli_query($link, $sqlInsertItens)) {
					printf("sqlitens: ",$sqlItens);
					var_dump($sqlInsertItens);
					printf("Errormessage: %s\n", mysqli_error($link));
				}
				else {
					// ITEM INSERIDO COM SUCESSO. PROSSEGUE COM INSERÇÃO DAS ENTREGAS E DISTRIBUIÇÕES
					$idItem = mysqli_insert_id($link);
					// ENTREGAS
					foreach ($entregas as $entregaKey => $entrega) {
						$sqlEntrega = "INSERT INTO `item_recebimentos` (`id_item`, `data_recebimento`, `qtde_recebida`) VALUES ('$idItem', '$entrega->data_recebimento', '$entrega->qtde_recebida');";
						if (!mysqli_query($link, $sqlEntrega)) {
							printf("Errormessage: %s\n", mysqli_error($link));
						}
					}
					// DISTRIBUICOES
					foreach ($distribuicoes as $distribuicaoKey => $distribuicao) {
						$sqlDist = "INSERT INTO `item_distribuicoes` (`id_item`, `data_distribuicao`, `qtde_distribuicao`) VALUES ('$idItem', '$distribuicao->data_distribuicao', '$distribuicao->qtde_distribuicao');";
						if (!mysqli_query($link, $sqlDist)) {
							printf("Errormessage: %s\n", mysqli_error($link));
						}
					}
				}
			}
			// DISTRIBUICOES
			
			$cadastrados+=1;
		}

	}
	// LOG de cadastro
	session_start();
	$sqlLog = "INSERT INTO `log_geral` (`rf`, `registro`) VALUES ('".strtolower($_SESSION['IDUsuario'])."', 'cadastro_doacao');";
	if(!mysqli_query($link, $sqlLog))
	    printf("Errormessage: %s\n", mysqli_error($link));

	echo $cadastrados;
	updtGS();
	return;	
}

function updtGS() {
	global $dbTables;
	require_once "gsheets.php";
}

 ?>
