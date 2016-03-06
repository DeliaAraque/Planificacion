<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "
		select c.id as id, c.nombre as nombre, a.nombre as area, c.\"idCoordinadorInst\" as \"idCoordinadorInst\" 
		from carrera as c 
			join area as a on a.id=c.\"idArea\"
		where c.nombre='$nombre'
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

<?php
	if($carrera->idCoordinadorInst) {
		$sql = "select cedula, nombre, apellido from persona where cedula='$carrera->idCoordinadorInst'";
		$exe = pg_query($sigpa, $sql);
		$coordinador = pg_fetch_object($exe);

		echo "<br/><strong>Coordinador Institucional:</strong> $coordinador->apellido $coordinador->nombre ($coordinador->cedula)";
	}
?>

	</div>

	<div class="col-xs-12">
		<strong>Sedes:</strong><br/>

		<ul>

<?php
	$sql = "
		select cs.id as id, s.nombre as sede, p.cedula as \"idCoordinador\", p.nombre as \"nombreCoordinador\", p.apellido as \"apellidoCoordinador\" 
		from \"carreraSede\" as cs 
			join sede as s on s.id=\"idSede\" 
			join persona as p on p.cedula=cs.\"idCoordinador\" 
		where cs.\"idCarrera\"='$carrera->id'
		order by s.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($cs = pg_fetch_object($exe))
		echo "<li>$cs->sede, Coordinador: $cs->apellidoCoordinador $cs->nombreCoordinador ($cs->idCoordinador)</li>";
?>

		</ul>
	</div>
</div><br/>

<div class="row">
	<div class="col-xs-12">
		<h4>Unidades Curriculares:</h4>
	</div>

	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Código</th>
					<th>Eje</th>
				</tr>
			</thead>

			<tbody>

<?php
	$sql = "
		select uc.id as id, uc.nombre as nombre, e.nombre as eje
		from \"unidadCurricular\" as uc 
			join carrera as c on c.id=uc.\"idCarrera\" 
			join eje as e on e.id=uc.\"idEje\" 
		where uc.\"idCarrera\"='$carrera->id' 
		order by uc.nombre, e.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($uc = pg_fetch_object($exe)) {
?>

				<tr>
					<td><?= $uc->nombre; ?></td>
					<td><?= $uc->id; ?></td>
					<td><?= $uc->eje; ?></td>
				</tr>

<?php
	}
?>

			</tbody>
			</table>
		</div>
	</div>
</div>