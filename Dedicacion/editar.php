<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = $_POST["id"];

	$sql = "select * from dedicacion where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$dedicacion = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar dedicación</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="dedicacion" method="POST" action="moduloPlanificacion/Dedicacion/modificar.php" data-exe="embem('moduloPlanificacion/Dedicacion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Abreviatura:
				<input type="text" name="id" value="<?= $id; ?>"placeholder="Abreviatura" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ]+$" onKeyUp="if(this.value != document.dedicacion.idAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="idAnt" value="<?= $id; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos sin espacios. Ej: MT.</p>
			</div>

			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" value="<?= $dedicacion->nombre; ?>" placeholder="Nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" onKeyUp="if(this.value != document.dedicacion.nombreAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $dedicacion->nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y las primeras letras deben estar en mayúscula. Ej: Medio Tiempo.</p>
			</div>

			<div class="form-group">
				Horas:
				<input type="text" name="horas" value="<?= $dedicacion->horas; ?>" placeholder="Horas" class="form-control" pattern="^[0-9]+$" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Horas</strong>. Ej: 8.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Dedicacion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>