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
    }
    if (psw1 != psw2) {
      alert("Mot de passe différent")
    }
  }

</script>

<?php

require_once "params.php";

require_once "connect.inc.php";
require_once "fonctions.php";

include 'ctrl_cert.inc.php';

  echo "<h2>Connection pour le dépot des actes</h2>";

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

  echo "<h2>Ajout / Modifier utilisateurs</h2>";

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
          <option selected value=Givors>Givors</option>
          <option value=Grigny>Grigny</option>
          <option value=Saint-Chamond>Saint Chamond</option>
          <option value=Venissieux>Venissieux</option>
          <option value=Corbas>Corbas</option>
          <option value=Pierre_Benite>Pierre Benite</option>
          <option value=Rive_de_Gier>Rive de Gier</option>
          <option value=Vaulx_en_Velin>Vaulx en Velin</option>
          <option value=Sitiv>Sitiv</option>
        </select>
        <p>Rentrer son email : <input type="email" name="email"/> </p>
        <p>Rentrer son mot de passe : <input type="password" name="psw1"/> </p>
        <p>Confirmer son mot de passe : <input type="password" name="psw2"/> </p>
        <br>
        <input type="submit" name="subajout" value="Ajouter" onclick="Check_psw(1);"/>
      </form>

      <br><br>
      <form name="ajout" action="" method="post">
      <p>Modifier un utilisateur</p>
      <label>Ville de l'utilisateur : </label>
      <select id="ville" name="ville">
        <option selected value=Givors>Givors</option>
        <option value=Grigny>Grigny</option>
        <option value=Saint-Chamond>Saint Chamond</option>
        <option value=Venissieux>Venissieux</option>
        <option value=Corbas>Corbas</option>
        <option value=Pierre_Benite>Pierre Benite</option>
        <option value=Rive_de_Gier>Rive de Gier</option>
        <option value=Vaulx_en_Velin>Vaulx en Velin</option>
        <option value=Sitiv>Sitiv</option>
      </select>
      <p>Rentrer son email : <input type="email" name="newemail"/> </p>
      <p>Rentrer son nouveau mot de passe : <input type="password" name="newpsw1"/> </p>
      <p>Confirmer son nouveau mot de passe : <input type="password" name="newpsw2"/> </p>
      <br>
      <input type="submit" name="modif" value="Modifier" onclick="Check_psw(2);"/>
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
    }
    if (empty($psw1) || empty($psw2)) {
        echo "les mots de passes doivent etre renseignés !! <br>";
    }else{
      if ($psw1 != $psw2) {
        echo "Mots de passes différents !!";
      }else {
        $pre= $pref_tab_all[$_POST['ville']];
        $siren= $siren_all[$_POST['ville']];
        $insee= $insee_all[$_POST['ville']];
        $hash= md5(crypt($psw1, $salt));

        if (exe("INSERT INTO ".$pre."user VALUES ('$insee', 1, '$email', '', '$hash', '$siren');")) {
          echo "Ajouté !!";
        }
      }
    }
  }

  if (isset($_POST['modif'])) {

    $newemail = isset($_POST['newemail']) ? $_POST['newemail'] : "";
    $newpsw1 = isset($_POST['newpsw1']) ? $_POST['newpsw1'] : "";
    $newpsw2 = isset($_POST['newpsw2']) ? $_POST['newpsw2'] : "";

    if (empty($newemail)) {
        echo "le mail doit etre renseigner !! <br>";
    }else {
      $pref= $pref_tab_all[$_POST['ville']];
      $sql= "SELECT * FROM ".$pref."user";
      $req=mysqli_query($link, $sql);

      foreach ($req as $user) {
        if ($newemail == $user['mels_notif']) {

          if (empty($newpsw1) || empty($newpsw2)) {
              echo "le nouveau mot de passe doit etre renseigner !! <br>";
          }else{
            if ($newpsw1 != $newpsw2) {
              echo "Mots de passes différents !!";
            }else {
              $hash= md5(crypt($newpsw1, $salt));

              if (exe("UPDATE ".$pref."user SET mdp= '$hash' WHERE mels_notif= '$newemail'")) {
                echo "Modifié !!";
              }
            }
          }
        }else {
          echo "Cet utilisateur n'existe pas à ".$_POST['ville'];
        }
      }
    }
  }

	require_once "disconnect.inc.php";

?>