<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Estructuras</h1>
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

	$sql = "select * from estructura order by nombre";
	$exe = pg_query($sigpa, $sql);

	while($estructura = pg_fetch_object($exe)) {
?>

					<tr>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $estructura->nombre; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center"> 
								<i class="fa fa-search fa-fw consultar" title="Mas información" onClick="moreInfo('moduloPlanificacion/Estructura/consultar.php', 'nombre=<?= $estructura->nombre ?>')"></i>
								
<?php
		$sql = "
			select COUNT(p.id) as n 
			from \"estructuraCS\" as ecs 
				join periodo as p on p.\"idECS\"=ecs.id 
			where ecs.\"idEstructura\"='$estructura->id'
		";
		$exe2 = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe2);
		$n = $n->n;

		if(! $n) {
?>

								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Estructura/editar.php', '#page-wrapper', 'nombre=<?= $estructura->nombre ?>')"></i>

<?php
		}
?>

								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $estructura->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=estructura&campo=nombre&valor=<?= $estructura->nombre ?>', 'moduloPlanificacion/Estructura/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva estructura" onClick="embem('moduloPlanificacion/Estructura/form.html', '#page-wrapper')" style="cursor: pointer"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>