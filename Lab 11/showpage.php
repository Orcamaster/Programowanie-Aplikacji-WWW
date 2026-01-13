<?php
// Funkcja pobierająca treść podstrony z bazy danych
function PokazPodstrone($title) {
    global $link;

    // Jeśli nie podano strony – strona główna
    if ($title === null || $title == '') {
        $title = 'glowna';
    }

    // Zabezpieczenie danych
    $title_clear = mysqli_real_escape_string(
        $link,
        htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
    );

    // Pobranie strony z CMS
    $query = "SELECT * FROM page_list WHERE page_title='$title_clear' AND status=1 LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);

    if (empty($row['id'])) {
        return "[nie_znaleziono_strony]";
    }

    // Treść strony
    $content = $row['page_content'];

    /* ====================================
       SHORTCODE: [lista_produktow]
       ==================================== */
    if (strpos($content, '[lista_produktow]') !== false) {

        $html = "<h2>Sklep</h2>";

        $q = mysqli_query($link, "SELECT * FROM product_list WHERE status='dostepny'");

        while ($p = mysqli_fetch_assoc($q)) {

            $img = $p['zdjecie'] ? "img/".$p['zdjecie'] : "img/brak.jpg";

            $html .= "
            <div style='border:1px solid #ccc; padding:10px; margin:10px'>
                <h3>{$p['tytul']}</h3>
                <img src='$img' style='max-width:150px'><br>
                <b>{$p['cena_netto']} zł</b><br>
                <a href='product.php?id={$p['id']}'>Zobacz produkt</a>
            </div>";
        }

        $content = str_replace('[lista_produktow]', $html, $content);
    }

    return $content;
}
?>
