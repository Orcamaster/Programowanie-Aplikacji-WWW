<?php
require_once("../cfg.php");
require_once("category.php");
require_once("product.php");

// Dynamiczne pobranie nazwy bieżącego pliku
$current_page = basename($_SERVER['PHP_SELF']);

// Wylogowanie
if (isset($_GET['logout'])) {
    unset($_SESSION['zalogowany']);
    session_destroy();
    header("Location: $current_page");
    exit;
}

// --- FUNKCJE FORMULARZY ---
function FormularzLogowania($error = '') {
    global $current_page;
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS</h1>
        '.($error ? '<p style="color:red; font-weight:bold;">'.$error.'</p>' : '').'
        <form method="post" action="'.$current_page.'">
            <table class="logowanie">
                <tr><td>Login:</td><td><input type="text" name="login_email"></td></tr>
                <tr><td>Hasło:</td><td><input type="password" name="login_pass"></td></tr>
                <tr><td></td><td><input type="submit" name="x1_submit" value="Zaloguj" class="shop-btn add"></td></tr>
            </table>
        </form>
        <br>
        <a href="'.$current_page.'?action=recover" style="color:#00aaff; text-decoration:underline;">Zapomniałem hasła</a>
    </div>';
}

function FormularzPrzypomnienia($msg = '') {
    global $current_page;
    return '
    <div class="logowanie">
        <h1 class="heading">Odzyskiwanie hasła</h1>
        '.($msg ? '<p style="color:green; font-weight:bold;">'.$msg.'</p>' : '').'
        <form method="post" action="'.$current_page.'?action=recover">
            <p>Podaj email administratora (zgodny z konfiguracją):</p>
            <input type="email" name="email_rec" required>
            <br><br>
            <input type="submit" name="recover_submit" value="Wyślij hasło" class="shop-btn">
        </form>
        <br>
        <a href="'.$current_page.'" style="color:#00aaff;">&laquo; Wróć do logowania</a>
    </div>';
}

// --- LOGIKA LOGOWANIA ---
if(isset($_POST['x1_submit'])){
    if($_POST['login_email'] == $login && $_POST['login_pass'] == $pass){
        $_SESSION['zalogowany'] = true;
        header("Location: $current_page");
        exit;
    } else {
        echo FormularzLogowania("Błędny login lub hasło.");
        exit;
    }
}

// --- LOGIKA ODZYSKIWANIA HASŁA ---
if (isset($_GET['action']) && $_GET['action'] == 'recover') {
    $adminEmailConfig = "admin@mojastrona.pl"; 

    if (isset($_POST['recover_submit'])) {
        $emailRec = $_POST['email_rec'];
        
        // Sprawdzamy czy podany mail to mail admina
        if ($emailRec === $adminEmailConfig) {
            $temat = "Przypomnienie hasła - Panel CMS";
            $tresc = "Twoje hasło do panelu to: " . $pass;
            $header = "From: system@moja_strona.pl\r\n";
            $header .= "Content-Type: text/plain; charset=utf-8";

            // Wysłanie maila (wymaga skonfigurowanego serwera SMTP/sendmail)
            if (mail($emailRec, $temat, $tresc, $header)) {
                echo FormularzPrzypomnienia("Hasło zostało wysłane na podany adres.");
            } else {
                echo FormularzPrzypomnienia("Błąd wysyłania e-maila. Sprawdź konfigurację serwera.");
            }
        } else {
            echo '<div class="logowanie"><h2 style="color:red">Podany email jest nieprawidłowy.</h2><a href="'.$current_page.'?action=recover">Spróbuj ponownie</a></div>';
        }
    } else {
        echo FormularzPrzypomnienia();
    }
    exit;
}

// --- SPRAWDZENIE SESJI ---

if(!isset($_SESSION['zalogowany'])){
    echo FormularzLogowania();
    exit;
}

// --- TREŚĆ PANELU ADMINISTRACYJNEGO (PO ZALOGOWANIU) ---
echo "<div style='text-align:right; padding:10px;'>
        Zalogowany jako: <b>$login</b> | 
        <a href='$current_page?logout=1' style='color:red; text-decoration:none;'>[Wyloguj]</a>
      </div>";

