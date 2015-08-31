<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Carreras</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th></th>
						<th>Nombre</th>
						<th>Área</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select c.id as id, c.nombre as nombre, a.nombre as area
		from carrera as c
			join area as a on a.id=c.\"idArea\"
		order by c.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($carrera = pg_fetch_object($exe)) {
?>

					<tr>
						<td>

<?php
		$sql = "
			select count(ecs.id) as n 
			from \"carreraSede\" as cs 
				join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id 
			where cs.\"idCarrera\"='$carrera->id'
		";
		$exe2 = pg_query($sigpa, $sql);
		$ncs = pg_fetch_object($exe2);

		if(!$ncs->n)
			echo "<i class=\"fa fa-exclamation-triangle alerta\" onClick=\"embem('moduloPlanificacion/Carrera/editar.php', '#page-wrapper', 'nombre=$carrera->nombre')\" title=\"Esta carrera necesita ser completada\"></i>";
?>

						</td>
						<td><?= $carrera->nombre; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7"><?= $carrera->area; ?></div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center"> 
								<i class="fa fa-search fa-fw consultar" title="Mas información" onClick="moreInfo('moduloPlanificacion/Carrera/consultar.php', 'nombre=<?= $carrera->nombre ?>')"></i>
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Carrera/editar.php', '#page-wrapper', 'nombre=<?= $carrera->nombre ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $carrera->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=carrera&campo=nombre&valor=<?= $carrera->nombre ?>', 'moduloPlanificacion/Carrera/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva carrera" onClick="embem('moduloPlanificacion/Carrera/form.php', '#page-wrapper')" style="cursor: pointer" colspan="3"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>