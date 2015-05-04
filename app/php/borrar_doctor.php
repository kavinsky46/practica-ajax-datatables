<?php
include("mysql.php" );

function fatal_error($sErrorMessage = '') {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
    die($sErrorMessage);
}

if (!$gaSql['link'] = mysql_pconnect($gaSql['server'], $gaSql['user'], $gaSql['password'])) {
    fatal_error('Could not open connection to server');
}

if (!mysql_select_db($gaSql['db'], $gaSql['link'])) {
    fatal_error('Could not select database ');
}

mysql_query('SET names utf8');

$nombre=$_REQUEST['doctor'];
$nombre="'".$nombre."'";

$sql='delete from `clinica_doctor` where `id_doctor` =(select `id_doctor` from `doctores` where nombre='.$nombre.');';
$query_res = mysql_query($sql, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

$sql='delete from `doctores` where nombre='.$nombre.';';
$query_res = mysql_query($sql, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

if (!$query_res) {
    $mensaje  = 'Error: ' . mysql_error() ;
    $estado = 0;
} else {
    $mensaje = "Doctor borrado";
    $estado = 1;
}

$resultado = array();
$resultado[] = array(
    'mensaje' => $mensaje,
    'estado' => $estado
);

echo json_encode($resultado);
?>
