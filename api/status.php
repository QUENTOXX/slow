<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Status</title>
<link rel="stylesheet" type="text/css" href="../style/style.css">
<?php
	require_once 'ctrl_ip.php';

	if ($error == 1) {
		http_response_code(403);
		die('Forbidden');
	}

	set_include_path("../connect.inc.php");
	set_include_path("../fonctions.php");
	set_include_path("../disconnect.inc.php");
	require_once '../config/params.php';
	require_once '../connect.inc.php';
	require_once '../fonctions.php';

	if (!isset($_GET['type'])) {
		$_GET['type']= "user";
	}

	$erreurs= 0;
	$errvil= [];
	$errcom= [];
	$jsonerr = array();

	$sql = "SELECT * FROM process WHERE actif= 1";
	$req=mysqli_query($link, $sql);

	foreach ($req as $user) {

		array_push($errvil, $user['ville']);
		array_push($errcom, $user['com']);
		$erreurs += 1;
	}

	$sql = "SELECT * FROM process";
	$req=mysqli_query($link, $sql);

	foreach ($req as $user) {

		if (in_array($user['ville'], $errvil)) {
			$jsonerr[$user['ville']]= 1;
		}else {
			$jsonerr[$user['ville']]= 0;
		}
	}

	if ($_GET['type'] == "user") {

		echo "<h2>Status ! </h2>";

		if ($erreurs == 0) {

			echo '<div class="info info-vert">✔️ Status <b> OK ! </b></div>';
		}else {

			echo '<div class="info info-rouge">⚠️ Status <b> '.$erreurs.' Erreur ! </b></div>';
			echo "<br><br>";
			foreach ($errvil as $key => $value) {

				if ($errcom[$key]== '') {
					echo "L'import pour <b>".$value."</b> a rencontrer un problème lors de la dèrnière exécution !! ";
				}else {
					echo "<b>".$value."</b> : ".$errcom[$key];
				}
				echo "<br><br>";
			}
		}
	}elseif ($_GET['type'] == "json") {
		// code... faire array liste et encode
		header("Content-Type: application/json");
		echo json_encode($jsonerr);
	}
//UPDATE process SET actif= 1 WHERE ville= 'Givors'
	require_once '../disconnect.inc.php';

?>
