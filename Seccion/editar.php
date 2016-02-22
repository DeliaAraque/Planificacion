<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$ID = htmlspecialchars($_POST["ID"], ENT_QUOTES);

	$sql = "
		select sec.\"ID\" as \"ID\", p.id as periodo, sec.id as id, sec.turno as turno, sec.multiplicador as multiplicador, sec.grupos as grupos, c.id as carrera, s.id as sede, e.id as estructura, sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\" as malla 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" and p.\"fechaFin\">=current_date 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\"
		where sec.\"ID\"='$ID' 
		order by p.id, sec.id, c.nombre, s.nombre, sec.\"periodoEstructura\"
	";
	$exe = pg_query($sigpa, $sql);
	$seccion = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header">Editar sección</h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<form name="seccion" method="POST" action="moduloPlanificacion/Seccion/modificar.php" data-exe="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Sección:
				<input type="text" name="id" placeholder="Sección" value="<?= $seccion->id; ?>" class="form-control" pattern="^[A-Z]$" required="required" />
				<input type="hidden" name="idAnt" value="<?= $seccion->id; ?>" />
				<p class="help-block">Solo está permitido un caracterer alfabético en mayusculas. Ej: A.</p>
			</div>

			<div class="form-group"> 
				Turno:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="turno" value="d" onClick="document.seccion.multiplicador.value='1';" <?php if($seccion->turno == "d") echo "checked=\"checked\""; ?> required="required"> Diurno </label>
					<label class="radio-inline"><input type="radio" name="turno" value="n" onClick="document.seccion.multiplicador.value='1.5';" <?php if($seccion->turno == "n") echo "checked=\"checked\""; ?> required="required"> Nocturno </label>
				</div>
			</div>

			<div class="form-group">
				Multiplicador:
				<input type="text" name="multiplicador" placeholder="Multiplicador" value="<?= $seccion->multiplicador; ?>" class="form-control" pattern="^[0-9]+(\.[0-9])*$" required="required" />
				<p class="help-block">El multiplicador determina si las horas de las unidades curriculares deben aumentar o disminuir según el turno. Ej: 1.5.</p>
			</div>

			<div class="form-group">
				<label class="checkbox-inline"><input type="checkbox" name="grupos" value="1" <?php if($seccion->grupos == "t") echo "checked=\"checked\""; ?>> Dividir en grupos </label>
			</div>

			<div class="form-group">
				Carrera:
				<select name="carrera" class="form-control" onChange="selectSede()" required="required">

<?php
	$sql="
		select c.id as id, c.nombre as nombre
		from periodo as p
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=\"idCarrera\" 
		where p.tipo='a' 
		group by c.id, c.nombre 
		order by c.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($carrera = pg_fetch_object($exe)) {
		echo "<option value=\"$carrera->id\"";

		if($seccion->carrera == $carrera->id)
			echo " selected=\"selected\"";

		echo ">$carrera->nombre</option>";
	}
?>

				</select>
			</div>

			<div class="form-group">
				Sede:
				<select name="sede" class="form-control" onChange="selectPeriodos()" required="required">

<?php
	$sql="
		select s.id as id, s.nombre as nombre 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join sede as s on s.id=\"idSede\" 
		where p.tipo='a' and cs.\"idCarrera\"='$seccion->carrera' 
		group by s.id, s.nombre 
		order by s.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($sede = pg_fetch_object($exe)) {
		echo "<option value=\"$sede->id\"";

		if($seccion->sede == $sede->id)
			echo " selected=\"selected\"";

		echo ">$sede->nombre</option>";
	}
?>

				</select>
				<p class="help-block">Antes de poder seleccionar algúna sede, debe elegir una carrera.</p>
			</div>

			<div class="form-group">
				Periodo académico:
				<select name="periodo" class="form-control" onChange="selectMallas()" required="required">

<?php
	$sql="
		select p.id as id
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
		where p.tipo='a' and cs.\"idCarrera\"='$seccion->carrera' and cs.\"idSede\"='$seccion->sede' 
		group by p.id
		order by p.id desc
	";
	$exe = pg_query($sigpa, $sql);

	while($periodo = pg_fetch_object($exe)) {
		echo "<option value=\"$periodo->id\"";

		if($seccion->periodo == $periodo->id)
			echo " selected=\"selected\"";

		echo ">$periodo->id</option>";
	}
?>

				</select>
				<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir una sede.</p>
			</div>

			<div class="form-group">
				Malla:
				<select name="malla" class="form-control" onChange="selectPeriodosE()" required="required">

<?php
	$sql="
		select mecs.id as id, m.id as nombre 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join \"mallaECS\" as mecs on mecs.\"idECS\"=ecs.id and mecs.estado is true 
			join malla as m on m.id=mecs.\"idMalla\" 
		where p.id='$seccion->periodo' and p.tipo='a' and cs.\"idCarrera\"='$seccion->carrera' and cs.\"idSede\"='$seccion->sede' 
		group by mecs.id, m.id
	";
	$exe = pg_query($sigpa, $sql);

	while($malla = pg_fetch_object($exe)) {
		echo "<option value=\"$malla->id\"";

		if($seccion->malla == $malla->id)
			echo " selected=\"selected\"";

		echo ">$malla->nombre</option>";
	}
?>

				</select>
				<p class="help-block">Antes de poder seleccionar alguna malla, debe elegir algún periodo académico.</p>
			</div>

			<div class="form-group" id="periodoEstructura">
				Periodo:
				<select name="periodoEstructura" class="form-control" required="required">

<?php
	$sql = "select estructura from estructura where id='$seccion->estructura'";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructura = json_decode($estructura->estructura);

	foreach($estructura->periodos as $periodo) {
		if(! $periodo->subperiodos) {
			echo "<option value=\"$periodo->id\"";

			if($seccion->periodoEstructura == $periodo->id)
				echo " selected=\"selected\"";

			echo ">$periodo->nombre</option>";
		}

		else {
			foreach($periodo->subperiodos as $subperiodo) {
				echo "<option value=\"$periodo->id-$subperiodo->id\"";

				if($seccion->periodoEstructura == "$periodo->id-$subperiodo->id")
					echo " selected=\"selected\"";

				echo ">$periodo->nombre - $subperiodo->nombre</option>";
			}
		}
	}
?>

				</select>
				<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir alguna malla.</p>
			</div>

			<div class="form-group text-center">
				<input type="hidden" name="ID" value="<?= $seccion->ID; ?>" />
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" />
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
			document.seccion.malla.innerHTML = "<option value=\"\">Malla</option>";
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

	function selectMallas() {
		var carrera = document.seccion.carrera;
		var sede = document.seccion.sede;
		var periodo = document.seccion.periodo;
		var malla = document.seccion.malla;

		if(! periodo.value) {
			malla.innerHTML = "<option value=\"\">Malla</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/mallas.php', malla, "carrera=" + carrera.value + "&sede=" + sede.value + "&periodo=" + periodo.value);
	}

	function selectPeriodosE() {
		var malla = document.seccion.malla;
		var periodoEstructura = document.seccion.periodoEstructura;

		if(! malla.value) {
			periodoEstructura.innerHTML = "<option value=\"\">Periodo</option>";
			return false;
		}

		embem('moduloPlanificacion/Seccion/periodosEstructura.php', periodoEstructura, "mecs=" + malla.value);
	}
</script>