<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);
	$fecha = htmlspecialchars($_POST["fecha"], ENT_QUOTES);
	$telefono = htmlspecialchars($_POST["telefono"], ENT_QUOTES);
	$direccion = htmlspecialchars($_POST["direccion"], ENT_QUOTES);

	if($fecha > date("Y-m-d")) {
		echo "La fecha de inauguración no puede ser mayor a la actual&&error";
		exit;
	}

	pg_query($sigpa, "begin");

	$sql = "insert into sede values(default, '$nombre', '$fecha', '$telefono', '$direccion')";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la sede <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);
		
		echo "Se guardó satisfactóriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

	else {
		$sql = "select COUNT(id) as n from sede where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n)
			echo "Ya existe una sede con ese nombre";

		else
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.";
	}

	echo "&&error";
	pg_query($sigpa, "rollback");
?>