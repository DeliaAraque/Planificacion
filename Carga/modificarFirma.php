<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[0-9]+$";

	if((! ereg("$re", $_POST["orden"])) || (! ereg("$re", $_POST["ordenAnt"]))) {
		echo "El orden indicado no cumple con el patrón necesario";
		exit;
	}

	$orden = $_POST["orden"];
	$ordenAnt = $_POST["ordenAnt"];

	if($orden != $ordenAnt) {
		$sql = "select COUNT(orden) as n from firma where orden='$orden'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Otra firma ocupa esa posición";
			exit;
		}
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if((! ereg("$re", $_POST["cargo"])) || (! ereg("$re", $_POST["cargoAnt"]))) {
		echo "El cargo indicado no cumple con el patrón necesario";
		exit;
	}

	$cargo = $_POST["cargo"];
	$cargoAnt = $_POST["cargoAnt"];

	if($cargo != $cargoAnt) {
		$sql = "select COUNT(orden) as n from firma where cargo='$cargo'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe ese cargo";
			exit;
		}
	}

	$idProfesor = htmlspecialchars($_POST["idProfesor"], ENT_QUOTES);

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update firma set orden = $orden, cargo = '$cargo', \"idProfesor\" = $idProfesor where orden = $ordenAnt";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la firma correctamente

	if($exe) {
		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la firma

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>