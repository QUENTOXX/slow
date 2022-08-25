<?php
//error_reporting (0);

require_once "config/params.php";

/* Lance une requête mysql et log les erreurs dans un fichier SQL.rrr */
function exe($rq) {

  if (!mysqli_query($GLOBALS['link'], $rq)) {
		// Log de l'erreur
		file_put_contents("SQL.rrr","\n".date("Y-m-d H:i:s")." ".mysqli_error($GLOBALS['link'])." [".$_SERVER['PHP_SELF']."]\n".$rq."\n",FILE_APPEND);
		echo "Erreur de requete";
		return(0);
	} else {
		return(1);
	}
}


/* EXTRACTION DE DONNEES UNIQUE ASSOC */
function Rech($table,$cond,$champ) {
  $sql0 = "SELECT ".$champ." FROM ".$table;
  if ($cond != null) {
    $sql0.=" WHERE ".$cond;
  }
  $res0 = mysqli_query($GLOBALS['link'], $sql0);
  //echo "<br/>".$sql0;
  return(mysqli_fetch_object($res0));
}

/* EXTRACTION DE DONNEES UNIQUE ARRAY */
function Rech_T($table,$cond,$champ) {
  $sql0 = "SELECT ".$champ." FROM ".$table;
  if ($cond != null) {
    $sql0.=" WHERE ".$cond;
  }
  $res0 = mysqli_query($GLOBALS['link'], $sql0);
  //echo $sql0;
  return( mysqli_fetch_array($res0));
}

/* Suppression des accents */
function stripAccents($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ', 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

/* Test l'existence d'une table */
function tab_exist($tab) {
	$res0=mysqli_query($GLOBALS['link'],"SELECT count(*) as istab FROM information_schema.TABLES WHERE TABLE_NAME = '$tab' AND TABLE_SCHEMA in (SELECT DATABASE())");
  	return(mysqli_fetch_object($res0)->istab);
}


/**************************/
/* Récupération des actes */
/**************************/
function load($insee) {

	global $pref_tab;

	$nb_load=0;

	// Création du répertoire de la commune
	$path= 'actes/'.$insee;
	if (!is_dir($path)) {
			mkdir($path, 0777);
	}

	// Récupération des actes déjà récupérés
	$nvu="actes/".$insee."/vu.txt";
	if (!file_exists($nvu)) file_put_contents($nvu,'');
	$vu=explode("\n",file_get_contents($nvu));
/*
	//récupération de l'argument si les actes n'ont pas tous été récupérés.
	if (isset($argv[2])) {
			$_GET['offset']= $argv[2];
	}
*/
	global $inc;
	$_GET['offset']= $inc;
	echo $_GET['offset'];
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
						echo "INSERT INTO ".$pref_tab."index_delib VALUES('$insee',$t->id,'$t->date','$nat','$t->number','$t->classification',\"".str_replace("\n",' ',str_replace('"','\"',($t->subject)))."\",\"".substr($list_fich,0,-1)."\",'$today');";
						// Ajout de l'acte dans la table mysql
						exe ("INSERT INTO ".$pref_tab."index_delib VALUES('$insee',$t->id,'$t->date','$nat','$t->number','$t->classification',\"".str_replace("\n",' ',str_replace('"','\"',($t->subject)))."\",\"".substr($list_fich,0,-1)."\",'$today');");

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
		//header("Refresh:0; url=$lien_suite");
		echo "<br>Importation non terminé, récupération de plus d'actes ! <br>";
		//exec("sh /etc/scripts/tschang.sh $nom $pass");
		return 0;
	}else {
		echo "<br>Importation terminé ! <br>";
		return 1;
	}

}


/*******************************/
/* Lance une requête sur S2low */
/*******************************/
function go_curl($user, $api, $nfich='') {

	global $mdp_acc;

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
	echo "<br> user : $user";
	if ($user!='')
		curl_setopt($ch, CURLOPT_USERPWD, $user.":".$mdp_acc); // L'identifiant et le mot de passe sont identiques


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

	//$proxyadd="http://192.168.76.3:3128";
	global $proxyadd;

	if (isset($proxyadd)) {
		curl_setopt($ch, CURLOPT_PROXY, $proxyadd);
		//echo "proxy set ".$proxyadd;
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
/*
	$info= curl_getinfo($ch);
	echo '<br>La requête a mis ' . $info['total_time'] . ' secondes à être envoyée à ' . $info['url'];
	$info= curl_getinfo($ch, CURLINFO_HTTP_CODE);
	echo "<br> code de retour : $info ";
	$info= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	echo "<br> last url : $info ";*/

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

?>
