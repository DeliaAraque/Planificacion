<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Periodos</h1>
		<!-- <small class="help-block">Para ver periodos antiguos haga click <a href="javascript: embem('moduloPlanificacion/Periodo/antiguo.php', '#page-wrapper')">aquí</a>.</small> -->
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<h4>Planificación:</h4><br/>

		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Carrera</th>
						<th>ID</th>
						<th>Fecha de inicio</th>
						<th>Fecha de fin</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select p.id as id, p.\"fechaInicio\" as \"fechaInicio\", p.\"fechaFin\" as \"fechaFin\", p.\"idECS\" \"idECS\", c.nombre as carrera, s.nombre as sede, e.nombre as estructura, p.\"fechaFin\" >= current_date as estado 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where p.tipo='p'
		order by c.nombre, s.nombre, e.nombre, p.id, p.\"fechaInicio\" desc, p.\"fechaFin\" desc
	";
	$exe = pg_query($sigpa, $sql);

	while($periodo = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= "$periodo->carrera - $periodo->sede ($periodo->estructura)"; ?></td>
						<td><?= "$periodo->id <i class=\"fa fa-circle\" style=\"color:" . (($periodo->estado == "t") ? "green" : "red") . ";\" title=\"" . (($periodo->estado == "t") ? "Activo" : "Inactivo") . "\">"; ?></td>
						<td><?php $fecha = explode("-", $periodo->fechaInicio); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?php $fecha = explode("-", $periodo->fechaFin); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Periodo/editar.php', '#page-wrapper', 'id=<?= $periodo->id ?>&ecs=<?= $periodo->idECS ?>')"></i>
								<i class="fa fa-forward fa-fw editar" title="Avanzar" onClick="embem('moduloPlanificacion/Periodo/avanzar.php', '#page-wrapper', 'id=<?= $periodo->id ?>&ecs=<?= $periodo->idECS ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $periodo->id ?> de la carrera <?= "$periodo->carrera - $periodo->sede ($periodo->estructura)"; ?>?')) sendReq('moduloPlanificacion/Periodo/eliminar.php', 'id=<?= $periodo->id ?>&ecs=<?= $periodo->idECS ?>', 'moduloPlanificacion/Periodo/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nuevo periodo" onClick="embem('moduloPlanificacion/Periodo/form.php', '#page-wrapper')" style="cursor: pointer" colspan="4"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="col-lg-12">
		<br/><h4>Académicos:</h4><br/>

		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Carrera</th>
						<th>ID</th>
						<th>Fecha de inicio</th>
						<th>Fecha de fin</th>
					</tr>
				</thead>

				<tbody>

<?php
	$sql = "
		select p.id as id, p.\"fechaInicio\" as \"fechaInicio\", p.\"fechaFin\" as \"fechaFin\", p.\"idECS\" \"idECS\", c.nombre as carrera, s.nombre as sede, e.nombre as estructura, p.\"fechaFin\" >= current_date as estado 
		from periodo as p 
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=cs.\"idCarrera\" 
			join sede as s on s.id=cs.\"idSede\" 
		where p.tipo='a'
		order by c.nombre, s.nombre, e.nombre, p.id, p.\"fechaInicio\" desc, p.\"fechaFin\" desc
	";
	$exe = pg_query($sigpa, $sql);

	while($periodo = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= "$periodo->carrera - $periodo->sede ($periodo->estructura)"; ?></td>
						<td><?= "$periodo->id <i class=\"fa fa-circle\" style=\"color:" . (($periodo->estado == "t") ? "green" : "red") . ";\" title=\"" . (($periodo->estado == "t") ? "Activo" : "Inactivo") . "\">"; ?></td>
						<td><?php $fecha = explode("-", $periodo->fechaInicio); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?></td>
						<td><?php $fecha = explode("-", $periodo->fechaFin); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?></td>
					</tr>

<?php
	}
?>

				</tbody>
			</table>
		</div>
	</div>
</div>