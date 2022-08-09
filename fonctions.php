<?php
//error_reporting (0);

/* Lance une requête mysql et log les erreurs dans un fichier SQL.rrr */
function exe($rq) {
  echo "<br>";
  var_dump($rq);
  echo "<br>";
  var_dump($GLOBALS['link']);
  echo "<br>";
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

?>
