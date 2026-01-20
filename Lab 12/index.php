<?php
    // Załadowanie pliku konfiguracyjnego
    require_once("cfg.php");
    // Załadowanie funkcji do wyświetlania podstron
    require_once("showpage.php"); 
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1.8">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Oskar Piotrowski">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/shop.js"></script>
    <title>Grafika komputerowa</title>
</head>

<body onload="startclock()">
    <div id="header">
        <h1 id="title">Grafika Komputerowa</h1>
    </div>

    <div id="navbar">
        <div class="nav-inner">
            <a class="navbtn" href="index.php">Strona Główna</a>
            <a class="navbtn" href="index.php?idp=rastrowa">Grafika Rastrowa</a>
            <a class="navbtn" href="index.php?idp=wektorowa">Grafika Wektorowa</a>
            <a class="navbtn" href="index.php?idp=grafika3d">Grafika 3D</a>
            <a class="navbtn" href="index.php?idp=ruchoma">Grafika Ruchoma</a>
            <a class="navbtn" href="index.php?idp=kolorujtlo">Skrypt</a>
            <a class="navbtn" href="index.php?idp=filmy">Filmy</a>
            <a class="navbtn" href="index.php?idp=kontakt">Kontakt</a>
            <a class="navbtn" href="index.php?idp=sklep">Sklep</a>
        </div>
    </div>

    <?php
        // Ustalanie, jaka podstrona została wybrana
        $page = filter_input(INPUT_GET, 'idp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$page) $page = 'glowna';

        // Pobranie treści podstrony
        $content = (string) PokazPodstrone($page);

        // Obsługa strony kontaktowej
        if ($page == 'kontakt') {
            require_once("contact.php");

            // Wywołanie funkcji mailowej po wysłaniu formularza
            if (isset($_POST['wyslij_kontakt'])) {
                wyslijMailakontakt("moja_strona@domena.pl");
            }

            // Wstawienie formularza kontaktowego do treści podstrony
            $content = str_replace("[formularz_kontaktowy]", PokazKontakt(), $content);
        }

        // Obsługa przypomnienia hasła
        if ($page == 'przypomnijhaslo') {
            require_once("contact.php");

            $content = str_replace("[formularz_przypomnij]", PrzypomnijHaslo(), $content);
        }

        // Wyświetlenie treści
        echo $content;
    ?>

    <div id="footer">
        <?php
            $nr_indeksu = '175336';
            $nrGrupy = '2';
            echo 'Oskar Piotrowski ('.$nr_indeksu.'), grupa '.$nrGrupy.' <br />';
        ?>
        <div id="zegarek"></div>
        <div id="data"></div>
        <div> <a class="navbtn" href="admin/admin.php">CMS</a> </div>
        
    </div>
</body>
</html>
