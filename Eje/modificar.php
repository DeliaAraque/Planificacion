<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if((! ereg("$re", $_POST["nombre"])) || (! ereg("$re", $_POST["nombreAnt"]))) {
		echo "El nombre indicado no cumple con el patrón necesario, solo están permitidos caracteres alfabéticos y las primeras letras deben estar en mayúscula";
		exit;
	}

	$nombre = $_POST["nombre"];
	$nombreAnt = $_POST["nombreAnt"];

	if($nombre == $nombreAnt) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	$sql = "select COUNT(id) as n from eje where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un eje con ese nombre";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update eje set nombre='$nombre' where nombre='$nombreAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó el eje

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó el eje <strong>$nombreAnt</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrió un error modificando el eje

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>