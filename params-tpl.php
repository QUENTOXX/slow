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

	$insee_all = array('Givors' => "WDGIVORS", 'Sitiv' => "WEBDELIB_AUTOMATE_SI", 'Saint-Chamond' => "sc_", 'Venissieux' => "ve_", 'Corbas' => "co_", 'Grigny' => "gr_", 'Pierre_Benite' => "pi_", 'Rive_de_Gier' => "ri_", 'Vaulx_en_Velin' => "va_");

	// Clé d'accès à l'ensemble des Actes
	$cle_ctrl='mdpsite';

?>
