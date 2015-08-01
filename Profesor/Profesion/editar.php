<?php
	require "../../../script/verifSesion.php";
	require "../../../lib/conexion.php";

	$nombre = $_POST["nombre"];
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar profesión</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="profesion" method="POST" action="moduloPlanificacion/Profesor/Profesion/modificar.php" data-exe="embem('moduloPlanificacion/Profesor/Profesion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nombre" class="form-control" data-type="text" onKeyUp="Verif(this)" value="<?= $nombre; ?>" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Debe indicar el nombre de la profesión, por ejemplo: T.S.U. Construcción Civil.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Profesor/Profesion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>