<?php
$login = "Orcamaster";
$pass  = "!QAZ2wsx#EDC4rfv";

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza   = 'moja_strona';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
if (!$link) {
    echo '<b>Przerwane połączenie z bazą</b>';
    exit;
}
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();
?>
