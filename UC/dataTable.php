<?php
	require "../../script/verifSesion.php";
	require "../../lib/conexion.php";
?>

{
	"data": [

<?php
	$sql = "
		select uc.id as id, uc.nombre as nombre, uc.renombrable as renombrable, e.nombre as eje, c.nombre as carrera
		from \"unidadCurricular\" as uc 
			join carrera as c on c.id=uc.\"idCarrera\" 
			join eje as e on e.id=uc.\"idEje\" 
	"
	. (($_SESSION["nivel"] == 3) ? " where uc.\"idCarrera\" ='$_SESSION[carreraCoord]'" : "") .

	"
		order by uc.nombre, c.nombre, e.nombre
	";
	$exe = pg_query($sigpa, $sql);

	while($uc = pg_fetch_object($exe)) {
?>

		[

<?php
		$sql = "select count(id) as n from \"ucMalla\" where \"idUC\"='$uc->id'";
		$exe2 = pg_query($sigpa, $sql);
		$nUCM = pg_fetch_object($exe2);

		if(! $nUCM->n) {
?>

			"<i class=\"fa fa-exclamation-triangle alerta\" title=\"Esta unidad curricular no es usada por ninguna malla\"></i>",

<?php
		}

		else {
?>

			"",

<?php
		}
?>

			"<?php echo $uc->nombre; if($uc->renombrable == "t") { ?> <i class=\"fa fa-pencil-square-o\" title=\"Renombrable\"></i> <?php } ?>",
			"<?= $uc->id; ?>",
			<?php if($_SESSION["nivel"] < 3) echo "\"$uc->carrera\"," ?>
			"<?php if($_SESSION["nivel"] < 3) { ?><div class=\"row\"> <div class=\"col-xs-7 col-sm-7 col-md-6 col-lg-7\"> <?php } ?> <?= $uc->eje; ?> <?php if($_SESSION["nivel"] < 3) { ?> </div> <div class=\"col-xs-5 col-sm-5 col-md-6 col-lg-5 text-center\"> <i class=\"fa fa-pencil fa-fw editar\" title=\"Editar\" onClick=\"embem('moduloPlanificacion/UC/editar.php', '#page-wrapper', 'id=<?= $uc->id ?>')\"></i> <i class=\"fa fa-trash-o fa-fw eliminar\" onClick=\"if(confirm('¿Realmente desea eliminar <?= $uc->nombre ?>?')) sendReq('../../script/eliminar.php', 'tabla=unidadCurricular&campo=id&valor=<?= $uc->id ?>', 'moduloPlanificacion/UC/index.php')\" title=\"Eliminar\"></i> </div> </div> <?php } ?>"
		],

<?php
	}
?>

		["", "", "", "", ""]

	]
}