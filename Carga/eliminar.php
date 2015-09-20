<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);

	$sql = "select COUNT(id) as n from carga where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n) {
		echo "No existe esa carga o ya fué eliminada";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "
		select per.apellido as \"apellidoProfesor\", per.nombre as \"nombreProfesor\", c.\"idProfesor\" as profesor, s.id as seccion, s.\"periodoEstructura\" as \"periodoEstructura\" 
		from carga as c 
			join persona as per on per.cedula=c.\"idProfesor\" 
			join seccion as s on s.\"ID\"=c.\"idSeccion\" 
		where c.id='$id'
	";
	$exe = pg_query($sigpa, $sql);
	$carga = pg_fetch_object($exe);

	$sql = "delete from carga where id='$id'";
	$exe = pg_query($sigpa, $sql);

// Si se eliminó la carga correctamente


	// Agregar elemento al registro de acciones realizadas

	$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se desasignó la sección <strong>$carga->seccion</strong> del <strong>$carga->periodoEstructura</strong> del profesor <strong>$carga->apellidoProfesor $carga->nombreProfesor ($carga->profesor)</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
	$exe = pg_query($sigpa, $sql);

	// --------------------

	echo "Se desasignó la sección <strong>$carga->seccion</strong> del <strong>$carga->periodoEstructura</strong> del profesor <strong>$carga->apellidoProfesor $carga->nombreProfesor ($carga->profesor)</strong>&&success";
	pg_query($sigpa, "commit");
	exit;

// --------------------

// Si ocurrio un error eliminando la carga

	echo "Ocurrió un error mientras el servidor intentaba eliminar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>