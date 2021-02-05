<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASSWORD', '');
define('DB_NAME', 'sisdc19');
define('DB_CHARSET', 'utf8');

$dbTables = [
	'doacoes' => 'CODIGO_DA_PLANILHA_GOOGLE',
	'item_recebimentos' => 'CODIGO_DA_PLANILHA_GOOGLE',
	'nome_da_tabela' => "CODIGO..."
];

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERRO: Não foi possível conectar. " . mysqli_connect_error());
}

?>
