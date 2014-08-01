<?php
if (!$rStdin = fopen("php://stdin", "r"))
    die();

echo "Name for modul : ";
$destPath = "phar/";
$sInput = trim(fgets($rStdin));

mkdir($destPath);

$phar = new Phar($destPath.$sInput.'.phar');
$phar->buildFromDirectory("../../module/".$sInput); 

?>
