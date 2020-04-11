<?php 
session_start();

// Inclui arquivo de configuração
require_once "config.php";

$rf = $_GET['rf'];

// $sql = "SELECT * FROM bens_patrimoniais WHERE `setor`='" . $setor;
$sql = "SELECT * FROM doacoes;";

$csv  = "doacoes_covid-" . date('d-m-Y-his') . '.csv';
// Gerar link inpage
// $file = fopen($csv, 'w');

// Abrir arquivo (download)
$file = fopen('php://output', 'w');
if (mysqli_character_set_name($link) === 'utf8') {
    fputs( $file, "\xEF\xBB\xBF" ); // Corrige caracteres (charset UTF-8)
}
function fixedName($nomeColuna) {
    return $nomeColuna;
    switch ($nomeColuna) {
        case 'id':
            $nomeColuna = 'ID';
            break;        
        case 'entrada':
            $nomeColuna = 'ENTRADA';
            break;
        case 'data_entrada':
            $nomeColuna = 'DATA DE ENTRADA';
            break;
        case 'id_responsavel':
            break;
        case 'responsavel_atendimento':
            $nomeColuna = 'REPONSAVEL DO ATENDIMENTO / ANDAMENTO';
            break;
        // "doador"
        // "tipo_formalizacao"
        // "descricao_item"
        // "tipo_item"
        // "quantidade"
        // "valor_total"
        // "destino"
        // "contato"
        // "prazo_periodo"
        // "endereco_entrega"
        // "responsavel_recebimento"
        // "status"
        // "numero_sei"
        // "observacao"
        // "comentario_sms"
        // "relatorio_sei"
        // "itens_pendentes_sei"
        // "monitoramento"
        // "conferido"
        // "data_inclusao"
        // "data_alteracao"
        default:            
            break;
    }
    return $nomeColuna;
}
// Monta tabela
if (!$mysqli_result = mysqli_query($link, $sql))
    printf("Error: %s\n", $link->error);
    // Nomes das colunas
    while ($column = mysqli_fetch_field($mysqli_result)) {        
        $column_names[] = fixedName($column->name);        
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
        // Troca ponto por vírgula (linha 10 corresponde ao Valor Total)
        $row[10] = str_replace('.', ',', $row[10]);
        // Write table rows in csv files        
        if (!fputcsv($file, $row, ";"))
            die('Can\'t write rows in csv file');
    }
fclose($file);

?>