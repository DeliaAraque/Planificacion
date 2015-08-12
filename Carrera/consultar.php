<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "
		select c.id as id, c.nombre as nombre, a.nombre as area, string_agg(concat_ws('&', s.nombre, e.nombre), '&&' order by s.nombre, e.nombre) as sedes
		from carrera as c
			join area as a on a.id=c.\"idArea\"
			join \"carreraSede\" as cs on cs.\"idCarrera\"=c.id
			join sede as s on s.id=\"idSede\"
			join \"estructuraCS\" as ecs on ecs.\"idCS\"=cs.id
			join estructura as e on e.id=ecs.\"idEstructura\"
		where c.nombre='$nombre'
		group by c.id, c.nombre, a.nombre
	";
	$exe = pg_query($sigpa, $sql);

	$carrera = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header"><?= "$carrera->nombre"; ?></h1>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<strong>Código:</strong> <?= $carrera->id; ?><br/>
		<strong>Área:</strong> <?= $carrera->area; ?>
	</div>
</div><br/>

<table class="table">
	<thead>
	<tr>
		<th>Sede</th>
		<th>Estructuras</th>
	</tr>
	</thead>

	<tbody>

<?php
	$sedes = explode("&&", $carrera->sedes);

	foreach($sedes as $sede) {
		list($sede, $estructura) = explode("&", $sede);
?>

	<tr>
		<td><?= $sede; ?></td>
		<td><?= $estructura; ?></td>
	</tr>

<?php
	}
?>

	</tbody>
</table>