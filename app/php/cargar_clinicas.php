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

$sQuery = "select id_clinica, nombre from clinicas";

$query_res = mysql_query($sQuery, $gaSql['link']) or fatal_error('MySQL Error: ' . mysql_errno());

$clinicas=array();
while($data=mysql_fetch_array($query_res)){
	$id_clinica=$data["id_clinica"];
	$clinicas[$id_clinica]=$data["nombre"];
}

if (isset($_REQUEST['clinicas'])){
	$clinicas_marcadas=$_REQUEST['clinicas'];
        foreach($clinicas as $clave=>$valor){
            if(in_array($valor,$clinicas_marcadas)){
                echo '<option selected value="'.$clave.'">'.$valor.'</option>';
            }
            else {
                echo '<option value="'.$clave.'">'.$valor.'</option>';
            }
        }
}
else {
    foreach($clinicas as $clave=>$valor){
        echo '<option value="'.$clave.'">'.$valor.'</option>';
    }
}
?>
