<?php
$path = base64_decode(str_rot13($_GET['q']));
$check = $_GET['c'];
$mime = base64_decode(str_rot13($_GET['m']));
if ($check != md5($path))
	return;

if (file_exists($path)) {
	header('Content-Type: '.$mime);
	header('Content-Length: '.filesize($path));
	readfile($path);
}
?>