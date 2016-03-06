<?php
	require "../../script/verifSesion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Planillas</h1>
		<small class="help-block">Para modificar las firmas de las planillas haga click <a href="javascript: embem('moduloPlanificacion/Carga/firmas.php', '#page-wrapper')">aquí</a>.</small>
	</div>
</div>

<div class="row">
	<div class="col-lg-12"><div class="dataTable_wrapper">
		<table class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th>Nombre</th>
				</tr>
			</thead>

			<tbody>

		<?php
			$archivos = scandir("Planillas/", true); // true para invertir el orden de la consulta
			$n = 0;

			if($archivos) {
				for($i = 0; $i < count($archivos); ++$i) {
					if(($archivos[$i] != ".") && ($archivos[$i] != "..")) {
		?>

				<tr>
					<td><div class="row">
						<div class="col-xs-8 col-sm-9 col-md-9 col-lg-9">
							<?= $archivos[$i]; ?>
						</div>

						<div class="col-xs-4 col-sm-3 col-md-3 col-lg-3 text-center">
							<a href="/moduloPlanificacion/Carga/Planillas/<?= $archivos[$i]; ?>" target="_blank"><i class="fa fa-search fa-fw consultar" title="Previsualizar"></i></a>
							<?php if($_SESSION["nivel"] < 3) { ?> <i class="fa fa-trash-o fa-fw eliminar" onClick="if(confirm('¿Realmente desea eliminar <?= $archivos[$i]; ?>?')) { sendReq('moduloPlanificacion/Carga/eliminarPlanilla.php', 'archivo=<?= $archivos[$i]; ?>'); embem('moduloPlanificacion/Carga/planillas.php', '#page-wrapper'); }" title="Eliminar"></i> <?php } ?>
						</div>
					</div></td>
				</tr>

		<?php
						++$n;
					}
				}

				if(! $n) {
		?>

				<tr>
					<td>No hay planillas</td>
				</tr>

		<?php
				}
			}

			else {
		?>

				<tr>
					<td>No hay planillas</td>
				</tr>

		<?php
			}
		?>

			</tbody>
		</table>
	</div></div>
</div>