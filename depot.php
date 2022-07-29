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

	include 'ctrl_cert.inc.php';

  echo "<h2>Dépot des actes</h2>";

  $insee= $insee_par_defaut; //$_POST['insee'];
  $id= $_POST['id'];
  $date_dec = date('Y-m-d', strtotime($_POST['date_deci']));
  $nat= $_POST['nature'];
  $num= $_POST['num'];
  $code= 0;
  $obj= $_POST['obj'];
  $pj= $_POST['pj'];
  $today= date("Y-m-d");

  if ($obj == "") {
    header("Location:".$_SERVER[HTTP_REFERER]);
    die;
  }

  foreach($insee_all as $ville => $y){
    if ($y == $insee) {
      $pref= $pref_tab_all[$ville];
    }
  }

  if ($id == "") {
    $id= 0;
  }

  if (  exe ("INSERT INTO ".$pref."index_delib VALUES('$insee',$id,'$date_dec','$nat','$num','$code','$obj',\"".substr($pj,0,-1)."\",'$today');")) {
    echo "Bien déposé";
  }else {
    echo "EREUUUUUUUUURE!!";
  }


	require_once "disconnect.inc.php";

?>
