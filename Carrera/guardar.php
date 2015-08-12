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

	$sql = "select COUNT(id) as n from carrera where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una carrera con ese código";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	$sql = "select COUNT(nombre) as n from carrera where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe una carrera con ese nombre";
		exit;
	}

	$area = htmlspecialchars($_POST["area"], ENT_QUOTES);

	$sql = "select id from area where id='$area'";
	$exe = pg_query($sigpa, $sql);
	$area = pg_fetch_object($exe);

	if(!$area->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$area = htmlspecialchars($_POST["area"], ENT_QUOTES);

	if(! $_POST["sede"]) {
		echo "Debe seleccionar al menos una sede";
		exit;
	}

	foreach($_POST["sede"] as $sede) {
		$sede = htmlspecialchars($sede, ENT_QUOTES);

		$sql = "select id, nombre from sede where id='$sede'";
		$exe = pg_query($sigpa, $sql);
		$sedeCheck = pg_fetch_object($exe);

		if(!$sedeCheck->id) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		if(! $_POST["estructura$sede"]) {
			echo "Debe seleccionar al menos una estructura en la sede $sedeCheck->nombre";
			exit;
		}

		$estructura = htmlspecialchars($_POST["estructura$sede"], ENT_QUOTES);

		$sql = "select id from estructura where id='$estructura'";
		$exe = pg_query($sigpa, $sql);
		$estructura = pg_fetch_object($exe);

		if(!$estructura->id) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		$estructuras[] = htmlspecialchars($_POST["estructura$sede"], ENT_QUOTES);
	}

	$sede = $_POST["sede"];

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into carrera values('$id', '$nombre', '$area')";
	$exe = pg_query($sigpa, $sql);

// Si se guardó la carrera correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la carrera <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		$n = 0;

		foreach($sede as $idSede) {

	// Asignar la sede a la carrera

			$sql = "insert into \"carreraSede\" values(default, '$id', '$idSede') returning id";
			$exe = pg_query($sigpa, $sql);

			$idCS = pg_fetch_object($exe);

		// Si ocurrió un error asignando la sede

			if(!$exe) {
				$sql = "select nombre from sede where id='$idSede'";
				$exe = pg_query($sigpa, $sql);
				$sede = pg_fetch_object($exe);

				echo "Ocurrió un error asignando la sede $sede->nombre&&error";
				pg_query($sigpa, "rollback");
				exit;
			}

		// --------------------

	// --------------------

	// Asignar la estructura según la sede de la carrera

			$sql = "insert into \"estructuraCS\" values(default, '$idCS->id', '$estructuras[$n]')";
			$exe = pg_query($sigpa, $sql);

		// Si ocurrió un error asignando la estructura

			if(!$exe) {
				$sql = "select nombre from sede where id='$idSede'";
				$exe = pg_query($sigpa, $sql);
				$sede = pg_fetch_object($exe);

				$sql = "select nombre from estructura where id='$estructuras[$n]'";
				$exe = pg_query($sigpa, $sql);
				$estructura = pg_fetch_object($exe);

				echo "Ocurrió un error asignando la estructura $estructura->nombre a $nombre en la sede $sede->nombre&&error";
				pg_query($sigpa, "rollback");
				exit;
			}

		// --------------------

			++$n;

	// --------------------

		}

		echo "Se guardó satisfactóriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrió un error guardando la carrera

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>