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
		select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, ucm.tipo as tipo 
		from \"unidadCurricular\" as uc 
			join \"ucMalla\" as ucm on ucm.\"idUC\"=uc.id 
		where uc.id='$id' and ucm.\"idMalla\"=(select \"idMalla\" from \"mallaECS\" where id='$mecs')
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
				<select name="profesor" class="form-control" onChange="horasDisponibles(this.value)" required="required">
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

	while($profesor = pg_fetch_object($exe)) {
		$sql = "
			select p.cedula as cedula, d.horas as dedicacion, p.condicion as condicion 
			from profesor as p 
				join dedicacion as d on d.id=p.dedicacion
			where p.cedula='$profesor->cedula'
		";
		$exe2 = pg_query($sigpa, $sql);
		$p = pg_fetch_object($exe2);

		$sql = "
			select ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, c.\"dividirHT\" as \"dividirHT\", s.multiplicador as multiplicador, s.grupos as grupos 
			from carga as c 
				join seccion as s on s.\"ID\"=c.\"idSeccion\" 
				join periodo as p on p.\"ID\"=s.\"idPeriodo\" 
				join \"mallaECS\" as mecs on mecs.id=s.\"idMECS\" 
				join \"ucMalla\" as ucm on ucm.\"idMalla\"=mecs.\"idMalla\" and ucm.\"idUC\"=c.\"idUC\" and ucm.periodo=s.\"periodoEstructura\" 
			where p.id='$periodo' and c.\"idProfesor\"='$p->cedula'
		";

		if($p->condicion != 3)
			$sql .= " or c.\"idSuplente\"='$p->cedula'";

		$exe2 = pg_query($sigpa, $sql);

		$total = 0;

		while($horas = pg_fetch_object($exe2)) {
			$ht = $horas->ht * $horas->multiplicador;
			$hp = $horas->hp * $horas->multiplicador;

			if($horas->grupos == "t") {
				$hp *= 2;

				if($horas->dividirHT == "t")
					$ht *= 2;
			}

			$total += $ht + $hp;
		}

		$options .= "<option value=\"$profesor->cedula\">$profesor->apellido $profesor->nombre ($profesor->cedula) - Horas disponibles: " . ($p->dedicacion - $total) . "</option>";
	}

	$sql = "
		select s.\"ID\" as \"ID\", s.id as id, s.grupos as grupos, s.turno as turno, s.multiplicador as multiplicador 
		from seccion as s 
		where s.\"idPeriodo\"=(select \"ID\" from periodo where id='$periodo' and tipo='a' and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')) and s.\"periodoEstructura\"='$periodoEstructura' 
		order by s.id
	";
	$exe = pg_query($sigpa, $sql);

	while($seccion = pg_fetch_object($exe)) {
		$sql = "select count(id) as n from carga where \"idSeccion\"='$seccion->ID' and \"idUC\"='$uc->id'";
		$exe2 = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe2);

		if($n->n > 0)
			continue;
?>

			<div class="row">
				<div class="col-xs-6">
					<div class="form-group">
						<label class="checkbox-inline">
							<input type="checkbox" name="seccion[]" value="<?= $seccion->id; ?>" onClick="enableSec(this); grupos(this)"> <?= $seccion->id; ?> 

<?php
		if($uc->tipo == "t") {
			if($seccion->grupos == "t")
				echo " <i class=\"fa fa-fw fa-users\" title=\"Se divide en grupos\"></i> <input type=\"hidden\" id=\"grupos$seccion->id\" value=\"1\" />";
		}

		if($seccion->turno == "n")
			echo " <i class=\"fa fa-fw fa-moon-o\" title=\"Nocturna\"></i> <input type=\"hidden\" id=\"nocturna$seccion->id\" value=\"1\" />";
