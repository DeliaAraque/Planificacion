<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

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
		if(! $periodo->subperiodos)
			echo "<option value=\"$periodo->id\">$periodo->nombre</option>";

		else {
			foreach($periodo->subperiodos as $subperiodo)
				echo "<option value=\"$periodo->id-$subperiodo->id\">$periodo->nombre - $subperiodo->nombre</option>";
		}
	}
?>