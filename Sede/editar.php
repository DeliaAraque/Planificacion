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
				<input type="text" name="nombre" value="<?= $nombre; ?>" placeholder="Nombre" class="form-control" data-type="text" data-msg="Solo se permiten números y guiones" onKeyUp="Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Debe indicar el nombre de la sede, por ejemplo: Ejido.</p>
			</div>

			<div class="form-group">
				<input type="date" name="fecha" value="<?= $sede->fecha; ?>" placeholder="Fecha de inauguración" class="form-control" data-type="[0-9-]" data-msg="Solo se permiten números y guiones" required="required" />
				<p class="help-block">Debe indicar la fecha en que se inauguró la sede, por ejemplo: 2015-12-31.</p>
			</div>

			<div class="form-group">
				<input type="text" name="telefono" value="<?= $sede->telefono; ?>" placeholder="Teléfono" class="form-control" data-type="[0-9+-]" data-msg="Solo se permiten caracteres válidos para números telefónicos" required="required" />
			</div>

			<div class="form-group">
				<textarea name="direccion" class="form-control" rows="2" placeholder="Dirección" required="required"><?= $sede->direccion; ?></textarea>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Sede/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>