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

	$ecs = htmlspecialchars($_POST["ecs"], ENT_QUOTES);

	$sql = "select count(id) as n from \"estructuraCS\" where id='$ecs'";
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
		where p.id='$idAnt' and p.\"idECS\"='$ecs' 
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
		$sql = "select COUNT(id) as n from periodo where id='$id' and tipo='p' and \"idECS\"='$ecs'";
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


	$sql = "update periodo set id='$id', \"fechaInicio\"='$fechaInicioP', \"fechaFin\"='$fechaFinP' where id='$periodoAnt->id' and tipo='p' and \"idECS\"='$ecs'";
	$exe = pg_query($sigpa, $sql);

	$sql2 = "update periodo set id='$id', \"fechaInicio\"='$fechaInicioA', \"fechaFin\"='$fechaFinA' where id='$periodoAnt->id' and tipo='a' and \"idECS\"='$ecs'";
	$exe = pg_query($sigpa, $sql2);

// Si se avanzó el periodo correctamente

	if($exe) {
		$sql3 = "
			select c.id as \"idCarrera\", c.nombre as carrera, s.id as \"idSede\", s.nombre as sede, e.id as \"idEstructura\", e.nombre as estructura 
			from \"estructuraCS\" as ecs
				join estructura as e on e.id=ecs.\"idEstructura\"
				join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			where ecs.id='$ecs'
		";
		$exe = pg_query($sigpa, $sql3);
		$ECS = pg_fetch_object($exe);

		$sql3 = "select * from estructura where id='$ECS->idEstructura'";
		$exe = pg_query($sigpa, $sql3);
		$estructura = pg_fetch_object($exe);
		$estructura = json_decode($estructura->estructura);

		foreach($estructura->periodos as $periodoEst) {
			if(! $periodoEst->subperiodos)
				$periodosEstructura[] = $periodoEst->id;

			else {
				foreach($periodoEst->subperiodos as $subperiodo)
					$periodosEstructura[] = "$periodoEst->id-$subperiodo->id";
			}
		}

		$sql3 = "
			select *
			from seccion as sec
			where sec.\"idPeriodo\" = (select \"ID\" from periodo where id='$id' and tipo='a' and \"idECS\"='$ecs')
		";
		$exe = pg_query($sigpa, $sql3);

		while($seccion = pg_fetch_object($exe)) {
			$siguienteSec = array_search($seccion->periodoEstructura, $periodosEstructura) + 1;

			if((! array_key_exists($siguienteSec, $periodosEstructura)) || ($seccion->id == "R")) {
				$sql3 = "delete from seccion where \"ID\" = $seccion->ID";
				$exe2 = pg_query($sigpa, $sql3);
				continue;
			}

			$sql3 = "
				select c.id as id, ucm.periodo as periodo, c.\"idUC\" as \"idUC\"
				from carga as c
					join \"ucMalla\" as ucm on ucm.\"idUC\" = c.\"idUC\"
				where c.\"idSeccion\" = $seccion->ID and ucm.periodo = '$seccion->periodoEstructura' and ucm.\"idMalla\" = (select \"idMalla\" from \"mallaECS\" where id = $seccion->idMECS)
			";
			$exe2 = pg_query($sigpa, $sql3);

			while($carga = pg_fetch_object($exe2)) {
				$siguienteUC = array_search($carga->periodo, $periodosEstructura) + 1;

				if(! array_key_exists($siguienteUC, $periodosEstructura)) {
					$sql3 = "delete from carga where id = $carga->id";
					$exe3 = pg_query($sigpa, $sql3);
					continue;
				}
				
				$borrar[$carga->id] = "select id from \"ucMalla\" where \"idUC\" = '$carga->idUC' and \"idMalla\" = (select \"idMalla\" from \"mallaECS\" where id = $seccion->idMECS) and periodo = '" . $periodosEstructura[$siguienteUC] . "'";
			}

			$actualizar[] = "update seccion set \"periodoEstructura\" = '" . $periodosEstructura[$siguienteSec] . "' where \"ID\" = $seccion->ID";
		}

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se avanzó el periodo <strong>$idAnt</strong> a <strong>$id</strong> de <strong>$ECS->carrera - $ECS->sede ($ECS->estructura)</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "\n\n" . htmlspecialchars($sql2, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se avanzó satisfactoriamente&&success";
		pg_query($sigpa, "commit");

		foreach ($borrar as $idCarga => $sql3) {
			$exe3 = pg_query($sigpa, $sql3);
			$nUC = pg_fetch_object($exe3);

			if(! $nUC->id)
				$borrar2[] = "delete from carga where id = $idCarga";
		}

		foreach ($borrar2 as $sql3)
			$exe3 = pg_query($sigpa, $sql3);

		foreach ($actualizar as $sql3)
			$exe3 = pg_query($sigpa, $sql3);

		exit;
	}

// --------------------

// Si ocurrio un error avanzando el periodo

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>