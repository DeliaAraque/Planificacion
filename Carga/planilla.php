<?php
	session_start();
	require "../../lib/conexion.php";

	$periodo = htmlspecialchars($_GET["periodo"], ENT_QUOTES);
	$mecs = htmlspecialchars($_GET["mecs"], ENT_QUOTES);
	$previsualizar = isset($_GET["previsualizar"]);
	$limite = 3;

	function horasDisponibles($profesor, $periodo, $ecs, $mecs) {
		global $sigpa;

		$re = "[0-9]{7,}";

		if(! ereg("$re", $profesor))
			exit;

		$periodo = htmlspecialchars($periodo, ENT_QUOTES);

		if($ecs) {
			$ecs = htmlspecialchars($ecs, ENT_QUOTES);

			$sql = "
				select *
				from periodo 
				where id='$periodo'
				 and tipo='a'
				 and \"idECS\"='$ecs'
			";
		}

		else {
			$mecs = htmlspecialchars($mecs, ENT_QUOTES);

			$sql = "
				select *
				from periodo 
				where id='$periodo'
				 and tipo='a'
				 and \"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')
			";
		}

		$exe = pg_query($sigpa, $sql);
		$periodo = pg_fetch_object($exe);

		$sql = "
			select p.cedula as cedula, d.horas as dedicacion, p.condicion as condicion 
			from profesor as p 
				join dedicacion as d on d.id=p.dedicacion
			where p.cedula='$profesor'
		";
		$exe = pg_query($sigpa, $sql);
		$profesor = pg_fetch_object($exe);

		$sql = "
			select ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, c.\"dividirHT\" as \"dividirHT\", s.multiplicador as multiplicador, s.grupos as grupos, ucm.tipo as tipo, c.\"horasComunes\" as \"horasComunes\"  
			from carga as c 
				join seccion as s on s.\"ID\"=c.\"idSeccion\" 
				join periodo as p on p.\"ID\"=s.\"idPeriodo\"
				join \"mallaECS\" as mecs on mecs.id=s.\"idMECS\" 
				join \"ucMalla\" as ucm on ucm.\"idMalla\"=mecs.\"idMalla\" and ucm.\"idUC\"=c.\"idUC\" and ucm.periodo=s.\"periodoEstructura\" 
			where c.\"idProfesor\"='$profesor->cedula' and p.\"fechaFin\" >= '$periodo->fechaInicio' and p.\"fechaFin\" <= '$periodo->fechaFin'
		";

		if($p->condicion != 3)
			$sql .= " or c.\"idSuplente\"='$profesor->cedula'";

		$exe = pg_query($sigpa, $sql);

		$total = 0;

		while($horas = pg_fetch_object($exe)) {
			$ht = $horas->ht * $horas->multiplicador;
			$hp = $horas->hp * $horas->multiplicador;

			if($horas->tipo == "t") {
				if($horas->grupos == "t") {
					$hp *= 2;

					if($horas->dividirHT == "t")
						$ht *= 2;
				}
			}

			$descuento = $horas->horasComunes * $horas->multiplicador;
			$total += $ht + $hp - $descuento + $horas->horasComunes;
		}

		return $profesor->dedicacion - $total;
	}

	$sql = "
		select per.\"ID\" as \"ID\", per.id as id, per.\"fechaInicio\" as \"fechaInicio\", per.\"fechaFin\" as \"fechaFin\", e.id as \"idEstructura\", e.nombre as estructura, c.id as \"idCarrera\", c.nombre as carrera, s.id as \"idSede\", s.nombre as sede 
		from periodo as per 
			join \"estructuraCS\" as ecs on ecs.id=per.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\"
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where per.id='$periodo' and per.tipo='a' and per.\"idECS\"=(select \"idECS\" from \"mallaECS\" where id='$mecs')
	";
	$exe = pg_query($sigpa, $sql);
	$periodo = pg_fetch_object($exe);

	$periodo->fechaInicio = explode("-", $periodo->fechaInicio);
	$periodo->fechaInicio = $periodo->fechaInicio[2] . "/" . $periodo->fechaInicio[1] . "/" . $periodo->fechaInicio[0];

	$periodo->fechaFin = explode("-", $periodo->fechaFin);
	$periodo->fechaFin = $periodo->fechaFin[2] . "/" . $periodo->fechaFin[1] . "/" . $periodo->fechaFin[0];

	$sql = "select id from \"carreraSede\" where \"idCarrera\" = '$periodo->idCarrera' and \"idSede\" = '$periodo->idSede'";
	$exe = pg_query($sigpa, $sql);
	$cs = pg_fetch_object($exe);
