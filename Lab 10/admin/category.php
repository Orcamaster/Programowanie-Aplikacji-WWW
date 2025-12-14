<?php
// System zarządzania kategoriami produktów w CMS

// Dołączenie pliku konfiguracyjnego (połączenie z bazą, sesja itp.)
require_once("../cfg.php");

// Klasa odpowiedzialna za zarządzanie kategoriami
class ZarzadzajKategoriami {

    // Zmienna przechowująca połączenie z bazą danych
    private mysqli $link;

    // Konstruktor klasy przyjmujący połączenie z bazą
    public function __construct($link) {
        $this->link = $link;
    }

    // Metoda dpowiadająca za:
    // - obsługę formularza dodawania kategorii
    // - zapis nowej kategorii do bazy danych
    // - wyświetlenie formularza HTML
    public function DodajKategorie() {

        // Sprawdzenie, czy formularz został wysłany
        if (isset($_POST['cat_add'])) {

            // Pobranie i zabezpieczenie nazwy kategorii
            $nazwa = mysqli_real_escape_string($this->link, $_POST['nazwa']);

            // Pobranie ID kategorii nadrzędnej (0 = kategoria główna)
            $matka = (int)$_POST['matka'];

            // Zapytanie INSERT dodające nową kategorię
            mysqli_query(
                $this->link,
                "INSERT INTO category_list (nazwa, matka) VALUES ('$nazwa', '$matka')"
            );

            // Komunikat potwierdzający dodanie
            echo "<p style='color:green;'>Kategoria dodana</p>";
        }

        // Wyświetlenie formularza dodawania kategorii
        echo "<h2>Dodaj kategorię</h2>";
        echo "<form method='post'>
                Nazwa: <input type='text' name='nazwa' required><br><br>
                Kategoria nadrzędna:
                <select name='matka'>
                    <option value='0'>-- kategoria główna --</option>";

        // Wyświetlenie listy kategorii głównych w <select>
        $this->SelectKategorie();

        // Zakończenie formularza
        echo "</select><br><br>
              <input type='submit' name='cat_add' value='Dodaj'>
              </form>";
    }

    // Metoda wyświetlająca listę kategorii głównych (matka = 0) w postaci <option> do formularza <select>
    private function SelectKategorie() {

        // Pobranie kategorii głównych z bazy
        $res = mysqli_query($this->link, "SELECT * FROM category_list WHERE matka=0");

        // Iteracja po wynikach i generowanie <option>
        while ($m = mysqli_fetch_assoc($res)) {
            echo "<option value='{$m['id']}'>{$m['nazwa']}</option>";
        }
    }

    // Metoda wyświetlająca drzewo kategorii:
    public function PokazKategorie() {

        echo "<h2>Drzewo kategorii</h2>";

        // Pobranie kategorii głównych
        $matki = mysqli_query($this->link, "SELECT * FROM category_list WHERE matka=0");

        echo "<ul>";

        // Pętla po kategoriach głównych
        while ($m = mysqli_fetch_assoc($matki)) {

            // Wyświetlenie kategorii głównej wraz z opcjami
            echo "<li><b>{$m['nazwa']}</b> 
                [<a href='admin.php?cat_edit={$m['id']}'>Edytuj</a>] 
                [<a href='admin.php?cat_delete={$m['id']}'>Usuń</a>]";

            // Pobranie podkategorii dla danej kategorii głównej
            $dzieci = mysqli_query(
                $this->link,
                "SELECT * FROM category_list WHERE matka={$m['id']}"
            );

            // Sprawdzenie, czy istnieją podkategorie
            if (mysqli_num_rows($dzieci) > 0) {

                echo "<ul>";

                // Pętla po podkategoriach
                while ($d = mysqli_fetch_assoc($dzieci)) {
                    echo "<li>{$d['nazwa']} 
                        [<a href='admin.php?cat_edit={$d['id']}'>Edytuj</a>] 
                        [<a href='admin.php?cat_delete={$d['id']}'>Usuń</a>]
                        </li>";
                }

                echo "</ul>";
            }

            echo "</li>";
        }

        echo "</ul>";
    }

    // Metoda umożliwiająca edycję nazwy istniejącej kategorii
    public function EdytujKategorie($id) {

        // Rzutowanie ID na int (bezpieczeństwo)
        $id = (int)$id;

        // Pobranie danych edytowanej kategorii
        $row = mysqli_fetch_assoc(mysqli_query(
            $this->link,
            "SELECT * FROM category_list WHERE id=$id"
        ));

        // Sprawdzenie, czy formularz edycji został wysłany
        if (isset($_POST['cat_update'])) {

            // Pobranie i zabezpieczenie nowej nazwy
            $nazwa = mysqli_real_escape_string($this->link, $_POST['nazwa']);

            // Aktualizacja danych w bazie
            mysqli_query(
                $this->link,
                "UPDATE category_list SET nazwa='$nazwa' WHERE id=$id"
            );

            // Przekierowanie do listy kategorii
            header("Location: admin.php?kategorie=1");
            exit;
        }

        // Formularz edycji kategorii
        echo "<h2>Edytuj kategorię</h2>";
        echo "<form method='post'>
                <input type='text' name='nazwa' value='{$row['nazwa']}' required>
                <br><br>
                <input type='submit' name='cat_update' value='Zapisz'>
              </form>";
    }

    // Metoda usuwająca kategorię oraz wszystkie jej podkategorie
    public function UsunKategorie($id) {

        // Rzutowanie ID na int
        $id = (int)$id;

        // Usunięcie kategorii oraz jej dzieci
        mysqli_query($this->link, "DELETE FROM category_list WHERE id=$id OR matka=$id");

        // Komunikat potwierdzający
        echo "<p style='color:red;'>Kategoria usunięta</p>";
    }
}

// Integracja z panelem admin.php
// Utworzenie obiektu klasy zarządzającej kategoriami
$kat = new ZarzadzajKategoriami($link);

// Obsługa akcji na podstawie parametrów GET
if (isset($_GET['kategorie'])) {
    // Widok: dodawanie + lista kategorii
    $kat->DodajKategorie();
    $kat->PokazKategorie();
}
elseif (isset($_GET['cat_edit'])) {
    // Widok: edycja kategorii
    $kat->EdytujKategorie($_GET['cat_edit']);
}
elseif (isset($_GET['cat_delete'])) {
    // Widok: usuwanie kategorii
    $kat->UsunKategorie($_GET['cat_delete']);
    $kat->PokazKategorie();
}
?>
