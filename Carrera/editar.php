<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "select * from carrera where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$carrera = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Editar carrera</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="carrera" method="POST" action="moduloPlanificacion/Carrera/modificar.php" data-exe="embem('moduloPlanificacion/Carrera/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Código:
				<input type="text" name="id" placeholder="Código" value="<?= $carrera->id; ?>" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$" onKeyUp="if(this.value != document.carrera.idAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="idAnt" value="<?= $carrera->id; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios. Ej: 03.</p>
			</div>

			<div class="form-group">
				Nombre:
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" onKeyUp="if(this.value != document.area.nombreAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: PNF Informática.</p>
			</div>

			<div class="form-group">
				Área:
				<select name="area" class="form-control" required="required">

<?php
	$sql = "select * from area order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($area = pg_fetch_object($exe)) {
?>

					<option value="<?= $area->id ?>" <?php if($carrera->idArea == $area->id) echo "selected=\"selected\""; ?>><?= $area->nombre ?></option>";

<?php
	}
?>

				</select>
			</div>

			<div class="form-group">
				<br/>Sede:

<?php
	$sql = "select \"idSede\" from \"carreraSede\" where \"idCarrera\"='$carrera->id' order by \"idSede\"";
	$exe = pg_query($sigpa, $sql);

	while($sede = pg_fetch_object($exe))
		$sedes[] = $sede->idSede;

	$n = 0;

	$sql = "select id, nombre from sede order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($sede=pg_fetch_object($exe)) {
?>

				<?php if($n) echo "<div class=\"form-group col-xs-12\"><hr/></div>" ?>

				<div class="row">
					<div class="col-xs-4 col-xs-offset-2"><div class="form-group">
						<label class="checkbox"><input type="checkbox" name="sede[]" value="<?= $sede->id; ?>" <?php if(in_array($sede->id, $sedes)) echo "checked=\"checked\""; ?>><strong><?= $sede->nombre; ?></strong></label>
					</div></div>

					<div class="col-xs-6"><div class="form-group">

<?php
		$sql = "select \"idEstructura\" from \"estructuraCS\" where \"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera->id' and \"idSede\"='$sede->id') order by \"idEstructura\"";
		$exe2 = pg_query($sigpa, $sql);

		while($estructura = pg_fetch_object($exe2))
			$estructuras[] = $estructura->idEstructura;

		$sql = "select id, nombre from estructura order by nombre";
		$exe2 = pg_query($sigpa, $sql);

		while($estructura = pg_fetch_object($exe2)) {
?>

						<label class="checkbox-inline"><input type="checkbox" name="estructura<?= $sede->id; ?>[]" value="<?= $estructura->id; ?>" <?php if(in_array($estructura->id, $estructuras)) echo "checked=\"checked\""; ?>><?= $estructura->nombre; ?></label><br/>

<?php
		}

		unset($estructuras);
?>

					</div></div>

				</div>

<?php
		++$n;
	}
?>
			</div>

			<div class="form-group" id="malla">
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Carrera/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>