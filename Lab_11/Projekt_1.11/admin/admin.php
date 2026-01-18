<!-- 
Moduł główny panelu administracyjnego.
Odpowiada za logowanie oraz obsługę podstron, kategorii oraz produktór (w przyszłości).
-->

<!DOCTYPE html>
<html lang="UTF-8">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Projekt 1">
        <meta name="keywords" content="HTML5, CSS3, JaavaScript">
        <meta name="author" content="Piotr Piotrowski">
        <title>Panel CMS</title>
        <link rel="shortcut icon" href="../image/ikony/penne-icon.png">
        <link rel="stylesheet" href="../style/admin.css">
        <link rel="stylesheet" href="../style/shop-admin.css">
        <script src="../js/edycja-kategorii.js" defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/theme/ayu-mirage.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/xml/xml.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/javascript/javascript.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/css/css.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/htmlmixed/htmlmixed.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Amarante&family=Luckiest+Guy&display=swap" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>

    <body>
        <div class="container">
            <header>
                <h1>Panel CMS</h1>
            </header>
            <main>

                <?php
                    session_start();
                    include '../cfg.php';
                    include '../contact.php';
                    include 'admin-podstrony.php';
                    include 'admin-kategorie.php';
                    include 'admin-produkty.php';

                    /**
                    * Obsługa wylogowania
                    * Sprawdza, czy akcja 'logout' została wywołana.
                    */
                    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                        session_destroy();
                        header('Location: admin.php');
                        exit();
                    }

                    /**
                    * FormularzLogowania() 
                    * Generuje formularz logowania do Panelu CMS.
                    */
                    function FormularzLogowania() {

                        // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu w nazwie strony.
                        $wynik = '
                            <div class="login">
                                <h2 class="heading">Panel logowania</h2>
                                <form method="post" name="LoginForm" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" class="login-form"> 
                                    <div class="form-group">
                                        <input type="text" name="login_email" class="logowanie" placeholder="E-mail" required />
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="login_pass" class="logowanie" placeholder="Hasło" required />
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" name="xl_submit" class="logowanie" value="Zaloguj się" />
                                    </div>  
                                </form>
                                <p id="password-reminder"><a href="admin.php?action=forgot_pass">Nie pamiętam hasła</a></p>
                            </div>';

                        return $wynik;
                    }

                    /**
                    * FormularzPrzypomnieniaHasla() 
                    * Generuje formularz do przypomnienia hasła dla administratora.
                    */
                    function FormularzPrzypomnieniaHasla() {

                        $form_haslo = '
                            <div class="login">
                                <h2 class="heading">Przypomnienie Hasła</h2>
                                <p>Podaj adres e-mail, aby otrzymać hasło.</p>
                                <form method="post" action="admin.php?action=forgot_pass" class="login-form">
                                    <div class="form-group">
                                        <input type="email" name="email_admin" class="logowanie" placeholder="E-mail Konta Administratora" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" name="przypomnij_submit" class="logowanie" value="Wyślij Hasło" />
                                    </div>
                                    <p id="password-reminder"><a href="admin.php">Wróć do logowania</a></p>
                                </form>
                            </div>';
                        return $form_haslo;
                    }

                    $error_message = '';

                    /**
                    * Główna logika zarządzania sesją.
                    * Obsługa wylogowanego oraz zalogowanego użytkownika.
                    */
                    if (!isset($_SESSION['logged_in'])) {

                        // Obsługa przypomnienia hasła.
                        if (isset($_GET['action']) && $_GET['action'] === 'forgot_pass') {
                            if (isset($_POST['przypomnij_submit'])) {
                                $input_email = isset($_POST['email_admin']) ? trim($_POST['email_admin']) : '';

                                if ($input_email === $login) {

                                    PrzypomnijHaslo($login, $pass);

                                } else {
                                    $email_error = '<p style="color: red;">Podany adres e-mail nie jest powiązany z kontem administratora.</p>';
                                }
                            }

                            echo $email_error ?? '';
                            echo FormularzPrzypomnieniaHasla();

                            exit();

                        } else {

                            // Obsługa logowania.
                            if((isset($_POST['xl_submit']))) {

                                $input_login = isset($_POST['login_email']) ? trim($_POST['login_email']) : '';
                                $input_pass = isset($_POST['login_pass']) ? trim($_POST['login_pass']) : '';

                                if ($input_login === $login && $input_pass === $pass) {

                                    $_SESSION['logged_in'] = true;
                                    header('Location: ' . $_SERVER['REQUEST_URI']);
                                    exit();

                                } else {
                                    $error_message = '<p style="color: red;">Podano nieprawidłowy e-mail lub hasło.</p>';
                                }
                            }
                        }
                    }

                    // Zalogowany użytkownik.
                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

                        $action = isset($_GET['action']) ? $_GET['action'] : '';
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

                        // Obsługa funkcji odpowiedzialnych za usuwanie. 
                        if ($action === 'delete' && $id > 0) {
                            echo UsunPodstrone($link, $id); 
                            $action = '';
                        } elseif ($action === 'delete_kat' && $id > 0) {
                            UsunKategorie($link, $id); 
                        } elseif ($action === 'delete_prod' && $id > 0) {
                            UsunProdukt($link, $id); 
                        } 

                        $content = '';

                        ObslugaPodstron($link);

                        // Renderowanie widoków.
                        if ($action === 'edit' && $id > 0) {
                            echo $content;
                            echo EdytujPodstrone($link, $id);
                        } elseif ($action === 'edit_prod' && $id > 0) {
                            echo '<h1 style="margin: 30px;">Edycja Produktu</h1>';
                            echo EdytujProdukt($link, $id);
                        } elseif ($action === 'add') {
                            echo $content;
                            echo DodajNowaPodstrone();
                        } elseif ($action === 'add_prod') {
                            echo '<h1 style="margin: 30px;">Dodaj Nowy Produkt</h1>';
                            echo DodajProdukt($link); // Wywołanie formularza i logiki zapisu
                        } elseif ($action === 'sklep') {
                            echo '<h1 style="margin: 30px;">Zarządzanie Sklepem</h1>';
                            echo $content; 
                            PokazKategorie($link);
                            echo '<hr style="margin: 50px 0; border: 1px solid #e6a020">';
                            echo PokazProdukty($link);
                        } else {
                            echo '<h1 style="margin: 30px;">Podstrony</h1>';
                            echo $content;
                            ListaPodstron($link);
                        }

                    } else {

                        // Renderowanie logowania.
                        echo $error_message;
                        echo FormularzLogowania();
                    }


                ?>
            </main>
        </div>
    </body>
</html>