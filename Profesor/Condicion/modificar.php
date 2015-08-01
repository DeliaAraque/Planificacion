<?php
	require "../../../script/verifSesion.php";
	require "../../../lib/conexion.php";

	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);
	$nombreAnt = htmlspecialchars($_POST["nombreAnt"], ENT_QUOTES);

	if($nombre == $nombreAnt) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	$sql = "select COUNT(id) as n from condicion where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un condición con ese nombre&&error";
		exit;
	}

	pg_query($sigpa, "begin");

	$sql = "update condicion set nombre='$nombre' where nombre='$nombreAnt'";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la condición <strong>$nombreAnt</strong> por <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);
		
		echo "Se modificó satisfactóriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

	else {
		echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.&&error";
		pg_query($sigpa, "rollback");
	}
?>