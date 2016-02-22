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
	}

	header img {
		height: 8em;
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

	<img src="../../css/img/ministerio.jpg" style="float: left;" />
	<img src="../../css/img/logo.png" style="float: right;" />

	<br/>República Bolivariana de Venezuela
	<br/>Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología
	<br/><?= "$carrera - $sede ($malla)"; ?>
	<br/>Periodo Académico <?= "$periodo ($fechaInicio - $fechaFin)"; ?>
</header>