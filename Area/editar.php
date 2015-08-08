<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar área</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="area" method="POST" action="moduloPlanificacion/Area/modificar.php" data-exe="embem('moduloPlanificacion/Area/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" onKeyUp="if(this.value != document.area.nombreAnt.value) Verif(this)" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y las primeras letras deben estar en mayúscula. Ej: Tecnología</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Area/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>