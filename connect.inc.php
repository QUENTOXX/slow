<?php

/* Connexion à la base mysql */
$link = mysqli_connect($host,$mysql_ident,$mysql_mdp) or die("Impossible de se connecter");
//mysqli_set_charset($link,"utf8_general_ci");

mysqli_set_charset($link, "utf8");



mysqli_select_db($link,$base) or die("Pas de base");

?>
