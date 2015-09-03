<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Secciones antiguas</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Periodo</th>
						<th>Sección</th>
						<th>Carrera</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select sec.\"ID\" as \"ID\", p.id as periodo, sec.id as id, sec.turno as turno, sec.grupos as grupos, c.nombre as carrera, s.nombre as sede, sec.\"periodoEstructura\" as \"periodoEstructura\" 
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" and p.\"fechaFin\"<current_date 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\"
		order by p.id, sec.id, c.nombre, s.nombre, sec.\"periodoEstructura\"
	";
	$exe = pg_query($sigpa, $sql);

	while($seccion = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $seccion->periodo; ?></td>
						<td>

<?php
		echo $seccion->id;

		if($seccion->grupos == "t")
			echo " <i class=\"fa fa-fw fa-users\" title=\"Se divide en grupos\"></i>";

		if($seccion->turno == "n")
			echo " <i class=\"fa fa-fw fa-moon-o\" title=\"Nocturna\"></i>";
?>

						</td>
						<td><?= "$seccion->carrera - $seccion->sede ($seccion->periodoEstructura)"; ?></td>
					</tr>

<?php
	}
?>

				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 text-center">
		<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Seccion/index.php', '#page-wrapper')" />
	</div>
</div>