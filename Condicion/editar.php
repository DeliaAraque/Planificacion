<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar condición</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="condicion" method="POST" action="moduloPlanificacion/Condicion/modificar.php" data-exe="embem('moduloPlanificacion/Condicion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" onKeyUp="if(this.value != document.condicion.nombreAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Debe indicar el nombre de la condición, por ejemplo: Contratado.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Condicion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>