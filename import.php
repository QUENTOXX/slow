<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
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

include 'ctrl_cert.inc.php';
if ($error>0) {
	echo '<div class="info info-rouge"><br>⚠️ Merci de corriger les erreurs avant de lancer les récupérations<br><br></div>';
	exit;
}

#if (!isset($_SERVER['HTTPS'])) {
#	echo '<div class="info info-rouge"><br>⛔ Il semblerait que la page ne soit pas HTTPS<br><br></div>';
#	exit;
#}



require_once "connect.inc.php";
require_once "fonctions.php";

set_time_limit(3600);


if (!isset($_GET['insee']))
	$insee='all';
else
	$insee=$_GET['insee'];

  // !! regarder a faire !!!
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
$new_nature=array("Contrats, conventions et avenants","Deliberations","Actes individuels","Actes reglementaires","Documents budgetaires et financiers");
// !!!!!!   enlever les accens pour sql    !!!!!!!!!!!
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

if ($insee == "all") { // a modif pour toute villes

	foreach ($pref_tab_all as $ville => $pref) {

		$sql="SELECT * FROM ".$pref."user WHERE actif=1 $w";
		$res=mysqli_query($link,$sql);
		//echo $sql;
		if (mysqli_num_rows($res)==0) {
		// S'il n'y a aucun utilisateur,
		// on considère que le certification est pour un usage individuel.
		// Il est conseiller de créer au moins 1 utilisateur
		// et de spécifier un mot de passe dans S2low.
			echo '<div class="info info-rouge">Vous n\'avez pas défini d\'utilisateur dans la table'.$pref.'user</div>';
			//load(''); // Lance la récupération des actes pour l'utilisateur unique => déconseiller
		} else {
			while ($row=mysqli_fetch_object($res)) {
				echo "<br> $insee";
				echo '<h2>Recherche des actes pour '.$row->insee.'...</h2>';
				$insee=$row->insee;
				$user_delib=$row;
				load($insee, $pref); // Lance la récupération des actes
			}
		}
	}

}else {

	while ($cherinsee = current($insee_all)) {
		if ($cherinsee == $insee) {

			$sql="SELECT * FROM ".$pref_tab_all[key($insee_all)]."user WHERE actif=1 $w";
			$res=mysqli_query($link,$sql);
			//echo $sql;
			if (mysqli_num_rows($res)==0) {
			// S'il n'y a aucun utilisateur,
			// on considère que le certification est pour un usage individuel.
			// Il est conseiller de créer au moins 1 utilisateur
			// et de spécifier un mot de passe dans S2low.
				echo '<div class="info info-rouge">Vous n\'avez pas défini d\'utilisateur dans la table'.$pref_tab_all[key($insee_all)].'user</div>';
				//load(''); // Lance la récupération des actes pour l'utilisateur unique => déconseiller
			} else {
				while ($row=mysqli_fetch_object($res)) {
					echo '<h2>Recherche des actes pour '.$row->insee.'...</h2>';
					$insee=$row->insee;
					$user_delib=$row;
					load($insee,$pref_tab_all[key($insee_all)]); // Lance la récupération des actes
				}
			}
			break;
		}
		next($insee_all);
	}
}



