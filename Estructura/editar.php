<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";

	$nombre = $_POST["nombre"];

	$sql = "select * from estructura where nombre='$nombre'";
	$exe = pg_query($sigpa, $sql);
	$estructura = pg_fetch_object($exe);
	$estructura = json_decode($estructura->estructura);
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Editar estructura</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">
		<form name="estructura" method="POST" action="moduloPlanificacion/Estructura/modificar.php" data-exe="embem('moduloPlanificacion/Estructura/index.php', '#page-wrapper')" role="form">
			<div class="form-group">
				<input type="text" name="nombre" placeholder="Nombre" value="<?= $nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ]*( [a-záéíóúñA-ZÁÉÍÓÚÑ]+)*$" onKeyUp="if(this.value != document.estructura.nombreAnt.value) Verif(this)" required="required" />
				<input type="hidden" name="nombreAnt" value="<?= $nombre; ?>" />
				<p class="help-block">Solo están permitidos caracteres alfabéticos y el primero debe estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: PNF Trimestral.</p>
			</div>

			<br/><h3>Periodos</h3><br/>

<?php
	$n = 0;

	foreach($estructura->periodos as $periodo) {
		if($n)
			echo "<div class=\"form-group col-xs-12\"><hr/></div>";
?>

			<div class="row">
				<div class="form-group <?= (!$n) ? "col-xs-8" : "col-xs-7"; ?>">
					<input type="text" name="nombrePeriodo<?= $n; ?>" placeholder="Nombre" value="<?= $periodo->nombre; ?>" class="form-control nombrePeriodo" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$" required="required" />
					<p class="help-block">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trayecto 1.</p>
				</div>

				<div class="form-group col-xs-4">
					<input type="text" name="idPeriodo<?= $n; ?>" placeholder="Identificador" value="<?= $periodo->id; ?>" class="form-control idPeriodo" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$" required="required" />
					<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: T1.</p>
				</div>

<?php
		if($n) {
?>

				<div class="form-group col-xs-1">
					<i class="fa fa-trash-o fa-fw eliminar" title="Eliminar periodo" onClick="this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode)"></i>
				</div>

<?php
		}
?>

				<div class="col-xs-offset-1 col-xs-11" style="border-left: 0.5em solid #444">
					<div class="checkbox"><label><input type="checkbox" onClick="subperiodos(this)" <?php if(!$periodo->duracion) echo "checked=\"checked\"" ?> > Subperiodos</label></div>

					<div class="form-group" <?php if(!$periodo->duracion) echo "style=\"display: none;\"" ?>>
						<div class="input-group">
							<input type="text" name="duracionPeriodo<?= $n; ?>" placeholder="Duración" value="<?= (!$periodo->duracion) ? "false" : "$periodo->duracion"; ?>" class="form-control duracionPeriodo" <?php if($periodo->duracion) echo "pattern=\"^[0-9]+$\"" ?> required="required" />
							<span class="input-group-addon" title="Meses"><i class="fa fa-calendar fa-fw"></i></span>
						</div>
						<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p>
					</div>

					<div class="row"><div>
						
<?php
		if(!$periodo->duracion) {
			$subn = 0;

			foreach($periodo->subperiodos as $subperiodo) {
				if($subn)
					echo "<div><div class=\"form-group col-xs-12\"><hr/></div>";
?>

						<div class="form-group <?= (!$subn) ? "col-xs-8" : "col-xs-7"; ?>">
							<input type="text" name="nombrePeriodo<?= $n; ?>Sub[]" placeholder="Nombre" value="<?= $subperiodo->nombre; ?>" class="form-control" pattern="^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$" required="required" onkeyup="if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)" />
							<p class="help-block">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trimestre 1.</p>
						</div>

						<div class="form-group col-xs-4">
							<input type="text" name="idPeriodo<?= $n; ?>Sub[]" placeholder="Identificador" value="<?= $subperiodo->id; ?>" class="form-control" pattern="^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$" required="required" onkeyup="if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)" />
							<p class="help-block">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: t1.</p>
						</div>

<?php
				if($subn) {
?>

						<div class="form-group col-xs-1">
							<i class="fa fa-trash-o fa-fw eliminar" title="Eliminar subperiodo" onClick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)"></i>
						</div>

<?php
				}
?>

						<div class="form-group col-xs-12">
							<div class="input-group">
								<input type="text" name="duracionPeriodo<?= $n; ?>Sub[]" placeholder="Duración" value="<?= $subperiodo->duracion; ?>" class="form-control" pattern="^[0-9]+$" required="required" onkeyup="if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)" />
								<span class="input-group-addon" title="Meses"><i class="fa fa-calendar fa-fw"></i></span>
							</div>
							<p class="help-block">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p>
						</div>

<?php
				if($subn)
					echo "</div>";

				++$subn;
			}

?>

						<div class="col-xs-12"><i class="fa fa-plus fa-fw agregar" title="Nuevo subperiodo" onClick="nuevoSubperiodo(this.parentNode)"></i></div>

<?php
		}
