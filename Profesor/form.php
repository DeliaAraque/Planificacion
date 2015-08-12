<?php
	require "../../script/verifSesion.php";
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
				Carrera:
				<select name="carrera" multiple class="form-control">

<?php
	require "../../lib/conexion.php";

	$sql="select * from carrera order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($carera=pg_fetch_object($exe))
		echo "
					<option value='$carera->id'>$carera->nombre</option>";
?>

				</select>
			</div>

			<div class="form-group">
				<input type="text" name="cedula" placeholder="Cédula" class="form-control" maxlength="8" pattern="[0-9]{7,}" onKeyUp="Verif(this)" required="required" title="Ingresela cédula del profesor" />
				<p class="help-block">Solo están permitidos caracteres numéricos y debe contener al menos 7. Ej: 12345678.</p>
			</div>

			<div class="form-group">
				<input type="text" name="nombre" placeholder="Primer nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$" required="required" title="Ingrese el nombre del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				<input type="text" name="segundoNombre" placeholder="Segundo nombre" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$" title="Ingrese el segundo nombre del profesor" />
				<p class="help-block">Opcional, solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Nombre.</p>
			</div>

			<div class="form-group">
				<input type="text" name="apellido" placeholder="Primer apellido" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$" required="required" title="Ingrese el apellido del profesor" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y la primera letra debe estar en mayúscula. Ej: Apellido.</p>
			</div>

			<div class="form-group">
				<input type="text" name="segundoApellido" placeholder="Segundo apellido" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñ]*$" title="Ingrese el segundo apellido del profesor" />
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
				<input type="text" name="correo" placeholder="Correo electŕonico" class="form-control" pattern="^[a-z0-9\-_\.]+@[a-z0-9\-_\.]+\.[a-z0-9\-_\.]+$" required="required" title="Ingrese el correo electrónico del profesor" />
				<p class="help-block">Ej: inicialnombreapellido@uptm.edu.ve</p>
			</div>

			<div class="form-group">
				<textarea name="direccion" placeholder="Dirección" rows="2" class="form-control" required="required"></textarea>
				<p class="help-block">Ej: Av. Monseñor Duque, Ejido.</p>
			</div>

			<div class="form-group">
				<input type="text" name="telefono" placeholder="Teléfono móvil" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}" required="required" />
				<p class="help-block">Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				<input type="text" name="telefonoFijo" placeholder="Teléfono Fijo" class="form-control" pattern="[0-9]{3,4}\-?[0-9]{7}" />
				<p class="help-block">Opcional. Ej: 0000-0000000</p>
			</div>

			<div class="form-group">
				<select name="profesion" class="form-control" required="required">
					<option value=""> Profesión </option>

<?php
	$sql="select * from profesion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($profesion=pg_fetch_object($exe))
		echo "
					<option value=\"$profesion->id\">$profesion->nombre</option>";
?>

				</select>
			</div>
			
			<div class="form-group">
				<select name="categoria" class="form-control" required="required">
					<option value=""> Categoría </option>

<?php
	$sql="select * from categoria order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($categoria=pg_fetch_object($exe))
		echo "
					<option value=\"$categoria->id\">$categoria->nombre</option>";
?>

				</select>
			</div>

			<div class="form-group">
				<select class="form-control" name="dedicacion" required="required">
					<option value=""> Dedicación </option>

<?php
	$sql="select * from dedicacion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($dedicacion=pg_fetch_object($exe))
		echo "
					<option value=\"$dedicacion->id\">$dedicacion->nombre</option>";
?>

				</select>
			</div>


			<div class="form-group"> 
				Condición:
				<div class="radio-inline">

<?php
	$sql="select * from condicion order by nombre";
	$exe=pg_query($sigpa, $sql);

	while($condicion=pg_fetch_object($exe))
		echo "
					<label class=\"radio-inline\"><input type=\"radio\" name=\"condicion\" value=\"$condicion->id\" required=\"required\"> $condicion->nombre </label>";
?>

				</div>
			</div>

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Regresar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Profesor/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>