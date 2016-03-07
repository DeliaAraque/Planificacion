<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ0-9\-]+$";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["id"]))) {
		echo "El código indicado no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fechaInicioP"])) {
		echo "La fecha de inicio del periodo de planificación no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fechaInicioP"]);
	$fechaInicioP = "$fecha[2]-$fecha[1]-$fecha[0]";

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fechaFinP"])) {
		echo "La fecha de fin del periodo de planificación no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fechaFinP"]);
	$fechaFinP = "$fecha[2]-$fecha[1]-$fecha[0]";

	if($fechaInicioP > $fechaFinP) {
		echo "La fecha de inicio del periodo de planificación no puede ser mayor a su fecha de fin";
		exit;
	}

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fechaInicioA"])) {
		echo "La fecha de inicio del periodo académico no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fechaInicioA"]);
	$fechaInicioA = "$fecha[2]-$fecha[1]-$fecha[0]";

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fechaFinA"])) {
		echo "La fecha de fin del periodo académico no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fechaFinA"]);
	$fechaFinA = "$fecha[2]-$fecha[1]-$fecha[0]";

	if($fechaInicioA > $fechaFinA) {
		echo "La fecha de inicio del periodo académico no puede ser mayor a su fecha de fin";
		exit;
	}

	$carrera = htmlspecialchars($_POST["ecs"], ENT_QUOTES);

	$sql = "select count(id) as n from \"estructuraCS\" where id='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);

	if(! $n->n) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$sql = "
		select p.id as id, p.\"fechaInicio\" as \"fechaInicio\", p.\"fechaFin\" as \"fechaFin\", p.\"idECS\" \"idECS\", c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where p.id='$idAnt' and p.\"idECS\"='$carrera' 
		order by p.tipo
	";
	$exe = pg_query($sigpa, $sql);
	$periodoAnt = pg_fetch_object($exe);

	$fechaInicioPAnt = $periodoAnt->fechaInicio;
	$fechaFinPAnt = $periodoAnt->fechaFin;

	$periodoAnt = pg_fetch_object($exe);

	if(($id == $periodoAnt->id) && ($fechaInicioP == $fechaInicioPAnt) && ($fechaFinP == $fechaFinPAnt) && ($fechaInicioA == $periodoAnt->fechaInicio) && ($fechaFinA == $periodoAnt->fechaFin)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

	if($id != $periodoAnt->id) {
		$sql = "select COUNT(id) as n from periodo where id='$id' and tipo='p' and \"idECS\"='$carrera'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Existe un periodo antiguo de con ese código para esta carrera";
			exit;
		}
	}

// --------------------

	pg_query($sigpa, "begin");


	$sql = "update periodo set id='$id', \"fechaInicio\"='$fechaInicioP', \"fechaFin\"='$fechaFinP' where id='$periodoAnt->id' and tipo='p' and \"idECS\"='$carrera'";
	$exe = pg_query($sigpa, $sql);

	$sql2 = "update periodo set id='$id', \"fechaInicio\"='$fechaInicioA', \"fechaFin\"='$fechaFinA' where id='$periodoAnt->id' and tipo='a' and \"idECS\"='$carrera'";
	$exe = pg_query($sigpa, $sql2);

// Si se modificó el príodo correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql3 = "
			select c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
			from \"estructuraCS\" as ecs
				join estructura as e on e.id=ecs.\"idEstructura\"
				join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			where ecs.id='$carrera'
		";
		$exe = pg_query($sigpa, $sql3);
		$ecs = pg_fetch_object($exe);

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó el periodo <strong>$id</strong> de <strong>$ecs->carrera - $ecs->sede ($ecs->estructura)</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "\n\n" . htmlspecialchars($sql2, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error modificando el periodo

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>