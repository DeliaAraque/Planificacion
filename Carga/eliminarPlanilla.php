<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$archivo = htmlspecialchars($_POST["archivo"], ENT_QUOTES);

	if(unlink("Planillas/$archivo")) {

		// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se eliminó la planilla <strong>$archivo</strong>', 'No SQL')";
		$exe = pg_query($sigpa, $sql);

		// --------------------

		echo "Se eliminó satisfactoriamente&&success";
	}

	else
		echo "No se pudo eliminar la planilla&&error";
?>