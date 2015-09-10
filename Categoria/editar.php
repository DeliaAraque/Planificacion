<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = $_POST["id"];

	$sql = "select * from categoria where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$categoria = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar categoría</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="categoria" method="POST" action="moduloPlanificacion/Categoria/modificar.php" data-exe="embem('moduloPlanificacion/Categoria/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Abreviatura:
				<input type="text" name="id" placeholder="Abreviatura" value="<?= $id; ?>" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ]+$" onKeyUp="if(this.value != document.categoria.idAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="idAnt" value="<?= $id; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos sin espacios. Ej: Inst.</p>
			</div>

			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $categoria->nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" onKeyUp="if(this.value != document.categoria.nombreAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $categoria->nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y las primeras letras deben estar en mayúscula. Ej: Instructor.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Categoria/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>