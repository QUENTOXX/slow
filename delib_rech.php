<?php
	session_start();

	require_once "config/params.php";
	require_once 'perso.php';

	// Spécification de l'accès total (si certains actes ne sont pas à destination du public)
	if (isset($_GET['ctrl'])) {
		if ($_GET['ctrl']==$cle_ctrl)
			$_SESSION['acces']=1;
	}
	if (!isset($_SESSION['acces']))
		$_SESSION['acces']=0;

?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Registres des Actes</title>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/jquery.dataTables.js"></script>
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
	if (!isset($_GET['insee'])){			// pour moi c'est l'ECPI
		if (isset($_GET['Villes']) && $_GET['Villes'] != "Toutes" && array_key_exists($_GET['Villes'], $insee_all)) {
				$_GET['insee']=$insee_all[$_GET['Villes']];
		}else {
				$_GET['insee']="Toutes";
		}
	}else {
		if (isset($_GET['Villes']) && $_GET['Villes'] != "Toutes" && array_key_exists($_GET['Villes'], $insee_all)) {
				$_GET['insee']=$insee_all[$_GET['Villes']];
		}else {
				$_GET['insee']="Toutes";
		}
	}

	$insee=$_GET['insee'];

	//haut de page (logo)

	if (isset($_GET['Villes']) && $_GET['Villes']!= "Toutes" && array_key_exists($_GET['Villes'], $pref_tab_all)) {

		$tab_perso= Recup_Fich_Tab($pref_tab_all[$_GET['Villes']]);

		if ($tab_perso != 0) {
			$lien= $tab_perso[1]["Logo"];
			$lien= ltrim($lien);
			$lien= rtrim($lien);

			if ($lien != "") {
				$lien= "Personalisation/logo/".$lien;
				image($lien);
			}
		}
	}

	//début page
	if (isset($_GET['Villes']) && $_GET['Villes']!= "Toutes" && array_key_exists($_GET['Villes'], $pref_tab_all)) {

		$tab_perso= Recup_Fich_Tab($pref_tab_all[$_GET['Villes']]);

		if ($tab_perso != 0) {
			$titre= $tab_perso[1]["Titre"];
			$titre= ltrim($titre);
			$titre= rtrim($titre);
		}else {
			$titre ="";
		}

		if ($titre == "") {
			echo "<h2>Registre des actes</h2>";
		}else {
			echo "<h2>$titre</h2>";
		}
	}else {
		echo "<h2>Registre des actes</h2>";
	}

	// classification
	echo "\n".'Classification <select id="classif" class="form-control"><option>Toutes</option>';
	$sql="SELECT * FROM class";
	$res=mysqli_query($link, $sql);
	while ($row=mysqli_fetch_object($res)) {
		echo "<option value='$row->class'>$row->class ".((strstr($row->class,".0")) ? "" : "&nbsp;&nbsp;&nbsp;").$row->nclass."</option>";
	}
	echo "</select>";

	$w="";


			echo "\n".'Natures <select id="nature" class="form-control"><option>Choisir</option>';
			echo "<option value=delib>Délibérations</option>";
			echo "<option value=decis>Décisions</option>";
			echo "<option value=arret>Arrêtés</option>";
			echo "<option value=docbf>Documents budgétaires et financiers</option>";
			echo "<option value=pv>PV</option>";
			echo "<option value=Toutes>Toutes</option>";

			echo "</select>";

			if (isset($_GET['nature'])) {
				if ($_GET['nature']!='Toutes' && $_GET['nature']!='Choisir')
					switch ($_GET['nature']) {
						case "delib":
							$w="AND nature= 'Délibérations'";
							break;
						case "decis":
							$w="AND nature= 'Autres'";
							break;
						case "arret":
							$w="AND nature= 'Actes réglementaires'";
							break;
						case "docbf":
							$w="AND nature= 'Documents budgétaires et financiers'";
							break;
						case "pv":
							$w="AND nature= 'PV'";
							break;

						default:
							$w="";
							break;
					}

			} else
					$w="";

		//}

	// Recherche par villes

	echo "\n".'Etablissements <select id="villes" class="form-control"><option>Choisir</option>';
	echo "<option value=Toutes>Toutes</option>";
	foreach ($pref_tab_all as $key => $value) {
		$vu= str_replace("_", " ", $key);
		echo "<option value=$key>$vu</option>";
	}

	echo "</select>";
//formulaire date
?>

<form name="Filter" method="POST">
    Date du :
    <input type="date" name="date_acte_deb" value="<?php echo date('Y-m-d'); ?>" />
    au:
    <input type="date" name="date_acte_fin" value="<?php echo date('Y-m-d'); ?>" />
    <input type="submit" name="submit" value="Rechercher"/>
</form>

