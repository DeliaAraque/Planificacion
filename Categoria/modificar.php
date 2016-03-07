<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["idAnt"]))) {
		echo "La abreviatura indicada no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	if($id != $idAnt) {
		$sql = "select COUNT(id) as n from categoria where id='$id'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una categoria con esa abreviatura";
			exit;
		}
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if((! ereg("$re", $_POST["nombre"])) || (! ereg("$re", $_POST["nombreAnt"]))) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];
	$nombreAnt = $_POST["nombreAnt"];

	if($nombre != $nombreAnt) {
		$sql = "select COUNT(id) as n from categoria where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una categoria con ese nombre";
			exit;
		}
	}

	if(($id == $idAnt) && ($nombre == $nombreAnt)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update categoria set id='$id', nombre='$nombre' where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la categoría correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la categoría <strong>$idAnt</strong> por <strong>$id</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error modificando la categoría

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>