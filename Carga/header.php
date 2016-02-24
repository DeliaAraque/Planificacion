<?php
	$carrera = $_GET["carrera"];
	$sede = $_GET["sede"];
	$malla = $_GET["malla"];
	$periodo = $_GET["periodo"];
	$fechaInicio = $_GET["fechaInicio"];
	$fechaFin = $_GET["fechaFin"];
?>

<style>
	header {
		font-size: 5pt;
		text-align: center;

		position: relative;
	}

	header img {
		position: absolute;
	}

	#ministerio {
		height: 8em;
		width: 30%;
		
		left: 0px;
	}

	#uptm {
		height: 10em;

		right: 0px;
	}

	div {
		font-weight: bold;
	}
</style>

<header>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>
	<br/><br/><br/><br/><br/>

	<img src="../../css/img/ministerio.jpg" id="ministerio" />
	<img src="../../css/img/logo.png" id="uptm" />

	<br/><div>República Bolivariana de Venezuela</div>
	<div>Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología</div>
	<div><?= "$carrera - $sede ($malla)"; ?></div>
	<div>Periodo Académico <?= "$periodo ($fechaInicio - $fechaFin)"; ?></div>
</header>