/**************************/
/* Récupération des actes */
/**************************/
function load($insee, $pref_tab) {

	$nb_load=0;

	// Création du répertoire de la commune
	@mkdir("actes/".$insee."/");

	// Récupération des actes déjà récupérés
	$nvu="actes/".$insee."/vu.txt";
	if (!file_exists($nvu)) file_put_contents($nvu,'');
	$vu=explode("\n",file_get_contents($nvu));


	if (!isset($_GET['offset'])) $_GET['offset']=0;
	$limit=100; // Nombres d'actes à lister

	// Début du message à envoyer, généralement en interne, s'il y a des nouveaux Actes
	$mel_obj=" Nouveaux Actes";
	//$mel_mess="Bonjour,<br><br>De nouveaux actes ont été passés au contrôle de légalité :<br> ";
	$mel_mess="Bonjour,\n\nDe nouveaux actes ont été passés au contrôle de légalité :\n ";
	$mel_delib_ind='';
	$mel_delib='';

	echo '<hr>';
	// Liste des actes de la commune
	$json=go_curl($insee, URL."modules/actes/api/list_actes.php?status_id=4&nature=2&offset=".$_GET['offset']."&limit=".$limit); // OK
	if ($json!='') {
		echo "json: $json";
		$json=json_decode($json);
		foreach($json->transactions as $k=>$t) {
			echo "<hr>$t->nature_descr ";
			echo $t->number;
			//print_r($t);
			if ($t->type==1) { // Acte non supprimé
				if (!in_array($t->id,$vu)) { // Vérifie si l'acte est déjà récupéré à partir du fichier vu.txt
					$nb_load++;
					// Récupération des PJ
					$list_doc=go_curl($insee, URL."modules/actes/actes_transac_get_files_list.php?transaction=".$t->id);
					if ($list_doc!='') {
						$list_fich="";
						$list=json_decode($list_doc);
						//print_r($list_doc);
						foreach($list as $k=>$d) {
							echo "<br>Fichier : ".$d->name;
							if (substr($d->name,-4) == '.pdf') {
								$nomf=substr($d->name,0,-4)."__".uniqid().".pdf";
								$list_fich.=$nomf."|";
								// Récupération du fichier
								go_curl($insee, URL."modules/actes/actes_download_file.php?tampon=true&file=".$d->id, $nomf);
							}
						}
						$nat=str_replace($GLOBALS['old_nature'], $GLOBALS['new_nature'], $t->nature_descr);
						echo " => $nat";

						// Notification de la récupération de l'acte
						file_put_contents("actes/".$insee."/vu.txt",$t->id."\n",FILE_APPEND | LOCK_EX);
						//date du jour
						$today= date("Y-m-d");
						// Ajout de l'acte dans la table mysql
						exe ("INSERT INTO ".$pref_tab."index_delib VALUES('$insee',$t->id,'$t->date','".utf8_decode($nat)."','$t->number','$t->classification',\"".utf8_decode(str_replace("\n",' ',str_replace('"','\"',($t->subject))))."\",\"".substr($list_fich,0,-1)."\",'$today');");

						// insertion date du jour
						//exe("INSERT INTO ".$pref_tab."index_delib (import_date) VALUES ('$today');");
						//exe("UPDATE ".$pref_tab."index_delib SET `import_date` = '$today' WHERE ".$pref_tab."index_delib.`insee` = $insee;");

						if ($nat=="Actes individuels")
							$mel_delib_ind.="- $t->number ($nat) $t->subject\n";//<br>";
						else
							$mel_delib.="- $t->number ($nat) $t->subject\n";//<br>";
					}
				} else {
					echo " => DEJA VU !!!";
				}
			} else { // Acte supprimé, donc il sera enlevé de la base
				echo "<h3>TYPE = ".$t->type." Num : ".$t->id."</h3>";
				foreach(glob("actes/".$insee."/*-".$t->number."-*.*") as $nf) {
					echo "<br>Supprimer ".$nf;
				}
				exe("DELETE FROM ".$pref_tab."index_delib WHERE insee='$insee' AND num='$t->number';");
			}

		}
	}

	if ($mel_delib!="")
		Envoi_mail_unique($GLOBALS['user_delib']->mels_notif, $mel_obj, $mel_mess.$mel_delib, 'Notification Actes',0,'\u2712\uFE0F');
	//else
	//	Envoi_mail_unique('mel@domaine.fr', 'Rien de neuf', utf8_decode('Rien à voir pour l\'instant'), 'Notification Actes',0,'\u2712\uFE0F');



	//if ($_GET['nb_load']>0)
	if (count($json->transactions)>0) {
		$lien_suite="import.php?insee=".$insee."&offset=".($_GET['offset']+$limit)."&nb_load=$nb_load";
		echo "<br><br><a href='$lien_suite'>Pour récupérer les actes plus anciens cliquer ici</a><br>";
		//echo "<meta http-equiv=\"refresh\" content=\"3;URL=$lien_suite\">";
	}

}


/*******************************/
/* Lance une requête sur S2low */
/*******************************/
function go_curl($user, $api, $nfich='') {

	echo "<br><i class='cl-bleu'>API : $api</i>";

	$data = array('api' => '1'); // Laisser à 1

	$ch = curl_init();

	// Paramétrage des options curl
	curl_setopt($ch, CURLOPT_URL, $api);
	// En cas d'utilisation d'un proxy, on renseigne ici son adresse
	//curl_setopt($ch, CURLOPT_PROXY, 'x.x.x.x:8080');
	//curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	// Dans mon cas l'user et le mot de passe sont identiques.
	// Il s'agit du login et mot de passe saisie dans l'administration de S2low
	if ($user!='')
		curl_setopt($ch, CURLOPT_USERPWD, $user.":".'79BES=p'); // L'identifiant et le mot de passe sont identiques


//	curl_setopt($ch, CURLOPT_HEADER, true);
//	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
//	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Circle)");
//	curl_setopt($ch, CURLOPT_TIMEOUT,60);
//	curl_setopt($ch, CURLOPT_FOLLOWLOCATION,TRUE);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_CAPATH, CA_PATH);
	curl_setopt($ch, CURLOPT_SSLCERT, PEM);
	curl_setopt($ch, CURLOPT_SSLCERTPASSWD, PASSWORD);
	curl_setopt($ch, CURLOPT_SSLKEY,  SSLKEY);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);

  $proxyadd="http://192.168.76.3:3128";
	if (isset($proxyadd)) {
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		echo "proxy set ".$proxyadd;
		if (isset($proxyauth)) {
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
		}
	}

	$curl_return = curl_exec($ch);

	if ($curl_return === false) {
		echo '<div class="info info-rouge">⚠️ Erreur dans le module curl';
		echo '<br>curl_errno = ' . curl_errno($ch) . ' ( ' . curl_error($ch) . ' )</div>';
		$curl_return='';
	} else {
		if ($nfich!='') {
			// Fichiers déposés dans le dossier actes/
			file_put_contents('actes/'.$user.'/'.$nfich,$curl_return);
			$curl_return='';
		}
	}
	curl_close($ch);

	return($curl_return);
}



function Envoi_mail_unique($pers,$obj,$mess,$info,$urgent=0,$ico='') {
// $pers   : Mel de la personne (Ex: moi@yahoo.fr)
// $obj    : Objet
// $mess   : Message
// $ico    : code pour l'emoji ex : '\ud83d\udcc6'

	$obj=str_replace('"',"'",$obj);
	if ($ico!="") {
		$obj='=?utf-8?B?'.base64_encode(json_decode('"'.$ico.'"')." ".$obj).'?=';
	}

	if ($info=="") $info="Information Actes";

	foreach(explode(",",$pers) as $m) {
		if (strlen($m)>5) {
			echo '<br>Envoi du mel à '.$m;
			$env_mel=mail($m, $obj, $mess);
			if (!$env_mel)
				echo '...'.$env_mel['message'];
		}
	}
  return(true);
}


require_once "disconnect.inc.php";

?>
</body>
</html>
