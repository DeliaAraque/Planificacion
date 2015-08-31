<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva malla</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="malla" method="POST" action="moduloPlanificacion/Malla/guardar.php" data-exe="embem('moduloPlanificacion/Malla/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Código" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+(\-[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$" onKeyUp="Verif(this)" required="required" />
				<p class="help-block">Solo están permitidos caracteres alfanuméricos y guiones. Ej: PNFI-2012.</p>
			</div>

			<div class="form-group">
				<input type="text" name="fecha" placeholder="Fecha de creación" class="form-control datePicker" pattern="^((0?[1-9]|[12][0-9])/0?2|((0?[1-9]|[12][0-9])|30)/(0?4|0?6|0?9|11)|((0?[1-9]|[12][0-9])|3[01])/(0?1|0?3|0?5|0?7|0?8|10|12))/[0-9]+$" required="required" />
				<p class="help-block">La fecha debe cumplir el formato Día/Mes/Año, Ej: 27/04/2011.</p>
			</div>

			<div class="form-group">
				<select name="carrera" class="form-control" onChange="selectES(this.value)" required="required">
					<option value="">Carrera</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select c.id as id, c.nombre as nombre
		from \"estructuraCS\" as ecs 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=\"idCarrera\" 
		group by c.id, c.nombre 
		order by c.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($carrera=pg_fetch_object($exe))
		echo "
					<option value=\"$carrera->id\">$carrera->nombre</option>";
?>

				</select>
			</div>

			<div class="form-group">
				<select name="estructura" class="form-control" onChange="estructuraMalla(document.malla.carrera.value, this.value)" required="required">
					<option value="">Estructura</option>
				</select>
				<p class="help-block">Antes de poder seleccionar alguna estructura, debe elegir alguna carrera.</p>
			</div>

			<div class="form-group" id="sedes">
			</div>

			<div class="form-group" id="malla">
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Malla/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>

<script>
	function selectES(carrera) {
		if(! carrera) {
			document.malla.estructura.innerHTML = "<option value=\"\">Estructura</option>";
			$("#sedes").html("");
			$("#malla").html("");
			return false;
		}

		embem('moduloPlanificacion/Malla/estructuras.php', document.malla.estructura, "carrera=" + carrera);
		embem('moduloPlanificacion/Malla/sedes.php', "#sedes", "carrera=" + carrera);
	}

	function estructuraMalla(carrera, estructura) {
		if(! estructura) {
			$("#malla").html("");
			return false;
		}

		embem('moduloPlanificacion/Malla/malla.php', "#malla", "carrera=" + carrera + "&estructura=" + estructura);
	}
</script>