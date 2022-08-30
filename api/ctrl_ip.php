
<?php
  function getIp(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  $ip= getIp();

  $cp_ip= explode(".", $ip);

  switch ($cp_ip[0]) {
    case 10:
      if ($cp_ip[1]>= 0 && $cp_ip[1]<= 255) {
        if ($cp_ip[2]>= 0 && $cp_ip[2]<= 255) {
          if ($cp_ip[3]>= 0 && $cp_ip[3]<= 255) {
            $error= 0;
          }else {
            $error= 1;
          }
        }else {
          $error= 1;
        }
      }else {
        $error= 1;
      }
      break;
    case 172:
    if ($cp_ip[1]>= 16 && $cp_ip[1]<= 31) {
      if ($cp_ip[2]>= 0 && $cp_ip[2]<= 255) {
        if ($cp_ip[3]>= 0 && $cp_ip[3]<= 255) {
          $error= 0;
        }else {
          $error= 1;
        }
      }else {
        $error= 1;
      }
    }else {
      $error= 1;
    }
      break;
    case 192:
    if ($cp_ip[1]== 168) {
      if ($cp_ip[2]>= 1 && $cp_ip[2]<= 255) {
        if ($cp_ip[3]>= 0 && $cp_ip[3]<= 255) {
          $error= 0;
        }else {
          $error= 1;
        }
      }else {
        $error= 1;
      }
    }else {
      $error= 1;
    }
      break;
    default:
      $error= 1;
      break;
  }



?>
