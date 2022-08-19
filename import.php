<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="refresh" content="DELAI;url=URL" />
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Récupération des actes sur s2low</title>
<link rel="stylesheet" type="text/css" href="style/style.css">
<body>
<?php

/*
// A faire //

// Récupération de la classification via :
$json=go_curl($insee, URL."/modules/actes/actes_classification_fetch.php", "class.json"); // OK

*/

ini_set('display_errors','on');
error_reporting(E_ALL);

require_once "params.php";
/*
//recupération de l'argument
if (isset($argv[1])) {
	if (!array_key_exists($argv[1], $cert_all)) {
		exit("L'argument n'est pas un établissement ou mal appelé !!");
	}
	$ville= $argv[1];
}else {
	exit("Il manque l'argument pour l'établissement à importer !!");
}

$cert= $cert_all[$ville];
$pref_tab= $pref_tab_all[$ville];
$mdp_acc= $mdp_all[$ville];
*/
global $ville;
global $cert;
global $pref_tab;

include 'ctrl_cert.inc.php';
if ($error>0) {
	echo '<div class="info info-rouge"><br>⚠️ Merci de corriger les erreurs avant de lancer les récupérations<br><br></div>';
	exit;
}
/*
if (!isset($_SERVER['HTTPS'])) {
	echo '<div class="info info-rouge"><br>⛔ Il semblerait que la page ne soit pas HTTPS<br><br></div>';
	exit;
}
*/


require_once "connect.inc.php";
require_once "fonctions.php";

set_time_limit(3600);


if (!isset($_GET['insee']))
	$insee='all';
else
	$insee=$_GET['insee'];

// Prise en compte du serveur Windows (merci Antoine)
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	define('PEM',     realpath('key').'\\'.$cert.'client.pem');
	define('SSLKEY',  realpath('key').'\\'.$cert.'key.pem');
	define('CA_PATH', realpath('key').'\\'.$cert.'ca.pem');
} else {
	// la partie x509 du certificat : openssl pkcs12 -in certificat.p12 -out client.pem -clcerts -nokeys
	define('PEM',     './key/'.$cert.'client.pem');
	//  la clé privée du certificat : openssl pkcs12 -in certificat.p12 -out key.pem -nocerts
	define('SSLKEY',  './key/'.$cert.'key.pem');
	//le certificat du CA :           openssl pkcs12 -in certificat.p12 -out ca.pem -cacerts -nokeys
	define('CA_PATH', './key/'.$cert.'ca.pem');
}



// Mot de passe choisi lors de la création openssl
define('PASSWORD', $pass);

// Permet de changer les classifications qui ont évoluées depuis 2011.
$old_nature=array("Contrats et conventions","Deliberations","Arretes individuels","Arretes reglementaires","Documents budgetaires et financiers");
$new_nature=array("Contrats, conventions et avenants","Délibérations","Actes individuels","Actes réglementaires","Documents budgétaires et financiers");

/*
// Liste des utilisateurs dans S2low
$json=go_curl('',URL."admin/users/admin_users.php?api=1&count=1000"); // OK
foreach (json_decode($json) as $u) {
	if ($u->givenname == "Visu") {
		$user=go_curl('',URL."admin/users/admin_user_detail.php?id=".$u->id); // OK
		print_r($user);
	}
}
*/

//$json=go_curl('',URL."admin/authorities/admin_authorities.php?api=1&count=1000"); // OK
//print_r($json);

if (!isset($_GET['nb_load']))
	$_GET['nb_load']=20;

// Filtre par commune
if ($insee=="all")
	$w="";
else
	$w="AND insee='$insee'";

// Liste des utilisateurs (communes) à récupérer
$sql="SELECT * FROM ".$pref_tab."user WHERE actif=1 $w";
$res=mysqli_query($link,$sql);
//echo $sql;
if (mysqli_num_rows($res)==0) {
// S'il n'y a aucun utilisateur,
// on considère que le certification est pour un usage individuel.
// Il est conseiller de créer au moins 1 utilisateur
// et de spécifier un mot de passe dans S2low.
	echo '<div class="info info-rouge">Vous n\'avez pas défini d\'utilisateur dans la table '.$pref_tab.'user</div>';
	//load(''); // Lance la récupération des actes pour l'utilisateur unique => déconseiller
} else {

	$inc= 0;
	while ($err == 0) {

		while ($row=mysqli_fetch_object($res)) {
			echo '<h2>Recherche des actes pour '.$row->insee.'...</h2>';
			$insee=$row->insee;
			$user_delib=$row;
			 // Lance la récupération des actes
		}
		$err= load($insee);
		$inc += 100;
	}

}
//effacement des definit pour le rappel suivant

runkit7_constant_remove('PEM');
runkit7_constant_remove('CA_PATH');
runkit7_constant_remove('SSLKEY');
runkit7_constant_remove('PASSWORD');

// déplacement des fonction dans fonction.php


//require_once "disconnect.inc.php";
return $err;

?>
</body>
</html>
