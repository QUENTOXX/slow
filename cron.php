<?php

  require_once "params.php";
  require_once "connect.inc.php";
  require_once "fonctions.php";

  $err= 0;

    if (isset($argv[1])) {
      if (!array_key_exists($argv[1], $cert_all)) {
        exit("L'argument n'est pas un établissement ou mal appelé !!");
      }
      $ville= $argv[1];

      $cert= $cert_all[$ville];
      $pref_tab= $pref_tab_all[$ville];
      $mdp_acc= $mdp_all[$ville];

      $today= date("Y-m-d H:i:s");
      $next= date('Y-m-d 23:00:00', strtotime($today. ' + 1 days'));

      exe("UPDATE process SET actif= 1 WHERE ville= '$ville'");
      exe("UPDATE process SET date_run= '$today' WHERE ville= '$ville'");
      exe("UPDATE process SET date_next_run= '$next' WHERE ville= '$ville'");

      $err= include_once 'import.php';

      if ($err == 1) {
        exe("UPDATE process SET actif= 0 WHERE ville= '$ville'");
        exe("UPDATE process SET date_next_run= '$next' WHERE ville= '$ville'");
        exe("UPDATE process SET status= 0 WHERE ville= '$ville'");
      }else {
        exe("UPDATE process SET status= 1 WHERE ville= '$ville'");
      }

    }else {

      $today= date("Y-m-d H:i:s");
      $avt= date('Y-m-d H:i:s', strtotime($today. ' - 30 minutes'));
      $aps= date('Y-m-d H:i:s', strtotime($today. ' + 30 minutes'));

      $sql = "SELECT * FROM process WHERE date_next_run BETWEEN '$avt' AND '$aps'";
      $req=mysqli_query($link, $sql);

      foreach ($req as $user) {

        $ville= $user['ville'];

        if ($user['actif'] == 1) {
          exe("UPDATE process SET com= 'problème pour exécuter le script car mal terminé précédement ' WHERE ville= '$ville'");
          exe("UPDATE process SET status= 1 WHERE ville= '$ville'");
          echo "Processus déja en cours !";
          break;
        }

        $cert= $cert_all[$ville];
        $pref_tab= $pref_tab_all[$ville];
        $mdp_acc= $mdp_all[$ville];

        $today= date("Y-m-d H:i:s");
        $next= date('Y-m-d 23:00:00', strtotime($today. ' + 1 days'));

        exe("UPDATE process SET actif= 1 WHERE ville= '$ville'");
        exe("UPDATE process SET date_run= '$today' WHERE ville= '$ville'");
        exe("UPDATE process SET date_next_run= '$next' WHERE ville= '$ville'");

        $err= include 'import.php';

        if ($err == 1) {
          exe("UPDATE process SET actif= 0 WHERE ville= '$ville'");
          exe("UPDATE process SET date_next_run= '$next' WHERE ville= '$ville'");
          exe("UPDATE process SET status= 0 WHERE ville= '$ville'");
          exe("UPDATE process SET com= '' WHERE ville= '$ville'");
        }
      }
    }


    require_once "disconnect.inc.php";

 ?>
