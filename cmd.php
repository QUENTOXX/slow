<?php

require_once "config/params.php";
require_once "connect.inc.php";
require_once "fonctions.php";

$words = array("actif", "unactif");

if (isset($argv[1]) && isset($argv[2]) && isset($argv[3])) {
  if (!array_key_exists($argv[1], $cert_all)) {
    exit("L'argument n'est pas un établissement ou mal appelé !!");
  }
  if (!in_array($argv[3], $words)) {
    exit("Commandes possible :
    cmd.php 'ville' process unactif
    cmd.php 'ville' 'user' actif
    cmd.php 'ville' 'user' unactif");
  }else {
    if ($argv[3] == "actif") {
      $actif= 1;
    }else {
      $actif= 0;
    }
  }

  $ville= $argv[1];

  if ($argv[2] == "process") {

    if (exe("UPDATE process SET actif= '$actif' WHERE ville= '$ville'")) {
      echo "Le processus pour $ville est pret à être relancé !";
    }

  }else {

    $mod_user= $argv[2];
    $pref= $pref_tab_all[$ville];

    $sql= "SELECT * FROM ".$pref."user";
    $req=mysqli_query($link, $sql);

    foreach ($req as $user) {
      if ($mod_user == $user['mels_notif']) {
        if (exe("UPDATE ".$pref."user SET actif= '$actif' WHERE mels_notif= '$mod_user'")) {
          echo "L'activité de l'utilisateur $mod_user a été mis à : $actif !!";
          $tmp= 0;
          break;
        }
      }else {
        $tmp= 1;
      }
      if ($tmp == 1) {
        echo "L'utilisateur $mod_user n'éxiste pas à $vile ou est mal écrit.";
      }
    }
  }
}else {
  echo("Commandes possible :
  cmd.php 'ville' process unactif
  cmd.php 'ville' 'user' actif
  cmd.php 'ville' 'user' unactif");
}

    require_once "disconnect.inc.php";
 ?>
