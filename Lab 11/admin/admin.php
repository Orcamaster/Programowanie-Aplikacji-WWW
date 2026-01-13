<?php
require_once("../cfg.php");
require_once("category.php");
require_once("product.php");

function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
            <table class="logowanie">
                <tr><td>Login</td><td><input name="login_email"></td></tr>
                <tr><td>Hasło</td><td><input type="password" name="login_pass"></td></tr>
                <tr><td></td><td><input type="submit" name="x1_submit" value="Zaloguj"></td></tr>
            </table>
        </form>
    </div>';
}

if(isset($_POST['x1_submit'])){
    if($_POST['login_email']==$login && $_POST['login_pass']==$pass){
        $_SESSION['zalogowany']=true;
    } else {
        echo FormularzLogowania();
        exit;
    }
}

if(!isset($_SESSION['zalogowany'])){
    echo FormularzLogowania();
    exit;
}

echo "<h1>Panel administracyjny</h1>";
echo "<a href='admin.php?kategorie=1'>Kategorie</a> | ";
echo "<a href='admin.php?produkty=1'>Produkty</a> | ";
echo "<a href='admin.php'>Podstrony</a><hr>";

$kat = new ZarzadzajKategoriami($link);
$prod = new ZarzadzajProduktami($link);

/* ======== ROUTER ======== */

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

/* ===== PRODUKTY ===== */

elseif(isset($_GET['produkty'])){
    $prod->DodajProdukt();
    $prod->PokazProdukty();
}
elseif(isset($_GET['prod_del'])){
    $prod->UsunProdukt($_GET['prod_del']);
    $prod->PokazProdukty();
}

/* ===== CMS STRON ===== */

elseif(isset($_GET['edit'])){
    EdytujPodstrone($link,$_GET['edit']);
}
elseif(isset($_GET['delete'])){
    UsunPodstrone($link,$_GET['delete']);
    ListaPodstron($link);
}
elseif(isset($_GET['add'])){
    DodajNowaPodstrone($link);
}
else{
    ListaPodstron($link);
    echo "<br><a href='admin.php?add=1'>Dodaj stronę</a>";
}

/* ==== FUNKCJE CMS STRON ==== */

function ListaPodstron($link){
    $q=mysqli_query($link,"SELECT * FROM page_list");
    echo "<h2>Podstrony</h2><table border=1>";
    while($r=mysqli_fetch_assoc($q)){
        echo "<tr><td>{$r['id']}</td><td>{$r['page_title']}</td>
        <td><a href='?edit={$r['id']}'>Edytuj</a> | <a href='?delete={$r['id']}'>Usuń</a></td></tr>";
    }
    echo "</table>";
}

function EdytujPodstrone($link,$id){
    $r=mysqli_fetch_assoc(mysqli_query($link,"SELECT * FROM page_list WHERE id=$id"));
    if(isset($_POST['save'])){
        $t=$_POST['title'];
        $c=$_POST['content'];
        mysqli_query($link,"UPDATE page_list SET page_title='$t', page_content='$c' WHERE id=$id");
        header("Location: admin.php");
        exit;
    }
    echo "<form method='post'>
    <input name='title' value='{$r['page_title']}'><br>
    <textarea name='content'>{$r['page_content']}</textarea><br>
    <input type='submit' name='save'>
    </form>";
}

function DodajNowaPodstrone($link){
    if(isset($_POST['add'])){
        mysqli_query($link,"INSERT INTO page_list (page_title,page_content,status)
        VALUES ('{$_POST['title']}','{$_POST['content']}',1)");
        header("Location: admin.php");
    }
    echo "<form method='post'>
    <input name='title'><br>
    <textarea name='content'></textarea><br>
    <input type='submit' name='add'>
    </form>";
}

function UsunPodstrone($link,$id){
    mysqli_query($link,"DELETE FROM page_list WHERE id=$id");
}
?>
