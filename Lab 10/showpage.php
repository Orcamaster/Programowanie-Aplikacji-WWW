<?php
    // Funkcja pobierająca treść podstrony z bazy danych
    function PokazPodstrone($title) {
        global $link;

        // Ustawienie strony głównej jeśli pusty parametr
        if ($title === null) {
            $title = 'glowna';
        }

        // Czyszczenie danych wejściowych
        $title_clear = mysqli_real_escape_string(
            $link,
            htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
        );

        // Zapytanie o aktywną stronę
        $query = "SELECT * FROM page_list WHERE page_title='$title_clear' AND status=1 LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        // Komunikat błędu jeśli brak wyniku
        if(empty($row['id'])) return '[nie_znaleziono_strony]';

        // Zwrócenie treści
        return $row['page_content'];
    }
?>