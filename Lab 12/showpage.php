<?php
function PokazPodstrone($title) {
    global $link;

    if (!$title) $title = 'glowna';

    $title_clear = mysqli_real_escape_string($link, htmlspecialchars($title, ENT_QUOTES, 'UTF-8'));
    $q = mysqli_query($link, "SELECT * FROM page_list WHERE page_title='$title_clear' AND status=1 LIMIT 1");
    $row = mysqli_fetch_assoc($q);

    // Jeśli nie znaleziono w bazie, sprawdzamy czy to strona specjalna
    if (!$row) {
        if ($title !== 'sklep' && $title !== 'koszyk' && $title !== 'produkt') {
            return "[nie_znaleziono_strony]";
        }
        $content = ''; 
    } else {
        $content = $row['page_content'];
    }

    /* =====================================================
       STRONA PRODUKTU (Szczegóły)
       ===================================================== */
    if ($title === 'produkt' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $q = mysqli_query($link, "SELECT p.*, c.nazwa AS kat FROM product_list p JOIN category_list c ON p.kategoria=c.id WHERE p.id=$id LIMIT 1");

        if (!mysqli_num_rows($q)) return "<h2>Produkt nie istnieje</h2>";

        $p = mysqli_fetch_assoc($q);
        $img = $p['zdjecie'] ? "img/".$p['zdjecie'] : "img/brak.jpg";
        $wKoszyku = isset($_SESSION['cart'][$id]);
        $cenaBrutto = $p['cena_netto'] * (1 + $p['vat']/100);
        $cenaFmt = number_format($cenaBrutto, 2, '.', '');

        $btnClass = $wKoszyku ? "shop-btn remove" : "shop-btn add";
        $btnText  = $wKoszyku ? "Usuń z koszyka" : "Dodaj do koszyka";

        $html = "
        <div class='product-details' style='display:flex; gap:30px; background:rgba(0,0,0,0.5); padding:30px; border-radius:10px;'>
            <div style='flex:1;'>
                <img src='$img' style='width:100%; border-radius:10px;'>
            </div>
            <div style='flex:1.5; color:white;'>
                <h1 style='margin-top:0'>{$p['tytul']}</h1>
                <p>Kategoria: <b>{$p['kat']}</b></p>
                <h2 style='color:#00aaff'>$cenaFmt zł <span style='font-size:0.5em; color:#ccc;'>(brutto)</span></h2>
                <p>Stan magazynowy: {$p['ilosc']} szt.</p>
                <p>Gabaryt: {$p['gabaryt']}</p>
                <hr style='border-color:#555'>
                <p>".nl2br($p['opis'])."</p>
                <br>
                <a href='#' class='$btnClass' onclick='toggleCart(this, $id)'>$btnText</a>
                <br><br>
                <a href='index.php?idp=sklep' style='color:#ccc; text-decoration:none;'>&laquo; Wróć do sklepu</a>
            </div>
        </div>";
        return $html;
    }

    /* =====================================================
       SKLEP (Lista produktów z filtrami i sortowaniem)
       ===================================================== */
    if ($title === 'sklep') {
        // Obsługa filtrów
        $search = $_POST['filter_search'] ?? '';
        $minPrice = isset($_POST['filter_price_min']) && $_POST['filter_price_min'] !== '' ? (float)$_POST['filter_price_min'] : null;
        $maxPrice = isset($_POST['filter_price_max']) && $_POST['filter_price_max'] !== '' ? (float)$_POST['filter_price_max'] : null;
        $sortOption = $_POST['filter_sort'] ?? 'newest';
        $filterCat = isset($_POST['filter_category']) ? (int)$_POST['filter_category'] : 0;

        // Pobranie listy kategorii do selecta
        $catQuery = mysqli_query($link, "SELECT * FROM category_list ORDER BY nazwa ASC");

        // Budowanie zapytania SQL
        $sql = "SELECT p.*, c.nazwa AS kat FROM product_list p JOIN category_list c ON p.kategoria=c.id WHERE p.status='dostepny' ";

        // 1. Wyszukiwanie
        if ($search) {
            $s = mysqli_real_escape_string($link, $search);
            $sql .= " AND p.tytul LIKE '%$s%' ";
        }

        // 2. Filtrowanie po kategorii (POPRAWIONE: Matka + Dzieci)
        if ($filterCat > 0) {
            // Pobieramy ID wybranej kategorii oraz jej dzieci
            $idsToCheck = [$filterCat];
            
            // Zapytanie o dzieci wybranej kategorii
            $childQuery = mysqli_query($link, "SELECT id FROM category_list WHERE matka = $filterCat");
            while($child = mysqli_fetch_assoc($childQuery)){
                $idsToCheck[] = $child['id'];
            }
            
            // Konwersja tablicy na string do zapytania IN (np. "1, 5, 6")
            $idString = implode(',', $idsToCheck);
            
            $sql .= " AND p.kategoria IN ($idString) ";
        }

        // 3. Cena brutto (p.cena_netto * (1 + p.vat/100))
        if ($minPrice !== null) {
            $sql .= " AND (p.cena_netto * (1 + p.vat/100)) >= $minPrice ";
        }
        if ($maxPrice !== null) {
            $sql .= " AND (p.cena_netto * (1 + p.vat/100)) <= $maxPrice ";
        }

        // 4. Sortowanie
        switch ($sortOption) {
            case 'price_asc':
                $sql .= " ORDER BY (p.cena_netto * (1 + p.vat/100)) ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY (p.cena_netto * (1 + p.vat/100)) DESC";
                break;
            case 'oldest':
                $sql .= " ORDER BY p.data_utworzenia ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY p.data_utworzenia DESC";
                break;
        }

        // Generowanie HTML paska filtrów
        $html = "
        <div class='filter-bar'>
            <form method='post' action='index.php?idp=sklep' style='display:flex; gap:10px; flex-wrap:wrap; align-items:center; width:100%; justify-content:center;'>
                
                <input type='text' name='filter_search' placeholder='Szukaj produktu...' value='$search' style='width:150px;'>
                
                <select name='filter_category'>
                    <option value='0'>-- Wszystkie kategorie --</option>";
                    // Reset wskaźnika
                    mysqli_data_seek($catQuery, 0); 
                    
                    while($c = mysqli_fetch_assoc($catQuery)){
                        $sel = ($filterCat == $c['id']) ? 'selected' : '';
                        // Oznaczenie wizualne, jeśli to podkategoria (opcjonalne)
                        $prefix = ($c['matka'] != 0) ? '&nbsp;&nbsp;- ' : '';
                        $html .= "<option value='{$c['id']}' $sel>$prefix{$c['nazwa']}</option>";
                    }
        $html .= "</select>

                <input type='number' name='filter_price_min' placeholder='Min zł' step='0.01' value='".($minPrice ?? '')."' style='width:80px;'>
                <input type='number' name='filter_price_max' placeholder='Max zł' step='0.01' value='".($maxPrice ?? '')."' style='width:80px;'>
                
                <select name='filter_sort'>
                    <option value='newest' ".($sortOption=='newest'?'selected':'').">Najnowsze</option>
                    <option value='oldest' ".($sortOption=='oldest'?'selected':'').">Najstarsze</option>
                    <option value='price_asc' ".($sortOption=='price_asc'?'selected':'').">Cena: rosnąco</option>
                    <option value='price_desc' ".($sortOption=='price_desc'?'selected':'').">Cena: malejąco</option>
                </select>

                <button type='submit' class='filter-btn'>Filtruj</button>
            </form>
        </div>
        <div class='shop-container' style='display:flex; flex-wrap:wrap; gap:20px; justify-content:center;'>";

        $q = mysqli_query($link, $sql);
        
        if (mysqli_num_rows($q) > 0) {
            while ($p = mysqli_fetch_assoc($q)) {
                $id = $p['id'];
                $img = $p['zdjecie'] ? "img/".$p['zdjecie'] : "img/brak.jpg";
                $cenaBrutto = $p['cena_netto'] * (1 + $p['vat']/100);
                $cenaFmt = number_format($cenaBrutto, 2, '.', '');
                
                $wKoszyku = isset($_SESSION['cart'][$id]);
                $btnClass = $wKoszyku ? "shop-btn remove" : "shop-btn add";
                $btnText  = $wKoszyku ? "Usuń" : "Do koszyka";

                $html .= "
                <div class='product-card' onclick=\"goToProduct('index.php?idp=produkt&id=$id')\" style='width:250px;'>
                    <img src='$img' style='width:100%; height:180px; object-fit:cover; border-radius:5px;'>
                    <h3 style='margin:10px 0; font-size:1.2em;'>{$p['tytul']}</h3>
                    <p style='color:#bbb; font-size:0.9em;'>{$p['kat']}</p>
                    <h4 style='color:#00aaff; margin:5px 0;'>$cenaFmt zł</h4>
                    <div class='actions'>
                         <button class='$btnClass' onclick='toggleCart(this, $id)'>$btnText</button>
                    </div>
                </div>";
            }
        } else {
            $html .= "<h3>Brak produktów spełniających kryteria.</h3>";
        }

        $html .= "</div>";

        // Pływający przycisk koszyka
        $cartCount = empty($_SESSION['cart']) ? 0 : array_sum($_SESSION['cart']);
        $cartDisplay = $cartCount > 0 ? 'flex' : 'none';
        
        $html .= "
        <a href='index.php?idp=koszyk' class='cart-floating-btn'>
            🛒
            <span id='cart-counter' class='cart-badge' style='display:$cartDisplay'>$cartCount</span>
        </a>";

        return $html;
    }

    /* =====================================================
       KOSZYK
       ===================================================== */
    if ($title === 'koszyk') {
        if (empty($_SESSION['cart'])) {
            return "<div class='glass-container' style='text-align:center; padding:50px;'>
                        <h2>Twój koszyk jest pusty</h2>
                        <a href='index.php?idp=sklep' class='shop-btn add'>Wróć do sklepu</a>
                    </div>";
        }

        $ids = implode(",", array_map('intval', array_keys($_SESSION['cart'])));
        $q = mysqli_query($link, "SELECT * FROM product_list WHERE id IN ($ids)");

        $html = "<div class='glass-container'><div style='padding:20px;'>
                 <h2>Twój koszyk</h2>";
        
        $suma = 0;

        while ($p = mysqli_fetch_assoc($q)) {
            $id = $p['id'];
            $ilosc = $_SESSION['cart'][$id];
            
            $cenaBrutto = $p['cena_netto'] * (1 + $p['vat']/100);
            $razem = $ilosc * $cenaBrutto;
            $suma += $razem;

            $razemFmt = number_format($razem, 2, '.', '');
            $cenaBruttoFmt = number_format($cenaBrutto, 2, '.', '');
            $img = $p['zdjecie'] ? "img/".$p['zdjecie'] : "img/brak.jpg";

            // ID: item-info-$id dla dynamicznego JS
            $html .= "
            <div class='cart-item' id='cart-row-$id' onclick=\"goToProduct('index.php?idp=produkt&id=$id')\">
                <img src='$img' style='width:60px; height:60px; object-fit:cover; border-radius:5px;'>
                
                <div class='cart-item-details'>
                    <h3 style='margin:0 0 5px 0'>{$p['tytul']}</h3>
                    <p id='item-info-$id' style='font-size:0.9em; color:#ddd;'>
                        $ilosc szt. po $cenaBruttoFmt zł
                    </p>
                    <p id='item-total-$id' style='margin-top:5px;'>
                        łącznie: <b>$razemFmt zł</b>
                    </p>
                </div>

                <div class='cart-item-actions'>
                    <input type='number' value='$ilosc' min='1' 
                           style='width:60px; padding:5px; border-radius:5px; border:none; text-align:center;'
                           onclick='stopProp(event)'
                           onchange='updateCartItem(this, $id)'>
                           
                    <button class='shop-btn remove' 
                            style='padding: 8px 12px; font-size:14px;' 
                            onclick='removeCartItem(this, $id)'>
                            Usuń
                    </button>
                </div>
            </div>";
        }
        $sumaFmt = number_format($suma, 2, '.', '');
        $html .= "<h3 style='text-align:right; border-top:1px solid #fff; padding-top:10px;'>
                    Suma zamówienia: <span id='cart-sum'>$sumaFmt zł</span>
                  </h3>
                  <div style='text-align:right; margin-top:20px;'>
                     <a href='index.php?idp=sklep' class='shop-btn' style='background:#555'>Kontynuuj zakupy</a>
                     <button class='shop-btn add'>Przejdź do płatności</button>
                  </div>
                  </div></div>";
        
        return $html;
    }

    return $content;
}
?>