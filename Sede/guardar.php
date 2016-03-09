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

	$sql = "select COUNT(id) as n from sede where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una sede con ese nombre";
		exit;
	}

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fecha"])) {
		echo "La fecha no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fecha"]);
	$fecha = "$fecha[2]-$fecha[1]-$fecha[0]";

	if($fecha > date("Y-m-d")) {
		echo "La fecha de inauguración no puede ser mayor a la actual";
		exit;
	}

	$re = "^[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*$";

	if(! ereg("$re", $_POST["telefono"])) {
		echo "El número de teléfono no cumple con el patrón necesario";
		exit;
	}

	$telefono = $_POST["telefono"];
	$direccion = htmlspecialchars($_POST["direccion"], ENT_QUOTES);

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into sede values(default, '$nombre', '$fecha', '$telefono', '$direccion')";
	$exe = pg_query($sigpa, $sql);

// Si se guardo la sede correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la sede <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se guardó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la sede

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>