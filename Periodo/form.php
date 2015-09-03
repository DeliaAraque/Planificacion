<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>


<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Nuevo periodo</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="sede" method="POST" action="moduloPlanificacion/Periodo/guardar.php" data-exe="embem('moduloPlanificacion/Periodo/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ0-9\-]+$" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios y guiones. Ej: 2015-B.</p>
			</div>

			<h4>Planificación:</h4>

			<div class="row">
				<div class="form-group col-xs-6">
					<input type="text" name="fechaInicioP" placeholder="Fecha de inicio" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 06/04/2015.</p>
				</div>

				<div class="form-group col-xs-6">
					<input type="text" name="fechaFinP" placeholder="Fecha de fin" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 12/04/2015.</p>
				</div>
			</div>

			<h4>Académico:</h4>

			<div class="row">
				<div class="form-group col-xs-6">
					<input type="text" name="fechaInicioA" placeholder="Fecha de inicio" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 13/04/2015.</p>
				</div>

				<div class="form-group col-xs-6">
					<input type="text" name="fechaFinA" placeholder="Fecha de fin" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
					<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 15/07/2015.</p>
				</div>
			</div>

			<div class="form-group"> 
				Carreras:
				<div class="checkbox">

<?php
		$sql="
			select ecs.id as id, c.nombre as carrera, s.nombre as sede, e.nombre as estructura 
			from \"carreraSede\" as cs 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
				join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id 
				join estructura as e on e.id=ecs.\"idEstructura\" 
			order by c.nombre, s.nombre, e.nombre
		";
		$exe=pg_query($sigpa, $sql);

		while($ecs=pg_fetch_object($exe))
			echo "<label class=\"checkbox\"><input type=\"checkbox\" name=\"carrera[]\" value=\"$ecs->id\"> $ecs->carrera - $ecs->sede ($ecs->estructura) </label>";
?>

				</div>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Periodo/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>