<?php           
              
    /**
    * PokazKategorieSklepu($link)
    * Pobiera i wyświetla listę stworzonych kategorii oraz podkategorii.
    */
    function PokazKategorieSklepu($link) {

        DodajKategorie($link);
        EdytujKategorieLogika($link);

        echo '<div class="admin-panel-sklep">';

        echo '<h2 class="naglowek-sekcji" onclick="toggleManagera()">
            KATEGORIE <span id="ikona-zwijania">▲</span> </h2>';

        echo '<div id="kontener-sklepu" class="sklep-kolumny">';

        // Lista kategorii
        echo '<div class="kolumna-lista">';
        echo '<div class="scroll-lista">';

        $res_parents = mysqli_query($link, "SELECT * FROM kategorie WHERE matka = 0 ORDER BY id ASC");

        while ($parent = mysqli_fetch_assoc($res_parents)) {
            $parent_id = (int)$parent['id'];

            // Zliczanie podkategorii
            $res_count = mysqli_query($link, "SELECT COUNT(*) as ile FROM kategorie WHERE matka = $parent_id");
            $row_count = mysqli_fetch_assoc($res_count);
            $ile_pod = $row_count['ile'];

            // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
            echo '
                <div class="grupa-kategorii">
                    <div class="kat-glowna" onclick="togglePodkat('.$parent_id.')">
                        <div class="kat-info">
                            <strong>ID: '.$parent_id.'</strong> ➜ '.htmlspecialchars($parent['nazwa']).' 
                            <span class="licznik-podkat">'.$ile_pod.'</span>
                        </div>

                        <div class="akcje" onclick="event.stopPropagation();">
                            <a href="javascript:void(0)" onclick="otworzEdycje('.$parent_id.', \''.addslashes($parent['nazwa']).'\', '.$parent['matka'].')">
                                <img src="../image/edit-icon.svg" alt="Edytuj">
                            </a>
                            <a href="admin.php?action=delete_kat&id='.$parent_id.'" onclick="return confirm(\'Usunąć kategorię i jej podkategorie?\')">
                                <img src="../image/delete-icon.svg" alt="Usuń">
                            </a>
                        </div>
                    </div>';

            echo '<div id="podkat-'.$parent_id.'" class="lista-podkategorii" style="display: none;">';

            $res_children = mysqli_query($link, "SELECT * FROM kategorie WHERE matka = $parent_id ORDER BY id ASC");
        
            while ($child = mysqli_fetch_assoc($res_children)) {
                echo '
                    <div class="element-podkat">
                        <span><span class="strzalka-pod">↪</span> ID: '.$child['id'].' ─ '.htmlspecialchars($child['nazwa']).'</span>
                        <div class="akcje">
                            <a href="javascript:void(0)" onclick="otworzEdycje('.$child['id'].', \''.addslashes($child['nazwa']).'\', '.$child['matka'].')">
                                <img src="../image/edit-icon.svg" alt="Edytuj">
                            </a>
                            <a href="admin.php?action=delete_kat&id='.$child['id'].'" onclick="return confirm(\'Usunąć podkategorię?\')">
                                <img src="../image/delete-icon.svg" alt="Usuń">
                            </a>
                        </div>
                    </div>';
            }
                echo '</div></div>';
        }   

        echo '</div>';
        echo '</div>';

        // Formularz dodawania kategorii i podkategorii
        echo '<div class="kolumna-formularz">';
        echo '<h3>Dodaj nową</h3>';
        echo '
            <form method="post" action="admin.php?action=sklep" class="form-sklepowy">
                <div class="pole-form">
                    <label>ID Rodzica:</label>
                    <input type="number" name="cat_mother" value="0" required />
                    <small>0 = kategoria główna</small>
                </div>
                <div class="pole-form">
                    <label>Nazwa:</label>
                    <input type="text" name="cat_name" required />
                </div>
                    <input type="submit" name="add_cat_submit" value="DODAJ KATEGORIĘ" class="btn-sklep" />
            </form>';
        echo '</div>';

        echo '</div>';
        echo '</div>';

        echo '<a class="main-style-button" href="admin.php">Wróć do podstron</a>';

        // Skrypt do zwijania i rozwijania panelu 
        echo '
            <script>
                function togglePodkat(id) {
                    var el = document.getElementById("podkat-" + id);
                    el.style.display = (el.style.display === "none") ? "block" : "none";
                }

                function toggleManagera() {
                    var body = document.getElementById("kontener-sklepu");
                    var icon = document.getElementById("ikona-zwijania");
                    if (body.style.display === "none") {
                        body.style.display = "flex";
                        icon.innerText = "▲";
                    } else {
                        body.style.display = "none";
                        icon.innerText = "▼";
                    }
                }
            </script>';
    }

    // Okno modalne do edycji kategorii w panelu administratora
    echo '
        <div id="editModal" class="okno-modalne-tlo">
            <div class="okno-modalne-tresc">
                <span class="zamknij-modal" onclick="zamknijEdycje()">&times;</span>
                <h3 class="naglowek-modal">Edytuj Kategorię</h3>

                <form method="post" action="admin.php?action=sklep" class="form-sklepowy">
                    <input type="hidden" id="edit_id" name="cat_id" value="" />

                    <div class="pole-form">
                        <label>ID Matki (rodzica):</label>
                        <input type="number" id="edit_mother" name="cat_mother" required />
                    </div>

                    <div class="pole-form">
                        <label>Nazwa kategorii:</label>
                        <input type="text" id="edit_name" name="cat_name" required />
                    </div>

                    <input type="submit" name="edit_cat_submit" value="ZAPISZ ZMIANY" class="btn-sklep" />
                </form>
            </div>
        </div>';


    /**
    * DodajKategorie($link)
    * Dodaje nową kategorię lub podkategorię w zależności od podanego ID rodzica
    */
    function DodajKategorie($link) {

        if (isset($_POST['add_cat_submit'])) {
            $nazwa = mysqli_real_escape_string($link, $_POST['cat_name']);
            $matka = (int)$_POST['cat_mother'];

            $query = "INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', $matka) LIMIT 1";

            if (mysqli_query($link, $query)) {
                echo "<script>window.location.href='admin.php?action=sklep';</script>";
                exit;
            }
        }
    }

    /**
    * EdytujKategorieLogika($link)
    * Wyświetla okno modalne umożliwiające edycję nazwy oraz ID rodzica danej kategorii
    */
    function EdytujKategorieLogika($link) {
        if (isset($_POST['edit_cat_submit'])) {
            $id = (int)$_POST['cat_id'];
            $nazwa = mysqli_real_escape_string($link, $_POST['cat_name']);
            $matka = (int)$_POST['cat_mother'];

            $query = "UPDATE kategorie SET nazwa='$nazwa', matka=$matka WHERE id=$id LIMIT 1";

            if (mysqli_query($link, $query)) {
                echo "<script>window.location.href='admin.php?action=sklep';</script>";
                exit;
            }
        }
    }

    /**
    * UsunKategorie($link, $id) 
    * Usuwa kategorię lub podkategorię o podanym ID z bazy danych.
    */
    function UsunKategorie($link, $id) {
        $id = (int)$id;

        $query_delete = "DELETE FROM kategorie WHERE id = $id OR matka = $id"; 

        if (mysqli_query($link, $query_delete)) 
        {
            echo "<script>window.location.href='admin.php?action=sklep';</script>";
            exit;
        }
    }