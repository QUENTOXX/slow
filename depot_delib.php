<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Dépot des Actes</title>
<link rel="stylesheet" type="text/css" href="style/style.css">
<?php

	require_once "params.php";

	require_once "connect.inc.php";
	require_once "fonctions.php";

  echo "<h2>Dépot des actes</h2>";

	//$user = array('nicolas@gmail.com' => 12345, 'idiotduvillage' => "4567",'ptdrtki' => "7894");

	if (isset($_POST['check'])) {

			$login = isset($_POST['login']) ? $_POST['login'] : "";
			$mdp = isset($_POST['mdp']) ? $_POST['mdp'] : "";

			$erreurs ="";

			if (empty($login)) {
					$erreurs .= "le mail doit etre renseigner !! <br>";
			}
			if (empty($mdp)) {
					$erreurs .= "le mot de passe doit etre renseigner !! <br>";
			}else{
					foreach ($pref_tab_all as $ville => $pref) {
						$sql = "SELECT * FROM ".$pref."user";
						$req=mysqli_query($link, $sql);

						foreach ($req as $user) {

							if ($login != $user['mels_notif'] || !hash_equals($user['mdp'], md5(crypt($mdp, $salt)))) {

									$erreurs ="";
									$erreurs .= "pseudo ou mot de passe incorect !! <br>";
							}else {
									echo "string";
									$siren= $user['siren'];
									$erreurs= "";
									break 2;
							}
						}
			}

			}
	}else {
		header("Location: login.php");
	}

?>
<?php

	if (empty($erreurs)) {

	?>
	<div id="okay">
			<?php
					echo("Bienvenue $login");
			?>
	</div>

	<form name="depot" action="depot.php" method="post" enctype="multipart/form-data">

		<?php
    	echo '<input type="hidden" name="siren" value="' . htmlspecialchars($siren) . '" />'."\n";
		?>
		<p>Rentrer la date de décision : <input type="date" name="date_deci" value="<?php echo date('Y-m-d'); ?>" /> </p>
		<label>Séléctionner sa Nature : </label>
		<select id="nature" name="nature">
			<option selected value="PV">PV</option>
			<option value="Déliberations">Délibération</option>
			<option value="Autres">Décisions</option>
			<option value="Actes réglementaires">Arrétés</option>
			<option value="Documents budgétaires et financiers">Documents budgétaires et financiers</option>
		</select>
		<p>Rentrer un numéro : <input type="text" name="num" placeholder="EX : PV202274Q"/> Le numéro est obligatoire et doit être non existant </p>
		<p>Rentrer une description : <input type="text" name="obj" placeholder=" Objet : obligatoire"/> </p>
		<label>Joindre un ou plusieurs PDF : </label>
		<input type="file" id="pj" name="pj[]" accept="application/pdf" multiple >
		<br><br>
		<input type="submit" name="depot" value="Déposer"/>
	</form>
	<br><br>
	<h2>Supprimer un acte</h2>
	<br>
	<form name="del_delib" action="depot.php" method="post" enctype="multipart/form-data">
		<?php
			echo '<input type="hidden" name="siren" value="' . htmlspecialchars($siren) . '" />'."\n";
		?>
		<p>Rentrer le numéro : <input type="text" name="del_num" placeholder="EX : PV202274Q"/></p>
		<br>
		<input type="submit" name="del_delib" value="Supprimer"/>
	</form>

	<?php
	}

	?>

	<?php

	if (!empty($erreurs)) {

	?>
	<div id="erreurs">
			<?= $erreurs; ?>
	</div>
	<p> Retour à la connection </p>
	<form method="post" action="depot_delib.php">
	<input type="submit" name="retour" value="OK" />
	</form>

	<?php
	}

?>


<?php

	require_once "disconnect.inc.php";

?>