?>

<meta charset="utf-8">

<style>
	h1,
	h2 {
		text-align: center;
		text-transform: uppercase;
	}

	h4 {
		padding-top: 1em;
		text-align: center;
	}

	table {
		border-collapse: collapse;
		<?php if(! $previsualizar) echo "font-size: 8pt;"; ?>
	}

	table.profesores {
		width: 100%;

		margin: auto;
	}

	table.secciones {
		width: 30%;
		display: inline-block;
		<?php if(! $previsualizar) echo "font-size: 6pt;"; ?>
		margin-left: 2%;
	}

	tr, td, th {
		page-break-inside: avoid !important;
	}

	th,
	td {
		border: 1px solid #000;
		padding: 0.5em 2em;
	}
</style>

<?php
	if(! $previsualizar) {
?>

<br/><br/><br/><br/><br/>
<h1>Planificación Académica</h1>
<h1><?= $periodo->carrera; ?></h1>
<h2><?= $periodo->sede; ?></h2>
<h2><?= $periodo->id; ?></h2>

<div style="page-break-after: always;"></div>

<?php
	}

	$sql="
		select sec.\"periodoEstructura\" as \"periodoEstructura\", array_to_string(array_agg(sec.id), '&') as secciones
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join \"mallaECS\" as mecs on mecs.\"idECS\"=ecs.id and mecs.estado is true 
			join malla as m on m.id=mecs.\"idMalla\" 
		where p.id='$periodo->id' and cs.\"idCarrera\"='$periodo->idCarrera' and cs.\"idSede\"='$periodo->idSede' and sec.\"idMECS\"='$mecs' 
		group by sec.\"periodoEstructura\"
	";
	$exe=pg_query($sigpa, $sql);

	while($seccion = pg_fetch_object($exe)) {
		$secciones[$seccion->periodoEstructura] = explode("&", $seccion->secciones);
		$periodos[] = $seccion->periodoEstructura;
	}

	$sql = "select * from estructura where id='$periodo->idEstructura'";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructura = json_decode($estructura->estructura);

	foreach($estructura->periodos as $periodoEst) {
		/*
			Propiedades del objeto Periodo:
				· Identificador (id)
				· Nombre (nombre)
				· Duración (duracion)
				· Subperiodos (subperiodos)
		*/

		if(! $periodoEst->subperiodos) {
			if(! in_array($periodoEst->id, $periodos))
				continue;

			$nSecciones = count($secciones[$periodoEst->id]);
?>

<table class="secciones">
	<tr>
		<th colspan="3"><?= "$periodoEst->nombre ($nSecciones " . (($nSecciones == 1) ? "sección" : "secciones") . ")"; ?></th>
	</tr>

	<tr>
		<th>Asignatura</th>
		<th>Nº Horas</th>
		<th>Total</th>
	</tr>

<?php
			$sql = "
				select uc.id as id, uc.nombre nombre, ucm.tipo as tipo, ucm.\"horasTeoricas\" + ucm.\"horasPracticas\" as horas
				from \"mallaECS\" as mecs 
					join malla as m on m.id=mecs.\"idMalla\" 
					join \"ucMalla\" as ucm on ucm.\"idMalla\"=m.id 
					join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
				where mecs.id='$mecs' and ucm.periodo='$periodoEst->id' and uc.\"idCarrera\"='$periodo->idCarrera' 
				order by uc.nombre
			";
			$exe = pg_query($sigpa, $sql);

			while($uc = pg_fetch_object($exe)) {
?>

	<tr>
		<td><?= $uc->nombre; ?></td>
		<td><?= $uc->horas; ?></td>
		<td>

<?php
				if($uc->tipo == "f")
					echo $uc->horas * $nSecciones;
?>

		</td>
	</tr>

<?php
			}
?>

</table>

<?php
		}

		else {
			/*
				Propiedades del objeto Subperiodos:
					· Identificador (id)
					· Nombre (nombre)
					· Duración (duracion)
			*/

			foreach($periodoEst->subperiodos as $subperiodo) {
				if(! in_array("$periodoEst->id-$subperiodo->id", $periodos))
					continue;

				$nSecciones = count($secciones["$periodoEst->id-$subperiodo->id"]);
?>

<table class="secciones">
	<tr>
		<th colspan="3"><?= "$periodoEst->nombre $subperiodo->nombre ($nSecciones " . (($nSecciones == 1) ? "sección" : "secciones") . ")"; ?></th>
	</tr>

	<tr>
		<th>Asignatura</th>
		<th>Nº Horas</th>
		<th>Total</th>
	</tr>

<?php
			$sql = "
				select uc.id as id, uc.nombre nombre, ucm.tipo as tipo, ucm.\"horasTeoricas\" + ucm.\"horasPracticas\" as horas
				from \"mallaECS\" as mecs 
					join malla as m on m.id=mecs.\"idMalla\" 
					join \"ucMalla\" as ucm on ucm.\"idMalla\"=m.id 
					join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
				where mecs.id='$mecs' and ucm.periodo='$periodoEst->id-$subperiodo->id' and uc.\"idCarrera\"='$periodo->idCarrera' 
				order by uc.nombre
			";
			$exe = pg_query($sigpa, $sql);

			while($uc = pg_fetch_object($exe)) {
?>

	<tr>
		<td><?= $uc->nombre; ?></td>
		<td><?= $uc->horas; ?></td>
		<td>

<?php
				if($uc->tipo == "f")
					echo $uc->horas * $nSecciones;
?>

		</td>
	</tr>

<?php
			}
?>

</table>

<?php
			}
		}
	}
