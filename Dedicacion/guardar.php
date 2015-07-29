<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);
	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);
	$horas = htmlspecialchars($_POST["horas"], ENT_QUOTES);

	pg_query($sigpa, "begin");

	$sql = "insert into dedicacion values('$id', '$nombre', '$horas')";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la dedicación <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);
		
		echo "Se guardó satisfactóriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

	else {
		$sql = "select COUNT(id) as n from dedicacion where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n)
			echo "Ya existe una dedicación con ese nombre";

		else
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.";
	}

	echo "&&error";
	pg_query($sigpa, "rollback");
?>