?>

						</label>
						<input type="hidden" name="ID<?= $seccion->id; ?>" value="<?= $seccion->ID; ?>" />
						<input type="hidden" id="multiplicador<?= $seccion->id; ?>" value="<?= $seccion->multiplicador; ?>" />
						<input type="hidden" id="horas<?= $seccion->id; ?>" value="0" />
					</div>
				</div>

				<div class="col-xs-6"><div class="form-group">
					<div id="sec<?= $seccion->id; ?>"></div>

<?php
		if($uc->tipo == "t") {
			if($seccion->grupos == "t") {
?>

					<div class="form-group">
						<label class="checkbox-inline"><input type="checkbox" name="dividirHT<?= $seccion->id; ?>" id="dividirHT<?= $seccion->id; ?>" value="<?= $seccion->id; ?>" onClick="grupos(this)" disabled="disabled"> Dividir horas teóricas en grupos </label>
					</div>

<?php
			}
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
				Horas disponibles: <code style="font-size: 1.5em;" id="horasDisponibles">0</code>
			</div>

			<div class="form-group text-center">
				<input type="hidden" name="carrera" value="<?= $carrera; ?>" />
				<input type="hidden" name="sede" value="<?= $sede; ?>" />
				<input type="hidden" name="unidadCurricular" value="<?= $uc->id; ?>" />

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

		$("#horasDisponibles").text(0);
		embem('moduloPlanificacion/Carga/profesores.php', profesor, "carrera=<?= $carrera; ?>&sede=<?= $sede; ?>&condicion=" + condicion);
	}

	function enableSec(seccion) {
		if(! document.carga.profesor.value) {
			popUp("Debe seleccionar un profesor");
			seccion.checked = false;
			return false;
		}

		var suplenteSelect = $("#suplente" + seccion.value);
		var dividirHT = $("#dividirHT" + seccion.value);

		if(seccion.checked) {
			suplenteSelect.removeAttr("disabled");
			dividirHT.removeAttr("disabled");
		}

		else {
			suplenteSelect.attr("disabled", "disabled");
			$("#suplente" + seccion.value +" option:first-child").attr("selected", "selected");
			dividirHT.attr("checked", false);
			dividirHT.attr("disabled", "disabled");
		}
	}

	function grupos(seccion) {
		if(! document.carga.profesor.value) {
			return false;
		}

		var id = seccion.value;

		var multiplicador = document.querySelector("#multiplicador" + seccion.value).value;
		var ht = <?= $uc->ht; ?> * multiplicador;
		var hp = <?= $uc->hp; ?> * multiplicador;

		if($("#grupos" + seccion.value).attr("value") == 1) {
			hp *= 2;

			if(! document.querySelector("#dividirHT" + seccion.value).checked)
				id = seccion.value + " (" + seccion.value + "1 - " + seccion.value + "2)";

			else {
				id = seccion.value + "1 - " + seccion.value + "2";
				ht *= 2;
			}
		}

		if($("#nocturna" + seccion.value).attr("value") == 1)
			id = "* " + id;

		if(seccion.value == id)
			id = "";

		else
			id += " - ";

		var horasAnt = $("#horas" + seccion.value).attr("value");

		var horas = parseFloat(ht) + parseFloat(hp);
		var horasDisponibles = $("#horasDisponibles").text();
		$("#horasDisponibles").text(parseFloat(horasDisponibles) + parseFloat(horasAnt));

		if((seccion.checked) || (seccion.name == "dividirHT" + seccion.value)) {
			$("#horas" + seccion.value).attr("value", horas);
			$("#horasDisponibles").text(parseFloat($("#horasDisponibles").text()) - parseFloat(horas));
		}

		else
			$("#horas" + seccion.value).attr("value", 0);

		horas += (horas == 1) ? " hora" : " horas";

		$("#sec" + seccion.value).text(id + horas);
	}

	function horasDisponibles(profesor) {
		var horas = $("#horasDisponibles");

		if(! profesor) {
			horas.text("0");
			return false;
		}

		embem('moduloPlanificacion/Carga/horasDisponibles.php', horas, "profesor=" + profesor + "&periodo=<?= $periodo ?>");
	}
</script>