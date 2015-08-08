<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "select * from sede where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$sede = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar sede</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="sede" method="POST" action="moduloPlanificacion/Sede/modificar.php" data-exe="embem('moduloPlanificacion/Sede/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" onKeyUp="if(this.value != document.sede.nombreAnt.value) Verif(this)" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y las primeras letras deben estar en mayúscula. Ej: Ejido.</p>
			</div>

			<div class="form-group">
				Fecha de inauguración:
				<input type="text" name="fecha" placeholder="Fecha de inauguración" value="<?php $fecha = explode("-", $sede->fecha); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
				<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 03/03/2009.</p>
			</div>

			<div class="form-group">
				Teléfono:
				<input type="tel" placeholder="Teléfono" name="telefono" value="<?= $sede->telefono; ?>" class="form-control" pattern="^[0-9]{4}\-?[0-9]{7}$" required="required" />
				<p class="help-block">El número de teléfono debe cumplir el formato Código-Número, Ej: 0274-2210746.</p>
			</div>

			<div class="form-group">
				Dirección:
				<textarea name="direccion" placeholder="Dirección" rows="2" class="form-control" required="required"><?= $sede->direccion; ?></textarea>
				<p class="help-block">Ej: Av. Monseñor Duque, Ejido.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Sede/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>