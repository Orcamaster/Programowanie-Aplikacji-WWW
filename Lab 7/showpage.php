<?php
    function PokazPodstrone($title) {
        global $link;

        $title_clear = mysqli_real_escape_string($link, htmlspecialchars($title));
        $query = "SELECT * FROM page_list WHERE page_title='$title_clear' AND status=1 LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        if(empty($row['id'])) return '[nie_znaleziono_strony]';
        return $row['page_content'];
    }
?>