<?php
	require "../../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Mallas</h1>
		<small class="help-block">Para ver o reactivar mallas inactivas haga click <a href="javascript: embem('moduloPlanificacion/Carrera/Malla/inactivas.php', '#page-wrapper')">aquí</a>.</small><br/>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Carrera</th>
						<th>Sede</th>
						<th>Estructura</th>
						<th>Malla</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../../lib/conexion.php";

	$sql = "
		select m.id as id, m.fecha as fecha, e.nombre as estructura, s.nombre as sede, c.nombre as carrera
		from malla as m 
			join \"mallaECS\" as mecs on mecs.\"idMalla\"=m.id
			join \"estructuraCS\" as ecs on ecs.id=mecs.\"idECS\" 
			join estructura as e on e.id=ecs.\"idEstructura\" 
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join sede as s on s.id=cs.\"idSede\" 
			join carrera as c on c.id=\"idCarrera\"
		where mecs.estado is true 
		order by c.nombre, s.nombre, e.nombre, m.id
	";
	$exe = pg_query($sigpa, $sql);

	while($malla = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $malla->carrera; ?></td>
						<td><?= $malla->sede; ?></td>
						<td><?= $malla->estructura; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-6"><?= $malla->id; ?></div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-6 text-center"> 
								<i class="fa fa-search fa-fw consultar" title="Mas información" onClick="moreInfo('moduloPlanificacion/Carrera/Malla/consultar.php', 'id=<?= $malla->id ?>')"></i>
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Carrera/Malla/editar.php', '#page-wrapper', 'id=<?= $malla->id ?>')"></i>
								<i class="fa fa-recycle fa-fw editar" title="Reusar" onClick="embem('moduloPlanificacion/Carrera/Malla/reusar.php', '#page-wrapper', 'id=<?= $malla->id ?>')"></i>
								<i class="fa fa-times fa-fw eliminar" onClick="if(confirm('¿Realmente desea desactivar <?= $malla->id ?> de la sede <?= $malla->sede ?>?')) sendReq('moduloPlanificacion/Carrera/Malla/estado.php', 'malla=<?= $malla->id ?>&carrera=<?= $malla->carrera ?>&sede=<?= $malla->sede ?>&estructura=<?= $malla->estructura ?>', 'moduloPlanificacion/Carrera/Malla/index.php')" title="Desactivar"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $malla->id ?> de la sede <?= $malla->sede ?>?')) sendReq('moduloPlanificacion/Carrera/Malla/eliminar.php', 'malla=<?= $malla->id ?>&carrera=<?= $malla->carrera ?>&sede=<?= $malla->sede ?>&estructura=<?= $malla->estructura ?>', 'moduloPlanificacion/Carrera/Malla/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva malla" onClick="embem('moduloPlanificacion/Carrera/Malla/form.php', '#page-wrapper')" style="cursor: pointer" colspan="4"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>