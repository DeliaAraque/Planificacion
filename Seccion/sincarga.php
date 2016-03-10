<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Secciones sin carga asignada</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Periodo Académico</th>
						<th><?= ($_SESSION["nivel"] == 3) ? "Periodo" : "Carrera"; ?></th>
						<th>Sección</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select sec.\"ID\" as \"ID\", p.id as periodo, sec.id as id, sec.turno as turno, sec.grupos as grupos, c.id as \"idCarrera\", c.nombre as carrera, s.nombre as sede, sec.\"periodoEstructura\" as \"periodoEstructura\", sec.\"idMECS\" as \"idMECS\" 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\"
	"
	. (($_SESSION["nivel"] == 3) ? " where c.id ='$_SESSION[carreraCoord]' and s.id ='$_SESSION[sedeCoord]'" : "") .

	"
		order by p.id, c.nombre, s.nombre, sec.\"periodoEstructura\", sec.id
	";
	$exe = pg_query($sigpa, $sql);

	while($seccion = pg_fetch_object($exe)) {
		if($seccion->id == "R")
			continue;

		$sql = "
			select m.id as id
			from \"mallaECS\" as mecs 
				join malla as m on m.id = mecs.\"idMalla\"
			where mecs.id='$seccion->idMECS'
		";
		$exe2 = pg_query($sigpa, $sql);
		$malla = pg_fetch_object($exe2);

		$sql = "
			select count(uc.id) as n 
			from \"mallaECS\" as mecs 
				join malla as m on m.id=mecs.\"idMalla\" 
				join \"ucMalla\" as ucm on ucm.\"idMalla\"=m.id 
				join \"unidadCurricular\" as uc on uc.id=ucm.\"idUC\" and uc.nombre not like 'Acreditable%' 
			where mecs.id='$seccion->idMECS' and ucm.periodo='$seccion->periodoEstructura' and uc.\"idCarrera\"='$seccion->idCarrera' 
		";
		$exe2 = pg_query($sigpa, $sql);
		$uc = pg_fetch_object($exe2);

		$sql = "
			select count(car.id) as n 
			from carga as car
				join \"unidadCurricular\" as uc on uc.id=car.\"idUC\" and uc.nombre not like 'Acreditable%'
			where car.\"idSeccion\"='$seccion->ID'
		";
		$exe2 = pg_query($sigpa, $sql);
		$carga = pg_fetch_object($exe2);

		if($carga->n == $uc->n)
			continue;
?>

					<tr>
						<td><?= $seccion->periodo; ?></td>
						<td>

<?php
		if($_SESSION["nivel"] < 3)
			echo "$seccion->carrera - $seccion->sede ($malla->id $seccion->periodoEstructura)";

		else
			echo "$seccion->periodoEstructura ($malla->id)";
?>

						</td>
						<td>

<?php
		echo $seccion->id;

		if($seccion->grupos == "t")
			echo " <i class=\"fa fa-fw fa-users\" title=\"Se divide en grupos\"></i>";

		if($seccion->turno == "n")
			echo " <i class=\"fa fa-fw fa-moon-o\" title=\"Nocturna\"></i>";
?>

						</td>
					</tr>

<?php
	}
?>

				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	if(! $_POST["popUp"]) {
?>

<div class="row">
	<div class="col-xs-12 text-center">
		<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" />
	</div>
</div>

<?php
	}
?>