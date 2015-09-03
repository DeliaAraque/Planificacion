<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = $_POST["id"];
	$idECS = $_POST["ecs"];

	$sql = "
		select p.id as id, p.\"fechaInicio\" as \"fechaInicio\", p.\"fechaFin\" as \"fechaFin\", p.\"idECS\" \"idECS\", c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where p.id='$id' and p.\"idECS\"='$idECS' 
		order by p.tipo
	";
	$exe = pg_query($sigpa, $sql);
	$periodo = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar periodo <small><?= "$periodo->carrera - $periodo->sede ($periodo->estructura)"; ?></small></h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="sede" method="POST" action="moduloPlanificacion/Periodo/modificar.php" data-exe="embem('moduloPlanificacion/Periodo/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" value="<?= $periodo->id ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ0-9\-]+$" required="required" />
				<input type="hidden" name="idAnt" value="<?= $periodo->id ?>" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios y guiones. Ej: 2015-B.</p>
			</div>

			<h4>Planificación:</h4>

			<div class="row">
				<div class="form-group col-xs-6">
					<input type="text" name="fechaInicioP" placeholder="Fecha de inicio" value="<?php $fecha = explode("-", $periodo->fechaInicio); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 06/04/2015.</p>
				</div>

				<div class="form-group col-xs-6">
					<input type="text" name="fechaFinP" placeholder="Fecha de fin" value="<?php $fecha = explode("-", $periodo->fechaFin); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 12/04/2015.</p>
				</div>
			</div>

			<h4>Académico:</h4>

<?php
	$periodo = pg_fetch_object($exe);
?>

			<div class="row">
				<div class="form-group col-xs-6">
					<input type="text" name="fechaInicioA" placeholder="Fecha de inicio" value="<?php $fecha = explode("-", $periodo->fechaInicio); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 13/04/2015.</p>
				</div>

				<div class="form-group col-xs-6">
					<input type="text" name="fechaFinA" placeholder="Fecha de fin" value="<?php $fecha = explode("-", $periodo->fechaFin); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 17/07/2015.</p>
				</div>
			</div>

			<input type="hidden" name="ecs" value="<?= $periodo->idECS ?>" />

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Periodo/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>