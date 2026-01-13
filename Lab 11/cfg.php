<?php
    // Dane logowania do panelu administracyjnego
    $login = "admin";
    $pass  = "!QAZ2wsx#EDC4rfv";

    // Parametry logowania do bazy danych
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza   = 'moja_strona';

    // Nawiązywanie połączenia z bazą danych
    $link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
    if (!$link) {
        echo '<b>Przerwane połączenie z bazą</b>';
        exit;
    }

    // Wyłączenie niektórych komunikatów o błędach
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // Start sesji dla panelu administracyjnego
    session_start();
?>
