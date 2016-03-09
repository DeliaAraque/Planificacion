<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[0-9]{7,}$";

	if(! ereg("$re", $_POST["cedula"])) {
		echo "El número de cédula no cumple con el patrón necesario";
		exit;
	}

	$cedula = $_POST["cedula"];

	$sql = "select COUNT(cedula) as n from profesor where cedula='$cedula'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Ya existe un profesor con esa cédula";
		exit;
	}

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	if($_POST["segundoNombre"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

		if(! ereg("$re", $_POST["segundoNombre"])) {
			echo "El segundo nombre no cumple con el patrón necesario";
			exit;
		}

		$segundoNombre = "'" . $_POST["segundoNombre"] . "'";
	}

	else
		$segundoNombre = "null";

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

	if(! ereg("$re", $_POST["apellido"])) {
		echo "El apellido indicado no cumple con el patrón necesario";
		exit;
	}

	$apellido = $_POST["apellido"];

	if($_POST["segundoApellido"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$";

		if(! ereg("$re", $_POST["segundoApellido"])) {
			echo "El segundo apellido no cumple con el patrón necesario";
			exit;
		}

		$segundoApellido = "'" . $_POST["segundoApellido"] . "'";
	}

	else
		$segundoApellido = "null";

	if(($_POST["sexo"] != "f") && ($_POST["sexo"] != "m")) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$sexo = $_POST["sexo"];

	$re = "^[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+(,[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+)*$";

	if(! ereg("$re", $_POST["correo"])) {
		echo "El correo electrónico indicado no cumple con el patrón necesario";
		exit;
	}

	$correo = $_POST["correo"];
	
	$direccion = htmlspecialchars($_POST["direccion"], ENT_QUOTES);

	$re = "^[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*$";

	if(! ereg("$re", $_POST["telefono"])) {
		echo "El número de teléfono móvil no cumple con el patrón necesario";
		exit;
	}

	$telefono = $_POST["telefono"];

	if($_POST["telefonoFijo"]) {
		$re = "^[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*$";

		if(! ereg("$re", $_POST["telefonoFijo"])) {
			echo "El número de teléfono fijo no cumple con el patrón necesario";
			exit;
		}

		$telefonoFijo = "'" . $_POST["telefonoFijo"] . "'";
	}

	else
		$telefonoFijo = "null";

	$profesion = htmlspecialchars($_POST["profesion"], ENT_QUOTES);
	$sql = "select id from profesion where id='$profesion'";
	$exe = pg_query($sigpa, $sql);
	$profesion = pg_fetch_object($exe);

	if(!$profesion->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$profesion = htmlspecialchars($_POST["profesion"], ENT_QUOTES);

	$categoria = htmlspecialchars($_POST["categoria"], ENT_QUOTES);
	$sql = "select id from categoria where id='$categoria'";
	$exe = pg_query($sigpa, $sql);
	$categoria = pg_fetch_object($exe);

	if(!$categoria->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$categoria = htmlspecialchars($_POST["categoria"], ENT_QUOTES);

	$dedicacion = htmlspecialchars($_POST["dedicacion"], ENT_QUOTES);
	$sql = "select id from dedicacion where id='$dedicacion'";
	$exe = pg_query($sigpa, $sql);
	$dedicacion = pg_fetch_object($exe);

	if(!$dedicacion->id) {
		echo "$sql Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$dedicacion = htmlspecialchars($_POST["dedicacion"], ENT_QUOTES);

	$condicion = htmlspecialchars($_POST["condicion"], ENT_QUOTES);
	$sql = "select id from condicion where id='$condicion'";
	$exe = pg_query($sigpa, $sql);
	$condicion = pg_fetch_object($exe);

	if(!$condicion->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$condicion = htmlspecialchars($_POST["condicion"], ENT_QUOTES);

	if(! isset($_SESSION["carrera"])) {
		if($_POST["carrera"]) {
			foreach($_POST["carrera"] as $carrera) {
				$carrera = htmlspecialchars($carrera, ENT_QUOTES);

				$sql = "select count(id) as n from \"carreraSede\" where id='$carrera'";
				$exe = pg_query($sigpa, $sql);
				$carrera = pg_fetch_object($exe);

				if(! $carrera->n) {
					echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
					exit;
				}
			}

			$carreras = $_POST["carrera"];
		}
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "select COUNT(cedula) as n from persona where cedula='$cedula'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if(! $n)
		$sql = "insert into persona values('$cedula', '$nombre', $segundoNombre, '$apellido', $segundoApellido, '$sexo', '$correo', '$direccion', '$telefono', $telefonoFijo)";

	else
		$sql = "update persona set nombre='$nombre', \"segundoNombre\"=$segundoNombre, apellido='$apellido', \"segundoApellido\"=$segundoApellido, sexo='$sexo', correo='$correo', direccion='$direccion', telefono='$telefono', \"telefonoFijo\"=$telefonoFijo where cedula='$cedula'";

	$exe = pg_query($sigpa, $sql);

// Si se guardo la persona correctamente o ya existe

	if($exe) {

	// Se guardan los datos del profesor

		$sql2 = "insert into profesor values('$cedula', '$categoria', '$condicion', '$dedicacion', '$profesion')";
		$exe = pg_query($sigpa, $sql2);

		if(! $exe) {
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
			pg_query($sigpa, "rollback");
		}

	// --------------------

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó al profesor <strong>$nombre $apellido ($cedula)</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "\n\n" . htmlspecialchars($sql2, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		$cedula2 = md5($cedula);

		$sql2 = "insert into usuario values('$cedula', '$cedula2', '$cedula2', null, 4)";
		$exe = pg_query($sigpa, $sql2);

	// Si el proceso lo hace un usuario con privilegios administrativos

		if(! isset($_SESSION["carrera"])) {
			if($_POST["carrera"]) {
				foreach($carreras as $carrera) {
					$sql = "insert into pertenece values('$carrera', '$cedula')";
					$exe = pg_query($sigpa, $sql);

					if(! $exe) {
						echo "Ocurrió un error mientras se asignaba la carrera al profesor, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
						pg_query($sigpa, "rollback");
					}
				}
			}
		}

	// --------------------

		echo "Se guardó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la persona

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>