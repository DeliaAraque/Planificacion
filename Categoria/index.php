<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Categorías</h1>
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
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from categoria order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($categoria = pg_fetch_object($exe)) {
?>

					<tr>
						<td><?= $categoria->id; ?></td>
						<td><div class="row">
							<div class="col-xs-8 col-sm-9 col-md-9 col-lg-9">
								<?= $categoria->nombre; ?>
							</div>

							<div class="col-xs-4 col-sm-3 col-md-3 col-lg-3 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Categoria/editar.php', '#page-wrapper', 'id=<?= $categoria->id ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $categoria->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=categoria&campo=id&valor=<?= $categoria->id ?>', 'moduloPlanificacion/Categoria/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva categoría" onClick="embem('moduloPlanificacion/Categoria/form.html', '#page-wrapper')" style="cursor: pointer" colspan="2"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>