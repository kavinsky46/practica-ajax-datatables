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
$numcolegiado=$_REQUEST['numcolegiado'];
$clinicas=$_REQUEST['clinicas'];

$query = "SELECT max(id_doctor)as id_doc FROM doctores";
$query_res = mysql_query($query, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

while($row = mysql_fetch_array($query_res)){
	$id_doc=$row['id_doc']+1;
}
if (!empty($nombre)) {
	$query = "insert into doctores (id_doctor, nombre, numcolegiado) values('".$id_doc."','".$nombre."','".$numcolegiado."')" ;
	$query_res = mysql_query($query, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());
}

for ($i=0;$i<count($clinicas);$i++){     
	$query_insert = "insert into clinica_doctor (id_doctor, id_clinica) values( 
        ".$id_doc.", 
        ".$clinicas[$i].")";
	$query_res = mysql_query($query_insert, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());
} 

if (!$query_res){
    $mensaje  = 'Error: ' . mysql_error() ;
    $estado = 0;
}else{
    $mensaje = "Doctor añadido.";
    $estado = 1;
}

$resultado = array();
$resultado[] = array(
      'mensaje' => $mensaje,
      'estado' => $estado
    );

echo json_encode($resultado);
?>
