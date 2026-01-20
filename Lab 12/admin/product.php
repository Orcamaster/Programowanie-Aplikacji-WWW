<?php
require_once("../cfg.php");

class ZarzadzajProduktami {
    private mysqli $link;

    public function __construct($link){
        $this->link = $link;
    }

    /* ===========================
       DODAJ PRODUKT + FORMULARZ
       =========================== */
    public function DodajProdukt() {

        if (isset($_POST['prod_add'])) {

            $tytul = mysqli_real_escape_string($this->link, $_POST['tytul']);
            $opis  = mysqli_real_escape_string($this->link, $_POST['opis']);
            $cena  = (float)$_POST['cena'];
            $vat   = (float)$_POST['vat'];
            $ilosc = (int)$_POST['ilosc'];
            $kat   = (int)$_POST['kategoria'];
            $gab   = $_POST['gabaryt'];

            $zdjecie = $_FILES['zdjecie']['name'];
            if($zdjecie){
                move_uploaded_file($_FILES['zdjecie']['tmp_name'], "../img/$zdjecie");
            }

            mysqli_query($this->link,
                "INSERT INTO product_list
                (tytul, opis, cena_netto, vat, ilosc, kategoria, gabaryt, zdjecie)
                VALUES
                ('$tytul','$opis','$cena','$vat','$ilosc','$kat','$gab','$zdjecie')"
            );

            echo "<p style='color:green'>Produkt dodany</p>";
        }

        echo "<h2>Dodaj produkt</h2>
        <form method='post' enctype='multipart/form-data'>
            Tytuł: <input name='tytul' required><br><br>
            Opis:<br><textarea name='opis'></textarea><br><br>
            Cena netto: <input name='cena' required><br><br>
            VAT: <input name='vat' value='23'><br><br>
            Ilość: <input name='ilosc' required><br><br>

            Kategoria:
            <select name='kategoria'>";

        $k = mysqli_query($this->link,"SELECT * FROM category_list");
        while($c = mysqli_fetch_assoc($k)){
            echo "<option value='{$c['id']}'>{$c['nazwa']}</option>";
        }

        echo "</select><br><br>

            Gabaryt:
            <select name='gabaryt'>
                <option value='maly'>maly</option>
                <option value='sredni'>sredni</option>
                <option value='duzy'>duzy</option>
                <option value='paleta'>paleta</option>
            </select><br><br>

            Zdjęcie: <input type='file' name='zdjecie'><br><br>
            <input type='submit' name='prod_add' value='Dodaj produkt'>
        </form>";
    }

    /* ===========================
       LISTA PRODUKTÓW
       =========================== */
    public function PokazProdukty() {

        $r = mysqli_query($this->link,
            "SELECT p.*, c.nazwa FROM product_list p
             JOIN category_list c ON p.kategoria = c.id"
        );

        echo "<h2>Lista produktów</h2>";
        echo "<table border='1' cellpadding='5'>
                <tr>
                    <th>ID</th>
                    <th>Tytuł</th>
                    <th>Cena</th>
                    <th>Kategoria</th>
                    <th>Akcje</th>
                </tr>";

        while($p = mysqli_fetch_assoc($r)){
            echo "<tr>
                <td>{$p['id']}</td>
                <td>{$p['tytul']}</td>
                <td>{$p['cena_netto']} zł</td>
                <td>{$p['nazwa']}</td>
                <td>
                    <a href='admin.php?prod_del={$p['id']}'>Usuń</a>
                </td>
            </tr>";
        }

        echo "</table>";
    }

    /* ===========================
       USUŃ PRODUKT
       =========================== */
    public function UsunProdukt($id) {
        $id = (int)$id;
        mysqli_query($this->link, "DELETE FROM product_list WHERE id=$id");
        echo "<p style='color:red'>Produkt usunięty</p>";
    }
}
