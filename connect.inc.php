<?php

/* Connexion à la base mysql */
$link = mysqli_connect($host,$mysql_ident,$mysql_mdp) or die("Impossible de se connecter");
mysqli_select_db($link,$base) or die("Pas de base");

?>