<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Sedes</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Fecha de inauguración</th>
						<th>Teléfono</th>
						<th>Dirección</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from sede order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($sede = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $sede->nombre; ?></td>
						<td><?php $fecha = explode("-", $sede->fecha); echo "$fecha[2]/$fecha[1]/$fecha[0]"; ?></td>
						<td><?= $sede->telefono; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $sede->direccion; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Sede/editar.php', '#page-wrapper', 'nombre=<?= $sede->nombre ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $sede->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=sede&campo=nombre&valor=<?= $sede->nombre ?>', 'moduloPlanificacion/Sede/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva sede" onClick="embem('moduloPlanificacion/Sede/form.html', '#page-wrapper')" style="cursor: pointer" colspan="4"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>