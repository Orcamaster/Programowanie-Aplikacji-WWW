<?php
require_once("cfg.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$action = $_REQUEST['action'] ?? 'view';
$isAjax = isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1;

// Funkcja pomocnicza do obliczania sumy koszyka
function getCartSummary($link) {
    if (empty($_SESSION['cart'])) return ['sum' => 0, 'count' => 0];
    
    $sum = 0;
    $ids = implode(",", array_map('intval', array_keys($_SESSION['cart'])));
    $q = mysqli_query($link, "SELECT id, cena_netto, vat FROM product_list WHERE id IN ($ids)");
    
    while ($p = mysqli_fetch_assoc($q)) {
        if(isset($_SESSION['cart'][$p['id']])) {
            $ilosc = $_SESSION['cart'][$p['id']];
            $cenaBrutto = $p['cena_netto'] * (1 + $p['vat']/100);
            $sum += $ilosc * $cenaBrutto;
        }
    }
    return [
        'sum' => number_format($sum, 2, '.', ''), 
        'count' => array_sum($_SESSION['cart'])
    ];
}

switch ($action) {
    
    case 'add':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            // Dodawanie/Usuwanie
            if (!isset($_SESSION['cart'][$id])) {
                $stockQ = mysqli_query($link, "SELECT ilosc FROM product_list WHERE id=$id");
                $stockRow = mysqli_fetch_assoc($stockQ);
                if($stockRow['ilosc'] > 0) {
                    $_SESSION['cart'][$id] = 1; 
                } else {
                }
            } else {
                unset($_SESSION['cart'][$id]);
            }
            
            if ($isAjax) {
                $summary = getCartSummary($link);
                echo json_encode(['status' => 'ok', 'count' => $summary['count']]);
                exit;
            }
        }
        header("Location: index.php?idp=sklep");
        exit;

    case 'remove':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            unset($_SESSION['cart'][$id]);
            if ($isAjax) {
                $summary = getCartSummary($link);
                echo json_encode([
                    'status' => 'ok', 
                    'count' => $summary['count'],
                    'cart_sum' => $summary['sum']
                ]);
                exit;
            }
        }
        header("Location: index.php?idp=koszyk");
        exit;

    case 'update':
        if (isset($_POST['id'], $_POST['ilosc'])) {
            $id = (int)$_POST['id'];
            $reqIlosc = (int)$_POST['ilosc'];

            if ($id > 0 && $reqIlosc > 0) {
                
                $stockQ = mysqli_query($link, "SELECT ilosc, cena_netto, vat FROM product_list WHERE id=$id");
                $prod = mysqli_fetch_assoc($stockQ);
                $maxStock = (int)$prod['ilosc'];
                $limitOsiagniety = false;

                if ($reqIlosc > $maxStock) {
                    // Ograniczenie do stanu magazynowego
                    $reqIlosc = $maxStock;
                    $limitOsiagniety = true;
                }

                $_SESSION['cart'][$id] = $reqIlosc;
                
                if ($isAjax) {
                    $cenaBrutto = $prod['cena_netto'] * (1 + $prod['vat']/100);
                    $itemTotal = number_format($reqIlosc * $cenaBrutto, 2, '.', '');
                    $cenaBruttoFmt = number_format($cenaBrutto, 2, '.', '');
                    
                    $summary = getCartSummary($link);
                    
                    echo json_encode([
                        'status' => 'ok',
                        'count' => $summary['count'],
                        'cart_sum' => $summary['sum'],
                        'item_total_text' => "łącznie: <b>$itemTotal zł</b>",
                        'item_unit_info' => "$reqIlosc szt. po $cenaBruttoFmt zł",
                        'new_qty' => $reqIlosc,
                        'alert' => $limitOsiagniety ? "Maksymalna dostępna ilość to $maxStock szt." : null
                    ]);
                    exit;
                }
            }
        }
        header("Location: index.php?idp=koszyk");
        exit;

    default:
        echo "<pre>";
        print_r($_SESSION['cart']);
        echo "</pre>";
        break;
}
?>