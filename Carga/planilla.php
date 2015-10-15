<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$periodo = htmlspecialchars($_GET["periodo"], ENT_QUOTES);
	$mecs = htmlspecialchars($_GET["mecs"], ENT_QUOTES);

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

<style>
	header {
		font-size: 5pt;
		text-align: center;
	}

	header img {
		height: 8em;
	}

	h4 {
		padding-top: 1em;
		text-align: center;
	}

	table {
		border-spacing: 0px;
		font-size: 8pt;

		margin: auto;
	}

	th,
	td {
		border: 1px solid #000;
		padding: 0.5em 2em;
	}
</style>

<header>
	<img src="../../css/img/ministerio.jpg" style="float: left;" />
	<img src="../../css/img/logo.png" style="float: right;" />

	<br/>República Bolivariana de Venezuela
	<br/>Ministerio del Poder Popular para la Educación Universitaria, Ciencia y Tecnología
	<br/><?= "$periodo->carrera - $periodo->sede ($periodo->estructura)"; ?>
	<br/>Periodo Académico <?= "$periodo->id ($periodo->fechaInicio - $periodo->fechaFin)"; ?>
</header>

<?= profesor(3); ?>
<?= profesor(3, "Aux"); ?>
<?= profesor(1); ?>

<?php
	function profesor($condicion, $categoria) {
		global $sigpa, $periodo, $mecs;

		$sql = "
			select count(car.id) as n 
			from carga as car 
				join profesor as prof on prof.cedula=car.\"idProfesor\" or prof.cedula=car.\"idSuplente\" 
				join seccion as sec on sec.\"ID\"=car.\"idSeccion\" 
			where prof.condicion='$condicion'" . (($categoria) ? " and prof.categoria like '$categoria%' " : "") . " and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs' 
		";
		$exe = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe);

		if(! $n->n)
			return false;

		if($condicion === 3) {
			if(! $categoria)
				echo "<h4>Personal Docente Ordinario</h4>";

			else if($categoria == "Aux")
				echo "<h4>Personal Auxiliar Docente Ordinario</h4>";
		}

		else if($condicion === 1)
			echo "<h4>Personal Docente Contratado</h4>";
?>

<table>
<thead>
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
</thead>

<tbody>

<?php
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
			where con.id='$condicion'" . (($categoria) ? " and cat.id like '$categoria%' " : "") . " and sec.\"idPeriodo\"='$periodo->ID' and sec.\"idMECS\"='$mecs' 
			group by prof.cedula, per.apellido, per.nombre, pro.nombre, prof.categoria, prof.dedicacion 
			order by per.apellido, per.nombre, prof.cedula
		";
		$exe = pg_query($sigpa, $sql);

		while($carga = pg_fetch_object($exe)) {
			$cargas = explode("&", $carga->cargas);
			$n = count($cargas);
			$i = 0;
?>

	<tr>
		<td rowspan="<?= $n; ?>">
			<?= "$carga->apellido $carga->nombre"; ?><br/>
			CI: <?= $carga->cedula; ?><br/>
			<?= $carga->profesion; ?>
		</td>
		<td rowspan="<?= $n; ?>"><?= $carga->dedicacion; ?></td>
		<td rowspan="<?= $n; ?>"><?= $carga->categoria; ?></td>
		<?= carga($cargas[$i]); ?>
		<td rowspan="<?= $n; ?>">Observación</td>
	</tr>

<?php
			while(--$n > 0) {
				++$i;
?>

	<tr>
		<?= carga($cargas[$i]); ?>
	</tr>

<?php
			}
		}
?>

</tbody>
</table>
<div style="page-break-after: always;"></div>

<?php
	}

	function carga($id) {
		global $sigpa;
?>

		<td><?= $id; ?></td>
		<td>A</td>
		<td>2</td>
		<td>3</td>
		<td>5</td>

<?php
	}
?>