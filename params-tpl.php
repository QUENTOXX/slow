<?php


	/* Paramétres de connexion à la base de données Mysql */
	$host='localhost';
	$mysql_ident='root';
	$mysql_mdp='';
	$base='publis2low';


	// Change le dossier courant utile lors du lancement des tâches cron
	chdir(dirname(__FILE__));


	// Préfixe des tables mysql
	//$pref_tab='gi_';

	$pref_tab_all = array('Givors' => "gi_", 'Sitiv' => "rs_", 'Saint-Chamond' => "sc_", 'Venissieux' => "ve_", 'Corbas' => "co_", 'Grigny' => "gr_", 'Pierre_Benite' => "pi_", 'Rive_de_Gier' => "ri_", 'Vaulx_en_Velin' => "va_");

	// Préfixe du certificat Exemple : $cert="test_", les fichiers seront test_client.pem, test_key.pem et test_ca.pem
	$cert="gi_";

	$cert_all = $pref_tab_all;//array('Givors' => "gi_", 'Sitiv' => "rs_", 'Saint-Chamond' => "sc_", 'Venissieux' => "ve_", 'Corbas' => "co_", 'Grigny' => "gr_", 'Pierre_Benite' => "pi_", 'Rive_de_Gier' => "ri_", 'Vaulx_en_Velin' => "va_");

	// Mot de passe donné lors de la création des fichiers pem
	$pass="";


	// Url vers la plateforme s2low que vous voulez atteindre
	define('URL', 'https://www.s2low.org/');

	// Insee de la commune ou EPCI dans le cas où l'insee n'est pas spécifiée
	$insee_par_defaut='WDGIVORS';

	$insee_all = array('Givors' => "WDGIVORS", 'Sitiv' => "WEBDELIB_AUTOMATE_SI", 'Saint-Chamond' => "WEBDELIB_AUTOMATE_STC", 'Venissieux' => "ve_", 'Corbas' => "WEBDELIB_AUTOMATE_COR", 'Grigny' => "WEBDELIB_AUTOMATE_GR", 'Pierre_Benite' => "WEBDELIB_AUTOMATE_PB", 'Rive_de_Gier' => "WEBDELIB_AUTOMATE_RI", 'Vaulx_en_Velin' => "va_");

	// code SIREN des Villes
	$siren_all = array('Givors' => 216900910, 'CCASGivors' => "", 'Sitiv' => 256910183, 'Saint-Chamond' => 214202079, 'CCASSaint-Chamond' => 264210113, 'Venissieux' => 216902593, 'CCASVenissieux' => 266910173, 'Corbas' => 216902734, 'CCASCorbas' => 266910413, 'Grigny' => 216900969, 'CCASGrigny' => 266910041, 'Pierre_Benite' => 216901520, 'CCASPierre_Benite' => 266910108, 'Rive_de_Gier' => 214201865, 'CCASRive_de_Gier' => 264210105, 'Vaulx_en_Velin' => 216902569, 'CCASVaulx_en_Velin' => 266910256);

	// Clé d'accès admin
	$cle_ctrl='';

	//salt pour mdp
	$salt="";

	//param proxy
	$proxy="";
	$proxyauth="";

?>
