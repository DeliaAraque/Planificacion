<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva firma</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="firma" method="POST" action="moduloPlanificacion/Carga/guardarFirma.php" data-exe="moreInfoClose(); embem('moduloPlanificacion/Carga/firmas.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="orden" placeholder="Orden" class="form-control" pattern="^[0-9]+$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres numéricos. Ej: 1.</p>
			</div>

			<div class="form-group">
				<input type="text" name="cargo" placeholder="Cargo" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Vicerrector Académico.</p>
			</div>

			<div class="form-group">
				<select name="idProfesor" class="form-control" required="required">
					<option value="">Profesor</option>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
		order by p.apellido, p.nombre, p.cedula
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe))
		echo "<option value=\"$profesor->cedula\">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
?>

				</select>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="moreInfoClose()" />
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