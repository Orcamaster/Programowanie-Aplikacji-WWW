<?php
$nr_indeksu = '175336';
$nrGrupy = '2';
$date = date('N');
echo 'Oskar Piotrowski '.$nr_indeksu.' grupa '.$nrGrupy.'<br/><br/>';

//echo 'Zastosowanie metody include() <br/>';
//include("labor_175336_2.php");
//require_once("labor_175336_2.php");

echo 'Zastosowanie if, else, switch <br/>';
if (is_numeric($nrGrupy) == TRUE) {
    if ($nrGrupt % 2 == 0) {
        echo 'Parzysty numer grupy<br/>';
    }
    else {
        echo 'Nieparzysty numer grupy<br/>';
    }
}
else {
    'Niepoprawny numer grupy <br/>';
}

switch ($date) {
    case 1:
        echo 'Poniedziałek';
        break;
    case 2:
        echo 'Wtorek';
        break;
    case 3:
        echo 'Środa';
        break;
    case 4:
        echo 'Czwartek';
        break;
    case 5:
        echo 'Piątek';
        break;
    case 6:
        echo 'Sobota';
        break;
    case 7:
        echo 'Niedziela';
        break;
}

echo 'Zastosowanie while, for <br/>';
$i = 0;
while ($i < strlen($nr_indeksu)) {
    for ($j = 0; $j < $nr_indeksu[$i]; $j++) {
        echo $nr_indeksu[$i];
    }
    echo '<br/>';
    $i++;
}

echo 'Zastosowanie $_GET, $_POST, $_SESSION <br/>';
if (isset($_GET['imie'])) {
    echo "Witaj, ".$_GET['imie'];
} else {
    echo "Nie podano imienia.";
}

if (isset($_POST['login'])) {
    echo "Login: " . $_POST['login'];
}

session_start();

if (!isset($_SESSION['licznik'])) {
    $_SESSION['licznik'] = 1;
} else {
    $_SESSION['licznik']++;
}

echo "Liczba odświeżeń: " . $_SESSION['licznik'];
?>