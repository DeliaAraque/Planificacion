<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-Z](,[A-Z])*$";

	if(! ereg("$re", $_POST["id"])) {
		echo "La(s) sección(es) indicada(s) no cumple(n) con el patrón necesario";
		exit;
	}

	$secciones = explode(",", $_POST["id"]);

	if(($_POST["turno"] != "d") && ($_POST["turno"] != "n")) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$turno = $_POST["turno"];

	$re = "^[0-9]+(\.[0-9])*$";

	if(! ereg("$re", $_POST["multiplicador"])) {
		echo "El multiplicador no cumple con el patrón necesario";
		exit;
	}

	$multiplicador = $_POST["multiplicador"];

	$grupos = (! isset($_POST["grupos"])) ? "false" : "true";

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$sql = "
		select count(c.id) as n
		from periodo as p
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
		where p.tipo='a' and c.id='$carrera' 
		group by c.id
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n < 1) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$sede = htmlspecialchars($_POST["sede"], ENT_QUOTES);

	$sql = "
		select count( s.id) as n
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join sede as s on s.id=cs.\"idSede\" 
		where p.tipo='a' and cs.\"idCarrera\"='$carrera' and s.id='$sede' 
		group by s.id
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n < 1) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$periodo = htmlspecialchars($_POST["periodo"], ENT_QUOTES);

	$sql = "
		select count(p.id) as n
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
		where p.tipo='a' and cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' and p.id='$periodo'
		group by p.id
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n < 1) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$malla = htmlspecialchars($_POST["malla"], ENT_QUOTES);

	$sql = "
		select ecs.id as id, ecs.\"idEstructura\" as \"idEstructura\" 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join \"mallaECS\" as mecs on mecs.\"idECS\"=ecs.id and mecs.estado is true 
			join malla as m on m.id=mecs.\"idMalla\" 
		where p.id='$periodo' and p.tipo='a' and cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' and mecs.id='$malla'
		group by ecs.id, ecs.\"idEstructura\"
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
 
	if($n->id < 1) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$estructura = $n->idEstructura;

	$periodoEstructura = htmlspecialchars($_POST["periodoEstructura"], ENT_QUOTES);

	foreach($secciones as $id) {
		$sql = "select COUNT(id) as n from seccion where id='$id' and \"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select id from \"estructuraCS\" where \"idEstructura\"='$estructura' and \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'))) and \"periodoEstructura\"='$periodoEstructura'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una sección \"$id\" con esas caracteristicas";
			exit;
		}
	}

// --------------------

	pg_query($sigpa, "begin");

	$nSecciones = 0;

	foreach($secciones as $id) {
		$sql = "insert into seccion values(default, '$id', '$turno', '$multiplicador', $grupos, '$malla', (select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select id from \"estructuraCS\" where \"idEstructura\"='$estructura' and \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'))), '$periodoEstructura')";
		$exe = pg_query($sigpa, $sql);

// Si se guardó la sección correctamente

		if($exe) {

	// Agregar elemento al registro de acciones realizadas

			$sql2 = "select nombre from carrera where id='$carrera'";
			$exe = pg_query($sigpa, $sql2);
			$carrera2 = pg_fetch_object($exe);

			$sql = "insert into historial values('" . (time() + ++$nSecciones) . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se agregó la sección <strong>$id</strong> del <strong>$periodoEstructura</strong> en <strong>$carrera2->nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
			$exe = pg_query($sigpa, $sql);

	// --------------------

		}

// --------------------

// Si ocurrio un error guardando la sección

		else {
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
			pg_query($sigpa, "rollback");
			exit;
		}

// --------------------

	}

	echo "Se guardó satisfactoriamente&&success";
	pg_query($sigpa, "commit");
?>