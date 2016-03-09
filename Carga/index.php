<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			Carga académica
			<i id="planillaBtn" class="fa fa-file-pdf-o pull-right" title="Generar planilla" style="cursor: pointer;" onClick="planilla()"></i>
			<i id="planillaBtn" class="fa fa-search pull-right" title="Previsualizar planilla" style="cursor: pointer;" onClick="planilla(true)"></i>
			<i id="planillaBtn" class="fa fa-font pull-right" title="Ver secciones sin carga asignada" style="cursor: pointer;" onClick="moreInfo('moduloPlanificacion/Seccion/sincarga.php', 'popUp=1')"></i>
		</h1>
	</div>
</div>

<div class="row">
	<div class="col-lg-12">

<?php
	if($_SESSION["nivel"] == 3) {
?>

			<input type="hidden" id="carreraSelect" value="<?= $_SESSION["carreraCoord"]; ?>" />
			<input type="hidden" id="sedeSelect" value="<?= $_SESSION["sedeCoord"]; ?>" />

<?php
	}

	else {
?>

		<div class="form-group">
			<select id="carreraSelect" class="form-control" onChange="selectSedes()" required="required">
				<option value="">Carrera</option>

<?php
	$sql="
		select c.id as id, c.nombre as nombre
		from seccion as sec 
			join periodo as p on p.\"ID\"=sec.\"idPeriodo\"
			join \"estructuraCS\" as ecs on ecs.id=p.\"idECS\"
			join \"carreraSede\" as cs on cs.id=ecs.\"idCS\" 
			join carrera as c on c.id=\"idCarrera\" 
		group by c.id, c.nombre 
		order by c.nombre
	";
	$exe=pg_query($sigpa, $sql);

	while($carrera=pg_fetch_object($exe))
		echo "<option value=\"$carrera->id\">$carrera->nombre</option>";
?>

			</select>
		</div>

		<div class="form-group">
			<select id="sedeSelect" class="form-control" onChange="selectPeriodos()" required="required">
				<option value="">Sede</option>
			</select>
			<p class="help-block">Antes de poder seleccionar algúna sede, debe elegir una carrera.</p>
		</div>

<?php
	}
?>

		<div class="form-group">
			<select id="periodoSelect" class="form-control" onChange="selectMallas()" required="required">
				<option value="">Periodo académico</option>
			</select>
			<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir una sede.</p>
		</div>

		<div class="form-group">
			<select id="mallaSelect" class="form-control" onChange="selectPeriodosE()" required="required">
				<option value="">Malla</option>
			</select>
			<p class="help-block">Antes de poder seleccionar alguna malla, debe elegir algún periodo académico.</p>
		</div>

		<div class="form-group">
			<select id="periodoEstructuraSelect" class="form-control" onChange="unidadesCurriculares()" required="required">
				<option value="">Periodo</option>
			</select>
			<p class="help-block">Antes de poder seleccionar algún periodo, debe elegir alguna malla.</p>
		</div>

		<div id="unidadesCurriculares">
		</div>
	</div>
</div>

<script>
	function selectSedes() {
		var carrera = document.getElementById("carreraSelect");
		var sede = document.getElementById("sedeSelect");

		if(! carrera.value) {
			sede.innerHTML = "<option value=\"\">Sede</option>";
			document.getElementById("periodoSelect").innerHTML = "<option value=\"\">Periodo académico</option>";
			document.getElementById("mallaSelect").innerHTML = "<option value=\"\">Malla</option>";
			document.getElementById("periodoEstructuraSelect").innerHTML = "<option value=\"\">Periodo</option>";
			document.getElementById("unidadesCurriculares").innerHTML = "";
			return false;
		}

		embem('moduloPlanificacion/Carga/sedes.php', sede, "carrera=" + carrera.value);
	}

	function selectPeriodos() {
		var carrera = document.getElementById("carreraSelect");
		var sede = document.getElementById("sedeSelect");
		var periodo = document.getElementById("periodoSelect");

		if(! sede.value) {
			periodo.innerHTML = "<option value=\"\">Periodo académico</option>";
			document.getElementById("mallaSelect").innerHTML = "<option value=\"\">Malla</option>";
			document.getElementById("periodoEstructuraSelect").innerHTML = "<option value=\"\">Periodo</option>";
			document.getElementById("unidadesCurriculares").innerHTML = "";
			return false;
		}

		embem('moduloPlanificacion/Carga/periodos.php', periodo, "carrera=" + carrera.value + "&sede=" + sede.value);
	}

	function selectMallas() {
		var carrera = document.getElementById("carreraSelect");
		var sede = document.getElementById("sedeSelect");
		var periodo = document.getElementById("periodoSelect");
		var malla = document.getElementById("mallaSelect");

		if(! periodo.value) {
			malla.innerHTML = "<option value=\"\">Malla</option>";
			document.getElementById("periodoEstructuraSelect").innerHTML = "<option value=\"\">Periodo</option>";
			document.getElementById("unidadesCurriculares").innerHTML = "";
			return false;
		}

		embem('moduloPlanificacion/Carga/mallas.php', malla, "carrera=" + carrera.value + "&sede=" + sede.value + "&periodo=" + periodo.value);
	}

	function selectPeriodosE() {
		var carrera = document.getElementById("carreraSelect");
		var sede = document.getElementById("sedeSelect");
		var periodo = document.getElementById("periodoSelect");
		var malla = document.getElementById("mallaSelect");
		var periodoEstructura = document.getElementById("periodoEstructuraSelect");

		if(! malla.value) {
			periodoEstructura.innerHTML = "<option value=\"\">Periodo</option>";
			document.getElementById("unidadesCurriculares").innerHTML = "";
			return false;
		}

		embem('moduloPlanificacion/Carga/periodosEstructura.php', periodoEstructura, "carrera=" + carrera.value + "&sede=" + sede.value + "&periodo=" + periodo.value + "&mecs=" + malla.value);
	}

	function unidadesCurriculares() {
		var carrera = document.getElementById("carreraSelect");
		var sede = document.getElementById("sedeSelect");
		var periodo = document.getElementById("periodoSelect");
		var malla = document.getElementById("mallaSelect");
		var periodoEstructura = document.getElementById("periodoEstructuraSelect");
		var unidadesCurriculares = document.getElementById("unidadesCurriculares");

		if(! periodoEstructura.value) {
			unidadesCurriculares.innerHTML = "";
			return false;
		}

		embem('moduloPlanificacion/Carga/unidadesCurriculares.php', unidadesCurriculares, "carrera=" + carrera.value + "&sede=" + sede.value + "&periodo=" + periodo.value + "&mecs=" + malla.value + "&periodoEstructura=" + periodoEstructura.value);
	}

	function planilla(previsualizar) {
		var periodo = document.getElementById("periodoSelect");
		var malla = document.getElementById("mallaSelect");

		if(! periodo.value) {
			popUp("Debe seleccionar un periodo académico");
			return false;
		}

		if(! malla.value) {
			popUp("Debe seleccionar una malla");
			return false;
		}

		if(previsualizar)
			window.open("moduloPlanificacion/Carga/planilla.php?mecs=" + malla.value + "&periodo=" + periodo.value + "&previsualizar=true", "_blank", "menubar=no, status=no, titlebar=no, width=" + screen.availWidth + ", height=" + screen.availHeight);

		else
			sendReq("moduloPlanificacion/Carga/generarPlanilla.php", "mecs=" + malla.value + "&periodo=" + periodo.value);
	}

	<?php if($_SESSION["nivel"] == 3) echo "selectPeriodos()"; ?>
</script>