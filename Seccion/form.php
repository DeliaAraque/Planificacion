<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Nueva sección</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="seccion" method="POST" action="moduloPlanificacion/Seccion/guardar.php" data-exe="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="id" placeholder="Sección" class="form-control" pattern="^[A-Z]$" required="required" />
				<p class="help-block">Solo está permitido un caracterer alfabético en mayusculas. Ej: A.</p>
			</div>

			<div class="form-group"> 
				Turno:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="turno" value="d" checked="checked" required="required"> Diurno </label>
					<label class="radio-inline"><input type="radio" name="turno" value="n" required="required"> Nocturno </label>
				</div>
			</div>

			<div class="form-group">
				<input type="text" name="multiplicador" placeholder="Multiplicador" class="form-control" pattern="^[0-9]+(\.[0-9])*$" required="required" />
				<p class="help-block">El multiplicador determina si las horas de las unidades curriculares deben aumentar o disminuir según el turno. Ej: 1.5.</p>
			</div>

			<div class="form-group">
				<label class="checkbox-inline"><input type="checkbox" name="grupos" value="1"> Dividir en grupos </label>
			</div>

			<div class="form-group">
				<select name="carrera" class="form-control" onChange="selectSede()" required="required">
					<option value="">Carrera</option>

<?php
	require "../../lib/conexion.php";

	$sql="
		select c.id as id, c.nombre as nombre
		from periodo as p
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=\"idCarrera\" 
		where p.tipo='p' 
		group by c.id, c.nombre 
		order by c.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($carrera=pg_fetch_object($exe))
		echo "<option value=\"$carrera->id\">$carrera->nombre</option>";
?>

				</select>
			</div>

			<div class="form-group">
				<select name="sede" class="form-control" onChange="selectPeriodos()" required="required">
					<option value="">Sede</option>
				</select>
				<p class="help-block">Antes de poder seleccionar algúna sede, debe elegir una carrera.</p>
			</div>

			<div class="form-group">
				<select name="periodo" class="form-control" onChange="selectEstructuras()" required="required">
					<option value="">Periodo académico</option>
				</select>
				<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir una sede.</p>
			</div>


			<div class="form-group">
				<select name="estructura" class="form-control" onChange="selectPeriodosE()" required="required">
					<option value="">Estructura</option>
				</select>
				<p class="help-block">Antes de poder seleccionar alguna estructura, debe elegir alguna carrera.</p>
			</div>

			<div class="form-group" id="periodoEstructura">
				<select name="periodoEstructura" class="form-control" required="required">
					<option value="">Periodo</option>
				</select>
				<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir alguna estructura.</p>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>

<script>
	function selectSede() {
		var carrera = document.seccion.carrera;
		var sede = document.seccion.sede;

		if(! carrera.value) {
			sede.innerHTML = "<option value=\"\">Sede</option>";
			document.seccion.periodo.innerHTML = "<option value=\"\">Periodo académico</option>";
			document.seccion.estructura.innerHTML = "<option value=\"\">Estructura</option>";
			document.seccion.periodoEstructura.innerHTML = "<option value=\"\">Periodo</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/sedes.php', sede, "carrera=" + carrera.value);
	}

	function selectPeriodos() {
		var carrera = document.seccion.carrera;
		var sede = document.seccion.sede;
		var periodo = document.seccion.periodo;

		if(! sede.value) {
			periodo.innerHTML = "<option value=\"\">Periodo académico</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/periodos.php', periodo, "carrera=" + carrera.value + "&sede=" + sede.value);
	}

	function selectEstructuras() {
		var carrera = document.seccion.carrera;
		var sede = document.seccion.sede;
		var periodo = document.seccion.periodo;
		var estructura = document.seccion.estructura;

		if(! periodo.value) {
			estructura.innerHTML = "<option value=\"\">Estructura</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/estructuras.php', estructura, "carrera=" + carrera.value + "&sede=" + sede.value + "&periodo=" + periodo.value);
	}

	function selectPeriodosE() {
		var estructura = document.seccion.estructura;
		var periodoEstructura = document.seccion.periodoEstructura;

		if(! estructura.value) {
			periodoEstructura.innerHTML = "<option value=\"\">Periodo</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/periodosEstructura.php', periodoEstructura, "estructura=" + estructura.value);
	}
</script>