<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	$sql = "select COUNT(id) as n from area where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un área con ese nombre";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into area values(default, '$nombre')";
	$exe = pg_query($sigpa, $sql);

// Si se guardo el área correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó el área <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se guardó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando el área

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>