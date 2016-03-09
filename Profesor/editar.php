<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$cedula = $_POST["cedula"];

	$sql = "
		select p.cedula as cedula, p.nombre as nombre, p.\"segundoNombre\" as \"segundoNombre\", p.apellido as apellido, p.\"segundoApellido\" as \"segundoApellido\", p.sexo as sexo, p.correo as correo, p.direccion as direccion, p.telefono as telefono, p.\"telefonoFijo\" as \"telefonoFijo\", prof.profesion as profesion, prof.categoria as categoria, prof.dedicacion as dedicacion, prof.condicion as condicion 
		from persona as p 
			join profesor as prof on prof.cedula=p.cedula 
		where p.cedula='$cedula'
	";
	$exe = pg_query($sigpa, $sql);
	$profesor = pg_fetch_object($exe);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar profesor</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="profesor" method="POST" action="moduloPlanificacion/Profesor/modificar.php" data-exe="embem('moduloPlanificacion/Profesor/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				Cédula:
				<input type="text" name="cedula" placeholder="Cédula" value="<?= $profesor->cedula; ?>" class="form-control" maxlength="8" pattern="[0-9]{7,}" onKeyUp="if(this.value != document.profesor.cedulaAnt.value) Verif(this)" required="required" title="Ingresela cédula del profesor" />
				<input type="hidden" name="cedulaAnt" value="<?= $profesor->cedula; ?>" />
				<p class="help-block">Solo están permitidos caracteres numéricos y debe contener al menos 7. Ej: 12345678.</p>
			</div>

			<div class="form-group">
				Nombre
				<input type="text" name="nombre" placeholder="Primer nombre" value="<?= $profesor->nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" title="Ingrese el nombre del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				Segundo nombre:
				<input type="text" name="segundoNombre" placeholder="Segundo nombre" value="<?= $profesor->segundoNombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" title="Ingrese el segundo nombre del profesor" />
				<p class="help-block">Opcional, solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				Apellido:
				<input type="text" name="apellido" placeholder="Primer apellido" value="<?= $profesor->apellido; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" title="Ingrese el apellido del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Apellido.</p>
			</div>

			<div class="form-group">
				Segundo apellido:
				<input type="text" name="segundoApellido" placeholder="Segundo apellido" value="<?= $profesor->segundoApellido; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" title="Ingrese el segundo apellido del profesor" />
				<p class="help-block">Opcional, solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Apellido.</p>
			</div>

			<div class="form-group"> 
				Sexo:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="sexo" value="f" <?php if($profesor->sexo == "f") echo "checked=\"checked\""; ?> required="required"> Femenino </label>
					<label class="radio-inline"><input type="radio" name="sexo" value="m" <?php if($profesor->sexo == "m") echo "checked=\"checked\""; ?> required="required"> Masculino </label>
				</div>
			</div>

			<div class="form-group">
				Correo:
				<input type="text" name="correo" placeholder="Correo electŕonico" value="<?= $profesor->correo; ?>" class="form-control" pattern="^[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+(,[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+)*$" required="required" title="Ingrese el correo electrónico del profesor" />
				<p class="help-block">Puede ingresar varios correos separados por comas y sin espacios. Ej: inicialnombreapellido@uptm.edu.ve,inicialnombreapellido@gmail.com</p>
			</div>

			<div class="form-group">
				Dirección:
				<textarea name="direccion" placeholder="Dirección" rows="2" class="form-control" required="required"><?= $profesor->direccion; ?></textarea>
				<p class="help-block">Ej: Av. Monseñor Duque, Ejido.</p>
			</div>

			<div class="form-group">
				Teléfono móvil:
				<input type="text" name="telefono" placeholder="Teléfono móvil" value="<?= $profesor->telefono; ?>" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*" required="required" />
				<p class="help-block">Puede ingresar varios teléfonos separados por comas y sin espacios. Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				Teléfono fijo:
				<input type="text" name="telefonoFijo" placeholder="Teléfono Fijo" value="<?= $profesor->telefonoFijo; ?>" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*" />
				<p class="help-block">Opcional. Puede ingresar varios teléfonos separados por comas y sin espacios. Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				Profesión:
				<select name="profesion" class="form-control" required="required">
					<option value="">Profesión</option>

<?php
	$sql="select * from profesion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($profesion = pg_fetch_object($exe)) {
		if($profesion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$profesion->id\"";

		if($profesor->profesion == $profesion->id)
			echo " selected=\"selected\"";

		echo ">$profesion->nombre</option>";
	}
?>

				</select>
			</div>
			
			<div class="form-group">
				Categoría:
				<select name="categoria" class="form-control" required="required">
					<option value="">Categoría</option>

<?php
	$sql="select * from categoria order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($categoria = pg_fetch_object($exe)) {
		if($categoria->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$categoria->id\"";

		if($profesor->categoria == $categoria->id)
			echo " selected=\"selected\"";

		echo ">$categoria->nombre</option>";
	}
?>

				</select>
			</div>

			<div class="form-group">
				Dedicación:
				<select class="form-control" name="dedicacion" required="required">
					<option value="">Dedicación</option>

<?php
	$sql="select * from dedicacion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($dedicacion = pg_fetch_object($exe)) {
		if($dedicacion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$dedicacion->id\"";

		if($profesor->dedicacion == $dedicacion->id)
			echo " selected=\"selected\"";

		echo ">$dedicacion->nombre</option>";
	}
?>

				</select>
			</div>

			<div class="form-group">
				Condición:
				<select class="form-control" name="condicion" required="required">
					<option value="">Condición</option>

<?php
	$sql="select * from condicion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($condicion = pg_fetch_object($exe)) {
		if($condicion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$condicion->id\"";

		if($profesor->condicion == $condicion->id)
			echo " selected=\"selected\"";

		echo ">$condicion->nombre</option>";
	}
?>

				</select>
			</div>

<?php
	if(! isset($_SESSION["carrera"])) {
?>

			<div class="form-group"> 
				Carreras:
				<div class="checkbox">

<?php
		$sql = "
			select per.\"idCS\" as \"idCS\"
			from persona as p 
				join profesor as prof on prof.cedula=p.cedula 
				join pertenece as per on per.\"idProfesor\"=prof.cedula
			where p.cedula='$cedula' 
		";
		$exe = pg_query($sigpa, $sql);

		while($carreras = pg_fetch_object($exe))
			$auxCarreras[] = $carreras->idCS;

		unset($carreras);

		foreach($auxCarreras as $carrera)
			$carreras[] = $carrera;

		$sql="
			select cs.id as id, c.nombre as carrera, s.nombre as sede 
			from \"carreraSede\" as cs 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			order by c.nombre, s.nombre";
		$exe=pg_query($sigpa, $sql);

		while($cs = pg_fetch_object($exe)) {
			echo "<label class=\"checkbox\"><input type=\"checkbox\" name=\"carrera[]\" value=\"$cs->id\"";

			if(in_array($cs->id, $carreras))
				echo "checked=\"checked\"";

			echo "> $cs->carrera ($cs->sede) </label>";
		}
?>

				</div>
			</div>

<?php
	}
?>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Profesor/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>