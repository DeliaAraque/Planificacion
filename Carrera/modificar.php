<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["idAnt"]))) {
		echo "El código indicado no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	if($id != $idAnt) {
		$sql = "select COUNT(id) as n from carrera where id='$id'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una carrera con ese código";
			exit;
		}
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if((! ereg("$re", $_POST["nombre"])) || (! ereg("$re", $_POST["nombreAnt"]))) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];
	$nombreAnt = $_POST["nombreAnt"];

	if($nombre != $nombreAnt) {
		$sql = "select COUNT(id) as n from carrera where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una carrera con ese nombre";
			exit;
		}
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

		foreach($_POST["estructura$sede"] as $estructura) {
			$estructura = htmlspecialchars($estructura, ENT_QUOTES);

			$sql = "select id from estructura where id='$estructura'";
			$exe = pg_query($sigpa, $sql);
			$estructuraCheck = pg_fetch_object($exe);

			if(!$estructuraCheck->id) {
				echo "Por aquí <strong>NO</strong> pasan inyecciones! :B $sede";
				exit;
			}

			$estructuras["estructura$sede"][] = $estructura;
		}

		$coordinador = htmlspecialchars($_POST["coordinador$sede"], ENT_QUOTES);

		$sql = "select cedula from profesor where cedula='$coordinador'";
		$exe = pg_query($sigpa, $sql);
		$coordinador = pg_fetch_object($exe);

		if(!$coordinador->cedula) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		$coordinadores[] = htmlspecialchars($_POST["coordinador$sede"], ENT_QUOTES);
	}

	$sede = $_POST["sede"];

	$coordinadorInst = ($_POST["coordinadorInst"]) ? htmlspecialchars($_POST["coordinadorInst"], ENT_QUOTES) : "null";

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update carrera set id='$id', nombre='$nombre', \"idArea\"='$area', \"idCoordinadorInst\" = $coordinadorInst where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la carrera correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la carrera <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		$whereCS = "where \"idCarrera\"='$id'";  // where para eliminar las sedes no seleccionadas de la carrera
		$n = 0;

		foreach($sede as $idSede) {
			$whereCS .= " and \"idSede\"!='$idSede'";

	// Verificar si la sede ya esta asignada a la carrera

			$sql = "select id from \"carreraSede\" where \"idCarrera\"='$id' and \"idSede\"='$idSede'";
			$exe = pg_query($sigpa, $sql);

			$idCS = pg_fetch_object($exe);

		// Asignar la sede a la carrera si no lo está

			if(! $idCS->id) {
				$sql = "insert into \"carreraSede\" values(default, '$id', '$coordinadores[$n]', '$idSede') returning id";
				$exe = pg_query($sigpa, $sql);

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

				$idCS = pg_fetch_object($exe);
			}

		// --------------------

		// Modificar los datos si existen

			else {
				$sql = "update \"carreraSede\" set \"idCoordinador\"='$coordinadores[$n]' where id='$idCS->id'";
				$exe = pg_query($sigpa, $sql);

				if(!$exe) {
					$sql = "select nombre from sede where id='$idSede'";
					$exe = pg_query($sigpa, $sql);
					$sede = pg_fetch_object($exe);

					echo "Ocurrió un error modificando los datos en la sede $sede->nombre&&error";
					pg_query($sigpa, "rollback");
					exit;
				}
			}

		// --------------------

			$sql = "select count(\"idCS\") as n from pertenece where \"idCS\"='$idCS->id' and \"idProfesor\"='$coordinadores[$n]'";
			$exe = pg_query($sigpa, $sql);
			$nPer = pg_fetch_object($exe);

			if(! $nPer->n) {
				$sql = "insert into pertenece values('$idCS->id', '$coordinadores[$n]')";
				$exe = pg_query($sigpa, $sql);
			}

	// --------------------

			$whereECS = "where \"idCS\"='$idCS->id'";  // where para eliminar las estructuras no seleccionadas de la sede

			foreach($estructuras["estructura$idSede"] as $estructura) {
				$whereECS .= " and \"idEstructura\"!='$estructura'";

	// Verificar si la estructura ya esta asignada a la sede

				$sql = "select id from \"estructuraCS\" where \"idCS\"='$idCS->id' and \"idEstructura\"='$estructura'";
				$exe = pg_query($sigpa, $sql);

				$idECS = pg_fetch_object($exe);

		// Asignar la estructura según la sede de la carrera

				if(! $idECS->id) {
					$sql = "insert into \"estructuraCS\" values(default, '$idCS->id', '$estructura')";
					$exe = pg_query($sigpa, $sql);

			// Si ocurrió un error asignando la estructura

					if(!$exe) {
						$sql = "select nombre from sede where id='$idSede'";
						$exe = pg_query($sigpa, $sql);
						$sede = pg_fetch_object($exe);

						$sql = "select nombre from estructura where id='$estructura'";
						$exe = pg_query($sigpa, $sql);
						$estructura = pg_fetch_object($exe);

						echo "Ocurrió un error asignando la estructura $estructura->nombre a $nombre en la sede $sede->nombre&&error";
						pg_query($sigpa, "rollback");
						exit;
					}
				}

			// --------------------

		// --------------------

	// --------------------

			}

			$sql = "delete from \"estructuraCS\" $whereECS";
			$exe = pg_query($sigpa, $sql);

			++$n;
		}

		$sql = "delete from \"carreraSede\" $whereCS";
		$exe = pg_query($sigpa, $sql);

		$sql = "update usuario set nivel = 4 where nivel = 3";
		$exe = pg_query($sigpa, $sql);

		$sql = "select \"idCoordinador\" as cedula from \"carreraSede\"";
		$exe = pg_query($sigpa, $sql);

		while($coordinador = pg_fetch_object($exe)) {
			$sql = "update usuario set nivel = 3 where cedula = $coordinador->cedula";
			$exe2 = pg_query($sigpa, $sql);
		}

		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrió un error modificando la carrera

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>