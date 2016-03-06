<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Firmas</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>Orden</th>
					<th>Cargo</th>
					<th>Profesor</th>
				</tr>
			</thead>

			<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "select * from firma order by orden";
	$exe = pg_query($sigpa, $sql);

	while($firma = pg_fetch_object($exe)) {
		$sql = "
			select pro.cedula as cedula, p.apellido as apellido, p.nombre as nombre
			from profesor as pro
				join persona as p on p.cedula = pro.cedula
			where pro.cedula = '$firma->idProfesor'
		";
		$exe2 = pg_query($sigpa, $sql);
		$profesor = pg_fetch_object($exe2);
?>

				<tr>
					<td><?= $firma->orden; ?></td>
					<td><?= $firma->cargo; ?></td>
					<td><div class="row">
						<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
							<?= "$profesor->apellido $profesor->nombre ($profesor->cedula)"; ?>
						</div>

						<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
							<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="moreInfo('moduloPlanificacion/Carga/editarFirma.php', 'orden=<?= $firma->orden ?>')"></i>
							<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $firma->cargo ?>?')) sendReq('../../script/eliminar.php', 'tabla=firma&campo=orden&valor=<?= $firma->orden ?>', 'moduloPlanificacion/Carga/firmas.php')" title="Eliminar"></i>
						</div>
					</div></td>
				</tr>

<?php
	}
?>

			</tbody>

			<tfoot>
				<tr>
					<td class="text-center" title="Nueva firma" onClick="moreInfo('moduloPlanificacion/Carga/firma.php')" style="cursor: pointer" colspan="3"><i class="fa fa-plus fa-fw editar"></i></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 text-center">
		<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Carga/planillas.php', '#page-wrapper')" /><br/><br/>
	</div>
</div>