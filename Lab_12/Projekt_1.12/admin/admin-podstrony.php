<!-- 
Moduł obsługujący zarządzanie podstronami.
Wyświetla listę podstron, pozwala na dodanie i edycję podstrony, 
oraz umożliwia usunięcie lub wygaszenie podstrony.
-->

<?php

    /**
    * ListaPodstron($link)
    * Pobiera i wyświetla listę posortowanych podstron w panelu administracyjnym.
    */
    function ListaPodstron($link) {

    $query = "SELECT id, page_title, status FROM page_list ORDER BY status DESC";
    $result = mysqli_query($link, $query); 

        echo '<ul class="page-list">';
        while ($row = mysqli_fetch_array($result)) {
            echo '<li>';
            echo '<div class="page-info">';

            // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
            echo htmlspecialchars($row['id']);
            if ($row['status'] == 0) {
                // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
                echo '. <span style="color: gray;">' . htmlspecialchars($row['page_title']) . ' (NIEAKTYWNE)</span>';
            } else {
                // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
                echo '. <span>' . htmlspecialchars($row['page_title']) . '</span>';
            }

            echo '</div>';
            echo '<div class="action-icons">';
            echo '<a href="admin.php?action=edit&id=' . $row['id'] . '"><img src="../image/ikony/edit-icon.svg" alt="Edytuj"></a>';
            echo '<a href="admin.php?action=delete&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć stronę o ID: ' . $row['id'] . '?\')"><img src="../image/ikony/delete-icon.svg" alt="Usuń"></a>';
            echo '</div>';
            echo '</li>';
        }

        echo '</ul>';
        echo '<a class="main-style-button" href="admin.php?action=add">Dodaj Podstronę</a>';
        echo '<a class="main-style-button" href="admin.php?action=sklep">Zarządzaj Sklepem</a>';
        echo '<a class="logout-button"href="admin.php?action=logout">Wyloguj</a>';
    }


    /**
    * EdytujPodstrone($link, $id)
    * Pobiera dane podstrony, wyświetla formularz edycji oraz zapisuje zmiany.
    */
    function EdytujPodstrone($link, $id) {

        $id = (int)$id;

        // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
        $query_select = "SELECT * FROM page_list WHERE id = $id LIMIT 1"; 
        $result = mysqli_query($link, $query_select);
        $data = mysqli_fetch_array($result);
        if (!$data) {
            return '<p style="color: red;">Nie znaleziono strony o podanym ID.</p>';
        }

        // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
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
    function UsunPodstrone($link, $id) {

        $id = (int)$id;

        // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
        $query_delete = "DELETE FROM page_list WHERE id = $id LIMIT 1"; 

        if (mysqli_query($link, $query_delete)) {
            return '<p style="color: green;">Strona o ID: ' . $id . ' została usunięta.</p>';
        } else {
            return '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>';
        }
    }

    /**
    * ObslugaPodstron($link) 
    * Funkcja do obsługi podstron w panelu administratora
    */
    function ObslugaPodstron($link) {

        global $action, $id, $content;

        // Obsługa edycji podstrony.
        if (isset($_POST['edit_submit'])) {
            $id_update = isset($_POST['id_edycji']) ? (int)$_POST['id_edycji'] : 0;

            $new_title = mysqli_real_escape_string($link, $_POST['page_title']);
            $new_content = mysqli_real_escape_string($link, $_POST['page_content']);
            $new_status = isset($_POST['status']) ? 1 : 0;

            if (empty($new_title) || empty($new_content)) {
                $content = '<p style="color: red;">Pola nie mogą być puste!</p>';
                $action = 'edit';
                $id = $id_update;
            } else {

                // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
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

        // Obsługa dodawania podstrony.
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
    }