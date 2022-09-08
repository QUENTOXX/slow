<?php


	/* Paramétres de connexion à la base de données Mysql */
	$host='localhost';
	$mysql_ident='root';
	$mysql_mdp='';
	$base='publis2low';


	// Change le dossier courant utile lors du lancement des tâches cron
	//chdir(dirname(__FILE__));


	// Préfixe des tables mysql

	//avec ccas
  //$pref_tab_all = array('Givors' => "gi_", 'CCASGivors' => "ccasgi_", 'Sitiv' => "rs_", 'Saint-Chamond' => "sc_", 'CCASSaint-Chamond' => "ccassc_", 'Venissieux' => "ve_", 'CCASVenissieux' => "ccasve_", 'Corbas' => "co_", 'Grigny' => "gr_", 'Pierre_Benite' => "pi_", 'CCASPierre_Benite' => "ccaspi_", 'Rive_de_Gier' => "ri_", 'CCASRive_de_Gier' => "ccasri_", 'Vaulx_en_Velin' => "va_", 'CCASVaulx_en_Velin' => "ccasva_");

	//sans ccas
	$pref_tab_all = array('Givors' => "gi_", 'Sitiv' => "rs_", 'Saint-Chamond' => "sc_", 'Venissieux' => "ve_", 'Corbas' => "co_", 'Grigny' => "gr_", 'Pierre_Benite' => "pb_", 'Rive_de_Gier' => "ri_", 'Vaulx_en_Velin' => "va_");


	// Préfixe du certificat Exemple : $cert="test_", les fichiers seront test_client.pem, test_key.pem et test_ca.pem
	$cert_all = $pref_tab_all;

	// Mot de passe donné lors de la création des fichiers pem
	$pass="";


	// Url vers la plateforme s2low que vous voulez atteindre
	define('URL', 'https://www.s2low.org/');

	// Insee de la commune ou EPCI dans le cas où l'insee n'est pas spécifiée ( c'est l'id webdelibre)

	$insee_all = array('Givors' => "WDGIVORS", 'CCASGivors' => "", 'Sitiv' => "WEBDELIB_AUTOMATE_SI", 'Saint-Chamond' => "WEBDELIB_AUTOMATE_STC", 'CCASSaint-Chamond' => "", 'Venissieux' => "WEBDELIB_AUTOMATE_VE", 'CCASVenissieux' => "", 'Corbas' => "WEBDELIB_AUTOMATE_COR", 'Grigny' => "WEBDELIB_AUTOMATE_GR", 'CCASGrigny' => "", 'Pierre_Benite' => "WEBDELIB_AUTOMATE_PB", 'CCASPierre_Benite' => "", 'Rive_de_Gier' => "WEBDELIB_AUTOMATE_RI", 'CCASRive_de_Gier' => "", 'Vaulx_en_Velin' => "", 'CCASVaulx_en_Velin' => "");

	// code SIREN des Villes
	$siren_all = array('Givors' => 216900910, 'CCASGivors' => "", 'Sitiv' => 256910183, 'Saint-Chamond' => 214202079, 'CCASSaint-Chamond' => 264210113, 'Venissieux' => 216902593, 'CCASVenissieux' => 266910173, 'Corbas' => 216902734, 'CCASCorbas' => 266910413, 'Grigny' => 216900969, 'CCASGrigny' => 266910041, 'Pierre_Benite' => 216901520, 'CCASPierre_Benite' => "", 'Rive_de_Gier' => 214201865, 'CCASRive_de_Gier' => "", 'Vaulx_en_Velin' => 216902569, 'CCASVaulx_en_Velin' => 266910256);

	//mdp accès pour recup s2low
	$mdp_all = array('Givors' => "WDGIVORS", 'CCASGivors' => "", 'Sitiv' => "79BES=p", 'Saint-Chamond' => "2qgdt5", 'CCASSaint-Chamond' => "", 'Venissieux' => "fYgz3Mc63U7L", 'CCASVenissieux' => "", 'Corbas' => "pn3xz65", 'CCASCorbas' => 266910413, 'Grigny' => "DeG69rYh5", 'CCASGrigny' => "", 'Pierre_Benite' => "pierreBenite-69@cteS", 'CCASPierre_Benite' => "", 'Rive_de_Gier' => "97m8iZaB", 'CCASRive_de_Gier' => "", 'Vaulx_en_Velin' => "", 'CCASVaulx_en_Velin' => "");

	// Clé d'accès admin
	$cle_ctrl='';

	//salt pour mdp
	$salt="";

	//param proxy
	$proxyadd="";
	$proxyauth="";

	//chemin pour le dossier /key (certificats)
	$certpath= "";

	//exe du scrpit
	//soit interval soit fixe
	$fixe= "23:00:00";
	//$interval= "24:00:00";

?>
