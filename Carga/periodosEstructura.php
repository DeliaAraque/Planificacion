<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$re = "^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$";

	if(! ereg("$re", $_POST["carrera"]))
		exit;

	$carrera = $_POST["carrera"];

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["sede"]))
		exit;

	$sede = $_POST["sede"];

	$re = "^[A-ZÁÉÍÓÚÑ0-9\-]+$";

	if(! ereg("$re", $_POST["periodo"]))
		exit;

	$periodo = $_POST["periodo"];

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["mecs"]))
		exit;

	$mecs = $_POST["mecs"];

	$sql="
		select sec.\"periodoEstructura\" as \"periodoEstructura\" 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join \"mallaECS\" as mecs on mecs.\"idECS\"=ecs.id and mecs.estado is true 
			join malla as m on m.id=mecs.\"idMalla\" 
		where p.id='$periodo' and cs.\"idCarrera\"='$carrera' and cs.\"idSede\"='$sede' and sec.\"idMECS\"='$mecs' 
	";
	$exe=pg_query($sigpa, $sql);

	while($periodo = pg_fetch_object($exe))
		$periodos[] = $periodo->periodoEstructura;

	$re = "^[0-9]+$";

	if(! ereg("$re", $_POST["mecs"]))
		exit;

	$mecs = $_POST["mecs"];

	$sql = "select estructura from estructura where id=(select \"idEstructura\" from \"estructuraCS\" where id=(select \"idECS\" from \"mallaECS\" where id='$mecs'))";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructura = json_decode($estructura->estructura);
?>

<option value="">Periodo</option>

<?php
	foreach($estructura->periodos as $periodo) {
		if(! $periodo->subperiodos) {
			if(! in_array($periodo->id, $periodos))
				continue;

			echo "<option value=\"$periodo->id\">$periodo->nombre</option>";
		}

		else {
			foreach($periodo->subperiodos as $subperiodo) {
				if(! in_array("$periodo->id-$subperiodo->id", $periodos))
					continue;

				echo "<option value=\"$periodo->id-$subperiodo->id\">$periodo->nombre - $subperiodo->nombre</option>";
			}
		}
	}
?>