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

	$siren= $_POST['siren'];
  //$insee= "$insee_par_defaut"; //$_POST['insee'];
	$id=0;
  $date_dec = date('Y-m-d', strtotime($_POST['date_deci']));
  $nat= $_POST['nature'];
  $num= $_POST['num'];
  $code= 0;
  $obj= $_POST['obj'];
	$listpj="";
  $today= date("Y-m-d");

	foreach ($siren_all as $villes => $values) {
		if ($values == $siren) {
			$insee= $insee_all[$villes];
		}
	}

  if ($obj == "") {
		echo "Veuillez renseigner l'objet !";
		?>
		<form method="post" action="depot_delib.php">
		<input type="submit" name="retour" value="OK" />
		</form>
		<?php
		//header("Location:".$_SERVER[HTTP_REFERER]);

    die;
  }

  foreach($insee_all as $ville => $y){
    if ($y == $insee) {
      $pref= $pref_tab_all[$ville];
    }
  }

	//avant check si c'est bien un pdf avec mim

	$path= 'actes/'.$insee;
	if (is_dir($path)) {
		foreach ($_FILES["pj"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
					if ($_FILES["pj"]["type"][$key] == "application/pdf") {
						$tmp_name = $_FILES["pj"]["tmp_name"][$key];
						$name = basename($_FILES["pj"]["name"][$key]);
						$listpj.=$name."|";
						move_uploaded_file($tmp_name, "$path/$name");
					}else {
						echo "Fichier \"".$_FILES["pj"]["name"][$key]."\" non upload car ce n'est pas un PDF<br/>";
					}
	    }
	}
	}else {
		mkdir($path, 0777);
		foreach ($_FILES["pj"]["error"] as $key => $error) {
	    if ($error == UPLOAD_ERR_OK) {
					if ($_FILES["pj"]["type"][$key] == "application/pdf") {
						$tmp_name = $_FILES["pj"]["tmp_name"][$key];
						$name = basename($_FILES["pj"]["name"][$key]);
						$listpj.=$name."|";
						move_uploaded_file($tmp_name, "$path/$name");
					}else {
						echo "Fichier \"".$_FILES["pj"]["name"][$key]."\" non upload car ce n'est pas un PDF<br/>";
					}
	    }
	}
	}

//\"".substr($pj,0,0)."\" pour pj
  if (  exe ("INSERT INTO ".$pref."index_delib VALUES('$insee',$id,'$date_dec','$nat','$num','$code','$obj',\"".substr($listpj,0,-1)."\",'$today');")) {
    echo "<br/>Acte bien déposé";
		echo "<br/>Si vous souhaitez déposé un autre acte cliquez ci-dessous";
		?>
		<form method="post" action="depot_delib.php">
		<input type="submit" name="retour" value="Déposer un autre acte" />
		</form>
		<?php

  }else {
    echo "<br/> Numéro déja existant !!";
		?>
		<form method="post" action="depot_delib.php">
		<input type="submit" name="retour" value="Déposer un acte" />
		</form>
		<?php
  }


	require_once "disconnect.inc.php";

?>
