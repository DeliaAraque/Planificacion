<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "select * from estructura where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructura = json_decode($estructura->estructura);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header"><?= "$nombre"; ?></h1>
	</div>
</div>

<?php
	/*
		Propiedades del objeto Periodo:
			· Identificador (id)
			· Nombre (nombre)
			· Duración (duracion)
			· Subperiodos (subperiodos)
	*/

	foreach($estructura->periodos as $periodo) {
?>

<div class="row">
	<div class="col-xs-12">
		<h4><?= $periodo->nombre; ?></h4>
	</div>
</div>

<div class="row">
	<div class="col-xs-11 col-xs-offset-1">
		<strong>Identificador:</strong> <?= $periodo->id; ?><br/>

<?php
		if(!$periodo->subperiodos)
			echo "<strong>Duración:</strong> $periodo->duracion " . (($periodo->duracion == 1) ? "mes" : "meses");

		else {
			/*
				Propiedades del objeto Subperiodos:
					· Identificador (id)
					· Nombre (nombre)
					· Duración (duracion)
			*/

			echo "<br/>";

			foreach($periodo->subperiodos as $subperiodo) {
?>

		<div class="row">
			<div class="col-xs-12">
				<h4><?= $subperiodo->nombre; ?></h4>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-11 col-xs-offset-1">
				<strong>Identificador:</strong> <?= $subperiodo->id; ?><br/>
				<strong>Duración:</strong> <?= $subperiodo->duracion; ?> <?= ($subperiodo->duracion == 1) ? "mes" : "meses"; ?>
			</div>
		</div>

<?php
			}
		}
?>

	</div>
</div><br/>

<?php
	}
?>