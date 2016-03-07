<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ0-9\-]+$";

	if(! ereg("$re", $_POST["id"])) {
		echo "El código indicado no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];

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

	if(! isset($_POST["carrera"])) {
		echo "Debe seleccionar al menos una carrera";
		exit;
	}

	foreach($_POST["carrera"] as $carrera) {
		$carrera = htmlspecialchars($carrera, ENT_QUOTES);

		$sql = "select count(id) as n from \"estructuraCS\" where id='$carrera'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);

		if(! $n->n) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		if($fechaFinP > date("Y-m-d")) {
			$sql = "select COUNT(id) as n from periodo where \"idECS\"='$carrera' and \"fechaFin\">current_date";
			$exe = pg_query($sigpa, $sql);
			$n = pg_fetch_object($exe);
			$n = $n->n;

			if($n) {
				$sql = "
					select c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
					from \"estructuraCS\" as ecs 
						join estructura as e on e.id=ecs.\"idEstructura\"
						join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
						join carrera as c on c.id=cs.\"idCarrera\" 
						join sede as s on s.id=cs.\"idSede\" 
					where ecs.id='$carrera'
				";
				$exe = pg_query($sigpa, $sql);
				$ecs = pg_fetch_object($exe);

				echo "Ya existe un periodo activo para $ecs->carrera - $ecs->sede ($ecs->estructura)";
				exit;
			}

		}

		$sql = "select COUNT(id) as n from periodo where id='$id' and tipo='p' and \"idECS\"='$carrera'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			$sql = "
				select c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
				from \"estructuraCS\" as ecs
					join estructura as e on e.id=ecs.\"idEstructura\"
					join \"carreraSede\" as cs cs.id=ecs.\"idCS\"
					join carrera as c on c.id=cs.\"idCarrera\" 
					join sede as s on s.id=cs.\"idSede\" 
				where ecs.id='$carrera'
			";
			$exe = pg_query($sigpa, $sql);
			$ecs = pg_fetch_object($exe);

			echo "Existe un periodo antiguo de $ecs->carrera - $ecs->sede ($ecs->estructura) con ese código";
			exit;
		}
	}

	$carreras = $_POST["carrera"];

// --------------------

	pg_query($sigpa, "begin");

	$n = 0;

	foreach($carreras as $carrera) {
		$sql = "insert into periodo values(default, '$id', '$fechaInicioP', '$fechaFinP', 'p', '$carrera')";
		$exe = pg_query($sigpa, $sql);

		$sql2 = "insert into periodo values(default, '$id', '$fechaInicioA', '$fechaFinA', 'a', '$carrera')";
		$exe = pg_query($sigpa, $sql2);

// Si se guardo el príodo correctamente

		if($exe) {

	// Agregar elemento al registro de acciones realizadas

			$sql = "insert into historial values('" . (time() + $n) . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se activó el nuevo periodo <strong>$id</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "\n\n" . htmlspecialchars($sql2, ENT_QUOTES) . "')";
			$exe = pg_query($sigpa, $sql);

	// --------------------

		}

// --------------------

// Si ocurrio un error guardando el periodo

		else {
			echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
			pg_query($sigpa, "rollback");
		}

		++$n;
	}

// --------------------

	echo "Se guardó satisfactoriamente&&success";
	pg_query($sigpa, "commit");
	exit;
?>