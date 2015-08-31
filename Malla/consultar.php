<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$id = htmlspecialchars($_POST["id"], ENT_QUOTES);

	$sql = "
		select m.id as id, m.fecha as fecha, e.nombre as \"estructuraNom\", e.estructura as \"estructuraEst\", c.nombre as carrera, string_agg(concat_ws('&', s.nombre, mecs.estado), '&&' order by s.nombre) as sedes
		from malla as m 
			join \"mallaECS\" as mecs on mecs.\"idMalla\"=m.id 
			join \"estructuraCS\" as ecs on ecs.id=mecs.\"idECS\" 
			join \"estructura\" as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where m.id='$id' 
		group by m.id, m.fecha, e.nombre, e.estructura, c.nombre
	";
	$exe = pg_query($sigpa, $sql);

	$malla = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header"><?= "$malla->id"; ?></h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<strong>Fecha de creación:</strong> <?php $fecha = explode("-", $malla->fecha); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?><br/>
		<strong>Carrera:</strong> <?= $malla->carrera; ?><br/>
		<strong>Estructura:</strong> <?= $malla->estructuraNom; ?><br/>

<?php
	$sedes = explode("&&", $malla->sedes);

	if(count($sedes) > 1) {
?>

		<strong>Sedes:</strong><br/>
			<ul>

<?php
		foreach($sedes as $sede) {
			$sede = explode("&", $sede);
?>

				<li><?= "$sede[0] <i class=\"fa fa-circle\" style=\"color:" . (($sede[1]=="t") ? "green" : "red") . ";\" title=\"" . (($sede[1]=="t") ? "Activa" : "Inactiva") . "\"></i>"; ?></li>

<?php
		}
?>

			</ul>

<?php
	}

	else {
		$sede = explode("&", $sedes[0]);
?>

		<strong>Sede:</strong> <?= "$sede[0] <i class=\"fa fa-circle\" style=\"color:" . (($sede[1]=="t") ? "green" : "red") . ";\" title=\"" . (($sede[1]=="t") ? "Activa" : "Inactiva") . "\"></i>"; ?><br/>

<?php
	}
?>

	</div>
</div><br/>

<table class="table">

<?php
	/*
		Propiedades del objeto Periodo:
			· Identificador (id)
			· Nombre (nombre)
			· Duración (duracion)
			· Subperiodos (subperiodos)
	*/

	$malla->estructuraEst = json_decode($malla->estructuraEst);

	foreach($malla->estructuraEst->periodos as $periodo) {
?>

	<tr><th class="text-center" style="color: white; background-color: #00005b;" colspan="4"><?= "$periodo->nombre ($periodo->id)"; ?></th></tr>

<?php
		if(!$periodo->subperiodos) {
?>

	<tr>
		<th>Código</th>
		<th>Nombre</th>
		<th>Horas Teóricas</th>
		<th>Horas Prácticas</th>
	</tr>

<?php
			$sql = "
				select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, ucm.\"horasTeoricas\" as \"horasTeoricas\", ucm.\"horasPracticas\" as \"horasPracticas\", ucm.tipo as tipo 
				from \"ucMalla\" as ucm
					join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
				where ucm.periodo='$periodo->id' and ucm.\"idMalla\"='$malla->id' 
				order by uc.nombre
			";
			$exe = pg_query($sigpa, $sql);

			while($unidadCurricular = pg_fetch_object($exe)) {
?>

	<tr>
		<td><?= $unidadCurricular->id; ?></td>
		<td>

<?php
				echo "$unidadCurricular->nombre";

				if($unidadCurricular->renombrable == "t")
					echo " <i class=\"fa fa-pencil-square-o\" title=\"Renombrable\"></i>";

				if($unidadCurricular->tipo == "t")
					echo " <i class=\"fa fa-flask\" title=\"Laboratorio\"></i>";
?>

		</td>
		<td><?= $unidadCurricular->horasTeoricas; ?></td>
		<td><?= $unidadCurricular->horasPracticas; ?></td>
	</tr>

<?php
			}
		}

		else {
			/*
				Propiedades del objeto Subperiodos:
					· Identificador (id)
					· Nombre (nombre)
					· Duración (duracion)
			*/

			foreach($periodo->subperiodos as $subperiodo) {
?>

	<tr><th class="text-center" style="color: #00005b;" colspan="4"><?= "$subperiodo->nombre ($subperiodo->id)"; ?></th></tr>
	<tr>
		<th>Código</th>
		<th>Nombre</th>
		<th>Horas Teóricas</th>
		<th>Horas Prácticas</th>
	</tr>

<?php
				$sql = "
					select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, ucm.\"horasTeoricas\" as \"horasTeoricas\", ucm.\"horasPracticas\" as \"horasPracticas\", ucm.tipo as tipo 
					from \"ucMalla\" as ucm
						join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" 
					where ucm.periodo='$periodo->id-$subperiodo->id' and ucm.\"idMalla\"='$malla->id' 
					order by uc.nombre
				";
				$exe = pg_query($sigpa, $sql);

				while($unidadCurricular = pg_fetch_object($exe)) {
?>

	<tr>
		<td><?= $unidadCurricular->id; ?></td>
		<td>

<?php
				echo "$unidadCurricular->nombre";

				if($unidadCurricular->renombrable == "t")
					echo " <i class=\"fa fa-pencil-square-o\" title=\"Renombrable\"></i>";

				if($unidadCurricular->tipo == "t")
					echo " <i class=\"fa fa-flask\" title=\"Laboratorio\"></i>";
?>

		</td>
		<td><?= $unidadCurricular->horasTeoricas; ?></td>
		<td><?= $unidadCurricular->horasPracticas; ?></td>
	</tr>

<?php
				}
			}
		}
	}
?>
</table>