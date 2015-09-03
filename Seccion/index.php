<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Secciones</h1>
		<small class="help-block">Para ver secciones de periodos antiguos haga click <a href="javascript: embem('moduloPlanificacion/Seccion/antigua.php', '#page-wrapper')">aquí</a>.</small>
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
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\" and p.\"fechaFin\">=current_date 
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
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= "$seccion->carrera - $seccion->sede ($seccion->periodoEstructura)"; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Seccion/editar.php', '#page-wrapper', 'ID=<?= $seccion->ID ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $seccion->id ?> de la carrera <?= "$seccion->carrera $seccion->periodo - $seccion->sede ($seccion->periodoEstructura)"; ?>?')) sendReq('../../script/eliminar.php', 'tabla=seccion&campo=ID&valor=<?= $seccion->ID ?>', 'moduloPlanificacion/Seccion/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva sección" onClick="embem('moduloPlanificacion/Seccion/form.php', '#page-wrapper')" style="cursor: pointer" colspan="3"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>