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
				<input type="text" name="id" value="<?= $id; ?>"placeholder="Abreviatura" class="form-control" data-type="text" onKeyUp="Verif(this)" required="required" />
				<input type="hidden" name="idAnt" value="<?= $id; ?>" />
				<p class="help-block">Debe indicar la abreviatura para la dedicación, por ejemplo: MT</p>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" value="<?= $dedicacion->nombre; ?>" placeholder="Nombre" class="form-control" data-type="text" required="required" />
				<p class="help-block">Debe indicar el nombre de la dedicación, por ejemplo: Medio Tiempo.</p>
			</div>

			<div class="form-group">
				<input type="text" name="horas" value="<?= $dedicacion->horas; ?>" placeholder="Horas" class="form-control" data-type="num" required="required" />
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Dedicacion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>