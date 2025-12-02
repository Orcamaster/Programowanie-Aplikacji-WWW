<?php
require_once("..\cfg.php");

function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
            <table class="logowanie">
                <tr>
                    <td class="log4_t">[email]</td>
                    <td><input type="text" name="login_email" class="logowanie" /></td>
                </tr>
                <tr>
                    <td class="log4_t">[haslo]</td>
                    <td><input type="password" name="login_pass" class="logowanie" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="x1_submit" class="logowanie" value="zaloguj" /></td>
                </tr>
            </table>
        </form>
    </div>
    ';
}

if (isset($_POST['x1_submit'])) {
    if ($_POST['login_email'] == $login && $_POST['login_pass'] == $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo "<p style='color:red;'>Błędny login lub hasło!</p>";
        echo FormularzLogowania();
        exit;
    }
}

if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    echo FormularzLogowania();
    exit;
}

function ListaPodstron($link) {
    $query = "SELECT * FROM page_list";
    $result = mysqli_query($link, $query);

    echo "<h2>Lista podstron</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Tytuł</th><th>Opcje</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['page_title']."</td>
                <td>
                    <a href='admin.php?edit=".$row['id']."'>Edytuj</a> |
                    <a href='admin.php?delete=".$row['id']."'>Usuń</a>
                </td>
              </tr>";
    }
    echo "</table>";
}

function EdytujPodstrone($link, $id) {
    $query = "SELECT * FROM page_list WHERE id=$id LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);

    echo "<h2>Edytuj podstronę</h2>";
    echo "<form method='post'>
            <input type='text' name='page_title' value='".$row['page_title']."' /><br><br>
            <textarea name='page_content' rows='10' cols='50'>".$row['page_content']."</textarea><br><br>
            <label><input type='checkbox' name='status' ".($row['status'] ? "checked" : "")."> Aktywna</label><br><br>
            <input type='submit' name='update' value='Zapisz zmiany'>
            <input type='submit' name='cancel' value='Anuluj'>
          </form>";

    if (isset($_POST['update'])) {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $update = "UPDATE page_list SET page_title='$title', page_content='$content', status='$status' WHERE id=$id";
        mysqli_query($link, $update);
        echo "<p style='color:green;'>Podstrona zaktualizowana!</p>";
    }

    if (isset($_POST['cancel'])) {
        header("Location: admin.php");
        exit;
    }
}

function DodajNowaPodstrone($link) {
    echo "<h2>Dodaj nową podstronę</h2>";
    echo "<form method='post'>
            <input type='text' name='page_title' placeholder='Tytuł podstrony' /><br><br>
            <textarea name='page_content' rows='10' cols='50' placeholder='Treść podstrony'></textarea><br><br>
            <label><input type='checkbox' name='status' checked> Aktywna</label><br><br>
            <input type='submit' name='insert' value='Dodaj'>
            <input type='submit' name='cancel' value='Anuluj'>
          </form>";

    if (isset($_POST['insert'])) {
        $title = $_POST['page_title'];
        $content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        $insert = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
        mysqli_query($link, $insert);
        echo "<p style='color:green;'>Podstrona dodana!</p>";
    }

    if (isset($_POST['cancel'])) {
        header("Location: admin.php");
        exit;
    }
}

function UsunPodstrone($link, $id) {
    $delete = "DELETE FROM page_list WHERE id=$id";
    mysqli_query($link, $delete);
    echo "<p style='color:red;'>Podstrona usunięta!</p>";
}

if (isset($_GET['edit'])) {
    EdytujPodstrone($link, $_GET['edit']);
} elseif (isset($_GET['delete'])) {
    UsunPodstrone($link, $_GET['delete']);
    ListaPodstron($link);
} elseif (isset($_GET['add'])) {
    DodajNowaPodstrone($link);
} else {
    ListaPodstron($link);
    echo "<br><a href='admin.php?add=1'>Dodaj nową podstronę</a>";
}
?>
