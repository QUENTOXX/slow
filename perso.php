<?php

function Recup_Fich_Tab($pref) {

   $fichier="Personalisation/".$pref."classification.ini";  //notre fichier de configuration
   $array=array();
   if(file_exists($fichier) && $fichier_lecture=file($fichier)){
      foreach($fichier_lecture as $ligne)
      {
        if(preg_match("#^\[(.*)\]\s+$#",$ligne,$matches))
        {
           $groupe=$matches[1];
           $array[$groupe]=array();
        }
        elseif($ligne[0]!=';')
        {
           list($item,$valeur)=explode("=",$ligne,2);
           if(!isset($valeur)) // S'il n'y a pas de valeur
                $valeur=''; // On donne une chaîne vide pour normalement eviter les erreurs
           $array[$groupe][$item]=$valeur;
        }
      }
    }else{
      echo "Le fichier de personalisation est introuvable ou incompatible<br />";
      return 0;
   }
   // $array contient le fichier .ini sous la forme d'un tableau à 2 niveaux. on verra si c'est pas mieux 1

   return $array;
}

function image($lien){

  if ((exif_imagetype($lien) == IMAGETYPE_JPEG) && (imagecreatefromjpeg($lien) !== false ))
  {
    list($larg, $haut)= getimagesize("$lien");
    if ($larg > 800 || $haut > 150) {
      echo "Vérifiez bien la taille votre image";
    }else {
        echo '<p align="center"><img src="'.$lien.'"></p>';
    }

}elseif ((exif_imagetype($lien) == IMAGETYPE_PNG) && (imagecreatefrompng($lien) !== false ))
{
  list($larg, $haut)= getimagesize("$lien");
  if ($larg > 800 || $haut > 150) {
    echo "Vérifiez bien la taille votre image";
  }else {
      echo '<p align="center"><img src="'.$lien.'"></p>';
  }
}
}

 ?>
