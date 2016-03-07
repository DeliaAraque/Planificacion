<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ0-9]+(\-[A-ZÁÉÍÓÚÑ0-9]+)*";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["idAnt"]))) {
		echo "El código indicado no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	if($id != $idAnt) {
		$sql = "select COUNT(id) as n from \"unidadCurricular\" where id='$id'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una unidad curricular con ese código";
			exit;
		}
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);
	$carreraAnt = htmlspecialchars($_POST["carreraAnt"], ENT_QUOTES);

	$sql = "select id from carrera where id='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	if(!$carrera->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$sql = "select id from carrera where id='$carreraAnt'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	if(!$carrera->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);
	$carreraAnt = htmlspecialchars($_POST["carreraAnt"], ENT_QUOTES);

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*,?( [a-záéíóúñA-ZÁÉÍÓÚÑ]+,?)*$";

	if((! ereg("$re", $_POST["nombre"])) || (! ereg("$re", $_POST["nombreAnt"]))) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];
	$nombreAnt = $_POST["nombreAnt"];

	$renombrable = (! isset($_POST["renombrable"])) ? "f" : "t";

	$eje = htmlspecialchars($_POST["eje"], ENT_QUOTES);

	$sql = "select id from eje where id='$eje'";
	$exe = pg_query($sigpa, $sql);
	$eje = pg_fetch_object($exe);

	if(!$eje->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$eje = htmlspecialchars($_POST["eje"], ENT_QUOTES);

	$sql = "select * from \"unidadCurricular\" where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);
	$ucAnt = pg_fetch_object($exe);

	if(($id == $ucAnt->id) && ($nombre == $ucAnt->nombre) && ($renombrable == $ucAnt->renombrable) && ($carrera == $ucAnt->idCarrera) && ($eje == $ucAnt->idEje)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	$renombrable = (! isset($_POST["renombrable"])) ? "false" : "true";

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update \"unidadCurricular\" set id='$id', nombre='$nombre', renombrable=$renombrable, \"idCarrera\"='$carrera', \"idEje\"='$eje' where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la unidad curricular

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la unidad curricular <strong>$nombreAnt</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";

		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrió un error modificando la unidad curricular

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>