<?php 
header('Content-Type: text/html; charset=utf-8');
require_once "config.php";

if (!mysqli_set_charset($link, "utf8")) {
	printf("Erro ao definir charset: %s<br>", mysqli_error($link));
	exit();
}

$erros = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
	$fiscal = json_decode($_POST['fiscal']);
	$sql = "SELECT * FROM doacoes;";
	
	mysqli_query($link, 'SET character_set_results=utf8');
	$link->set_charset("utf8");

	$retornoQuery = $link->query($sql);
	$doacoes = [];
	if($retornoQuery->num_rows > 0){
		while ($row = $retornoQuery->fetch_assoc()) {
			// BUSCA ITENS DE CADA DOAÇÃO
			$sqlItens = "SELECT * FROM `doacao_itens` WHERE `id_doacao`=".$row['id'].";";
			$queryItens = $link->query($sqlItens);
			$doacaoItens = [];
			if ($queryItens->num_rows > 0) {
				while ($item = $queryItens->fetch_assoc()) {
					// BUSCA RECEBIMENTOS E DISTRIBUICOES DE CADA ITEM
					$sqlDistri = "SELECT * FROM `item_distribuicoes` WHERE `id_item`=".$item['id'].";";
					$distribuicoes = [];
					$queryInterna = $link->query($sqlDistri);
					if ($queryInterna->num_rows > 0) {
						while ($distri = $queryInterna->fetch_assoc()) {
							array_push($distribuicoes, $distri);
						}
					}
					$sqlReceb = "SELECT * FROM `item_recebimentos` WHERE `id_item`=".$item['id'].";";
					$recebimentos = [];
					$queryInterna = $link->query($sqlReceb);
					if ($queryInterna->num_rows > 0) {
						while ($receb = $queryInterna->fetch_assoc()) {
							array_push($recebimentos, $receb);
						}
					}

					$item['distribuicoes'] = $distribuicoes;
					$item['recebimentos'] = $recebimentos;
					
					array_push($doacaoItens, $item);
				}
			}
			$row['doacao_itens'] = $doacaoItens;
			array_push($doacoes, $row);
		}
	}
	echo json_encode($doacoes);
	// echo json_last_error();
	        
	$link->close();

	return;
}

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
	$doacaoConferida = json_decode(file_get_contents('php://input'));
	$sqlDoacao = "UPDATE doacoes SET ";
	$doacaoItens = [];
	
	foreach ($doacaoConferida as $doacaoKey => $doacaoValue) {
		// INSERE TODOS OS CAMPOS, MENOS OS DE ITENS DA DOAÇÃO
		if ($doacaoKey === 'doacao_itens') {
			foreach ($doacaoValue as $iKey => $iValue) {
				array_push($doacaoItens, $iValue);					
			}
		}
		else if ($doacaoKey !== 'data_alteracao') {
			// $sqlDoacao .= "`".$doacaoKey."`='".utf8_decode($doacaoValue)."',";			
			$sqlDoacao .= "`".$doacaoKey."`='".$doacaoValue."',";
		}		
	}

	$colunasDoacaoItens = "";
	foreach ($doacaoItens as $coluna => $doacaoItem) {
		// PERCORRE TODOS OS ITENS (DOACAO_ITEM)
		$sqlListaItens = '';
		$itemColunas = ['tipo_item',
			'categoria_item',
			'descricao_item',
			'destino',
			'endereco_entrega',
			'responsavel_recebimento',
			'quantidade',
			'unidade_medida'];

		if(property_exists($doacaoItem, 'id') && $doacaoItem->id > 0) {
			// SE ITEM JÁ EXISTIR NO BANCO, ATUALIZA OS DADOS DELE
			$sqlListaItens = "UPDATE `doacao_itens` SET ";
			foreach ($itemColunas as $key => $itemCol) {
				$sqlListaItens .= "`".$itemCol."`='".$doacaoItem->{$itemCol}."',";
				// DESCOMENTAR LINHA ABAIXO (comentando a de cima) CASO OCORRA ERRO DE CHARSET EM PRODUÇÃO
				// $sqlListaItens .= "`".$itemCol."`='".utf8_decode($doacaoItem->{$itemCol})."',";
			}
			$sqlListaItens = rtrim($sqlListaItens,',');
			$sqlListaItens .= " WHERE id='$doacaoItem->id';";			
		}
		else {
			// SE FOR NOVO ITEM, INSERE NO BANCO
			$sqlListaItens = "INSERT INTO `doacao_itens` (`id_doacao`,";
			foreach ($itemColunas as $key => $itemCol) {
				$sqlListaItens .= "`".$itemCol."`,";
			}
			$sqlListaItens = rtrim($sqlListaItens,',');

			$sqlListaItens .= ") VALUES ('$doacaoConferida->id',";
			foreach ($itemColunas as $key => $itemCol) {
				$sqlListaItens .= "'".$doacaoItem->{$itemCol}."',";
			}
			$sqlListaItens = rtrim($sqlListaItens,',');
			$sqlListaItens .= ");";			
		}
		if (!mysqli_query($link, $sqlListaItens)) {
			printf("Errormessage: %s\n", mysqli_error($link));
		}
		else {
			// SE QUERY DO DOACAO_ITEM FOR BEM SUCEDIDA, INSERE/ATUALIZA ENTREGAS E RECEBIMENTOS DO ITEM
			$doacaoItemId = mysqli_insert_id($link) > 0 ? mysqli_insert_id($link) : $doacaoItem->id;

			// ATUALIZA, ADICIONA OU REMOVE DISTRIBUIÇÕES E RECEBIMENTOS DO ITEM
			// DISTRIBUIÇÕES
			foreach ($doacaoItem->distribuicoes as $key => $distribuicao) {
				$sqlLista = '';
				if(property_exists($distribuicao, 'id') && $distribuicao->id > 0) {
					$sqlLista = "UPDATE `item_distribuicoes` SET `data_distribuicao`='".$distribuicao->data_distribuicao."', `qtde_distribuicao`='".$distribuicao->qtde_distribuicao."' WHERE id=".$distribuicao->id.";";
				}
				else {
					$sqlLista = "INSERT INTO `item_distribuicoes` (`id_item`, `data_distribuicao`, `qtde_distribuicao`) VALUES ('".$doacaoItemId."', '".$distribuicao->data_distribuicao."', '".$distribuicao->qtde_distribuicao."');";
				}
				if (!mysqli_query($link, $sqlLista)) {
					printf("Errormessage: %s\n", mysqli_error($link));
				}
			}
			// RECEBIMENTOS
			foreach ($doacaoItem->recebimentos as $key => $recebimento) {
				$sqlLista = '';
				if(property_exists($recebimento, 'id') && $recebimento->id > 0) {
					$sqlLista = "UPDATE `item_recebimentos` SET `data_recebimento`='".$recebimento->data_recebimento."', `qtde_recebida`='".$recebimento->qtde_recebida."' WHERE id=".$recebimento->id.";";
				}
				else {
					$sqlLista = "INSERT INTO `item_recebimentos` (`id_item`, `data_recebimento`, `qtde_recebida`) VALUES ('".$doacaoItemId."', '".$recebimento->data_recebimento."', '".$recebimento->qtde_recebida."');";
				}
				if (!mysqli_query($link, $sqlLista)) {
					printf("Errormessage: %s\n", mysqli_error($link));
				}
			}
			// DISTRIBUIÇÕES E RECIMENTOS END
		}
	}

	$sqlDoacao = rtrim($sqlDoacao,',');
	$sqlDoacao .= " WHERE id=".$doacaoConferida->id.";";
	if(!mysqli_query($link, $sqlDoacao)){
		printf("Errormessage: %s\n", mysqli_error($link));
	}
	else {
		session_start();
		$sqlLog = "INSERT INTO `log_geral` (`rf`, `registro`) VALUES ('".strtolower($_SESSION['IDUsuario'])." - ".$_SESSION['nomeUsuario']."', 'alteracao_doacao');";
		if(!mysqli_query($link, $sqlLog)) {
	    printf("Errormessage: %s\n", mysqli_error($link));
	  }
	  else {
	  	updtGS();
	  }
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

	function logDelete($usuario, $wholeItem, $link) {
		$sql = "INSERT INTO log_delete (`rf`,`item`) VALUES ('".$usuario."','".$wholeItem."');";
		if(!mysqli_query($link, $sql))
			printf("Errormessage: %s\n", mysqli_error($link));
		else {
			updtGS();
			echo 1;
		}
	}

	if (property_exists($itemObj, 'id_item')) {
		// SE OBJETO POSSUI PROP id_item, É UMA ENTREGA OU DISTRIBUICAO. REMOVE DA RESPECTIVA TABELA
		$tabela = '';
		if (property_exists($itemObj, 'data_recebimento')) {
			$tabela = "item_recebimentos";
		}
		else if (property_exists($itemObj, 'data_distribuicao')) {
			$tabela = "item_distribuicoes";
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

		logDelete($usuario, $wholeItem, $link);

		return;
	}
	else if(property_exists($itemObj, 'id_doacao')) {
		// SE OBJETO POSSUI PROP id_doacao, É UM ITEM DA DOAÇÃO (DOACAO_ITEM). REMOVE DA RESPECTIVA TABELA
		$sqlDelete = "DELETE FROM `doacao_itens` WHERE `id`='$itemRemovido';";
		if(!mysqli_query($link, $sqlDelete))
			printf("Errormessage: %s\n", mysqli_error($link));
		else
			echo 1;

		logDelete($usuario, $wholeItem, $link);
	}

	$sql = "DELETE FROM doacoes WHERE `id`=".$itemRemovido.";";

	if(!mysqli_query($link, $sql))
		printf("Errormessage: %s\n", mysqli_error($link));
	else
		echo 1;
	
	// $sql = "INSERT INTO log_delete (`rf`,`item`) VALUES ('".$usuario."','".$wholeItem."');";
	// if(!mysqli_query($link, $sql))
	// 	printf("Errormessage: %s\n", mysqli_error($link));
	// else
	// 	echo 1;
	logDelete($usuario, $wholeItem, $link);

	return;
}

function updtGS() {
	global $dbTables;
	require_once "gsheets.php";
}
?>
