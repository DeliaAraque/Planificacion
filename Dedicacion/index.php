<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Dedicaciones</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Abreviatura</th>
						<th>Nombre</th>
						<th>Horas</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from dedicacion order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($dedicacion = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $dedicacion->id; ?></td>
						<td><?= $dedicacion->nombre; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $dedicacion->horas; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Dedicacion/editar.php', '#page-wrapper', 'id=<?= $dedicacion->id ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $dedicacion->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=dedicacion&campo=id&valor=<?= $dedicacion->id ?>', 'moduloPlanificacion/Dedicacion/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva dedicación" onClick="embem('moduloPlanificacion/Dedicacion/form.html', '#page-wrapper')" style="cursor: pointer" colspan="3"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>