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

?>
// apres connection rajouter id en value de name insee

  <form name="depot" action="depot.php" method="post">

    <p>Rentrer un ID : <input type="number" name="id" placeholder="EX : 789456"/> </p>
    <p>Rentrer la date de décision : <input type="date" name="date_deci" value="<?php echo date('Y-m-d'); ?>" /> </p>
    <label>Séléctionner sa Nature : </label>
    <select id="nature" name="nature" onchange="check_value()">
      <option selected value="PV">PV</option>
      <option value="Deliberations">Délibération</option>
    </select>
    <p>Rentrer un numéro : <input type="text" name="num" placeholder="EX : PV202274Q"/> </p>
    <p>Rentrer un objet : <input type="text" name="obj" placeholder=" objet : obligatoire"/> </p>
    <label>Joindre un ou plusieurs PDF : </label>
    <input type="file" id="pj" name="pj" accept="application/pdf" multiple >
    <br><br>
    <input type="submit" name="submit" value="Déposer"/>
  </form>


<?php

	require_once "disconnect.inc.php";

?>
