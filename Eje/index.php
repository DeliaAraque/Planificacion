<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Ejes</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
				<thead>
					<tr>
						<th>Nombre</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from eje order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($eje = pg_fetch_object($exe)) {
?>

					<tr>
						<td><div class="row">
							<div class="col-xs-8 col-sm-9 col-md-9 col-lg-9">
								<?= $eje->nombre; ?>
							</div>

							<div class="col-xs-4 col-sm-3 col-md-3 col-lg-3 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Eje/editar.php', '#page-wrapper', 'nombre=<?= $eje->nombre ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $eje->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=eje&campo=nombre&valor=<?= $eje->nombre ?>', 'moduloPlanificacion/Eje/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nuevo eje" onClick="embem('moduloPlanificacion/Eje/form.html', '#page-wrapper')" style="cursor: pointer"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>