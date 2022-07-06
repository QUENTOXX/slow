<?php
	session_start();

	require_once "params.php";

	// Spécification de l'accès total (si certains actes ne sont pas à destination du public)
	if (isset($_GET['ctrl'])) {
		if ($_GET['ctrl']==$cle_ctrl)
			$_SESSION['acces']=1;
	}
	if (!isset($_SESSION['acces']))
		$_SESSION['acces']=0;

	function RecupURL()
	{
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
			$url = "https";
		else
			$url = "http";

		// Ajoutez // à l'URL.
		$url .= "://";

		// Ajoutez l'hôte (nom de domaine, ip) à l'URL.
		$url .= $_SERVER['HTTP_HOST'];

		// Ajouter l'emplacement de la ressource demandée à l'URL

		$url .= rawurldecode($_SERVER['REQUEST_URI']);
		// Afficher l'URL
		return $url;
	}

?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Registres des Actes</title>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="style/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="style/style.css">
<style>

</style>

<body>
<?php

	set_time_limit(6000);
	require_once "connect.inc.php";
	require_once "fonctions.php";


	// Filtre des communes
	if (!isset($_GET['insee']))
		$_GET['insee']=$insee_par_defaut; // Pour moi c'est l'EPCI

	$insee=$_GET['insee'];

	echo "<h2>Registre des actes</h2>";

	echo "\n".'Classification <select id="classif" class="form-control"><option>Toutes</option>';
	$sql="SELECT * FROM class";
	$res=mysqli_query($link, $sql);
	while ($row=mysqli_fetch_object($res)) {
		echo "<option value='$row->class'>$row->class ".((strstr($row->class,".0")) ? "" : "&nbsp;&nbsp;&nbsp;").utf8_encode($row->nclass)."</option>";
	}
	echo "</select>";

	// Filtres pour le public : affichera les actes de natures Délibérations, mais exclus Actes individuels, Actes réglementaires, Autres, ...
	$w="AND nature LIKE '%rations'";

	if ($_SESSION['acces']==1) { // => Acces total pour les besoins internes à notre EPCI
		echo "\n".' &nbsp; Nature <select id="nature" class="form-control"<option>Choisir</option>';
		echo "<option>Choisir</option>";
		$sql="SELECT DISTINCT nature FROM ".$pref_tab."index_delib ORDER BY nature";
		$res=mysqli_query($link, $sql);
		//echo $sql;
		while ($row=mysqli_fetch_object($res)) {
			echo "<option>".utf8_encode($row->nature)."</option>";
		}
		echo "<option value=All>Toutes</option>";
		echo "<option value=Toutes>Public</option>";
		echo "</select>";

		if (isset($_GET['nature'])) {
			if ($_GET['nature']!='Toutes')
				$w="AND nature='".$_GET['nature']."'";

		} else
				$w="";


	}
	// Recherche par villes

	echo "\n".'Villes <select id="villes" class="form-control"><option>Choisir</option>';
	echo "<option value=Toutes>Toutes</option>";
	echo "<option value=Givors>Givors</option>";
	echo "<option value=Grigny>Grigny</option>";
	echo "<option value=Saint-Chamont>Saint Chamont</option>";
	echo "<option value=Venissieux>Venissieux</option>";
	echo "<option value=Corbas>Corbas</option>";
	echo "<option value=Pierre_Benite>Pierre Benite</option>";
	echo "<option value=Rive_de_Gier>Rive de Gier</option>";
	echo "<option value=Vaulx_en_Velin>Vaulx en Velin</option>";
	echo "<option value=Sitiv>Sitiv</option>";

echo "</select>";

?>


 &nbsp; Date <input class="form-control" type="date" id="date_acte" placeholder="Date de l'acte" >
<button class="btn" id="B_date_acte">🔍</button>

<?php
	$sql="SELECT * FROM ".$pref_tab."index_delib WHERE insee='$insee' $w ORDER BY del_date DESC";
	$res=mysqli_query($link, $sql);
	//echo $sql;
	echo '<table id="delib" class="display compact" cellspacing="0" width="100%">';
	echo "<thead><tr><th>Date</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Pièces jointes</th></tr></thead>";
	echo "<tfoot><tr><th>Date</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Pièces jointes</th></tr></tfoot>";
	echo "<tbody>";
	while ($row=mysqli_fetch_object($res)) {
		echo "<tr>";
		//echo "<td>".Aff_date($row->del_date)."</td>";
		echo "<td>$row->del_date</td>";
		echo "<td>$row->num</td>";
		echo "<td>$row->code</td>";
		echo "<td>".utf8_encode($row->obj)."</td>";
		echo "<td>";
		$tmp=explode("|",$row->pj);
		foreach($tmp as $pj)
			echo "<a href='actes/$row->insee/$pj' target='_blank'><img src='ico/pdf.png' /></a>";
		echo "</td>";
		echo "</tr>";
	}
	echo "</body></table>";

?>

<script>
$(document).ready(function() {
	var table = $('#delib').dataTable( {
		"language": { "url": "french.json" },
		"order": [[ 0, "desc" ],[ 1, "desc" ]],
		"lengthMenu": [[25, 50, 100, -1, 10], [25, 50, 100, "Tous", 10]]
		}
	);


});

$("#classif").change(function() {
	if ($(this).val()!="Toutes")
		$("input[type=search]").focus().val($(this).val()).blur().trigger('keyup');
})

$("#B_date_acte").click(function() {
	$("input[type=search]").focus().val($("#date_acte").val()).blur().trigger('keyup');
})

$("#nature").change(function() {
	let searchParams = new URLSearchParams(window.location.search);
	//alert($(this).val());
//	$("input[type=search]").focus().val($(this).val()).blur().trigger('keyup');
	if ($(this).val()== "Toutes") {
		if (searchParams.has('Villes')) {
			let ville= searchParams.get('Villes');
			document.location="delib_rech.php?insee=<?php echo $_GET['insee']; ?>&nature="+$(this).val()+"&Villes="+ville;
		}else {
			document.location="delib_rech.php?insee=<?php echo $_GET['insee']; ?>&nature="+$(this).val()
		}
	}else if ($(this).val()== "All") {
		if (searchParams.has('Villes')) {
			let ville= searchParams.get('Villes');
			document.location="delib_rech.php?Villes"+ville;
		}else {
			document.location="delib_rech.php";
		}
	}else {
		if (searchParams.has('Villes')) {
			let ville= searchParams.get('Villes');
			document.location="delib_rech.php?insee=<?php echo $_GET['insee']; ?>&nature="+$(this).val()+"&Villes="+ville;
		}else {
			document.location="delib_rech.php?insee=<?php echo $_GET['insee']; ?>&nature="+$(this).val()
		}
	}
})

$("#villes").change(function() {

	let searchParams = new URLSearchParams(window.location.search);

	if ((!searchParams.has('insee')) || (!searchParams.has('nature'))) {
		document.location="delib_rech.php?Villes="+$(this).val();
	}else {
		let insee= searchParams.get('insee');
		let nature= searchParams.get('nature');

		document.location="delib_rech.php?insee="+insee+"&nature="+nature+"&Villes="+$(this).val();
	}
})

</script>
<?php
	require_once "disconnect.inc.php";
?>
