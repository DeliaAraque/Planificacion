<?php
	session_start();
	require "../../lib/conexion.php";

	$periodo = htmlspecialchars($_GET["periodo"], ENT_QUOTES);
	$mecs = htmlspecialchars($_GET["mecs"], ENT_QUOTES);
	$previsualizar = isset($_GET["previsualizar"]);
	$limite = 4;

	$sql = "
		select per.\"ID\" as \"ID\", per.id as id, per.\"fechaInicio\" as \"fechaInicio\", per.\"fechaFin\" as \"fechaFin\", e.nombre as estructura, c.nombre as carrera, s.nombre as sede 
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
		width: 100%;

		border-collapse: collapse;
		<?php if(! $previsualizar) echo "font-size: 8pt;"; ?>

		margin: auto;
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
?>

<?= profesor(3); ?>
<?= profesor(3, "Aux"); ?>
<?= profesor(1); ?>

<?php
	function profesor($condicion, $categoria) {
		global $sigpa, $periodo, $mecs, $previsualizar, $limite;

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

<table>
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
			select prof.cedula as cedula, per.apellido as apellido, per.nombre as nombre, pro.nombre as profesion, prof.categoria as categoria, prof.dedicacion as dedicacion, array_to_string(array_agg(car.\"idUC\"), '&') as cargas
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
			group by prof.cedula, per.apellido, per.nombre, pro.nombre, prof.categoria, prof.dedicacion 
			order by per.apellido, per.nombre, prof.cedula
		";
		$exe = pg_query($sigpa, $sql);

		$nProfesores = 1;

		while($profesor = pg_fetch_object($exe)) {
			$cargas = explode("&", $profesor->cargas);
			$cargas = array_unique($cargas);
			$n = count($cargas);

			// Carga asignada

			$sql = "
				select uc.id as \"idUC\", uc.nombre as \"unidadCurricular\", car.\"nuevoNombre\" as \"nuevoNombre\", sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\", string_agg(concat_ws('&', sec.id, sec.turno, sec.multiplicador, sec.grupos, sec.\"ID\"), '&&' order by sec.id) as secciones
				from carga as car 
					join seccion as sec on sec.\"ID\"=car.\"idSeccion\" and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs'
					join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
					join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
					join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
					join carrera as c on c.id=cs.\"idCarrera\" 
					join sede as s on s.id=cs.\"idSede\"
					join \"mallaECS\" as mecs on mecs.id=sec.\"idMECS\" 
					join \"unidadCurricular\" as uc on uc.id=car.\"idUC\" 
					join \"ucMalla\" as ucm on ucm.\"idUC\"=uc.id
				where car.\"idProfesor\"='$profesor->cedula' or car.\"idSuplente\"='$profesor->cedula'
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
	<td rowspan="<?= $n; ?>">Observación</td>
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

<table>
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

			$sql = "select \"dividirHT\" from carga where \"idProfesor\" = '$profesor' and \"idSeccion\" = '$seccion[4]' and \"idUC\" = '$carga->idUC'";
			$exe = pg_query($sigpa, $sql);
			$dividirHT = pg_fetch_object($exe);

			$ht = $uc->ht * $seccion[2];
			$hp = $uc->hp * $seccion[2];

			if($uc->tipo == "t") {
				if($seccion[3] == "t") {
					$hp *= 2;

					if($dividirHT->dividirHT == "t") {
						$ht *= 2;

						$seccion[0] = $seccion[0] . "1-" . $seccion[0] . "2";
					}

					else
						$seccion[0] = $seccion[0] . "(" . $seccion[0] . "1-" . $seccion[0] . "2)";
				}
			}

			$total += $ht + $hp;

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

<script>
	var div = document.querySelectorAll("body .saltoPagina");

	div[div.length - 1].style.pageBreakAfter = "avoid";
</script>