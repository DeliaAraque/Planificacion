<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-Z]$";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["idAnt"]))) {
		echo "La sección indicado no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	$ID = htmlspecialchars($_POST["ID"], ENT_QUOTES);

	$sql = "select count(\"ID\") as n from seccion where \"ID\"='$ID' and id='$idAnt'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n < 1) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

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

	$sql = "
		select sec.\"ID\" as \"ID\", p.id as periodo, sec.id as id, sec.turno as turno, sec.multiplicador as multiplicador, sec.grupos as grupos, c.id as carrera, s.id as sede, e.id as estructura, sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\" as malla 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\"
		where sec.\"ID\"='$ID' 
		order by p.id, sec.id, c.nombre, s.nombre, sec.\"periodoEstructura\"
	";
	$exe = pg_query($sigpa, $sql);
	$seccionAnt = pg_fetch_object($exe);
	$seccionAnt->grupos = ($seccionAnt->grupos == "f") ? "false" : "true";

	if(($id == $seccionAnt->id) && ($turno == $seccionAnt->turno) && ($multiplicador == $seccionAnt->multiplicador) && ($grupos == $seccionAnt->grupos) && ($carrera == $seccionAnt->carrera) && ($periodo == $seccionAnt->periodo) && ($sede == $seccionAnt->sede) && ($malla == $seccionAnt->malla) && ($periodoEstructura == $seccionAnt->periodoEstructura)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	if(($id != $seccionAnt->id) || ($periodo != $seccionAnt->periodo) || ($periodoEstructura != $seccionAnt->periodoEstructura)) {
		$sql = "select COUNT(id) as n from seccion where id='$id' and \"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select id from \"estructuraCS\" where \"idEstructura\"='$estructura' and \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'))) and \"periodoEstructura\"='$periodoEstructura'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una sección con esas caracteristicas";
			exit;
		}
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update seccion set id='$id', turno='$turno', multiplicador='$multiplicador', grupos=$grupos, \"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select id from \"estructuraCS\" where \"idEstructura\"='$estructura' and \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'))), \"periodoEstructura\"='$periodoEstructura' where \"ID\"='$ID'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la sección correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql2 = "select nombre from carrera where id='$carrera'";
		$exe = pg_query($sigpa, $sql2);
		$carrera = pg_fetch_object($exe);

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la sección <strong>$id</strong> del <strong>$periodoEstructura</strong> en <strong>$carrera->nombre</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error modificando la sección

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>