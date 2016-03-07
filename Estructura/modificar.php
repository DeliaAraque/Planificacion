<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

// Validación

	$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$";

	if((! ereg("$re", $_POST["nombre"])) || (! ereg("$re", $_POST["nombreAnt"]))) {
		echo "El nombre indicado no cumple con el patrón necesario";
		exit;
	}

	$nombre = $_POST["nombre"];
	$nombreAnt = $_POST["nombreAnt"];

	$sql = "
		select COUNT(p.id) as n 
		from estructura as e
			join \"estructuraCS\" as ecs on ecs.\"idEstructura\"=e.id 
			join periodo as p on p.\"idECS\"=ecs.id 
		where e.nombre='$nombreAnt'
	";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n) {
		echo "Esta estructura no puede ser modificada porque es/fue usada en algún período académico";
		exit;
	}

	if($nombre != $nombreAnt) {
		$sql = "select COUNT(id) as n from estructura where nombre='$nombre'";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);
		$n = $n->n;

		if($n) {
			echo "Ya existe una estructura con ese nombre";
			exit;
		}
	}

	if(!is_numeric($_POST["cantidad"])) {
		echo "Por aquí <strong>NO</strong> pasan inyecciones :B&&info";
		exit;
	}

	$cantidad = $_POST["cantidad"];

// --------------------

// Creación del esqueleto de la estructura con JSON

	$estructura = "
		{
			\"periodos\" : [
	";

	for($i = 0; $i <= $cantidad; ++$i) {

	// Validación

		$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$";

		if(! ereg("$re", $_POST["nombrePeriodo$i"])) {
			echo "El nombre del periodo" . ($i + 1) . " no cumple con el patrón necesario";
			exit;
		}

		$nombrePeriodo = $_POST["nombrePeriodo$i"];

		$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

		if(! ereg("$re", $_POST["idPeriodo$i"])) {
			echo "El identificador del periodo <strong>$nombrePeriodo</strong> no cumple con el patrón necesario";
			exit;
		}

		$idPeriodo = $_POST["idPeriodo$i"];

		$re = "^([0-9]+$|false)";

		if(! ereg("$re", $_POST["duracionPeriodo$i"])) {
			echo "La duración del periodo <strong>$nombrePeriodo</strong> no cumple con el patrón necesario";
			exit;
		}

		$duracionPeriodo = $_POST["duracionPeriodo$i"];

	// --------------------

		$estructura .= "
				{
					\"nombre\" : \"$nombrePeriodo\",
					\"id\" : \"$idPeriodo\",
		";

	// Si el periodo no contiene subperiodos

		if(is_numeric($duracionPeriodo)) {
			$estructura .= "
					\"duracion\" : \"$duracionPeriodo\",
					\"subperiodos\" : false
			";
		}

	// --------------------

	// Si el periodo contiene subperiodos

		else if($duracionPeriodo == "false") {
			$estructura .= "
					\"duracion\" : false,
					\"subperiodos\" : [
			";

			$nombrePeriodoSub = $_POST["nombrePeriodo$i" . "Sub"];
			$idPeriodoSub = $_POST["idPeriodo$i" . "Sub"];
			$duracionPeriodoSub = $_POST["duracionPeriodo$i" . "Sub"];

			for($i2 = 0; $i2 < count($nombrePeriodoSub); ++$i2) {

		// Validación

				$re = "^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$";

				if(! ereg("$re", $nombrePeriodoSub[$i2])) {
					echo "El nombre del subperiodo" . ($i2 + 1) . " en el periodo <strong>$nombrePeriodo</strong> no cumple con el patrón necesario";
					exit;
				}

				$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

				if(! ereg("$re", $idPeriodoSub[$i2])) {
					echo "El identificador del subperiodo <strong>$nombrePeriodoSub[$i2]</strong> en el periodo <strong>$nombrePeriodo</strong> no cumple con el patrón necesario";
					exit;
				}

				$re = "^[0-9]+$";

				if(! ereg("$re", $duracionPeriodoSub[$i2])) {
					echo "La duración del subperiodo <strong>$nombrePeriodoSub[$i2]</strong> en el periodo <strong>$nombrePeriodo</strong> no cumple con el patrón necesario";
					exit;
				}

		// --------------------

				$estructura .= "
						{
							\"nombre\" : \"$nombrePeriodoSub[$i2]\",
							\"id\" : \"$idPeriodoSub[$i2]\",
							\"duracion\" : \"$duracionPeriodoSub[$i2]\"
						},";
			}

			$estructura = substr($estructura, 0, -1);  // Eliminar la coma en "}," del ultimo subperiodo

			$estructura .= "
					]
			";
		}

	// --------------------

		$estructura .= "
				},";
	}

	$estructura = substr($estructura, 0, -1);  // Eliminar la coma en "}," del ultimo periodo

	$estructura .= "
			]
		}
	";

	$sql = "select estructura from estructura where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$estructuraAnt = pg_fetch_object($exe);

	if(($nombre == $nombreAnt) && ($estructura == $estructuraAnt->estructura)) {
		echo "No se hizo ningún cambio&&info";
		exit;
	}

// --------------------

	pg_query($sigpa, "begin");

	$sql = "update estructura set nombre='$nombre', estructura='$estructura' where nombre='$nombreAnt'";
	$exe = pg_query($sigpa, $sql);

// Si se modificó la estructura correctamente

	if($exe) {

	// Agregar elemento al registro de acciones realizadas

		$sql = "insert into historial values('" . time() . "', '$_SESSION[nombre] $_SESSION[apellido] ($_SESSION[cedula])', 'Se modificó la estructura <strong>$nombreAnt</strong>', '" . htmlspecialchars($sql, ENT_QUOTES) . "')";
		$exe = pg_query($sigpa, $sql);

	// --------------------
		
		echo "Se modificó satisfactoriamente&&success";
		pg_query($sigpa, "commit");
		exit;
	}

// --------------------

// Si ocurrio un error modificando la estructura

	echo "Ocurrió un error mientras el servidor intentaba guardar la información, por favor vuelva a intentarlo y si el error persiste comuníquelo al administrador del sistema&&error";
	pg_query($sigpa, "rollback");

// --------------------

?>