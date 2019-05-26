<?php 
session_start();

// Inclui arquivo de configuração
require_once "config.php";

$setor = $_GET['setor'];
$divisao = $_GET['divisao'];

$sql = "SELECT * FROM bens_patrimoniais WHERE `setor`='" . $setor;

if ($divisao != ''){
    $sql .= "' AND `divisao`='".$divisao;
};
$sql .= "';";
// Se setor for "TODOS", retorna todos os itens do cadastro
if($setor == "TODOS")
    $sql = "SELECT * FROM bens_patrimoniais;";

$divisao = $divisao == '' ? $divisao : ('_' . $divisao);
$csv  = "bens_cadastrados-" . $setor . $divisao . "-" . date('d-m-Y-his') . '.csv';
// Gerar link inpage
// $file = fopen($csv, 'w');

// Abrir arquivo (download)
$file = fopen('php://output', 'w');
if (mysqli_character_set_name($link) === 'utf8') {
    fputs( $file, "\xEF\xBB\xBF" ); // Corrige caracteres (charset UTF-8)
}
// Monta tabela
if (!$mysqli_result = mysqli_query($link, $sql))
    printf("Error: %s\n", $link->error);
    // Nomes das colunas
    while ($column = mysqli_fetch_field($mysqli_result)) {        
        $column_names[] = $column->name;        
    }    

    header('Content-Type: text/csv;charset=UTF-8');
    header('Content-Encoding: UTF-8');
    header('Content-Disposition: attachment; filename="' . $csv . '"');
    header('Pragma: no-cache');    
    header('Expires: 0');

    // Write column names in csv file
    if (!fputcsv($file, $column_names, ";"))
        die('Can\'t write column names in csv file');
    
    // Get table rows
    while ($row = mysqli_fetch_row($mysqli_result)) {
        // Write table rows in csv files
        if (!fputcsv($file, $row, ";"))
            die('Can\'t write rows in csv file');
    }
fclose($file);

?>