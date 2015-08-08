<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	if($_POST["carrera"]) {
		$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);
		$sql = "select id from carrera where id='$carrera'";
		$exe = pg_query($sigpa, $sql);
		$carrera = pg_fetch_object($exe);

		if(!$carrera->id) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);
	}

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

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$";

	if(! ereg("$re", $_POST["nombre"])) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];

	if($_POST["segundoNombre"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$";

		if(! ereg("$re", $_POST["segundoNombre"])) {
			echo "El segundo nombre no cumple con el patrón necesario";
			exit;
		}

		$segundoNombre = "'" . $_POST["segundoNombre"] . "'";
	}

	else
		$segundoNombre = "null";

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$";

	if(! ereg("$re", $_POST["apellido"])) {
		echo "El apellido indicado no cumple con el patrón necesario";
		exit;
	}

	$apellido = $_POST["apellido"];

	if($_POST["segundoApellido"]) {
		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$";

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

	$re = "^[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+$";

	if(! ereg("$re", $_POST["correo"])) {
		echo "El correo electrónico indicado no cumple con el patrón necesario";
		exit;
	}

	$correo = $_POST["correo"];
	
	$direccion = htmlspecialchars($_POST["direccion"], ENT_QUOTES);

	$re = "^[0-9]{3,4}\-?[0-9]{7}$";

	if(! ereg("$re", $_POST["telefono"])) {
		echo "El número de teléfono móvil no cumple con el patrón necesario";
		exit;
	}

	$telefono = $_POST["telefono"];

	if($_POST["telefonoFijo"]) {
		$re = "^[0-9]{3,4}\-?[0-9]{7}$";

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
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
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

// --------------------
/*
	pg_query($sigpa, "begin");
*/

	$sql = "insert into persona values('$cedula', '$nombre', $segundoNombre, '$apellido', $segundoApellido, '$sexo', '$correo', '$direccion', '$telefono', $telefonoFijo)";
	echo "$sql <br/>";

	$sql = "insert into profesor values('$cedula', '$categoria', '$condicion', '$dedicacion', '$profesion')";
	echo "$sql <br/>";

/*
	$exe = pg_query($sigpa, $sql);

// Si se guardo la sede correctamente

	if($exe) {
		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la sede <strong>$nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);
		
		echo "Se guardó satisfactóriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error guardando la sede

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");
*/
// --------------------

?>