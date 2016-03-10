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
			<table class="table table-striped table-bordered table-hover dataTable" id="UCDT">
				<thead>
					<tr>
						<th></th>
						<th>Nombre</th>
						<th>Código</th>
						<?php if($_SESSION["nivel"] < 3) echo "<th>Carrera</th>" ?>
						<th>Eje</th>
					</tr>
				</thead>

<?php
	if($_SESSION["nivel"] < 3) {
?>

				<tfoot>
					<tr>
						<td class="text-center" title="Nueva unidad curricular" onClick="embem('moduloPlanificacion/UC/form.php', '#page-wrapper')" style="cursor: pointer" colspan="<?= ($_SESSION["nivel"] == 3) ? "4" : "5" ?>"><i class="fa fa-plus fa-fw agregar"></i></td>
					</tr>
				</tfoot>

<?php
	}
?>

			</table>
		</div>
	</div>
</div>

<script>
	$("#UCDT").dataTable( {
		"responsive": true,
		"aLengthMenu": [[15, 30, -1], [15, 30, "Todos"]],
		"language":  {
			"sProcessing":     "Procesando...",
			"sLengthMenu":     "_MENU_ elementos por página",
			"sZeroRecords":    "No hay elementos",
			"sEmptyTable":     "No hay elementos",
			"sInfo":           "Total: _MAX_ elementos (_START_-_END_)",
			"sInfoEmpty":      "No hay elementos",
			"sInfoFiltered":   "(se encontraron _TOTAL_ coincidencias)",
			"sInfoPostFix":    "",
			"sSearch":         "Buscar:",
			"sUrl":            "",
			"sInfoThousands":  ",",
			"sLoadingRecords": "Cargando...",
			"oPaginate": {
				"sFirst":    "<i class=\"fa fa-angle-double-left fa-fw\" title=\"Primera página\"></i>",
				"sLast":     "<i class=\"fa fa-angle-double-right fa-fw\" title=\"Última página\"></i>",
				"sPrevious": "<i class=\"fa fa-angle-left fa-fw\" title=\"Anterior\"></i>",
				"sNext":     "<i class=\"fa fa-angle-right fa-fw\" title=\"Siguiente\"></i>"
			},
			"oAria": {
				"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
			}
		},
		"retrieve": true,
		"ajax": "moduloPlanificacion/UC/dataTable.php",
		"deferRender": true
	});
</script>