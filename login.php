<!DOCTYPE html>
<html lang="fr" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="referrer" content="origin-when-crossorigin">
<title>Connection</title>
<link rel="stylesheet" type="text/css" href="style/style.css">
<script>

  function Check_psw(form) {
    if (form == 1) {
      var psw1 = document.getElementByName("psw1").value;
      var psw2 = document.getElementByName("psw2").value;
    }else if (form == 2) {
      var psw1 = document.getElementByName("newpsw1").value;
      var psw2 = document.getElementByName("newpsw2").value;
    }else if (form == 3) {
      var psw1 = document.getElementByName("suppsw1").value;
      var psw2 = document.getElementByName("suppsw2").value;
    }
    if (psw1 != psw2) {
      alert("Mot de passe différent")
      return 0;
    }
  }

</script>

<?php
session_start();
if (isset($_POST['deco'])) {
  session_destroy();
}

require_once "config/params.php";

require_once "connect.inc.php";
require_once "fonctions.php";

  echo "<h2>Connection pour le dépot ou suppression des actes</h2>";

?>

<form action="depot_delib.php" method="POST">
    <table>
        <tr>
            <td>
                Mail :
            </td>
            <td>
                <input type="email" id="login" name="login" />
            </td>
        </tr>
        <tr>
            <td>
                Mot de passe :
            </td>
            <td>
                <input type="password" id="mdp" name="mdp" />
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" id="check" name="check" value="Connection" />
            </td>
        </tr>
    </table>
</form>

<?php

  echo "<h2>Ajout / Modifier / Supprimer utilisateurs</h2>";

?>
<form name="admin" action="" method="POST">
    <table>
        <tr>
            <td>
                Mot de passe admin :
            </td>
            <td>
                <input type="password" id="mdpadmin" name="mdpadmin" />
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" id="checkadmin" name="checkadmin" value="Connection" />
            </td>
        </tr>
    </table>