?>

					</div>
				</div></div>
			</div>

<?php
		++$n;
	}
?>

			<div class="row">
				<div class="form-group col-xs-12"><i class="fa fa-plus fa-fw agregar" title="Nuevo periodo" onClick="nuevoPeriodo(this)"></i></div>
			</div>

			<input type="hidden" name="cantidad" value="<?= $n-1; ?>" />

			<div class="form-group text-center">
				<input type="submit" value="Guardar" class="btn btn-lg btn-primary" />
				<input type="button" value="Cancelar" class="btn btn-lg" onClick="embem('moduloPlanificacion/Estructura/index.php', '#page-wrapper')" />
			</div>
		</form>
	</div>
</div>

<script>
	function subperiodos(obj) {
		var duracion = obj.parentNode.parentNode.parentNode.querySelector(".form-group");
		var duracionInput = duracion.querySelector("input");
		var subperiodos = obj.parentNode.parentNode.parentNode.querySelector(".row");

		if(obj.checked) {
			duracion.style.display = "none";
			$(duracionInput).attr("value", "false");
			$(duracionInput).removeAttr("pattern");

			var nameNom = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".nombrePeriodo").name + "Sub[]";
			var nameId = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".idPeriodo").name + "Sub[]";
			var nameDuracion = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".duracionPeriodo").name + "Sub[]";

			subperiodos.innerHTML = "<div><div class=\"form-group col-xs-8\"> <input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trimestre 1.</p> </div> <div class=\"form-group col-xs-4\"> <input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: t1.</p> </div> <div class=\"form-group col-xs-12\"> <div class=\"input-group\"> <input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span> </div> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p> </div> <div class=\"col-xs-12\"><i class=\"fa fa-plus fa-fw agregar\" title=\"Nuevo subperiodo\" onClick=\"nuevoSubperiodo(this.parentNode)\"></i></div></div>";

			/*
				<div class=\"form-group col-xs-8\">
					<input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trimestre 1.</p>
				</div>

				<div class=\"form-group col-xs-4\">
					<input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: t1.</p>
				</div>

				<div class=\"form-group col-xs-12\">
					<div class=\"input-group\">
						<input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
						<span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span>
					</div>
					<p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p>
				</div>

				<div class=\"col-xs-12\"><i class=\"fa fa-plus fa-fw agregar\" title=\"Nuevo subperiodo\" onClick=\"nuevoSubperiodo(this.parentNode)\"></i></div>
			*/
		}

		else {
			subperiodos.innerHTML = "";

			duracion.style.display = "block";
			$(duracionInput).attr("value", "");
			$(duracionInput).attr("pattern", "^[0-9]+$");
		}
	}

	function nuevoSubperiodo(obj) {
		var subP = document.createElement("div");

		var nameNom = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".nombrePeriodo").name + "Sub[]";
		var nameId = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".idPeriodo").name + "Sub[]";
		var nameDuracion = obj.parentNode.parentNode.parentNode.parentNode.querySelector(".duracionPeriodo").name + "Sub[]";

		subP.innerHTML = "<div> <div class=\"form-group col-xs-12\"><hr/></div> <div class=\"form-group col-xs-7\"> <input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trimestre 1.</p> </div> <div class=\"form-group col-xs-4\"> <input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: t1.</p> </div> <div class=\"form-group col-xs-1\"> <i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Eliminar subperiodo\" onClick=\"this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)\"></i> </div> <div class=\"form-group col-xs-12\"> <div class=\"input-group\"> <input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span> </div> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p> </div> </div>";

		/*
			<div>
				<div class=\"form-group col-xs-12\"><hr/></div>

				<div class=\"form-group col-xs-7\">
					<input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trimestre 1.</p>
				</div>

				<div class=\"form-group col-xs-4\">
					<input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: t1.</p>
				</div>

				<div class=\"form-group col-xs-1\">
					<i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Eliminar subperiodo\" onClick=\"this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)\"></i>
				</div>

				<div class=\"form-group col-xs-12\">
					<div class=\"input-group\">
						<input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
						<span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span>
					</div>
					<p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p>
				</div>
			</div>
		*/

		obj.parentNode.insertBefore(subP, obj);
	}

	function nuevoPeriodo(obj) {
		var cantidad = document.estructura.cantidad;
		cantidad.value = parseInt(cantidad.value) + 1;

		var periodo = document.createElement("div");

		var nameNom = "nombrePeriodo" + cantidad.value;
		var nameId = "idPeriodo" + cantidad.value;
		var nameDuracion = "duracionPeriodo" + cantidad.value;

		periodo.innerHTML = "<div class=\"row\"> <div class=\"form-group col-xs-12\"><hr/></div> <div class=\"form-group col-xs-7\"> <input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control nombrePeriodo\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trayecto 1.</p> </div> <div class=\"form-group col-xs-4\"> <input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control idPeriodo\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: T1.</p> </div> <div class=\"form-group col-xs-1\"> <i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Eliminar periodo\" onClick=\"this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode)\"></i> </div> <div class=\"col-xs-offset-1 col-xs-11\" style=\"border-left: 0.5em solid #444\"> <div class=\"checkbox\"><label> <input type=\"checkbox\" onClick=\"subperiodos(this)\"> Subperiodos </label></div> <div class=\"form-group\"> <div class=\"input-group\"> <input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control duracionPeriodo\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" /> <span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span> </div> <p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p> </div> <div class=\"row\"></div> </div> </div>";

		/*
			<div class=\"row\">
				<div class=\"form-group col-xs-12\"><hr/></div>

				<div class=\"form-group col-xs-7\">
					<input type=\"text\" name=\"" + nameNom + "\" placeholder=\"Nombre\" class=\"form-control nombrePeriodo\" pattern=\"^[A-ZÁÉÍÓÚÑ][a-záéíóúñA-ZÁÉÍÓÚÑ0-9]*( [a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+)*$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos aunque el primero deberá ser alfabético y estar en mayúculas, el uso de las mismas en los demás caracteres viene dado según su criterio. Ej: Trayecto 1.</p>
				</div>

				<div class=\"form-group col-xs-4\">
					<input type=\"text\" name=\"" + nameId + "\" placeholder=\"Identificador\" class=\"form-control idPeriodo\" pattern=\"^[a-záéíóúñA-ZÁÉÍÓÚÑ0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
					<p class=\"help-block\">Solo están permitidos caracteres alfanuméricos sin espacios, es recomendable que permita determinar facilmente a que periodo pertenece. Ej: T1.</p>
				</div>

				<div class=\"form-group col-xs-1\">
					<i class=\"fa fa-trash-o fa-fw eliminar\" title=\"Eliminar periodo\" onClick=\"this.parentNode.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode.parentNode)\"></i>
				</div>

				<div class=\"col-xs-offset-1 col-xs-11\" style=\"border-left: 0.5em solid #444\">
					<div class=\"checkbox\"><label>
						<input type=\"checkbox\" onClick=\"subperiodos(this)\"> Subperiodos
					</label></div>

					<div class=\"form-group\">
						<div class=\"input-group\">
							<input type=\"text\" name=\"" + nameDuracion + "\" placeholder=\"Duración\" class=\"form-control duracionPeriodo\" pattern=\"^[0-9]+$\" required=\"required\" onkeyup=\"if(!this.value) $(this).removeAttr('value'); else $(this).attr('value', this.value)\" />
							<span class=\"input-group-addon\" title=\"Meses\"><i class=\"fa fa-calendar fa-fw\"></i></span>
						</div>
						<p class=\"help-block\">Solo están permitidos caracteres numéricos, la unidad de medida utilizada es <strong>Meses</strong>. Ej: 3.</p>
					</div>

					<div class=\"row\"></div>
				</div>
			</div>
		*/

		obj.parentNode.insertBefore(periodo, obj);
	}
</script>