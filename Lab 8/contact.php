<?php

function PokazKontakt() {
    $html = '
    <h2>Formularz kontaktowy</h2>
    <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
        <label>Twój email:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Temat wiadomości:<br>
            <input type="text" name="temat" required>
        </label><br><br>

        <label>Treść wiadomości:<br>
            <textarea name="tresc" rows="6" cols="50" required></textarea>
        </label><br><br>

        <input type="submit" name="wyslij_kontakt" value="Wyślij">
    </form>
    ';

    return $html;
}

function wyslijMailakontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email']))
    {
        echo '[nie_wypelniles_pola]';
    }
    else
    {
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['recipient'] = $odbiorca;

        $header = "From: Formularz kontaktowy <".$mail['sender'].">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding:";
        $header .= "X-Sender: <". $mail['sender'].">\n";
        $header .= "X-Mailer: PRapWWW mail 1.2\n";
        $header .= "X-Priority: 3\n";
        $header .= "Return-Path: <". $mail['sender'].">\n";
        mail($mail['recipient'],$mail['subject'],$mail['body'],$header);
        echo '[wiadomosc_wyslana]';
    }
}

function PrzypomnijHaslo() {
    require("cfg.php");
    
    if (!isset($_POST['przypomnijhaslo'])) {
        return '
        <h2>Przypomnienie hasła</h2>
        <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
            <label>Email administratora:<br>
                <input type="email" name="email" required>
            </label><br><br>

            <input type="submit" name="przypomnij" value="Wyślij hasło">
        </form>
        ';
    }

    if (empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        return;
    }

    $odbiorca = $_POST['email'];
    $temat = "Przypomnienie hasła do panelu administracyjnego";
    $tresc = "Login: ".$login."\nHasło: ".$pass."\n";
    $nadawca = "strona@gmail.com";

    $header = "From: Panel CMS <".$nadawca.">\n";
    $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";

    if (mail($odbiorca, $temat, $tresc, $header)) {
        echo '[wiadomosc_wyslana]';
    } else {
        echo '[blad_wysylania_maila]';
    }
}

?>