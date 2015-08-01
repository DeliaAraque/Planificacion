<?php
	require "../../../script/verifSesion.php";
	require "../../../lib/conexion.php";

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);
	$idAnt = htmlspecialchars($_POST["idAnt"], ENT_QUOTES);
	$nombre = htmlspecialchars($_POST["nombre"], ENT_QUOTES);

	$sql = "select * from categoria where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);
	$categoriaAnt = pg_fetch_object($exe);

	if(($id == $idAnt) && ($nombre == $categoriaAnt->nombre)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	$sql = "select COUNT(id) as n from categoria where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un categoría con esa abreviatura&&error";
		exit;
	}
	/* 
	$sql = "select COUNT(id) as n from categoria where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un categoría con es nombre&&error";
		exit;
	}
	*/

	pg_query($sigpa, "begin");

	$sql = "update categoria set id='$id', nombre='$nombre' where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la categoría <strong>$idAnt</strong> por <strong>$id</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
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