?>

<div style="page-break-after: always;"></div>

<?= profesor(3); ?>
<?= profesor(3, "Aux"); ?>
<?= profesor(1); ?>

<?php
	function profesor($condicion, $categoria) {
		global $sigpa, $periodo, $mecs, $previsualizar, $limite, $cs;

		$totalProfesores = 0;

		$sql = "
			select car.\"idProfesor\" as profesor 
			from carga as car 
				join profesor as prof on prof.cedula=car.\"idProfesor\" or prof.cedula=car.\"idSuplente\" 
				join seccion as sec on sec.\"ID\"=car.\"idSeccion\" 
			where prof.condicion='$condicion'" . (($categoria) ? " and prof.categoria like '$categoria%' " : "") . " and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs' 
			group by car.\"idProfesor\"
		";
		$exe = pg_query($sigpa, $sql);

		while($profesor = pg_fetch_object($exe))
			++$totalProfesores;

		if(! $totalProfesores)
			return false;

		if($condicion === 3) {
			if(! $categoria)
				$titulo = "<h4>Personal Docente Ordinario</h4>";

			else if($categoria == "Aux")
				$titulo = "<h4>Personal Auxiliar Docente Ordinario</h4>";
		}

		else if($condicion === 1)
			$titulo = "<h4>Personal Docente Contratado</h4>";

		echo $titulo;
?>

<table class="profesores">
<tr>
	<th rowspan="2">
		Profesor
	</th>

	<th rowspan="2">
		Ded.
	</th>

	<th rowspan="2">
		Cat.
	</th>

	<th rowspan="2">
		Unidad Curricular
	</th>

	<th rowspan="2">
		Sec.
	</th>

	<th colspan=2>
		Horas
	</th>

	<th rowspan="2">
		Horas sem.
	</th>

	<th rowspan="2">
		Observaciones
	</th>
</tr>

<tr>
	<th title="Horas Teoricas">
		T
	</th>

	<th title="Horas Practicas">
		P
	</th>
</tr>

<?php
		// Profesores

		$sql = "
			select prof.cedula as cedula, per.apellido as apellido, per.nombre as nombre, pro.nombre as profesion, prof.categoria as categoria, prof.dedicacion as dedicacion, ded.horas as horas, string_agg(concat_ws('&', car.\"idUC\", sec.\"periodoEstructura\"), '&&') as cargas
			from carga as car 
				join profesor as prof on prof.cedula=car.\"idProfesor\" or prof.cedula=car.\"idSuplente\" 
				join persona as per on per.cedula=prof.cedula 
				join profesion as pro on pro.id=prof.profesion 
				join categoria as cat on cat.id=prof.categoria 
				join condicion as con on con.id=prof.condicion 
				join dedicacion as ded on ded.id=prof.dedicacion 
				join seccion as sec on sec.\"ID\"=car.\"idSeccion\"
				join \"unidadCurricular\" as uc on uc.id = car.\"idUC\" 
			where prof.condicion='$condicion'" . (($categoria) ? " and prof.categoria like '$categoria%' " : "") . " and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs' 
			group by prof.cedula, per.apellido, per.nombre, pro.nombre, prof.categoria, prof.dedicacion, ded.horas
			order by per.apellido, per.nombre, prof.cedula
		";
		$exe = pg_query($sigpa, $sql);

		$nProfesores = 1;

		while($profesor = pg_fetch_object($exe)) {
			$cargas = explode("&&", $profesor->cargas);
			$cargas = array_unique($cargas);
			$n = count($cargas);
			$hd = horasDisponibles($profesor->cedula, $periodo->id, null, $mecs);

			// Carga asignada

			$sql = "
				select uc.id as \"idUC\", uc.nombre as \"unidadCurricular\", car.\"nuevoNombre\" as \"nuevoNombre\", sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\", string_agg(concat_ws('&', sec.id, sec.turno, sec.multiplicador, sec.grupos, sec.\"ID\"), '&&' order by sec.id) as secciones
				from carga as car 
					join seccion as sec on sec.\"ID\"=car.\"idSeccion\"
					join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
					join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
					join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
					join carrera as c on c.id=cs.\"idCarrera\" 
					join sede as s on s.id=cs.\"idSede\"
					join \"mallaECS\" as mecs on mecs.id=sec.\"idMECS\" 
					join \"unidadCurricular\" as uc on uc.id=car.\"idUC\" 
					join \"ucMalla\" as ucm on ucm.\"idUC\"=uc.id
				where (car.\"idProfesor\"='$profesor->cedula' or car.\"idSuplente\"='$profesor->cedula') and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs'
				group by uc.id, uc.nombre, car.\"nuevoNombre\", sec.\"periodoEstructura\", sec.\"idMECS\"
				order by uc.nombre, sec.\"periodoEstructura\"
			";
			$exe2 = pg_query($sigpa, $sql);
			$carga = pg_fetch_object($exe2);
?>

<tr>
	<td rowspan="<?= $n; ?>">
		<?= "$profesor->apellido $profesor->nombre"; ?><br/>
		CI: <?= $profesor->cedula; ?><br/>
		<?= $profesor->profesion; ?>
	</td>
	<td rowspan="<?= $n; ?>"><?= $profesor->dedicacion; ?></td>
	<td rowspan="<?= $n; ?>"><?= $profesor->categoria; ?></td>
	<?= mostrarCarga($carga, $profesor->cedula); ?>
	<td rowspan="<?= $n; ?>">

<?php
			$sql = "
				select *
				from observacion
				where
					\"idCS\" = '$cs->id'
					and \"idPeriodo\" = '$periodo->id'
					and \"idProfesor\" = '$profesor->cedula'
			";
			$exe3 = pg_query($sigpa, $sql);
			$observacion = pg_fetch_object($exe3);

			if($previsualizar) {
?>

		<form action="guardarObservacion.php" method="POST" role="form">
			<textarea style="display: block;" onChange="this.form.contenido.value = this.value; sendForm(this.form);"><?= $observacion->contenido; ?></textarea>

			<input type="hidden" name="contenido" value="<?= $observacion->contenido; ?>" />
			<input type="hidden" name="periodo" value="<?= $periodo->id; ?>" />
			<input type="hidden" name="carreraSede" value="<?= $cs->id; ?>" />
			<input type="hidden" name="profesor" value="<?= $profesor->cedula; ?>" />
		</form>

<?php
			}

			else {
				if($observacion->contenido)
					echo "$observacion->contenido<br/>";
			}

			$sql = "
				select car.\"idProfesor\" as profesor, car.\"idSuplente\" as suplente, sec.id as seccion, sec.\"periodoEstructura\" as \"periodoEstructura\", uc.nombre as \"unidadCurricular\", car.\"nuevoNombre\" as \"nuevoNombre\"
				from carga as car 
					join seccion as sec on sec.\"ID\"=car.\"idSeccion\" 
					join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
					join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
					join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
					join carrera as c on c.id=cs.\"idCarrera\" 
					join sede as s on s.id=cs.\"idSede\"
					join \"mallaECS\" as mecs on mecs.id=sec.\"idMECS\" 
					join \"unidadCurricular\" as uc on uc.id=car.\"idUC\" 
				where (car.\"idProfesor\"='$profesor->cedula' or car.\"idSuplente\"='$profesor->cedula') and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs'
				order by uc.nombre, sec.\"periodoEstructura\", sec.id
			";
			$exe3 = pg_query($sigpa, $sql);

			$suplenciaAnt = "";
			$ucSuplenciaAnt = "";
			$suplenteAnt = "";
			$ucSuplenteAnt = "";

			while($suplencia = pg_fetch_object($exe3)) {
				if($suplencia->profesor != $profesor->cedula) {
					$sql = "select apellido, nombre, cedula from persona where cedula='$suplencia->profesor'";
					$exe4 = pg_query($sigpa, $sql);
					$profesorCarga = pg_fetch_object($exe4);

					if($suplenciaAnt != $profesorCarga->cedula) {
						echo "<br/><b>Suplente de</b> $profesorCarga->apellido $profesorCarga->nombre en $suplencia->unidadCurricular ($suplencia->periodoEstructura) - $suplencia->seccion";
						$suplenciaAnt = $profesorCarga->cedula;
						$ucSuplenciaAnt = $carga->unidadCurricular;
					}

					else {
						if($ucSuplenciaAnt != $suplencia->unidadCurricular) {
							echo "; $suplencia->unidadCurricular ($suplencia->periodoEstructura) - $suplencia->seccion";
							$ucSuplenciaAnt = $carga->unidadCurricular;
						}

						else 
							echo ", $suplencia->seccion";
					}
				}

				else if($suplencia->suplente) {
					$sql = "select apellido, nombre, cedula from persona where cedula='$suplencia->suplente'";
					$exe4 = pg_query($sigpa, $sql);
					$suplente = pg_fetch_object($exe4);

					if($suplenteAnt != $suplente->cedula) {
						echo "<br/><b>Suple</b> $suplente->apellido $suplente->nombre en $suplencia->unidadCurricular ($suplencia->periodoEstructura) - $suplencia->seccion";
						$suplenteAnt = $suplente->cedula;
						$ucSuplenteAnt = $carga->unidadCurricular;
					}

					else {
						if($ucSuplenteAnt != $suplencia->unidadCurricular) {
							echo "; $suplencia->unidadCurricular ($suplencia->periodoEstructura) - $suplencia->seccion";
							$ucSuplenteAnt = $carga->unidadCurricular;
						}

						else 
							echo ", $suplencia->seccion";
					}
				}
			}

			if(($suplenteAnt) || ($suplenciaAnt))
				echo "<br/>";
?>

		<b>Total: <?= $profesor->horas . (($profesor->horas == 1) ? " hora" : " horas"); ?></b>

<?php 
	if ($hd < 0) {
		echo "<br/>+" . ($hd * -1) . " de trabajo voluntario";
	}
?>
	</td>
</tr>

<?php
			while(--$n > 0) {
				$carga = pg_fetch_object($exe2);
?>

<tr>
	<?= mostrarCarga($carga); ?>
</tr>

<?php
			}

			if(! $previsualizar) {
				if(($nProfesores % $limite == 0) && ($nProfesores < $totalProfesores)) {
?>

</table>

<div style="page-break-after: always;"></div>

<?= $titulo; ?>

<table class="profesores">
<tr>
	<th rowspan="2">
		Profesor
	</th>

	<th rowspan="2">
		Ded.
	</th>

	<th rowspan="2">
		Cat.
	</th>

	<th rowspan="2">
		Unidad Curricular
	</th>

	<th rowspan="2">
		Sec.
	</th>

	<th colspan=2>
		Horas
	</th>

	<th rowspan="2">
		Horas sem.
	</th>

	<th rowspan="2">
		Observaciones
	</th>
</tr>

<tr>
	<th title="Horas Teoricas">
		T
	</th>

	<th title="Horas Practicas">
		P
	</th>
</tr>

<?php
				}
			}

			++$nProfesores;
		}
?>

</table>
<div style="page-break-after: always;" class="saltoPagina"></div>

<?php
	}

	function mostrarCarga($carga, $profesor) {
		global $sigpa, $periodo, $mecs;

		$sql = "
			select ucm.\"horasTeoricas\" as ht, ucm.\"horasPracticas\" as hp, ucm.tipo as tipo 
			from \"unidadCurricular\" as uc 
				join \"ucMalla\" as ucm on ucm.\"idUC\"=uc.id 
			where uc.id='$carga->idUC' and ucm.\"idMalla\"=(select \"idMalla\" from \"mallaECS\" where id='$mecs')
		";
		$exe = pg_query($sigpa, $sql);
		$uc = pg_fetch_object($exe);
?>

		<td><?= "$carga->unidadCurricular " . (($carga->nuevoNombre) ? " - $carga->nuevoNombre " : "") . " ($carga->periodoEstructura)"; ?></td>
		<td>

<?php
		$secciones = array_unique(explode("&&", $carga->secciones));
		$total = 0;

		foreach ($secciones as $seccion) {
			$seccion = explode("&", $seccion);

			$sql = "select \"dividirHT\", \"horasComunes\" from carga where \"idProfesor\" = '$profesor' and \"idSeccion\" = '$seccion[4]' and \"idUC\" = '$carga->idUC'";
			$exe = pg_query($sigpa, $sql);
			$horasCarga = pg_fetch_object($exe);

			$ht = $uc->ht * $seccion[2];
			$hp = $uc->hp * $seccion[2];

			if($uc->tipo == "t") {
				if($seccion[3] == "t") {
					$hp *= 2;

					if($horasCarga->dividirHT == "t") {
						$ht *= 2;

						$seccion[0] = $seccion[0] . "1-" . $seccion[0] . "2";
					}

					else
						$seccion[0] = $seccion[0] . "(" . $seccion[0] . "1-" . $seccion[0] . "2)";
				}
			}

			$descuento = $horasCarga->horasComunes * $seccion[2];
			$total += $ht + $hp - $descuento + $horasCarga->horasComunes;

			echo (($seccion[1] != "d") ? "*" : "") . "$seccion[0] <br/>";
		}
?>

		</td>
		<td><?= $uc->ht; ?></td>
		<td><?= $uc->hp; ?></td>
		<td><?= $total; ?></td>

<?php
	}
?>

<script src="/css/template/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/lib/sigpa.js"></script>

<script>
	var div = document.querySelectorAll("body .saltoPagina");

	div[div.length - 1].style.pageBreakAfter = "avoid";

	formularios();
</script>