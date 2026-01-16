<!-- 
Moduł główny panelu administracyjnego.
Odpowiada za logowanie, funkcje dodawania i edytowania podstron.
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
        <link rel="shortcut icon" href="../image/penne-icon.png">
        <link rel="stylesheet" href="../style/admin.css">
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
                    include '../cfg.php';          // Łączenie z bazą danych.
                    include '../contact.php';      // Moduł obsługujący formularz kontaktowy.

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
                    function FormularzLogowania() 
                    {

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
                    * Generuje formularz do przypomnienia hasła.
                    */
                    function FormularzPrzypomnieniaHasla() 
                    {
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
                    */

                    if (!isset($_SESSION['logged_in']))         // Wylogowany użytkownik.
                    {
                        // Obsługa przypomnienia hasła.
                        if (isset($_GET['action']) && $_GET['action'] === 'forgot_pass')
                        {
                            if (isset($_POST['przypomnij_submit']))
                            {
                                // Pobranie e-maila
                                $input_email = isset($_POST['email_admin']) ? trim($_POST['email_admin']) : '';

                                // Sprawdzenie, czy e-mail pasuje do konfiguracji.
                                if ($input_email === $login) 
                                {
                                    // Wywołanie funkcji wysyłającej wiadomość z hasłem.
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
                            if((isset($_POST['xl_submit'])))
                            {
                                // Pobieranie danych do logowania.
                                $input_login = isset($_POST['login_email']) ? trim($_POST['login_email']) : '';
                                $input_pass = isset($_POST['login_pass']) ? trim($_POST['login_pass']) : '';

                                if ($input_login === $login && $input_pass === $pass) 
                                {
                                    $_SESSION['logged_in'] = true;

                                    // Zapobieganie ponownemu wysłaniu formularza.
                                    header('Location: ' . $_SERVER['REQUEST_URI']);
                                    exit();

                                } else {
                                    $error_message = '<p style="color: red;">Podano nieprawidłowy e-mail lub hasło.</p>';
                                }
                            }
                        }
                    }

                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true)         // Zalogowany użytkownik.
                    {
                        $action = isset($_GET['action']) ? $_GET['action'] : '';
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;         // Zabezpieczenie id.
                        $content = '';

                        // Obsługa edycji (update).
                        if (isset($_POST['edit_submit'])) 
                        {
                            $id_update = isset($_POST['id_edycji']) ? (int)$_POST['id_edycji'] : 0;

                            // Zabezpieczenie danych przed wstrzyknięciem.
                            $new_title = mysqli_real_escape_string($link, $_POST['page_title']);
                            $new_content = mysqli_real_escape_string($link, $_POST['page_content']);
                            $new_status = isset($_POST['status']) ? 1 : 0;
                        
                            if (empty($new_title) || empty($new_content)) 
                            {
                                $content = '<p style="color: red;">Pola nie mogą być puste!</p>';
                                $action = 'edit';
                                $id = $id_update;
                            } else {

                                // SQL: Aktualizacja danych w bazie.
                                // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
                                $query_update = "UPDATE page_list SET 
                                page_title = '$new_title', 
                                page_content = '$new_content', 
                                status = $new_status 
                                WHERE id = $id_update LIMIT 1";

                                if (mysqli_query($link, $query_update)) {
                                    $content = '<p style="color: green;">Strona o ID ' . $id_update . ' została zaktualizowana.</p>';
                                    $action = '';         // Powrót do listy wszystkich podstron z bazy danych.
                                } else {
                                    $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
                                }
                            }
                        }

                        // Obsługa dodawania (create).
                        if (isset($_POST['add_submit'])) {

                            // Zabezpieczenie danych przed wstrzyknięciem.
                            $new_title = mysqli_real_escape_string($link, $_POST['page_title']);
                            $new_content = mysqli_real_escape_string($link, $_POST['page_content']);
                            $new_status = isset($_POST['status']) ? 1 : 0;
                
                            if (empty($new_title) || empty($new_content)) {
                                $content = '<p style="color: red;">Pola nie mogą być puste!</p>';
                                $action = 'add';
                            } else {

                                // SQL: Dodanie danych do bazy.
                                $query_insert = "INSERT INTO page_list (page_title, page_content, status) 
                                VALUES ('$new_title', '$new_content', $new_status)";
                
                                if (mysqli_query($link, $query_insert)) {
                                    $content = '<p style="color: green;">Utworzono nową stronę o ID: ' . mysqli_insert_id($link) . '.</p>';
                                    $action = '';         // Powrót do listy wszystkich podstron z bazy danych.
                                } else {
                                    $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
                                }
                            }
                        }

                        // Renderowanie widoków.
                        if ($action === 'edit' && $id > 0) {
                            echo $content;                // Wyświetla komunikaty o błędach formularza edycji.
                            echo EdytujPodstrone($link, $id);
                        } elseif ($action === 'add') {
                            echo $content;                // Wyświetla komunikaty o błędach formularza dodawania.
                            echo DodajNowaPodstrone();
                        } elseif ($action === 'delete' && $id > 0) {
                            echo '<h1 style="margin: 30px;">Podstrony</h1>';
                            echo UsunPodstrone($link, $id); 
                            ListaPodstron($link);         // Wyświetla zaktualizowaną listę.
                        } else {
                            echo '<h1 style="margin: 30px;">Podstrony</h1>';
                            echo $content;               // Wyświetla komunikaty o sukcesie aktualizacji, dodawania oraz usunięcia.
                            ListaPodstron($link);
                        }
            
                    } else {
                        // Renderowanie logowania.
                        echo $error_message;               // Wyświetla komunikaty o błędnym logowaniu.
                        echo FormularzLogowania();
                    }


                    /**
                    * ListaPodstron($link) 
                    * Wyświetla listę wszystkich podstron z opcjami edycji oraz usunięcia.
                    */
                    function ListaPodstron($link) 
                    {

                        // SQL: Wyświetlenie listy, sortowanie po aktywności.
                        $query = "SELECT id, page_title, status FROM page_list ORDER BY status DESC";
                        $result = mysqli_query($link, $query); 

                        echo '<ul class="page-list">';
                        while ($row = mysqli_fetch_array($result)) 
                        {
                            echo '<li>';
                            echo '<div class="page-info">';

                            // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
                            echo htmlspecialchars($row['id']);
                            if ($row['status'] == 0) 
                            {

                                // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
                                echo '. <span style="color: gray;">' . htmlspecialchars($row['page_title']) . ' (NIEAKTYWNE)</span>';
                            } else 
                            {
                                
                                // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
                                echo '. <span>' . htmlspecialchars($row['page_title']) . '</span>';
                            }
                            echo '</div>';
                            echo '<div class="action-icons">';
                            echo '<a href="admin.php?action=edit&id=' . $row['id'] . '"><img src="../image/edit-icon.svg" alt="Edytuj"></a>';
                            echo '<a href="admin.php?action=delete&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć stronę o ID: ' . $row['id'] . '?\')"><img src="../image/delete-icon.svg" alt="Usuń"></a>';
                            echo '</div>';
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '<a class="add-button" href="admin.php?action=add">Dodaj</a>';
                        echo '<a class="logout-button"href="admin.php?action=logout">Wyloguj</a>';
                    }


                    /**
                    * EdytujPodstrone($link, $id)
                    * Pobiera dane podstrony i wyświetla formularz edycji.
                    */
                    function EdytujPodstrone($link, $id)
                    {
                        $id = (int)$id;

                        // SQL: Wyświetlenie danych podstrony po ID.
                        // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
                        $query_select = "SELECT * FROM page_list WHERE id = $id LIMIT 1"; 
                        $result = mysqli_query($link, $query_select);
                        $data = mysqli_fetch_array($result);
                        if (!$data) 
                        {
                            return '<p style="color: red;">Nie znaleziono strony o podanym ID.</p>';
                        }
            
                        // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
                        $tytul = htmlspecialchars($data['page_title']);
                        $tresc = htmlspecialchars($data['page_content']);

                        $aktywny_checked = ($data['status'] == 1) ? 'checked' : '';

                        $form = '
                            <h2>Edytuj: ' . $tytul . '</h2>
                            <form method="post" action="admin.php?action=edit&id=' . $id . '"class="edit-form">
                                <input type="hidden" name="id_edycji" value="' . $id . '">

                                <div class="form-group">
                                    <input type="text" name="page_title" value="' . $tytul . '"placeholder="Tytuł strony"/>
                                </div>

                                <div class="form-group">
                                    <textarea id="html_code_edit" name="page_content" rows="15" placeholder="Treść strony">' . $tresc . '</textarea>
                                </div>

                                <div class="form-group checkbox-group">
                                    <label>
                                        <input type="checkbox" name="status" value="1" ' . $aktywny_checked . ' /> Aktywna
                                    </label>
                                </div>

                                <div class="form-group">
                                    <input type="submit" name="edit_submit" value="Zapisz" />
                                    </div>
                            </form>

                        <script>
                            var editor = CodeMirror.fromTextArea(document.getElementById("html_code_edit"), {
                                lineNumbers: true,
                                mode: "htmlmixed",
                                theme: "ayu-mirage",
                                lineWrapping: true
                            });
                        </script>';

                        return $form;
                    }

                    /**
                    * DodajNowaPodstrone()
                    * Wyświetla formularz do tworzenia nowej podstrony.
                    */
                    function DodajNowaPodstrone() {
                        $form = '
                            <h2>Dodaj nową podstronę</h2>
                            <form method="post" action="admin.php?action=add" class="edit-form">
                    
                                <div class="form-group">
                                    <input type="text" name="page_title" value="" placeholder="Tytuł strony"/>
                                </div>

                                <div class="form-group">
                                    <textarea id="html_code_add" name="page_content" rows="15"></textarea>
                                </div>

                                <div class="form-group checkbox-group">
                                    <label>
                                        <input type="checkbox" name="status" value="1" checked /> Aktywna
                                    </label>
                                </div>

                                <div class="form-group">
                                    <input type="submit" name="add_submit" value="Utwórz" onclick="editor_add.save()" />
                                </div>
                            </form>

                            <script>
                                var editor_add = CodeMirror.fromTextArea(document.getElementById("html_code_add"), {
                                lineNumbers: true,
                                mode: "htmlmixed",
                                theme: "ayu-mirage",
                                lineWrapping: true
                                });
                            </script>';

                        return $form;
                    }

                    /**
                    * UsunPodstrone($link, $id) 
                    * Usuwa podstronę o podanym ID z bazy danych.
                    */
                    function UsunPodstrone($link, $id) 
                    {
                        $id = (int)$id;

                        // SQL: Usuwanie danych po ID.
                        // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
                        $query_delete = "DELETE FROM page_list WHERE id = $id LIMIT 1"; 
            
                        if (mysqli_query($link, $query_delete)) 
                        {
                            return '<p style="color: green;">Strona o ID: ' . $id . ' została usunięta.</p>';
                        } else {
                            return '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
                        }
                    }

                ?>
            </main>
        </div>
    </body>
</html>