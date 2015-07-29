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
				<input type="text" name="id" placeholder="Abreviatura" class="form-control" data-type="text" onKeyUp="Verif(this)" value="<?= $id; ?>" required="required" />
				<input type="hidden" name="idAnt" value="<?= $id; ?>" />
				<p class="help-block">Debe indicar el nombre de la abreviatura, por ejemplo: Inst</p>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nombre" class="form-control" data-type="text" onKeyUp="Verif(this)" value="<?= $categoria->nombre; ?>" required="required" />
				<p class="help-block">Debe indicar el nombre de la categoría, por ejemplo: Instructor.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Categoria/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>