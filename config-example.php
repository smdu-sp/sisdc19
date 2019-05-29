<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASSWORD', '');
define('DB_NAME', 'sicabe');
define('DB_CHARSET', 'utf8');

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
 
if($link === false){
    die("ERRO: Não foi possível conectar. " . mysqli_connect_error());
}

?>
