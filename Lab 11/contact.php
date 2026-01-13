<?php
    // Funkcja generująca formularz kontaktowy
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

    // Funkcja wysyłająca mail z formularza kontaktowego
    function wyslijMailakontakt($odbiorca)
    {
        // Sprawdzenie, czy wszystkie pola zostały wypełnione
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo '[nie_wypelniles_pola]';
        } else {
            // Przygotowanie danych e-mail
            $mail['subject'] = filter_input(INPUT_POST, 'temat', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $mail['body'] = filter_input(INPUT_POST, 'tresc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $mail['sender'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $mail['recipient'] = $odbiorca;

            // Przygotowanie nagłówków
            $header = "From: Formularz kontaktowy <".$mail['sender'].">\n";
            $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding:";
            $header .= "X-Sender: <". $mail['sender'].">\n";
            $header .= "X-Mailer: PRapWWW mail 1.2\n";
            $header .= "X-Priority: 3\n";
            $header .= "Return-Path: <". $mail['sender'].">\n";

            // Wysyłanie wiadomości
            mail($mail['recipient'],$mail['subject'],$mail['body'],$header);
            echo '[wiadomosc_wyslana]';
        }
    }

    // Funkcja przypomnienia hasła administracyjnego
    function PrzypomnijHaslo() {
        require("cfg.php");
        
        // Wyświetlanie formularza, jeśli nie został jeszcze wysłany
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

        // Sprawdzenie poprawności danych
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if (!$email) {
            echo '[nie_wypelniles_pola]';
            return;
        }

        // Przygotowanie wiadomości
        $odbiorca = $_POST['email'];
        $temat = "Przypomnienie hasła do panelu administracyjnego";
        $tresc = "Login: ".$login."\nHasło: ".$pass."\n";
        $nadawca = "strona@gmail.com";

        // Przygotowanie nagłówków
        $header = "From: Panel CMS <".$nadawca.">\n";
        $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";

        // Wysyłanie maila
        if (mail($odbiorca, $temat, $tresc, $header)) {
            echo '[wiadomosc_wyslana]';
        } else {
            echo '[blad_wysylania_maila]';
        }
    }
?>