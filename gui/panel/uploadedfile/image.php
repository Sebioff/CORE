<?php
$path = base64_decode(str_rot13($_GET['q']));
$check = $_GET['c'];
$mime = base64_decode(str_rot13($_GET['m']));
if ($check != md5($path)
	|| $path != realpath($path)
	|| !file_exists($path)
)
	return;

header('Content-Type: '.$mime);
header('Content-Length: '.filesize($path));
readfile($path);
?>