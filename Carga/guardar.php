<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$sql = "select id from carrera where id='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	if(! $carrera->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$sede = htmlspecialchars($_POST["sede"], ENT_QUOTES);

	$sql = "select id from sede where id='$sede'";
	$exe = pg_query($sigpa, $sql);
	$sede = pg_fetch_object($exe);

	if(! $sede->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$sede = htmlspecialchars($_POST["sede"], ENT_QUOTES);

	$unidadCurricular = htmlspecialchars($_POST["unidadCurricular"], ENT_QUOTES);

	$sql = "select id from \"unidadCurricular\" where id='$unidadCurricular'";
	$exe = pg_query($sigpa, $sql);
	$unidadCurricular = pg_fetch_object($exe);

	if(! $unidadCurricular->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$unidadCurricular = htmlspecialchars($_POST["unidadCurricular"], ENT_QUOTES);

	if($_POST["nombre"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

		if(! ereg("$re", $_POST["nombre"])) {
			echo "El nuevo nombre no cumple con el patrón necesario";
			exit;
		}

		$nombre = "'$_POST[nombre]'";
	}

	else
		$nombre = "null";

	$profesor = htmlspecialchars($_POST["profesor"], ENT_QUOTES);

	$sql = "select cedula from profesor where cedula='$profesor'";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);

	if(! $profesor->cedula) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$profesor = htmlspecialchars($_POST["profesor"], ENT_QUOTES);

	if(! $_POST["seccion"]) {
		echo "Debe seleccionar al menos una seccion";
		exit;
	}

	foreach($_POST["seccion"] as $seccion) {
		$seccion = htmlspecialchars($seccion, ENT_QUOTES);
		$seccionID = htmlspecialchars($_POST["ID$seccion"], ENT_QUOTES);

		$sql = "select count(id) as n from seccion where \"ID\"='$seccionID'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);

		if(! $n->n) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		if($_POST["ls"]) {
			if($_POST["suplente$seccion"]) {
				$suplente = htmlspecialchars($_POST["suplente$seccion"], ENT_QUOTES);

				$sql = "
					select count(p.cedula) as n 
					from persona as p 
						join profesor as prof on prof.cedula=p.cedula 
						join pertenece as per on per.\"idProfesor\"=prof.cedula 
					where prof.condicion='1' and per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede') and prof.cedula='$suplente'
				";
				$exe = pg_query($sigpa, $sql);
				$n = pg_fetch_object($exe);

				if(! $n->n) {
					echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
					exit;
				}
			}
		}
	}

	$secciones = $_POST["seccion"];

// --------------------

	pg_query($sigpa, "begin");

	$n = 0;

	foreach($secciones as $seccion) {
		$seccion = htmlspecialchars($seccion, ENT_QUOTES);
		$seccionID = htmlspecialchars($_POST["ID$seccion"], ENT_QUOTES);
		$dividirHT = (! isset($_POST["dividirHT$seccion"])) ? "false" : "true";
		$suplente = ($_POST["suplente$seccion"]) ? $_POST["suplente$seccion"] : "null";
		$horasComunes = htmlspecialchars($_POST["horasComunes$seccion"], ENT_QUOTES);

		if(! $horasComunes)
			$horasComunes =  0;

		$sql = "insert into carga values(default, $dividirHT, $nombre, '$profesor', '$seccionID', $suplente, '$unidadCurricular', $horasComunes)";
		$exe = pg_query($sigpa, $sql);

// Si ocurrio un error asignando la carga

		if(! $exe) {
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
			pg_query($sigpa, "rollback");
			exit;
		}

// --------------------

// Si se asignó la carga correctamente

	// Agregar elemento al registro de acciones realizadas

		else {
			$sql2 = "select apellido, nombre, cedula from persona where cedula='$profesor'";
			$exe = pg_query($sigpa, $sql2);
			$persona = pg_fetch_object($exe);

			$sql2 = "select \"periodoEstructura\" as \"periodoEstructura\" from seccion where \"ID\"='$seccionID'";
			$exe = pg_query($sigpa, $sql2);
			$periodoEstructura = pg_fetch_object($exe);
			$periodoEstructura = $periodoEstructura->periodoEstructura;

			$descripcion = "Se asignó la sección <strong>$seccion</strong> del <strong>$periodoEstructura</strong> al profesor <strong>$persona->apellido $persona->nombre ($persona->cedula)</strong>";

			if($suplente != "null") {
				$sql2 = "select apellido, nombre, cedula from persona where cedula='$suplente'";
				$exe = pg_query($sigpa, $sql2);
				$persona = pg_fetch_object($exe);

				$descripcion .= ". Suple <strong>$persona->apellido $persona->nombre ($persona->cedula)</strong>";
			}

			$sql = "insert into historial values('" . (time() + $n) . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', '$descripcion', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
			$exe = pg_query($sigpa, $sql);
		}

	// --------------------

// --------------------

		++$n;
	}

	echo "Se asignó la carga satisfactoriamente&&success";
	pg_query($sigpa, "commit");
?>