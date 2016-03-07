<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$";

	if((! ereg("$re", $_POST["id"])) || (! ereg("$re", $_POST["idAnt"]))) {
		echo "El código no cumple con el patrón necesario";
		exit;
	}

	$id = $_POST["id"];
	$idAnt = $_POST["idAnt"];

	if($id != $idAnt) {
		$sql = "select COUNT(id) as n from malla where id='$id'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una malla con ese código";
			exit;
		}
	}

	$re = "^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$";

	if(! ereg("$re", $_POST["fecha"])) {
		echo "La fecha no cumple con el patrón necesario";
		exit;
	}

	$fecha = explode("/", $_POST["fecha"]);
	$fecha = "$fecha[2]-$fecha[1]-$fecha[0]";

	if($fecha > date("Y-m-d")) {
		echo "La fecha de creación no puede ser mayor a la actual";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$sql = "select id from carrera where id='$carrera'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);

	if(!$carrera->id) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);

	$estructura = htmlspecialchars($_POST["estructura"], ENT_QUOTES);

	$sql = "
		select count(ecs.id) as n 
		from \"carreraSede\" as cs 
			join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id and \"idEstructura\"='$estructura'
		where cs.\"idCarrera\"='$carrera'
	";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
 
	if(!$estructura->n) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$estructura = htmlspecialchars($_POST["estructura"], ENT_QUOTES);

	if(! count($_POST["sede"])) {
		echo "Debe seleccionar al menos una sede";
		exit;
	}

	foreach($_POST["sede"] as $sede) {
		$sede = htmlspecialchars($sede, ENT_QUOTES);

		$sql = "select count(id) as n from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'";
		$exe = pg_query($sigpa, $sql);
		$sede = pg_fetch_object($exe);

		if(!$sede->n) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}
	}

	$sedes = $_POST["sede"];

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["cantidad"])) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
		exit;
	}

	$cantidad = $_POST["cantidad"];

	for($i = 0; $i < $cantidad; ++$i) {
		$unidadCurricular = htmlspecialchars($_POST["unidadCurricular$i"], ENT_QUOTES);

		if(! $unidadCurricular)
			continue;

		$sql = "select count(id) as n from \"unidadCurricular\" where id='$unidadCurricular'";
		$exe = pg_query($sigpa, $sql);
		$unidadCurricular = pg_fetch_object($exe);

		if(! $unidadCurricular->n) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		$unidadesCurriculares[] = htmlspecialchars($_POST["unidadCurricular$i"], ENT_QUOTES);

		$tipos[] = (! isset($_POST["tipo$i"])) ? "false" : "true";

		$re = "^[1-9]$";

		if(! ereg("$re", $_POST["ht$i"])) {
			echo "Las horas teóricas de la unidad curricular <strong>" . $_POST["unidadCurricular$i"] . "</strong> no cumplen con el patrón necesario";
			exit;
		}

		$horasTeoricas[] = $_POST["ht$i"];

		$re = "^[0-9]$";

		if(! ereg("$re", $_POST["hp$i"])) {
			echo "Las horas prácticas de la unidad curricular <strong>" . $_POST["unidadCurricular$i"] . "</strong> no cumplen con el patrón necesario";
			exit;
		}

		$horasPracticas[] = $_POST["hp$i"];

		$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)?$";

		if(! ereg("$re", $_POST["periodo$i"])) {
			echo "Por aquí <strong>NO</strong> pasan inyecciones! :B";
			exit;
		}

		$periodos[] = $_POST["periodo$i"];
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update malla set id='$id', fecha='$fecha' where id='$idAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la malla correctamente

	if($exe) {
		$malla = pg_fetch_object($exe);

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la malla <strong>$id</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		$whereMECS = "where \"idMalla\"='$id'";  // where para desasignar las sedes no seleccionadas de la malla

		foreach($sedes as $sede) {
			$sql = "
				select count(ecs.id) as n 
				from \"carreraSede\" as cs 
					join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id and \"idEstructura\"='$estructura' 
				where cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' 
			";
			$exe = pg_query($sigpa, $sql);
			$estructuraCS = pg_fetch_object($exe);

			if($estructuraCS->n == 0) {
				$sql = "
					insert into \"estructuraCS\" 
					values(default, (
						select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede'
					), '$estructura')
				";
				$exe = pg_query($sigpa, $sql);

				if(! $exe) {
					$sql = "select nombre from estructura where id='$estructura'";
					$exe = pg_query($sigpa, $sql);
					$estructura = pg_fetch_object($exe);

					$sql = "select nombre from sede where id='$sede'";
					$exe = pg_query($sigpa, $sql);
					$sede = pg_fetch_object($exe);

					echo "Ocurrio un error asignando la estructura <strong>$estructura->nombre</strong> a la sede <strong>$sede->nombre</strong>&&error";
					pg_query($sigpa, "rollback");
					exit;
				}
			}

			$whereMECS .= "
				 and \"idECS\"!=(
					select ecs.id as \"idECS\" 
					from carrera as c 
						join sede as s on s.id='$sede' 
						join \"carreraSede\" as cs on cs.\"idCarrera\"=c.id and cs.\"idSede\"=s.id 
						join estructura as e on e.id='$estructura' 
						join \"estructuraCS\" as ecs on ecs.\"idEstructura\"=e.id and ecs.\"idCS\"=cs.id 
					where c.id='$carrera'
				)
			";

			$sql = "
				select count(id) as n 
				from \"mallaECS\" 
				where \"idMalla\"='$id' and \"idECS\"=(
						select ecs.id as \"idECS\" 
						from carrera as c 
							join sede as s on s.id='$sede' 
							join \"carreraSede\" as cs on cs.\"idCarrera\"=c.id and cs.\"idSede\"=s.id 
							join estructura as e on e.id='$estructura' 
							join \"estructuraCS\" as ecs on ecs.\"idEstructura\"=e.id and ecs.\"idCS\"=cs.id 
						where c.id='$carrera'
					)
			";
			$exe = pg_query($sigpa, $sql);
			$nMECS = pg_fetch_object($exe);
			$nMECS = $nMECS->n;

			if($nMECS == 0) {
				$sql = "
					insert into \"mallaECS\" 
					values(default, true, (
						select ecs.id 
						from \"carreraSede\" as cs 
							join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id and \"idEstructura\"='$estructura' 
						where cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede'
					), '$id')
				";
				$exe = pg_query($sigpa, $sql);
			}
		}

		$sql = "delete from \"mallaECS\" $whereMECS";
		$exe = pg_query($sigpa, $sql);

	// Asignación de las unidades curriculares a la malla

		$whereUCM = "where \"idMalla\"='$id'";  // where para desasignar las unidades curriculares no seleccionadas de la malla

		for($i = 0; $i < count($unidadesCurriculares); ++$i) {
			$sql = "select id from \"ucMalla\" where \"idMalla\"='$id' and \"idUC\"='$unidadesCurriculares[$i]' and periodo='$periodos[$i]'";
			$exe = pg_query($sigpa, $sql);
			$UCM = pg_fetch_object($exe);

			if(! $UCM->id) {
				$sql = "insert into \"ucMalla\" values(default, '$horasTeoricas[$i]', '$horasPracticas[$i]', $tipos[$i], '$periodos[$i]', '$unidadesCurriculares[$i]', '$id') returning id";
				$exe = pg_query($sigpa, $sql);

				if(! $exe) {
					$sql = "select nombre from \"unidadCurricular\" where id='$unidadesCurriculares[$i]'";
					$exe = pg_query($sigpa, $sql);
					$unidadCurricular = pg_fetch_object($exe);

					echo "Ocurrió un error mientras se intentaba asignar la unidad curricular <strong>$unidadCurricular->nombre</strong> a la malla, por favor comuníquelo al administrador del sistema&&error";
					pg_query($sigpa, "rollback");
					exit;
				}

				$UCM = pg_fetch_object($exe);
				$whereUCM .= " and id!='$UCM->id'";

				continue;
			}

			$whereUCM .= " and id!='$UCM->id'";

			$sql = "update \"ucMalla\" set \"horasTeoricas\"='$horasTeoricas[$i]', \"horasPracticas\"='$horasPracticas[$i]', tipo=$tipos[$i] where \"idMalla\"='$id' and \"idUC\"='$unidadesCurriculares[$i]' and periodo='$periodos[$i]'";
			$exe = pg_query($sigpa, $sql);
		}

		$sql = "delete from \"ucMalla\" $whereUCM";
		$exe = pg_query($sigpa, $sql);

	// --------------------

		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error modificando la malla

	echo "Ocurrió un error mientras el servidor intentaba modificar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>