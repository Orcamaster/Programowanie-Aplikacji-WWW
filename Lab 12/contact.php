<?php
    // Funkcja generująca formularz kontaktowy
    function PokazKontakt() {
        $html = '
        <div class="glass-container"> <div style="padding: 20px;"> 
            <h2>Formularz kontaktowy</h2>
            <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                <label>Twój email:<br>
                    <input type="email" name="email" required 
                           style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:1px solid #ccc; color:black;">
                </label><br><br>

                <label>Temat wiadomości:<br>
                    <input type="text" name="temat" required 
                           style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:1px solid #ccc; color:black;">
                </label><br><br>

                <label>Treść wiadomości:<br>
                    <textarea name="tresc" rows="6" required 
                              style="width:100%; padding:10px; margin:5px 0; border-radius:5px; border:1px solid #ccc; color:black; resize:vertical;"></textarea>
                </label><br><br>

                <input type="submit" name="wyslij_kontakt" value="Wyślij" class="shop-btn add" style="width:100%">
            </form>
        </div>
        </div>
        ';
        return $html;
    }

    // Funkcja wysyłająca mail z formularza kontaktowego
    function wyslijMailakontakt($odbiorca)
    {
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        } else {
            $mail['subject'] = htmlspecialchars($_POST['temat']);
            $mail['body'] = htmlspecialchars($_POST['tresc']);
            $mail['sender'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $mail['recipient'] = $odbiorca;

            if ($mail['sender']) {
                $header  = "From: Formularz <no-reply@localhost>\n";
                $header .= "Reply-To: " . $mail['sender'] . "\n";
                $header .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";

                if (mail($mail['recipient'], $mail['subject'], $mail['body'], $header)) {
                    echo '<script>alert("Wiadomość wysłana!");</script>';
                } else {
                    echo '<script>alert("Błąd wysyłania (sprawdź konfig serwera).");</script>';
                }
            }
        }
    }

    function PrzypomnijHaslo() {
        require("cfg.php");
        
        if (!isset($_POST['przypomnijhaslo'])) {
            return '
            <div class="glass-container"><div style="padding:20px;">
            <h2>Przypomnienie hasła</h2>
            <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
                <label>Email administratora:<br>
                    <input type="email" name="email" required style="width:100%; padding:10px; color:black; border-radius:5px;">
                </label><br><br>
                <input type="submit" name="przypomnijhaslo" value="Wyślij hasło" class="shop-btn add">
            </form>
            </div></div>
            ';
        }
        
        // Logika wysyłania hasła
        $email = $_POST['email'];
        $header = "From: Admin <no-reply@localhost>\nMIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\n";
        mail($email, "Hasło", "Pass: $pass", $header);
        echo '<script>alert("Wysłano (o ile SMTP działa).");</script>';
    }
?>