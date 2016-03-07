<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["id"])) {
		echo "El código no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];

	$sql = "select COUNT(id) as n from \"unidadCurricular\" where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una unidad curricular con ese código";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$sql = "select id from carrera where id='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	if(!$carrera->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*,?( [a-záéíóúñA-ZÁÉÍÓÚÑ]+,?)*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	$renombrable = (! isset($_POST["renombrable"])) ? "false" : "true";

	$eje = htmlspecialchars($_POST["eje"], ENT_QUOTES);

	$sql = "select id from eje where id='$eje'";
	$exe = pg_query($sigpa, $sql);
	$eje = pg_fetch_object($exe);

	if(!$eje->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$eje = htmlspecialchars($_POST["eje"], ENT_QUOTES);

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into \"unidadCurricular\" values('$id', '$nombre', $renombrable, '$carrera', '$eje')";
	$exe = pg_query($sigpa, $sql);

// Si se guardo la unidad curricular correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la unidad curricular <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se guardó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la unidad curricular

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>