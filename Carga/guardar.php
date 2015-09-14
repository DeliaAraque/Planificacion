<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	if($_POST["nombre"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

		if(! ereg("$re", $_POST["nombre"])) {
			echo "La sección indicada no cumple con el patrón necesario";
			exit;
		}

		$nombre = $_POST["nombre"];
	}

	$profesor = htmlspecialchars($_POST["profesor"], ENT_QUOTES);

	$sql = "select cedula from profesor where cedula='$profesor'";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);

	if(!$profesor->cedula) {
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

		if($_POST["suplente$seccion"]) {
			$sql = "
				select count(p.cedula) as n 
				from persona as p 
					join profesor as prof on prof.cedula=p.cedula 
					join pertenece as per on per.\"idProfesor\"=prof.cedula
				where prof.condicion='$condicion' and per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede') and prof.cedula='$_POST[suplente$seccion]'
			";
			$exe = pg_query($sigpa, $sql);
			$n = pg_fetch_object($exe);

			if(! $n->n) {
				echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
				exit;
			}
		}
	}

	$seccion = $_POST["seccion"];

// --------------------

	pg_query($sigpa, "begin");

	$sql = "insert into seccion values(default, '$id', '$turno', '$multiplicador', $grupos, '$malla', (select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select id from \"estructuraCS\" where \"idEstructura\"='$estructura' and \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'))), '$periodoEstructura')";
	$exe = pg_query($sigpa, $sql);

// Si se guardó la sección correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql2 = "select nombre from carrera where id='$carrera'";
		$exe = pg_query($sigpa, $sql2);
		$carrera = pg_fetch_object($exe);

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la sección <strong>$id</strong> del <strong>$periodoEstructura</strong> en <strong>$carrera->nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se guardó satisfactóriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la sección

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>