<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Nuevo profesor</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="profesor" method="POST" action="moduloPlanificacion/Profesor/guardar.php" data-exe="embem('moduloPlanificacion/Profesor/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="cedula" placeholder="Cédula" class="form-control" maxlength="8" pattern="[0-9]{7,}" onKeyUp="Verif(this); obtenerDatos(this.value);" required="required" title="Ingresela cédula del profesor" />
				<p class="help-block">Solo están permitidos caracteres numéricos y debe contener al menos 7. Ej: 12345678.</p>
				<a href="" style="display: none;"></a>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Primer nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" title="Ingrese el nombre del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				<input type="text" name="segundoNombre" placeholder="Segundo nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" title="Ingrese el segundo nombre del profesor" />
				<p class="help-block">Opcional, solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				<input type="text" name="apellido" placeholder="Primer apellido" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" required="required" title="Ingrese el apellido del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Apellido.</p>
			</div>

			<div class="form-group">
				<input type="text" name="segundoApellido" placeholder="Segundo apellido" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*( [A-ZÁÉÍÓÚÑ][a-záéíóúñ]*)*$" title="Ingrese el segundo apellido del profesor" />
				<p class="help-block">Opcional, solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Apellido.</p>
			</div>

			<div class="form-group"> 
				Sexo:
				<div class="radio-inline">
					<label class="radio-inline"><input type="radio" name="sexo" value="f" checked="checked" required="required"> Femenino </label>
					<label class="radio-inline"><input type="radio" name="sexo" value="m" required="required"> Masculino </label>
				</div>
			</div>

			<div class="form-group">
				<input type="text" name="correo" placeholder="Correo electŕonico" class="form-control" pattern="^[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+(,[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+)*$" required="required" title="Ingrese el correo electrónico del profesor" />
				<p class="help-block">Puede ingresar varios correos separados por comas y sin espacios. Ej: inicialnombreapellido@uptm.edu.ve,inicialnombreapellido@gmail.com</p>
			</div>

			<div class="form-group">
				<textarea name="direccion" placeholder="Dirección" rows="2" class="form-control" required="required"></textarea>
				<p class="help-block">Ej: Av. Monseñor Duque, Ejido.</p>
			</div>

			<div class="form-group">
				<input type="text" name="telefono" placeholder="Teléfono móvil" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*" required="required" />
				<p class="help-block">Puede ingresar varios teléfonos separados por comas y sin espacios. Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				<input type="text" name="telefonoFijo" placeholder="Teléfono Fijo" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}(,([0-9]{3,4}\-?[0-9]{7}))*" />
				<p class="help-block">Opcional. Puede ingresar varios teléfonos separados por comas y sin espacios. Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				<select name="profesion" class="form-control" required="required">
					<option value=""> Profesión </option>

<?php
	$sql="select * from profesion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($profesion=pg_fetch_object($exe)) {
		if($profesion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$profesion->id\">$profesion->nombre</option>";
	}
?>

				</select>
			</div>
			
			<div class="form-group">
				<select name="categoria" class="form-control" required="required">
					<option value=""> Categoría </option>

<?php
	$sql="select * from categoria order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($categoria=pg_fetch_object($exe)) {
		if($categoria->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$categoria->id\">$categoria->nombre</option>";
	}
?>

				</select>
			</div>

			<div class="form-group">
				<select class="form-control" name="dedicacion" required="required">
					<option value=""> Dedicación </option>

<?php
	$sql="select * from dedicacion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($dedicacion=pg_fetch_object($exe)) {
		if($dedicacion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$dedicacion->id\">$dedicacion->nombre</option>";
	}
?>

				</select>
			</div>

			<div class="form-group">
				<select class="form-control" name="condicion" required="required">
					<option value=""> Condición </option>

<?php
	$sql="select * from condicion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($condicion=pg_fetch_object($exe)) {
		if($condicion->nombre == "Sin asignar")
			continue;

		echo "<option value=\"$condicion->id\">$condicion->nombre</option>";
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
		$sql="
			select cs.id as id, c.nombre as carrera, s.nombre as sede 
			from \"carreraSede\" as cs 
				join carrera as c on c.id=cs.\"idCarrera\" 
				join sede as s on s.id=cs.\"idSede\" 
			order by c.nombre, s.nombre";
		$exe=pg_query($sigpa, $sql);

		while($cs=pg_fetch_object($exe))
			echo "
						<label class=\"checkbox\"><input type=\"checkbox\" name=\"carrera[]\" value=\"$cs->id\"> $cs->carrera ($cs->sede) </label>";
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

<script>
	function obtenerDatos(cedula) {
		var formulario = document.profesor;
		var enlace = formulario.cedula.parentNode.querySelector("a");

		enlace.style.display = "none";

		$.ajax( {
			url: "moduloPlanificacion/Profesor/obtenerDatos.php",
			data: "cedula=" + cedula,
			type: "POST",
			success: function (respuesta) {
				if(respuesta) {
					var datos = JSON.parse(respuesta);

					if(datos.cedula) {
						enlace.innerHTML = "Pertenece a " + datos.apellido + " " + datos.nombre + ", haga click aquí para modificar los datos de este profesor.";
						enlace.style.display = "inline";
						enlace.href = "javascript: embem('moduloPlanificacion/Profesor/editar.php', '#page-wrapper', 'cedula=" + datos.cedula + "')";
					}

					else if(confirm("Se encontraron datos de una persona registrada con esta cédula, desea cargarlos?")) {
						formulario.nombre.value = datos.nombre;

						if(datos.segundoNombre)
							formulario.segundoNombre.value = datos.segundoNombre;

						formulario.apellido.value = datos.apellido;

						if(datos.segundoApellido)
							formulario.segundoApellido.value = datos.segundoApellido;

						if(datos.sexo == "f")
							document.querySelector("input[name='sexo'][value='f']").checked = "checked";

						else if(datos.sexo == "m")
							document.querySelector("input[name='sexo'][value='m']").checked = "checked";

						formulario.correo.value = datos.correo;
						formulario.direccion.innerHTML = datos.direccion;
						formulario.telefono.value = datos.telefono;
						formulario.telefonoFijo.value = datos.telefonoFijo;
					}
				}
			}
		});
	}
</script>