<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["id"])) {
		echo "La abreviatura indicada no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];

	$sql = "select COUNT(id) as n from dedicacion where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una dedicación con esa abreviatura";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	$sql = "select COUNT(id) as n from dedicacion where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una dedicación con ese nombre";
		exit;
	}

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["horas"])) {
		echo "Las horas indicadas no cumplen con el patrón necesario";
		exit;
	}

	$horas = $_POST["horas"];

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into dedicacion values('$id', '$nombre', '$horas')";
	$exe = pg_query($sigpa, $sql);

// Si se guardó la dedicación correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la dedicación <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se guardó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la dedicación

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>