<?php
	require "../../../script/verifSesion.php";
	require "../../../lib/conexion.php";

	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);
	$cantidad = $_POST["cantidad"];

	if(!is_numeric($cantidad)) {
		echo "Por aqui no pasan inyecciones :B&&info";
		exit;
	}

	$sql = "select COUNT(id) as n from estructura where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un área con ese nombre&&error";
		exit;
	}

	$estructura = "
		{
			\"periodos\" : [
	";

	for($i = 0; $i <= $cantidad; ++$i) {
		$nombrePeriodo = htmlspecialchars($_POST["nombrePeriodo$i"], ENT_QUOTES);
		$idPeriodo = htmlspecialchars($_POST["idPeriodo$i"], ENT_QUOTES);
		$duracionPeriodo = $_POST["duracionPeriodo$i"];

		$estructura .= "
				{
					\"nombre\" : \"$nombrePeriodo\",
					\"id\" : \"$idPeriodo\",
		";

		if(is_numeric($duracionPeriodo)) {
			$estructura .= "
					\"duracion\" : \"$duracionPeriodo\",
					\"subperiodos\" : false
			";
		}

		else if($duracionPeriodo == "null") {
			$estructura .= "
					\"duracion\" : false,
					\"subperiodos\" : []
			";
		}

		$estructura .= "
				},
		";
	}

	$estructura .= "
			]
		}
	";

	$sql = "insert into estructura values(default, '$nombre', '$estructura')";
	//$exe = pg_query($sigpa, $sql);

	echo "<br/>$sql&&info";

/*
	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);

	pg_query($sigpa, "begin");

	$sql = "insert into area values(default, '$nombre')";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó el área <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);
		
		echo "Se guardó satisfactóriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

	else {
		$sql = "select COUNT(id) as n from area where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n)
			echo "Ya existe un área con ese nombre";

		else
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema.";
	}

	echo "&&error";
	pg_query($sigpa, "rollback");
*/
?>