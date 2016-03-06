<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$orden = $_POST["orden"];

	$sql = "select * from firma where orden='$orden'";
	$exe = pg_query($sigpa, $sql);
	$firma = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Editar firma</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="firma" method="POST" action="moduloPlanificacion/Carga/modificarFirma.php" data-exe="moreInfoClose(); embem('moduloPlanificacion/Carga/firmas.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Orden:
				<input type="text" name="orden" value="<?= $firma->orden; ?>" placeholder="Orden" class="form-control" pattern="^[0-9]+$" onKeyUp="if(this.value != document.firma.ordenAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="ordenAnt" value="<?= $firma->orden; ?>" />
				<p class="help-block">Solo están permitidos caracteres numéricos. Ej: 1.</p>
			</div>

			<div class="form-group">
				Cargo:
				<input type="text" name="cargo" value="<?= $firma->cargo; ?>" placeholder="Cargo" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" onKeyUp="if(this.value != document.firma.cargoAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="cargoAnt" value="<?= $firma->cargo; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Vicerrector Académico.</p>
			</div>

			<div class="form-group">
				Profesor:
				<select name="idProfesor" class="form-control" required="required">

<?php
	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
		order by p.apellido, p.nombre, p.cedula
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe)) {
		echo "<option value=\"$profesor->cedula\"";

		if($firma->idProfesor == $profesor->cedula)
			echo "selected=\"selected\"";

		echo ">$profesor->apellido $profesor->nombre ($profesor->cedula)</option>";
	}
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