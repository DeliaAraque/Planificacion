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