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
				Coordinador Institucional:
				<select name="coordinadorInst" class="form-control" onChange="noRepetido(this.value)">
					<option value="">Coordinador Institucional:</option>

<?php
	$sql="
		select p.cedula as cedula, p.apellido as apellido, p.nombre as nombre 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
		order by p.apellido, p.nombre, p.cedula
	";
	$exe=pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe)) {
		$sql="select count(\"idCoordinador\") as n from \"carreraSede\" where \"idCoordinador\"='$profesor->cedula'";
		$exe2=pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe2);

		if($n->n)
			continue;

		echo "<option value='$profesor->cedula' ";

		if($carrera->idCoordinadorInst == $profesor->cedula)
			echo "selected=\"selected\"";

		echo ">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
	}
?>

				</select>

				<p class="help-block">Opcional.</p>
			</div>

			<div class="form-group">
				<br/>Sede:

<?php
	$sql = "select \"idSede\" from \"carreraSede\" where \"idCarrera\"='$carrera->id' order by \"idSede\"";
	$exe = pg_query($sigpa, $sql);

	while($sede = pg_fetch_object($exe))
		$sedes[] = $sede->idSede;

	$sql = "select id, nombre from sede order by nombre";
	$exe = pg_query($sigpa, $sql);

	$n = 0;
	$optionsBan = "null";

	while($sede=pg_fetch_object($exe)) {
?>

				<?php if($n) echo "<div class=\"form-group col-xs-12\"><hr/></div>" ?>

				<div class="row">
					<div class="col-xs-4"><div class="form-group"><label class="checkbox-inline"><input type="checkbox" name="sede[]" value="<?= $sede->id; ?>" onClick="enableProf(this)" <?php if(in_array($sede->id, $sedes)) echo "checked=\"checked\""; ?>><strong><?= $sede->nombre; ?></strong></label></div></div>

					<div class="col-xs-8"><div class="form-group">

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
<?php
	
?>
					<div class="col-xs-12"><div class="form-group">
						<select name="coordinador<?= $sede->id; ?>" id="coordinador<?= $sede->id; ?>" class="form-control" onChange="noRepetido(this.value)" required="required" <?php if(! in_array($sede->id, $sedes)) echo "disabled=\"disabled\""; ?>>
							<option value="">Coordinador</option>

<?php
		if(in_array($sede->id, $sedes)) {
			$sql="
				select p.cedula as cedula, p.apellido as apellido, p.nombre as nombre 
				from pertenece as per
					join profesor as prof on prof.cedula=per.\"idProfesor\" 
					join persona as p on p.cedula=prof.cedula 
				where per.\"idCS\"=(select id from \"carreraSede\" where \"idCarrera\"='$carrera->id' and \"idSede\"='$sede->id') 
				order by p.apellido, p.nombre, p.cedula
			";
		}

		else {
			$sql="
				select p.cedula as cedula, p.apellido as apellido, p.nombre as nombre 
				from persona as p 
					join profesor as prof on prof.cedula=p.cedula 
				order by p.apellido, p.nombre, p.cedula
			";
		}

		$exe2=pg_query($sigpa, $sql);

		while($profesor = pg_fetch_object($exe2)) {
			$sql="select count(\"idCoordinador\") as n from \"carreraSede\" where \"idCarrera\"!='$carrera->id' and \"idCoordinador\"='$profesor->cedula'";
			$exe3=pg_query($sigpa, $sql);
			$nProf = pg_fetch_object($exe3);

			if($nProf->n)
				continue;

			if(in_array($sede->id, $sedes)) {
				$sql="select count(\"idCoordinador\") as n from \"carreraSede\" where \"idCarrera\"='$carrera->id' and \"idCoordinador\"='$profesor->cedula'";
				$exe3=pg_query($sigpa, $sql);
				$nProf = pg_fetch_object($exe3);

				if($nProf->n) {
					$selected = "selected=\"selected\"";
					$optionsBan .= ",\"$profesor->cedula\"";
					$valueInput = $profesor->cedula;
				}
			}

			echo "<option value='$profesor->cedula' $selected $disabled>$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
			unset($selected);
			unset($disabled);
		}
?>

						</select>
						<input type="hidden" value="<?= $valueInput; ?>" />
					</div></div>
				</div>

<?php
		unset($valueInput);
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

<script>
	var optionsBan = new Array(<?= $optionsBan; ?>);

	for(var i = 0; i < optionsBan.length; ++i)
		$("select[name^='coordinador'] option[value='" + optionsBan[i] + "']").attr("disabled", "disabled");

	$("option[selected='selected']").removeAttr("disabled");

	function enableProf(sede) {
		var coordinadorSelect = $("#coordinador" + sede.value);

		if(sede.checked)
			coordinadorSelect.removeAttr("disabled");

		else {
			coordinadorSelect.attr("disabled", "disabled");

			$("#coordinador" + sede.value +" option:first-child").attr("selected", "selected");

			var input = sede.parentNode.parentNode.parentNode.parentNode.querySelector("input[type='hidden']");

			$("select[name^='coordinador'] option[value='" + input.value + "']").removeAttr("disabled");
			input.value = "";
		}
	}

	function noRepetido(cedula) {
		$("select[name^='coordinador'] option[value='" + $("select:focus + input").attr("value") + "']").removeAttr("disabled");

		if(cedula)
			$("select[name^='coordinador'] option[value='" + cedula + "']").attr("disabled", "disabled");

		$("select:focus option[value='" + cedula + "']").removeAttr("disabled");
		$("select:focus + input").attr("value", cedula);
	}
</script>