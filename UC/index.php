<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Unidades Curriculares</h1>
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
						<th>Código</th>
						<th>Carrera</th>
						<th>Eje</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, e.nombre as eje, c.nombre as carrera
		from \"unidadCurricular\" as uc 
			join carrera as c on c.id=uc.\"idCarrera\" 
			join eje as e on e.id=uc.\"idEje\" 
		order by uc.nombre, c.nombre, e.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($uc = pg_fetch_object($exe)) {
?>

					<tr>
						<td>

<?php
		$sql = "select count(id) as n from \"ucMalla\" where \"idUC\"='$uc->id'";
		$exe2 = pg_query($sigpa, $sql);
		$nUCM = pg_fetch_object($exe2);

		if(! $nUCM->n)
			echo "<i class=\"fa fa-exclamation-triangle alerta\" title=\"Esta unidad curricular no es usada por ninguna malla\"></i>";
?>

						</td>
						<td><?php echo $uc->nombre; if($uc->renombrable == "t") echo " <i class=\"fa fa-pencil-square-o\" title=\"Renombrable\"></i>"; ?></td>
						<td><?= $uc->id; ?></td>
						<td><?= $uc->carrera; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $uc->eje; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/UC/editar.php', '#page-wrapper', 'id=<?= $uc->id ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $uc->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=unidadCurricular&campo=id&valor=<?= $uc->id ?>', 'moduloPlanificacion/UC/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva unidad curricular" onClick="embem('moduloPlanificacion/UC/form.php', '#page-wrapper')" style="cursor: pointer" colspan="5"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>