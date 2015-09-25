<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar profesión</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="profesion" method="POST" action="moduloPlanificacion/Profesion/modificar.php" data-exe="embem('moduloPlanificacion/Profesion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*\.?( [a-záéíóúñA-ZÁÉÍÓÚÑ]+\.?)*$" onKeyUp="if(this.value != document.profesion.idAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: TSU Construcción Civil.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Profesion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>