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
						<th></th>
						<th>Nombre</th>
						<th>CI</th>
						<th>Teléfono</th>
						<th>Correo</th>
					</tr>
				</thead>

				<tbody>

<?php
	require "../../lib/conexion.php";

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.apellido as apellido, p.telefono as telefono, p.correo as correo, p.direccion as direccion, prof.categoria as categoria, prof.condicion as condicion, prof.dedicacion as dedicacion, prof.profesion as profesion 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
		order by p.cedula, p.apellido, p.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($profesor = pg_fetch_object($exe)) {
?>

					<tr>
						<td>

<?php
		$sql = "select count(\"idProfesor\") as n from pertenece where \"idProfesor\"='$profesor->cedula'";
		$exe2 = pg_query($sigpa, $sql);
		$n = pg_fetch_object($exe2);
		$n = $n->n;

		if(($profesor->correo == "Sin asignar") || ($profesor->direccion == "Sin asignar") || ($profesor->telefono == "Sin asignar") || ($profesor->categoria == "No") || ($profesor->condicion == "0") || ($profesor->dedicacion == "No") || ($profesor->profesion == "0") || ($n == "0"))
			echo "<i class=\"fa fa-exclamation-triangle alerta\" onClick=\"embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=$profesor->cedula')\" title=\"Este profesor necesita ser completado\"></i>";
?>

						</td>
						<td><?= "$profesor->apellido $profesor->nombre"; ?></td>
						<td><?= $profesor->cedula; ?></td>
						<td><?= $profesor->telefono; ?></td>
						<td><div class="row">
							<div class="col-xs-7 col-sm-7 col-md-6 col-lg-7">
								<?= $profesor->correo; ?>
							</div>

							<div class="col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center">
								<i class="fa fa-pencil fa-fw editar" title="Editar" onClick="embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=<?= $profesor->cedula ?>')"></i>
								<i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar a <?= "$profesor->apellido $profesor->nombre ($profesor->cedula)"; ?>?')) sendReq('../../script/eliminar.php', 'tabla=persona&campo=cedula&valor=<?= $profesor->cedula ?>', 'moduloPlanificacion/Profesor/index.php')" title="Eliminar"></i>
							</div>
						</div></td>
					</tr>

<?php
	}
?>

				</tbody>

				<tfoot>
					<tr>
						<td class="text-center" title="Nuevo profesor" onClick="embem('moduloPlanificacion/Profesor/form.php', '#page-wrapper')" style="cursor: pointer" colspan="5"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>