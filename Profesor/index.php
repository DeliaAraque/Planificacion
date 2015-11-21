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
			<table class="table table-striped table-bordered table-hover dataTable" id="ProfesorDT">
				<thead>
					<tr>
						<th></th>
						<th>Nombre</th>
						<th>CI</th>
						<th>Teléfono</th>
						<th>Correo</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td class="text-center" title="Nuevo profesor" onClick="embem('moduloPlanificacion/Profesor/form.php', '#page-wrapper')" style="cursor: pointer" colspan="5"><i class="fa fa-plus fa-fw editar"></i></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<script>
	$("#ProfesorDT").dataTable( {
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
		"ajax": "moduloPlanificacion/Profesor/dataTable.php",
		"deferRender": true
	});
</script>