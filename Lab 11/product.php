<?php
require_once("cfg.php");

$id = (int)$_GET['id'];

$q = mysqli_query($link,
"SELECT p.*, c.nazwa 
 FROM product_list p 
 JOIN category_list c ON p.kategoria = c.id
 WHERE p.id = $id");

if(mysqli_num_rows($q)==0){
    echo "Nie znaleziono produktu";
    exit;
}

$p = mysqli_fetch_assoc($q);
?>

<h1><?= $p['tytul'] ?></h1>
<p><b>Kategoria:</b> <?= $p['nazwa'] ?></p>

<?php if($p['zdjecie']){ ?>
<img src="img/<?= $p['zdjecie'] ?>" style="max-width:300px"><br>
<?php } ?>

<p><?= nl2br($p['opis']) ?></p>

<h2><?= $p['cena_netto'] ?> zł + VAT</h2>
<p>Dostępne sztuki: <?= $p['ilosc'] ?></p>
