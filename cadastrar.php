<?php 

require_once "config.php";

$cadastrados = 0;
$erros = [];

if($_SERVER["REQUEST_METHOD"] == "POST") {
	// Prepara comando
	$sql = "INSERT INTO bens_patrimoniais (
		nomeServidor,
		rf,
		orgao,
		coordenadoria,
		divisao,
		assessoria,
		sala,
		andar,
		chapa,
		chapaOutraUnidade,
		nomeOutraUnidade
	) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

	$listaDeBens = json_decode($_POST['insertList']);
	
	if($stmt = mysqli_prepare($link, $sql)){
		// Vincula as variáveis como parâmetros
		mysqli_stmt_bind_param($stmt, "sssssssssss", 
			$param_nomeServidor,
			$param_rf,
			$param_orgao,
			$param_coordenadoria,
			$param_divisao,
			$param_assessoria,
			$param_sala,
			$param_andar,
			$param_chapa,
			$param_chapaOutraUnidade,
			$param_nomeOutraUnidade
		);

		foreach ($listaDeBens as $key => $item) {
			$param_nomeServidor = utf8_decode($item->nomeServidor);
			$param_rf = $item->rf;
			$param_orgao = isset($item->orgao) ? utf8_decode($item->orgao) : NULL;
			$param_coordenadoria = isset($item->coordenadoria) ? utf8_decode($item->coordenadoria) : NULL;
			$param_divisao = isset($item->divisao) ? utf8_decode($item->divisao) : NULL;
			$param_assessoria = isset($item->assessoria) ? utf8_decode($item->assessoria) : NULL;
			$param_sala = isset($item->sala) ? utf8_decode($item->sala) : NULL;
			$param_andar = isset($item->andar) ? $item->andar : NULL;
			$param_chapa = isset($item->chapa) ? $item->chapa : NULL;
			$param_chapaOutraUnidade = isset($item->chapaOutraUnidade) ? $item->chapaOutraUnidade : NULL;
			$param_nomeOutraUnidade = isset($item->nomeOutraUnidade) ? utf8_decode($item->nomeOutraUnidade) : NULL;
			if(mysqli_stmt_execute($stmt)){
				$cadastrados += 1;
			}
			else {
				array_push($erros, json_encode($item));
			}
		}

		echo count($erros) > 0 ? json_encode($erros) : $cadastrados;
	}
	
}
 ?>
