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

$nombreAntiguo=$_REQUEST['nombreAntiguo'];
$nombreNuevo=$_REQUEST['doctor'];
$numcolegiado=$_REQUEST['numcolegiado'];
$clinicas=$_REQUEST['clinicas'];

$nombreAntiguo="'".$nombreAntiguo."'";
$nombreNuevo="'".$nombreNuevo."'";

$sql="select id_doctor from doctores where nombre=".$nombreAntiguo.";";
$query_res = mysql_query($sql, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

while($row = mysql_fetch_array($query_res)){
	$id_doc=$row['id_doctor'];
}

$sql='delete from `clinica_doctor` where `id_doctor` ='.$id_doc.';';
$query_res = mysql_query($sql, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

for ($i=0;$i<count($clinicas);$i++){     
	$query_insert = "insert into clinica_doctor (id_doctor, id_clinica) values( 
        ".$id_doc.", 
        ".$clinicas[$i].")";
	$query_res = mysql_query($query_insert, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());
} 

$query = "update doctores set 
			nombre = ". $nombreNuevo .", 
            numcolegiado = '". $numcolegiado ."' 
            WHERE id_doctor = '". $id_doc. "'";
$query_res = mysql_query($query, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

if (!$query_res) {
    $mensaje  = 'Error: ' . mysql_error() ;
    $estado = 0;
}else{
    $mensaje = "Doctor editado.";
    $estado = 1;
}
$resultado = array();
$resultado[] = array(
    'mensaje' => $mensaje,
    'estado' => $estado
  );
echo json_encode($resultado);
?>
