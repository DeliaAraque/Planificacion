<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Profesores</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Cedula</th>
						<th>Nombre</th>
						<th>Apellido</th>
						<th>Correo</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from profesor order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $profesor->cedula; ?></td>
						<td><?= $profesor->nombre; ?></td>
						<td><?= $profesor->apellido; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $profesor->correo; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=<?= $profesor->cedula ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $profesor->cedula ?>?')) sendReq('../../script/eliminar.php', 'tabla=profesor&campo=cedula&valor=<?= $profesor->cedula ?>', 'moduloPlanificacion/Profesor/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nuevo profesor" onClick="embem('moduloPlanificacion/Profesor/form.php', '#page-wrapper')" style="cursor: pointer" colspan="4"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>