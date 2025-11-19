<?php
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza = 'moja_strona';

    $link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);
    if (!$link) {
        echo '<b>przerwane polaczenie</b>';
        exit;
    }
    // if(!mysqli_select_db($baza)) echo 'nie wybrano bazy';

    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    require_once("showpage.php");

    if($_GET['idp'] == '') $strona = 'html/glowna.html';
    if($_GET['idp'] == 'grafika3d') $strona = 'html/grafika3d.html';
    if($_GET['idp'] == 'rastrowa') $strona = 'html/rastrowa.html';
    if($_GET['idp'] == 'wektorowa') $strona = 'html/wektorowa.html';
    if($_GET['idp'] == 'ruchoma') $strona = 'html/ruchoma.html';
    if($_GET['idp'] == 'kolorujtlo') $strona = 'html/kolorujtlo.html';
    if($_GET['idp'] == 'filmy') $strona = 'html/filmy.html';

    if (!file_exists($strona)) {
        $strona = 'html/glowna.html';
    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1.6">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Oskar Piotrowski">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <title>Grafika komputerowa</title>
</head>

<body onload="startclock()">

    <div id="header">
        <h1 id="title">Grafika Komputerowa</h1>
    </div>

    <div id="navbar">
        <a class="navbtn" href="index.php">Strona Główna</a>
        <a class="navbtn" href="index.php?idp=rastrowa">Grafika Rastrowa</a>
        <a class="navbtn" href="index.php?idp=wektorowa">Grafika Wektorowa</a>
        <a class="navbtn" href="index.php?idp=grafika3d">Grafika 3D</a>
        <a class="navbtn" href="index.php?idp=ruchoma">Grafika Ruchoma</a>
        <a class="navbtn" href="index.php?idp=kolorujtlo">Skrypt</a>
        <a class="navbtn" href="index.php?idp=filmy">Filmy</a>
    </div>

    <?php
        if (isset($_GET['idp'])) {
            $page = $_GET['idp'];
        }
        else {
            $page = 'glowna';
        }
        echo PokazPodstrone($page);
    ?>

    <div id="footer">
        <?php
            $nr_indeksu = '175336';
            $nrGrupy = '2';
            echo 'Oskar Piotrowski ('.$nr_indeksu.'), grupa '.$nrGrupy.' <br />';
        ?>
        <div id="zegarek"></div>
        <div id="data"></div>
    </div>

</body>
</html>
