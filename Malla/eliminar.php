<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$";

	if(! ereg("$re", $_POST["malla"])) {
		echo "El código de la malla no cumple con el patrón necesario";
		exit;
	}

	$malla = $_POST["malla"];

	$sql = "select COUNT(id) as n from malla where id='$malla'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n) {
		echo "No existe una malla con ese código";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if(! ereg("$re", $_POST["carrera"])) {
		echo "El nombre de la carrera no cumple con el patrón necesario";
		exit;
	}

	$carrera = $_POST["carrera"];

	$sql = "select COUNT(id) as n from carrera where nombre='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n) {
		echo "No existe una carrera con ese nombre";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if(! ereg("$re", $_POST["sede"])) {
		echo "El nombre de la sede no cumple con el patrón necesario";
		exit;
	}

	$sede = $_POST["sede"];

	$sql = "select COUNT(id) as n from sede where nombre='$sede'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n) {
		echo "No existe una sede con ese nombre";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if(! ereg("$re", $_POST["estructura"])) {
		echo "El nombre de la estructura no cumple con el patrón necesario";
		exit;
	}

	$estructura = $_POST["estructura"];

	$sql = "select COUNT(id) as n from estructura where nombre='$estructura'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n) {
		echo "No existe una estructura con ese nombre";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "select COUNT(id) as n from \"mallaECS\" where \"idMalla\"='$malla'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n > 1) {
		$sql = "
delete from \"mallaECS\" 
where \"idMalla\"='$malla' and 
	\"idECS\"=(
		select ecs.id as \"idECS\" 
		from carrera as c 
			join sede as s on s.nombre='$sede' 
			join \"carreraSede\" as cs on cs.\"idCarrera\"=c.id and cs.\"idSede\"=s.id 
			join estructura as e on e.nombre='$estructura' 
			join \"estructuraCS\" as ecs on ecs.\"idEstructura\"=e.id and ecs.\"idCS\"=cs.id 
		where c.nombre='$carrera'
	) 
returning id
		";
		$mensaje = "Se eliminó la malla <strong>$malla</strong> en la sede <strong>$sede</strong>";
	}

	else {
		$sql = "delete from malla where id='$malla' returning id";
		$mensaje = "Se eliminó la malla <strong>$malla</strong>";
	}

	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);

// Si se eliminó la malla correctamente

	if($n->id !== "") {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', '$mensaje', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se eliminó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error eliminando la malla

	echo "Ocurrió un error mientras el servidor intentaba eliminar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>