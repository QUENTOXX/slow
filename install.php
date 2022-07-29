<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Registres des Actes - Installation</title>
<link rel="stylesheet" type="text/css" href="style/style.css">
<img src="img/PubliS2low.svg" />
<?php

	require_once "params.php";

	require_once "connect.inc.php";
	require_once "fonctions.php";

	@mkdir ('actes');
	@mkdir ('key');

	foreach ($pref_tab_all as $ville => $pref) {

		if (!tab_exist($pref.'index_delib')) {
			// Création de la table contenant la liste des actes
			exe("CREATE TABLE ".$pref."index_delib (
				insee varchar(100) COLLATE utf8_bin NOT NULL,
				id int(11) NOT NULL,
				del_date date NOT NULL,
				nature varchar(50) COLLATE utf8_bin NOT NULL,
				num varchar(20) COLLATE utf8_bin NOT NULL,
				code char(20) COLLATE utf8_bin NOT NULL,
				obj text COLLATE utf8_bin NOT NULL,
				pj mediumtext COLLATE utf8_bin NOT NULL,
				import_date date,
				UNIQUE KEY insee_num (insee,num)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

			echo '<br>Création de la table '.$pref.'index_delib';
		} else {
			exe("ALTER TABLE ".$pref."index_delib ADD import_date date");
			echo '<div class="info info-vert">✔️ La table <b>'.$pref.'index_delib</b> existe déjà</div>';
		}
	}


	if (!tab_exist('class')) {
		// Création de la table de la classification
		exe("CREATE TABLE IF NOT EXISTS class (
			class char(6) NOT NULL,
			nclass varchar(100) NOT NULL,
			UNIQUE KEY class (class)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		echo '<br>Création de la table class';

		// Classification
		exe(utf8_decode("INSERT INTO class (class, nclass) VALUES
		('1.0',	'Commande Publique'),
		('1.1',	'Marchés publics'),
		('1.2',	'Délégation de service public'),
		('1.3',	'Conventions de Mandat'),
		('1.4',	'Autres types de contrats'),
		('1.5',	'Transactions /protocole d accord transactionnel'),
		('1.6',	'Actes relatifs à la maîtrise d\'oeuvre'),
		('1.7',	'Actes speciaux et divers'),
		('2.0',	'Urbanisme'),
		('2.1',	'Documents d urbanisme'),
		('2.2',	'Actes relatifs au droit d occupation ou d utilisation des sols'),
		('2.3',	'Droit de preemption urbain'),
		('3.0',	'Domaine et patrimoine'),
		('3.1',	'Acquisitions'),
		('3.2',	'Alienations'),
		('3.3',	'Locations'),
		('3.4',	'Limites territoriales'),
		('3.5',	'Autres actes de gestion du domaine public'),
		('3.6',	'Autres actes de gestion du domaine prive'),
		('4.0',	'Fonction publique'),
		('4.1',	'Personnel titulaires et stagiaires de la F.P.T.'),
		('4.2',	'Personnel contractuel'),
		('4.3',	'Fonction publique hospitaliere'),
		('4.4',	'Autres categories de personnels'),
		('4.5',	'Regime indemnitaire'),
		('5.0',	'Institutions et vie politique'),
		('5.1',	'Election executif'),
		('5.2',	'Fonctionnement des assemblees'),
		('5.3',	'Designation de representants'),
		('5.4',	'Delegation de fonctions'),
		('5.5',	'Delegation de signature'),
		('5.6',	'Exercice des mandats locaux'),
		('5.7',	'Intercommunalite'),
		('5.8',	'Decision d ester en justice'),
		('6.0',	'Libertés publiques et pourvoirs de police'),
		('6.1',	'Police municipale'),
		('6.2',	'Pouvoir du president du conseil general'),
		('6.3',	'Pouvoir du president du conseil regional'),
		('6.4',	'Autres actes reglementaires'),
		('6.5',	'Actes pris au nom de l Etat et soumis au controle hierarchique'),
		('7.0',	'Finances locales'),
		('7.1',	'Decisions budgetaires'),
		('7.10','Divers'),
		('7.2',	'Fiscalité'),
		('7.3',	'Emprunts'),
		('7.4',	'Interventions economiques'),
		('7.5',	'Subventions'),
		('7.6',	'Contributions budgetaires'),
		('7.7',	'Avances'),
		('7.8',	'Fonds de concours'),
		('7.9',	'Prise de participation (SEM, etc...)'),
		('8.0',	'Domaines de competences par themes'),
		('8.1',	'Enseignement'),
		('8.2',	'Aide sociale'),
		('8.3',	'Voirie'),
		('8.4',	'Amenagement du territoire'),
		('8.5',	'Politique de la ville-habitat-logement'),
		('8.6',	'Emploi-formation professionnelle'),
		('8.7',	'Transports'),
		('8.8',	'Environnement'),
		('8.9',	'Culture'),
		('9.0',	'Autres domaines de competences'),
		('9.1',	'Autres domaines de competences des communes'),
		('9.2',	'Autres domaines de competences des departements'),
		('9.3',	'Autres domaines de competences des regions'),
		('9.4',	'Voeux et motions');"));
	} else {
		echo '<div class="info info-vert">✔️ La table <b>class</b> existe déjà</div>';
	}

	foreach ($pref_tab_all as $ville => $pref) {

			if (!tab_exist($pref.'user')) {
				// Création de la table utilisateur
				exe("CREATE TABLE ".$pref."user (
					insee varchar(100) NOT NULL,
					actif int(1) NOT NULL,
					mels_notif text COLLATE utf8_bin NOT NULL,
					mels_notif_conf text COLLATE utf8_bin NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
				echo '<br>Création de la table '.$pref.'user';

					// Exemple de données utilisateurs
					exe("INSERT INTO ".$pref."user (insee, actif, mels_notif, mels_notif_conf) VALUES
					(57999,	1,	'mel1@domaine.fr,mel2@domaine.fr,mel3@domaine.fr',	''),
					(57777,	1,	'mel1@domaine.fr',	'');");
				}	else {
					echo '<div class="info info-vert">✔️ La table <b>'.$pref.'user</b> existe déjà</div>';
				}
	}

	echo '<br>';

	include 'ctrl_cert.inc.php';

	foreach ($pref_tab_all as $ville => $pref) {

		$list_user=Rech($pref.'user', '1', 'GROUP_CONCAT(insee) as list')->list;

		echo 'Liste des utilisateurs de '.$ville.' : '.$list_user;
		echo "<br>";
	}

	// A faire
	echo '<br><hr><br>A faire sur votre hébergement :';
	echo '<li> Ajouter la tâche cron du script <i class="cl-bleu">'.dirname(__FILE__).'/import.php</i> dans votre hébergement</li>';
	echo '<li> Editer la table <b class="cl-bleu">'.$pref_tab.'user</b> pour ajouter des utilisateurs (=communes)</li>';



	if ($error<1) {
		echo '<br><hr>';
		echo '<br><a href="import.php">Lancer le premier import</a>';
		echo '<br><a href="delib_rech.php">Afficher la liste des délibérations</a>';
	} else {
		echo '<div class="info info-rouge">⚠️ Merci de corriger les erreurs avant de lancer les récupérations</div>';
	}

	require_once "disconnect.inc.php";

?>