</form>
<br><br>
<?php

  if (isset($_POST['checkadmin'])) {
    if ($_POST['mdpadmin'] == $cle_ctrl) {

      echo "Connecté admin !!";
      ?>
      <br><br>
      <form name="ajout" action="" method="post">
        <br>
        <p>Ajouter un utilisateur</p>
        <label>Ville de l'utilisateur : </label>
        <select id="ville" name="ville">
          <option>Choisir</option>
          <?php
          foreach ($pref_tab_all as $key => $value) {
            $vu= str_replace("_", " ", $key);
            echo "<option value=$key>$vu</option>";
          }
           ?>
        </select>
        <p>Rentrer son email : <input type="email" name="email"/> </p>
        <p>Rentrer son mot de passe : <input type="password" name="psw1"/> </p>
        <p>Confirmer son mot de passe : <input type="password" name="psw2"/> </p>
        <br>
        <input type="submit" name="subajout" value="Ajouter" onclick="Check_psw(1);"/>
      </form>

      <br><br>
      <form name="modif" action="" method="post">
      <p>Modifier un utilisateur</p>
      <label>Ville de l'utilisateur : </label>
      <select id="ville" name="ville">
        <option>Choisir</option>
        <?php
        foreach ($pref_tab_all as $key => $value) {
          $vu= str_replace("_", " ", $key);
          echo "<option value=$key>$vu</option>";
        }
         ?>
      </select>
      <p>Rentrer son email : <input type="email" name="newemail"/> </p>
      <p>Rentrer son nouveau mot de passe : <input type="password" name="newpsw1"/> </p>
      <p>Confirmer son nouveau mot de passe : <input type="password" name="newpsw2"/> </p>
      <br>
      <input type="submit" name="modif" value="Modifier" onclick="Check_psw(2);"/>
    </form>

    <br><br>
    <form name="suppr" action="" method="post">
      <br>
      <p>Supprimer un utilisateur</p>
      <label>Ville de l'utilisateur : </label>
      <select id="ville" name="ville">
        <option>Choisir</option>
        <?php
        foreach ($pref_tab_all as $key => $value) {
          $vu= str_replace("_", " ", $key);
          echo "<option value=$key>$vu</option>";
        }
         ?>
      </select>
      <p>Rentrer son email : <input type="email" name="supemail"/> </p>
      <p>Rentrer son mot de passe : <input type="password" name="suppsw1"/> </p>
      <p>Confirmer son mot de passe : <input type="password" name="suppsw2"/> </p>
      <br>
      <input type="submit" name="suppr" value="Supprimer" onclick="Check_psw(3);"/>
    </form>

    <br><br>
    <form name="forcer" action="" method="post">
      <br>
      <p>Pour forcer l'import d'une ville</p>
      <label>Ville en question : </label>
      <select id="ville" name="ville">
        <option>Choisir</option>
        <?php
        foreach ($pref_tab_all as $key => $value) {
          $vu= str_replace("_", " ", $key);
          echo "<option value=$key>$vu</option>";
        }
         ?>
      </select>
      <br><br>
      <input type="submit" name="forcer" value="Forcer le script"/>
      <br><br>
    </form>

      <?php
    }
  }

  if (isset($_POST['subajout'])) {

    $email = isset($_POST['email']) ? $_POST['email'] : "";
    $psw1 = isset($_POST['psw1']) ? $_POST['psw1'] : "";
    $psw2 = isset($_POST['psw2']) ? $_POST['psw2'] : "";

    if (empty($email)) {
        echo "le mail doit etre renseigner !! <br>";
        unset($_POST['subajout']);
        unset($_POST['checkadmin']);
    }else {
      if (empty($psw1) || empty($psw2)) {
          echo "les mots de passes doivent etre renseignés !! <br>";
          unset($_POST['subajout']);
          unset($_POST['checkadmin']);
      }else{
        if ($psw1 != $psw2) {
          echo "Mots de passes différents !!";
          unset($_POST['subajout']);
          unset($_POST['checkadmin']);
        }else {

          $pre= $pref_tab_all[$_POST['ville']];
          foreach ($pref_tab_all as $key => $value) {
            $sql= "SELECT * FROM ".$value."user";
            $req=mysqli_query($link, $sql);

            foreach ($req as $user) {
              if ($email == $user['mels_notif']) {
                echo "Cet utilisateur existe déja à $key !!";
                unset($_POST['subajout']);
                unset($_POST['checkadmin']);
                exit();
              }
            }
          }

          $siren= $siren_all[$_POST['ville']];
          $insee= $insee_all[$_POST['ville']];
          $hash= md5(crypt($psw1, $salt));

          if (exe("INSERT INTO ".$pre."user VALUES ('$insee', 1, '$email', '', '$hash', '$siren');")) {
            echo "Ajouté !!";
            unset($_POST['subajout']);
            unset($_POST['checkadmin']);
          }
        }
      }
    }
  }

  if (isset($_POST['modif'])) {

    $newemail = isset($_POST['newemail']) ? $_POST['newemail'] : "";
    $newpsw1 = isset($_POST['newpsw1']) ? $_POST['newpsw1'] : "";
    $newpsw2 = isset($_POST['newpsw2']) ? $_POST['newpsw2'] : "";
    $tmp= 0;

    if (empty($newemail)) {
        echo "le mail doit etre renseigner !! <br>";
        unset($_POST['modif']);
        unset($_POST['checkadmin']);
    }else {
      $pref= $pref_tab_all[$_POST['ville']];
      $sql= "SELECT * FROM ".$pref."user";
      $req=mysqli_query($link, $sql);

      foreach ($req as $user) {
        if ($newemail == $user['mels_notif']) {

          if (empty($newpsw1) || empty($newpsw2)) {
              echo "le nouveau mot de passe doit etre renseigner !! <br>";
              unset($_POST['modif']);
              unset($_POST['checkadmin']);
          }else{
            if ($newpsw1 != $newpsw2) {
              echo "Mots de passes différents !!";
              unset($_POST['modif']);
              unset($_POST['checkadmin']);
            }else {
              $hash= md5(crypt($newpsw1, $salt));

              if (exe("UPDATE ".$pref."user SET mdp= '$hash' WHERE mels_notif= '$newemail'")) {
                echo "Modifié !!";
                $tmp= 0;
                unset($_POST['modif']);
                unset($_POST['checkadmin']);
              }
            }
          }
        }else {
          $tmp= 1;
        }
      }
    }
    if ($tmp == 1) {
      echo "Cet utilisateur n'existe pas à ".$_POST['ville'];
      unset($_POST['modif']);
      unset($_POST['checkadmin']);
    }
  }

  if (isset($_POST['suppr'])) {

    $supemail = isset($_POST['supemail']) ? $_POST['supemail'] : "";
    $suppsw1 = isset($_POST['suppsw1']) ? $_POST['suppsw1'] : "";
    $suppsw2 = isset($_POST['suppsw2']) ? $_POST['suppsw2'] : "";
    $tmp= 0;

    if (empty($supemail)) {
        echo "le mail doit etre renseigner !! <br>";
        unset($_POST['suppr']);
        unset($_POST['checkadmin']);
    }else {
      $pref= $pref_tab_all[$_POST['ville']];
      $sql= "SELECT * FROM ".$pref."user";
      $req=mysqli_query($link, $sql);

      foreach ($req as $user) {
        if ($supemail == $user['mels_notif']) {

          if (empty($suppsw1) || empty($suppsw2)) {
              echo "le mot de passe doit etre renseigner !! <br>";
              unset($_POST['suppr']);
              unset($_POST['checkadmin']);
          }else{
            if ($suppsw1 != $suppsw2) {
              echo "Mots de passes différents !!";
              unset($_POST['suppr']);
              unset($_POST['checkadmin']);
            }else {
              if (!hash_equals($user['mdp'], md5(crypt($suppsw1, $salt)))) {
                echo "Mauvais mot de passe !!";
              }else {

                if (exe("DELETE FROM ".$pref."user WHERE mels_notif= '$supemail'")) {
                  echo "Supprimer !!";
                  $tmp= 0;
                  unset($_POST['suppr']);
                  unset($_POST['checkadmin']);
                  break;
                }
              }
            }
          }
        }else {
          $tmp= 1;
        }
      }
    }
    if ($tmp == 1) {
      echo "Cet utilisateur n'existe pas à ".$_POST['ville'];
      unset($_POST['suppr']);
      unset($_POST['checkadmin']);
    }
  }

  if (isset($_POST['forcer'])) {

    $fv= $_POST['ville'];
    $today= date("Y-m-d H:i:s");
    $fs= date('Y-m-d H:i:s', strtotime($today. ' + 10 minutes'));

    if (exe("UPDATE process SET date_next_run= '$fs' WHERE ville= '$fv'")) {
      echo "L'import de $fv s'effectuera dans la prochaine rotation (maximum 30 minutes)";
    }
  }

	require_once "disconnect.inc.php";

?>
