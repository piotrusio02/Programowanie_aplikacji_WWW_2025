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
                    include '../cfg.php';

                    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
                        session_destroy();
                        header('Location: admin.php');
                        exit();
                    }

                    function FormularzLogowania() 
                    {
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
                        </div>';
            
                        return $wynik;
                    }

                    $error_message = '';

                    if (!isset($_SESSION['logged_in']))
                    {
                        if((isset($_POST['xl_submit'])))
                        {
                            $input_login = isset($_POST['login_email']) ? trim($_POST['login_email']) : '';
                            $input_pass = isset($_POST['login_pass']) ? trim($_POST['login_pass']) : '';
                            if ($input_login === $login && $input_pass === $pass) 
                            {
                                $_SESSION['logged_in'] = true;
                                header('Location: ' . $_SERVER['REQUEST_URI']);
                                exit();

                            } else {
                                $error_message = '<p style="color: red;">Podano nieprawidłowy e-mail lub hasło.</p>';
                            }
                        }
                    }

                    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) 
                    {
                        $action = isset($_GET['action']) ? $_GET['action'] : '';
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                        $content = '';

                        if (isset($_POST['edit_submit'])) 
                        {
                            $id_update = isset($_POST['id_edycji']) ? (int)$_POST['id_edycji'] : 0;
                            $new_title = mysqli_real_escape_string($link, $_POST['page_title']);
                            $new_content = mysqli_real_escape_string($link, $_POST['page_content']);
                            $new_status = isset($_POST['status']) ? 1 : 0;
                        
                            if (empty($new_title) || empty($new_content)) 
                            {
                                $content = '<p style="color: red;">Pola nie mogą być puste!</p>';
                                $action = 'edit';
                                $id = $id_update;
                            } else {
                                $query_update = "UPDATE page_list SET 
                                page_title = '$new_title', 
                                page_content = '$new_content', 
                                status = $new_status 
                                WHERE id = $id_update LIMIT 1";

                                if (mysqli_query($link, $query_update)) {
                                    $content = '<p style="color: green;">Strona o ID ' . $id_update . ' została zaktualizowana.</p>';
                                    $action = '';
                                } else {
                                    $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
                                }
                            }
                        }
                        if (isset($_POST['add_submit'])) {
                            $new_title = mysqli_real_escape_string($link, $_POST['page_title']);
                            $new_content = mysqli_real_escape_string($link, $_POST['page_content']);
                            $new_status = isset($_POST['status']) ? 1 : 0;
                
                            if (empty($new_title) || empty($new_content)) {
                                $content = '<p style="color: red;">Pola nie mogą być puste!</p>';
                                $action = 'add';
                            } else {
                                $query_insert = "INSERT INTO page_list (page_title, page_content, status) 
                                VALUES ('$new_title', '$new_content', $new_status)";
                
                                if (mysqli_query($link, $query_insert)) {
                                    $content = '<p style="color: green;">Utworzono nową stronę o ID: ' . mysqli_insert_id($link) . '.</p>';
                                    $action = '';
                                } else {
                                    $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
                                }
                            }
                        }
                        if ($action === 'edit' && $id > 0) {
                            echo $content;
                            echo EdytujPodstrone($link, $id);
                        } elseif ($action === 'add') {
                            echo $content;
                            echo DodajNowaPodstrone();
                        } elseif ($action === 'delete' && $id > 0) {
                            echo '<h1 style="margin: 30px;">Podstrony</h1>';
                            echo UsunPodstrone($link, $id); 
                            ListaPodstron($link);
                        } else {
                            echo '<h1 style="margin: 30px;">Podstrony</h1>';
                            echo $content;
                            ListaPodstron($link);
                        }
            
                    } else {
                        echo $error_message; 
                        echo FormularzLogowania();
                    }


                    function ListaPodstron($link) 
                    {

                        $query = "SELECT id, page_title, status FROM page_list ORDER BY status DESC";
                        $result = mysqli_query($link, $query); 

                        echo '<ul class="page-list">';
                        while ($row = mysqli_fetch_array($result)) 
                        {
                            echo '<li>';
                            echo '<div class="page-info">';
                            echo htmlspecialchars($row['id']);
                            if ($row['status'] == 0) 
                            {
                                echo '. <span style="color: gray;">' . htmlspecialchars($row['page_title']) . ' (NIEAKTYWNE)</span>';
                            } else 
                            {
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


                    function EdytujPodstrone($link, $id)
                    {
                        $id = (int)$id;
                        $query_select = "SELECT * FROM page_list WHERE id = $id LIMIT 1"; 
                        $result = mysqli_query($link, $query_select);
                        $data = mysqli_fetch_array($result);
                        if (!$data) 
                        {
                            return '<p style="color: red;">Nie znaleziono strony o podanym ID.</p>';
                        }
            
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

                    function UsunPodstrone($link, $id) 
                    {
                        
                        $id = (int)$id;
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