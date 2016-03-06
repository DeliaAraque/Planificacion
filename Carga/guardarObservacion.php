<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$contenido = htmlspecialchars($_POST["contenido"], ENT_QUOTES);
	$periodo = htmlspecialchars($_POST["periodo"], ENT_QUOTES);
	$carreraSede = htmlspecialchars($_POST["carreraSede"], ENT_QUOTES);
	$profesor = htmlspecialchars($_POST["profesor"], ENT_QUOTES);

	if($contenido)
		$sql = "insert into observacion values('$contenido', $carreraSede, '$periodo', $profesor)";

	else
		$sql = "delete from observacion where \"idCS\" = $carreraSede and \"idPeriodo\" = '$periodo' and \"idProfesor\" = $profesor";

	$exe = pg_query($sigpa, $sql);

	if(! $exe) {
		$sql = "update observacion set contenido = '$contenido' where \"idCS\" = $carreraSede and \"idPeriodo\" = '$periodo' and \"idProfesor\" = $profesor";
		pg_query($sigpa, $sql);
	}
?>