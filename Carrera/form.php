<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva carrera</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="carrera" method="POST" action="moduloPlanificacion/Carrera/guardar.php" data-exe="embem('moduloPlanificacion/Carrera/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios. Ej: 03.</p>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: PNF Informática.</p>
			</div>

			<div class="form-group">
				<select name="area" class="form-control" required="required">
					<option value="">Área</option>

<?php
	require "../../lib/conexion.php";

	$sql="select * from area order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($area=pg_fetch_object($exe))
		echo "
					<option value='$area->id'>$area->nombre</option>";
?>

				</select>

				<p class="help-block">En caso de que no exista el area que desea seleccionar, deberá registrarla <a href="javascript: embem('moduloPlanificacion/Area/form.html', '#page-wrapper')">aquí</a>.</p>
			</div>

			<div class="form-group">
				<select name="coordinadorInst" class="form-control" onChange="noRepetido(this.value)">
					<option value="">Coordinador Institucional</option>

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

		$profesores .= "<option value='$profesor->cedula'>$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
	}

	echo $profesores;
?>

				</select>

				<p class="help-block">Opcional.</p>
			</div>

			<div class="form-group"> 
				Sede:

<?php
	$sql="select id, nombre from estructura order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($estructura = pg_fetch_object($exe))
		$estructuras .= "<option value='$estructura->id'>$estructura->nombre</option>";

	$sql="select id, nombre from sede order by nombre";
	$exe=pg_query($sigpa, $sql);

	$n = 0;

	while($sede=pg_fetch_object($exe)) {
?>

				<?php if($n) echo "<div class=\"form-group col-xs-12\"><hr/></div>" ?>

				<div class="row">
					<div class="col-xs-4"><div class="form-group"><?= "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"sede[]\" value=\"$sede->id\" onClick=\"estructura(this)\"> $sede->nombre </label>"; ?></div></div>
					<div class="col-xs-8"><div class="form-group">
						<select name="estructura<?= $sede->id; ?>" id="estructura<?= $sede->id; ?>" class="form-control" required="required" disabled="disabled">
							<option value="">Estructura</option>
							<?= $estructuras; ?>
						</select>
					</div></div>
					<div class="col-xs-12"><div class="form-group">
						<select name="coordinador<?= $sede->id; ?>" id="coordinador<?= $sede->id; ?>" class="form-control" onChange="noRepetido(this.value)" required="required" disabled="disabled">
							<option value="">Coordinador</option>
							<?= $profesores; ?>
						</select>
						<input type="hidden" value="" />
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
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Carrera/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>

<script>
	function estructura(sede) {
		var estructuraSelect = $("#estructura" + sede.value);
		var coordinadorSelect = $("#coordinador" + sede.value);

		if(sede.checked) {
			estructuraSelect.removeAttr("disabled");
			coordinadorSelect.removeAttr("disabled");
		}

		else {
			estructuraSelect.attr("disabled", "disabled");
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