echo "<h1>Panel administracyjny</h1>";
// Menu nawigacyjne
echo "<a href='$current_page?kategorie=1'>Kategorie</a> | ";
echo "<a href='$current_page?produkty=1'>Produkty</a> | ";
echo "<a href='$current_page'>Podstrony</a><hr>";

$kat = new ZarzadzajKategoriami($link);
$prod = new ZarzadzajProduktami($link);

/* ======== ROUTER ======== */

// KATEGORIE
if(isset($_GET['kategorie'])){
    $kat->DodajKategorie();
    $kat->PokazKategorie();
}
elseif(isset($_GET['cat_edit'])){
    $kat->EdytujKategorie($_GET['cat_edit']);
}
elseif(isset($_GET['cat_delete'])){
    $kat->UsunKategorie($_GET['cat_delete']);
    $kat->PokazKategorie();
}

// PRODUKTY
elseif(isset($_GET['produkty'])){
    $prod->DodajProdukt();
    $prod->PokazProdukty();
}
elseif(isset($_GET['prod_del'])){
    $prod->UsunProdukt($_GET['prod_del']);
    $prod->PokazProdukty();
}

// PODSTRONY (CMS)
elseif(isset($_GET['edit'])){
    EdytujPodstrone($link, $_GET['edit']);
}
elseif(isset($_GET['delete'])){
    UsunPodstrone($link, $_GET['delete']);
    ListaPodstron($link);
}
elseif(isset($_GET['add'])){
    DodajNowaPodstrone($link);
}
else{
    ListaPodstron($link);
    echo "<br><a href='$current_page?add=1'>Dodaj nową podstronę</a>";
}

/* ==== FUNKCJE CMS STRON ==== */

function ListaPodstron($link){
    global $current_page;
    $q = mysqli_query($link, "SELECT * FROM page_list LIMIT 100");
    echo "<h2>Lista podstron</h2><table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";
    while($r = mysqli_fetch_assoc($q)){
        echo "<tr>
                <td>{$r['id']}</td>
                <td>{$r['page_title']}</td>
                <td>
                    <a href='$current_page?edit={$r['id']}'>Edytuj</a> | 
                    <a href='$current_page?delete={$r['id']}' onclick='return confirm(\"Czy na pewno?\")'>Usuń</a>
                </td>
              </tr>";
    }
    echo "</table>";
}

function EdytujPodstrone($link, $id){
    global $current_page;
    $id = (int)$id;
    $r = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM page_list WHERE id=$id"));
    
    if(isset($_POST['save'])){
        $t = mysqli_real_escape_string($link, $_POST['title']);
        $c = mysqli_real_escape_string($link, $_POST['content']);
        mysqli_query($link, "UPDATE page_list SET page_title='$t', page_content='$c' WHERE id=$id");
        header("Location: $current_page");
        exit;
    }
    echo "<h3>Edycja strony: {$r['page_title']}</h3>";
    echo "<form method='post'>
    Tytuł: <input name='title' value='{$r['page_title']}' style='width:300px;'><br><br>
    Treść:<br><textarea name='content' style='width:600px; height:300px;'>{$r['page_content']}</textarea><br><br>
    <input type='submit' name='save' value='Zapisz zmiany'>
    </form>";
    echo "<br><a href='$current_page'>&laquo; Anuluj</a>";
}

function DodajNowaPodstrone($link){
    global $current_page;
    if(isset($_POST['add'])){
        $t = mysqli_real_escape_string($link, $_POST['title']);
        $c = mysqli_real_escape_string($link, $_POST['content']);
        mysqli_query($link, "INSERT INTO page_list (page_title, page_content, status) VALUES ('$t', '$c', 1)");
        header("Location: $current_page");
        exit;
    }
    echo "<h3>Dodaj nową stronę</h3>";
    echo "<form method='post'>
    Tytuł: <input name='title' style='width:300px;' required><br><br>
    Treść:<br><textarea name='content' style='width:600px; height:300px;'></textarea><br><br>
    <input type='submit' name='add' value='Dodaj stronę'>
    </form>";
    echo "<br><a href='$current_page'>&laquo; Anuluj</a>";
}

function UsunPodstrone($link, $id){
    $id = (int)$id;
    mysqli_query($link, "DELETE FROM page_list WHERE id=$id");
    echo "<p style='color:red'>Podstrona usunięta.</p>";
}
?>