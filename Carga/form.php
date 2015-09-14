<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);
	$carrera = htmlspecialchars($_POST["carrera"], ENT_QUOTES);
	$sede = htmlspecialchars($_POST["sede"], ENT_QUOTES);
	$periodo = htmlspecialchars($_POST["periodo"], ENT_QUOTES);
	$mecs = htmlspecialchars($_POST["mecs"], ENT_QUOTES);
	$periodoEstructura = htmlspecialchars($_POST["periodoEstructura"], ENT_QUOTES);

	$sql = "
		select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable 
		from \"unidadCurricular\" as uc 
		where uc.id='$id'
	";
	$exe = pg_query($sigpa, $sql);
	$uc = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header"><?= "$uc->nombre ($uc->id)"; ?></h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="carga" method="POST" action="moduloPlanificacion/Carga/guardar.php" data-exe="moreInfoClose(); unidadesCurriculares()" role="form">

<?php
	if($uc->renombrable == "t") {
?>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nuevo nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" />
				<p class="help-block">Opcional, si no se especifica algún nombre, se mantendrá el valor predefinido. Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Programación Orientada a Objetos.</p>
			</div>

<?php
	}
?>

			<div class="form-group"> 
				Condición:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="condicion" value="3" onChange="profesores()" checked="checked" required="required"> Ordinario </label>
					<label class="radio-inline"><input type="radio" name="condicion" value="1" onChange="profesores()" required="required"> Contratado </label>
				</div>
			</div>

			<div class="form-group">
				<select name="profesor" class="form-control" required="required">
					<option value="">Profesor</option>

<?php
	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
			join pertenece as per on per.\"idProfesor\"=prof.cedula
		where prof.condicion='3' and per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede') 
		order by p.cedula, p.apellido, p.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe))
		echo "<option value=\"$profesor->cedula\">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
?>

				</select>
			</div>

<?php
	$options = "";

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
			join pertenece as per on per.\"idProfesor\"=prof.cedula
		where prof.condicion='1' and per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera' and \"idSede\"='$sede') 
		order by p.cedula, p.apellido, p.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe))
		$options .= "<option value=\"$profesor->cedula\">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";

	$sql = "
		select s.\"ID\" as \"ID\", s.id as id, s.grupos as grupos 
		from seccion as s 
		where s.\"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')) and s.\"periodoEstructura\"='$periodoEstructura' 
		order by s.id
	";
	$exe = pg_query($sigpa, $sql);

	while($seccion = pg_fetch_object($exe)) {
?>

			<div class="row">
				<div class="col-xs-4">
					<div class="form-group">
						<label class="checkbox-inline"><input type="checkbox" name="seccion[]" value="<?= $seccion->id; ?>" onClick="enableSec(this); grupos('<?= $seccion->id; ?>')"> <?= $seccion->id; ?> </label>
						<input type="hidden" name="ID<?= $seccion->id; ?>" value="<?= $seccion->ID; ?>" />
					</div>
				</div>

				<div class="col-xs-8"><div class="form-group">
					<div id="sec<?= $seccion->id; ?>"></div>

<?php
		if($seccion->grupos == "t") {
?>

					<div class="form-group">
						<label class="checkbox-inline"><input type="checkbox" name="dividirHT<?= $seccion->id; ?>" id="dividirHT<?= $seccion->id; ?>" value="1" disabled="disabled"> Dividir horas teóricas en grupos </label>
					</div>

<?php
		}
?>

				</div></div>

				<div class="col-xs-12"><div class="form-group">
					<select name="suplente<?= $seccion->id; ?>" id="suplente<?= $seccion->id; ?>" class="form-control" disabled="disabled">
						<option value="">Suplente</option>

						<?= $options ?>

					</select>
				</div></div>
			</div>

<?php
	}
?>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="moreInfoClose()" />
			</div>
		</form>
	</div>
</div>

<script>
	function profesores() {
		var condicion = document.carga.condicion.value;
		var profesor = document.carga.profesor;

		embem('moduloPlanificacion/Carga/profesores.php', profesor, "carrera=<?= $carrera; ?>&sede=<?= $sede; ?>&condicion=" + condicion);
	}

	function enableSec(seccion) {
		var suplenteSelect = $("#suplente" + seccion.value);
		var dividirHT = $("#dividirHT" + seccion.value);

		if(seccion.checked) {
			suplenteSelect.removeAttr("disabled");
			dividirHT.removeAttr("disabled");
		}

		else {
			suplenteSelect.attr("disabled", "disabled");
			$("#suplente" + seccion.value +" option:first-child").attr("selected", "selected");
			dividirHT.attr("disabled", "disabled");
		}
	}
</script>