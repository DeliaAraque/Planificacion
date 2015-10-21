<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$cedula = htmlspecialchars($_POST["cedula"], ENT_QUOTES);

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.\"segundoNombre\" as \"segundoNombre\", p.apellido as apellido, p.\"segundoApellido\" as \"segundoApellido\", p.correo as correo, p.direccion as direccion, p.telefono as telefono, p.\"telefonoFijo\" as \"telefonoFijo\", pr.nombre as profesion, cat.nombre as categoria, ded.nombre as dedicacion, con.nombre as condicion 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
			join profesion as pr on pr.id=prof.profesion 
			join categoria as cat on cat.id=prof.categoria 
			join condicion as con on con.id=prof.condicion 
			join dedicacion as ded on ded.id=prof.dedicacion 
		where p.cedula='$cedula'
	";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-xs-12">
		<h1 class="page-header"><?= "$profesor->apellido $profesor->nombre"; ?></h1>

		<h4>Datos personales:</h4>
		<strong>Cédula:</strong> <?= $profesor->cedula; ?><br/>
		<strong>Nombre:</strong> <?= "$profesor->apellido $profesor->segundoApellido $profesor->nombre $profesor->segundoApellido"; ?><br/>
		<strong>Correo:</strong> <?= $profesor->correo; ?><br/>
		<strong>Teléfono:</strong> <?= $profesor->telefono; ?><br/>
		<?php if($profesor->telefonoFijo) { ?> <strong>Teléfono fijo:</strong> <?= $profesor->telefonoFijo; ?><br/> <?php } ?>
		<strong>Dirección:</strong> <?= $profesor->direccion; ?><br/><br/>

		<h4>Datos académicos:</h4>
		<strong>Dedicación:</strong> <?= $profesor->dedicacion; ?><br/>
		<strong>Condición:</strong> <?= $profesor->condicion; ?><br/>
		<strong>Categoría:</strong> <?= $profesor->categoria; ?><br/>
		<strong>Profesión:</strong> <?= $profesor->profesion; ?><br/>

<?php
	$sql = "select count(*) as n from pertenece where \"idProfesor\"='$profesor->cedula'";
	$exe = pg_query($sigpa, $sql);
	$n = pg_fetch_object($exe);
	$n = $n->n;

	if($n == 1) {
		$sql="
			select c.nombre as carrera, s.nombre as sede 
			from pertenece as p 
				join \"carreraSede\" as cs on cs.id=p.\"idCS\" 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			where p.\"idProfesor\"='$profesor->cedula'
		";
		$exe=pg_query($sigpa, $sql);
		$carrera = pg_fetch_object($exe);
?>

		<strong>Carrera a la que pertenece:</strong> <?= "$carrera->carrera ($carrera->sede)"; ?><br/>

<?php
	}

	else if($n > 1) {
?>

		<strong>Carreras a las que pertenece:</strong>
		<ul>

<?php
		$sql="
			select c.nombre as carrera, s.nombre as sede 
			from pertenece as p 
				join \"carreraSede\" as cs on cs.id=p.\"idCS\" 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			where p.\"idProfesor\"='$profesor->cedula' 
			order by c.nombre
		";
		$exe=pg_query($sigpa, $sql);

		while($carrera = pg_fetch_object($exe)) {
?>

		<li><?= "$carrera->carrera ($carrera->sede)"; ?></li>

<?php
		}
?>

		</ul>

<?php
	}
?>

	</div>
</div>


<?php
	$sql = "select count(*) as n from carga where \"idProfesor\"='$profesor->cedula' or \"idSuplente\"='$profesor->cedula'";
	$exe = pg_query($sigpa, $sql);
	$nCarga = pg_fetch_object($exe);
	$nCarga = $nCarga->n;

	if($nCarga > 0) {
?>

<div class="row">
	<div class="col-xs-12">
		<br/><h4>Carga:</h4>
	</div>

	<div class="col-lg-12">
		<div class="dataTable_wrapper">
			<table class="table table-striped table-bordered table-hover dataTable">
			<thead>
				<tr>
					<th>Periodo</th>
					<th>Carrera</th>
					<th>Unidad curricular</th>
					<th>Seccion</th>
				</tr>
			</thead>

			<tbody>

<?php
		$sql = "
			select car.\"idProfesor\" as profesor, car.\"idSuplente\" as suplente, p.id as periodo, sec.id as seccion, c.nombre as carrera, s.nombre as sede, sec.\"periodoEstructura\" as \"periodoEstructura\", uc.nombre as \"unidadCurricular\", car.\"nuevoNombre\" as \"nuevoNombre\", sec.turno as turno, sec.grupos as grupos 
			from carga as car 
				join seccion as sec on sec.\"ID\"=car.\"idSeccion\" 
				join periodo as p on p.\"ID\"=sec.\"idPeriodo\" 
				join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\" 
				join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\"
				join \"mallaECS\" as mecs on mecs.id=sec.\"idMECS\" 
				join \"unidadCurricular\" as uc on uc.id=car.\"idUC\" 
			where car.\"idProfesor\"='$profesor->cedula' or car.\"idSuplente\"='$profesor->cedula' 
			order by p.id desc, c.nombre, s.nombre, sec.\"periodoEstructura\", uc.nombre, sec.id
		";
		$exe = pg_query($sigpa, $sql);

		while($carga = pg_fetch_object($exe)) {
?>

				<tr>
					<td><?= $carga->periodo; ?></td>
					<td><?= "$carga->carrera - $carga->sede ($carga->periodoEstructura)"; ?></td>
					<td><?php if($carga->nuevoNombre) echo "$carga->nuevoNombre -"; ?> <?= $carga->unidadCurricular; ?></td>
					<td>

<?php
			echo $carga->seccion;

			if($carga->grupos == "t")
				echo " <i class=\"fa fa-fw fa-users\" title=\"Se divide en grupos\"></i>";

			if($carga->turno == "n")
				echo " <i class=\"fa fa-fw fa-moon-o\" title=\"Nocturna\"></i>";

			if($carga->profesor != $profesor->cedula) {
				$sql = "select apellido, nombre, cedula from persona where cedula='$carga->profesor'";
				$exe2 = pg_query($sigpa, $sql);
				$profesorCarga = pg_fetch_object($exe2);

				echo " - Suplente de $profesorCarga->apellido $profesorCarga->nombre ($profesorCarga->cedula)";
			}

			else {
				if($carga->suplente) {
					$sql = "select apellido, nombre, cedula from persona where cedula='$carga->suplente'";
					$exe2 = pg_query($sigpa, $sql);
					$suplente = pg_fetch_object($exe2);

					echo " - Suple $suplente->apellido $suplente->nombre ($suplente->cedula)";
				}
			}
?>

					</td>
				</tr>

<?php
		}
?>

			</tbody>
			</table>
		</div>
	</div>
</div>

<?php
	}
?>