<?php
//vérification post non vide
if (isset($_POST['date_acte_deb']) && isset($_POST['date_acte_fin'])) {
	$date_deb = date('Y-m-d', strtotime($_POST['date_acte_deb']));
	$date_fin = date('Y-m-d', strtotime($_POST['date_acte_fin']));

	$d= "AND del_date BETWEEN ' ".$date_deb." ' AND ' ".$date_fin." ' ";
}else {
	$d= "";
}

	if ($insee == "Toutes" || $_GET['Villes'] == "Toutes" || (!isset($_GET['Villes']))) {

		echo '<table id="delib" class="display compact" cellspacing="0" width="100%">';
		echo "<thead><tr><th>Date de décision</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Etablissement</th><th>Pièces jointes</th><th>Date d'affichage</th></tr></thead>"; //debut
		echo "<tfoot><tr><th>Date de décision</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Etablissement</th><th>Pièces jointes</th><th>Date d'affichage</th></tr></tfoot>"; //fin
		echo "<tbody>";

		foreach($pref_tab_all as $ville => $pref){

			$insee=$insee_all[$ville];
			$sql="SELECT * FROM ".$pref."index_delib WHERE insee='$insee' $w $d ORDER BY import_date DESC";
			$res=mysqli_query($link, $sql);
			//echo $sql;

			while ($row=mysqli_fetch_object($res)) {
				echo "<tr>";
				//echo "<td>".Aff_date($row->del_date)."</td>";
				echo "<td>$row->del_date</td>"; //date ajout
				echo "<td>$row->num</td>"; // numéro
				echo "<td>$row->code</td>"; //class (code)
				echo "<td>$row->obj</td>"; //objet
//				echo "<td>".utf8_encode($row->obj)."</td>"; //objet
				echo "<td>$ville </td>";
				echo "<td>";
				//piece jointe
				$tmp=explode("|",$row->pj);
				foreach($tmp as $pj){
					if ($pj != "") {
						echo "<a href='actes/$row->insee/$pj' target='_blank'><img src='ico/pdf.png' /></a>";
					}
				}
				echo "</td>";
				echo "<td>$row->import_date </td>";
				echo "</tr>";
			}

		}
		echo "</body></table>";
		echo "<br>";
	}elseif (array_key_exists($_GET['Villes'], $pref_tab_all)) {

		echo "<br>";

		$sql="SELECT * FROM ".$pref_tab_all[$_GET['Villes']]."index_delib WHERE insee='$insee' $w $d"."ORDER BY import_date DESC";
		$res=mysqli_query($link, $sql);
		//echo $sql;

		echo '<table id="delib" class="display compact" cellspacing="0" width="100%">';
		echo "<thead><tr><th>Date de décision</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Etablissement</th><th>Pièces jointes</th><th>Date d'affichage</th></tr></thead>"; //debut
		echo "<tfoot><tr><th>Date de décision</th><th>Numéro</th><th>Classification</th><th>Objet</th><th>Etablissement</th><th>Pièces jointes</th><th>Date d'affichage</th></tr></tfoot>"; //fin
		echo "<tbody>";
		while ($row=mysqli_fetch_object($res)) {
			echo "<tr>";
			//echo "<td>".Aff_date($row->del_date)."</td>";
			echo "<td>$row->del_date</td>"; //date ajout
			echo "<td>$row->num</td>"; // numéro
			echo "<td>$row->code</td>"; //class (code)
			echo "<td>$row->obj</td>"; //objet
			echo "<td>".$_GET['Villes']."</td>";
			echo "<td>";
			//piece jointe
			$tmp=explode("|",$row->pj);
			foreach($tmp as $pj){
				if ($pj != "") {
					echo "<a href='actes/$row->insee/$pj' target='_blank'><img src='ico/pdf.png' /></a>";
				}
			}
			echo "</td>";
			echo "<td>$row->import_date </td>";
			echo "</tr>";
		}
		echo "</body></table>";
		echo "<br>";
	}

?>

<script>
$(document).ready(function() {
	var table = $('#delib').dataTable( {
		"language": { "url": "french.json" },
		"order": [[ 0, "desc" ],[ 1, "desc" ]],
		"lengthMenu": [[50, 100, -1], [50, 100, "Tous"]]
		}
	);


});

$("#classif").change(function() {
	if ($(this).val()!="Toutes")
		$("input[type=search]").focus().val($(this).val()).blur().trigger('keyup');
})
/*
$("#B_date_acte_fin").click(function() {
	$("input[type=search]").focus().val($("#date_acte_deb").val()).blur().trigger('keyup');
})
*/
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
			document.location="delib_rech.php?Villes="+ville;
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

	if ($(this).val() == "Choisir") {
		document.location="delib_rech.php?Villes=Toutes";
	}else {
		document.location="delib_rech.php?Villes="+$(this).val();
	}
})

</script>
<?php
	require_once "disconnect.inc.php";
?>
