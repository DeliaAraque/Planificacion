<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva unidad curricular</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="unidadCurricular" method="POST" action="moduloPlanificacion/UC/guardar.php" data-exe="embem('moduloPlanificacion/UC/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ0-9]+(\-[A-ZÁÉÍÓÚÑ0-9]+)*$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos en mayúsculas y guiones. Ej: PIAP1.</p>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*,?( [a-záéíóúñA-ZÁÉÍÓÚÑ]+,?)*$" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Algorítmica y Programación.</p>
			</div>

			<div class="form-group">
				<label class="checkbox-inline"><input type="checkbox" name="renombrable" value="1"> Renombrable </label>
			</div>

<?php
	if($_SESSION["nivel"] == 3) {
?>

			<input type="hidden" name="carrera" value="<?= $_SESSION["carreraCoord"]; ?>" />

<?php
	}

	else {
?>

			<div class="form-group">
				<select name="carrera" class="form-control" required="required">
					<option value="">Carrera</option>

<?php
	$sql="select * from carrera order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($carrera=pg_fetch_object($exe))
		echo "
					<option value='$carrera->id'>$carrera->nombre</option>";
?>

				</select>
			</div>

<?php
	}
?>

			<div class="form-group">
				<select name="eje" class="form-control" required="required">
					<option value="">Eje</option>

<?php
	$sql="select * from eje order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($eje=pg_fetch_object($exe))
		echo "
					<option value='$eje->id'>$eje->nombre</option>";
?>

				</select>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/UC/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>