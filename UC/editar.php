<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = $_POST["id"];

	$sql = "select * from \"unidadCurricular\" where id='$id'";
	$exe = pg_query($sigpa, $sql);
	$uc = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Editar unidad curricular</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="unidadCurricular" method="POST" action="moduloPlanificacion/UC/modificar.php" data-exe="embem('moduloPlanificacion/UC/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Código:
				<input type="text" name="id" placeholder="Código" value="<?= $uc->id; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ0-9]+(\-[A-ZÁÉÍÓÚÑ0-9]+)*$" onKeyUp="if(this.value != document.unidadCurricular.idAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="idAnt" value="<?= $uc->id; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos en mayúsculas y guiones. Ej: PIAP1.</p>
			</div>

			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $uc->nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*,?( [a-záéíóúñA-ZÁÉÍÓÚÑ]+,?)*$" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $uc->nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Algorítmica y Programación.</p>
			</div>

			<div class="form-group">
				<label class="checkbox-inline"><input type="checkbox" name="renombrable" value="1" <?php if($uc->renombrable == "t") echo "checked=\"checked\""; ?>> Renombrable </label>
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
				Carrera:
				<select name="carrera" class="form-control" required="required">

<?php
	$sql="select * from carrera order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($carrera = pg_fetch_object($exe)) {
?>

					<option value="<?= $carrera->id; ?>" <?php if($uc->idCarrera == $carrera->id) echo "selected=\"selected\""; ?>><?= $carrera->nombre; ?></option>";

<?php
	}
?>

				</select>
			</div>

<?php
	}
?>
			<input type="hidden" name="carreraAnt" value="<?= $uc->idCarrera; ?>" />

			<div class="form-group">
				Eje:
				<select name="eje" class="form-control" required="required">

<?php
	$sql="select * from eje order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($eje=pg_fetch_object($exe)) {
?>

					<option value="<?= $eje->id; ?>" <?php if($uc->idEje == $eje->id) echo "selected=\"selected\""; ?>><?= $eje->nombre; ?></option>";

<?php
	}
?>

				</select>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/UC/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>