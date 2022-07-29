<?php
$error=0;

if (!file_exists('./key/'.$cert.'client.pem')) {
	echo '<div class="info info-rouge">⚠️ <b>key/'.$cert.'client.pem</b> n\'a pas encore été généré</div>';
	$error++;
}	
	
if (!file_exists('./key/'.$cert.'key.pem')) {
	echo '<div class="info info-rouge">⚠️ <b>key/'.$cert.'key.pem</b> n\'a pas encore été généré</div>';
	$error++;
}
	
if (!file_exists('./key/'.$cert.'ca.pem')) {
	echo '<div class="info info-rouge">⚠️ <b>key/'.$cert.'ca.pem</b> n\'a pas encore été généré</div>';
	$error++;
}

?>