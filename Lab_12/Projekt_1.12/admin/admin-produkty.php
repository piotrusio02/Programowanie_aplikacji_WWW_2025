<!-- 
Moduł obsługujący zarządzanie produktami w panelu admina.
Wyświetla produkty, pozwala dodać produkt do listy,
usunać produkt z listy oraz edytować produkt. 
Obsługuje funkcje filtrowania po kategoriach produktu.
-->

<?php

    /**
     * PokazProdukty($link)
     * Pobiera i wyświetla listę produktów. Pozwala na filtorwanie po kategoriach i podkategoriach
     */
    function PokazProdukty($link) {

        DodajProdukt($link);
        EdytujProdukt($link);

        if (isset($_GET['action']) && ($_GET['action'] == 'add_prod' || $_GET['action'] == 'edit_prod')) {
            return; 
        }

        // Filtrowanie wyników
        $kat_id = isset($_GET['filtr_kat']) ? (int)$_GET['filtr_kat'] : 0;
        $podkat_id = isset($_GET['filtr_podkat']) ? (int)$_GET['filtr_podkat'] : 0;
        $where = "";

        if ($kat_id == 0) {
            $podkat_id = 0;
        }

        if ($podkat_id > 0) {
            $where = " WHERE kategoria = $podkat_id";
        } elseif ($kat_id > 0) {
            $where = " WHERE kategoria = $kat_id OR kategoria IN (SELECT id FROM kategorie WHERE matka = $kat_id)";
        }

        // Interfejs
        echo '
            <div class="panel-sklep">
                <div class="filtry-produkty">
                    <div class="sekcja-filtrow">
                        <form method="get" action="admin.php">
                            <input type="hidden" name="action" value="sklep">

                            <label>Kategoria: </label>
                            <select name="filtr_kat" onchange="const p = this.form.filtr_podkat; if(p) p.value=\'\'; this.form.submit();" class="pole-wyboru">
                                <option value="">Wszystkie produkty</option> '. PobierzOpcjeKategoriiAdmin($link, $kat_id, 0) .'
                            </select>';

        $disabled = ($kat_id == 0) ? 'disabled' : '';

        echo '
                            <label style="margin-left:15px;">Podkategoria: </label>
                            <select name="filtr_podkat" onchange="this.form.submit()" class="pole-wyboru" '.$disabled.'>
                                <option value="">Wszystkie podkategorie</option>';

        if ($kat_id > 0) {
        echo PobierzOpcjeKategoriiAdmin($link, $podkat_id, $kat_id);
        }

        echo '
                            </select>
                        </form>
                    </div>
                    <a href="admin.php?action=add_prod" class="btn-sklep">Dodaj Nowy Produkt</a>
                </div>';

        echo '
                <div class="siatka-produktow">';

        $query = "SELECT * FROM produkty $where ORDER BY id DESC";
        $result = mysqli_query($link, $query);

        // Obsługa BLOB
        if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

        $img = '../image/produkty/no-photo-available.png';
        if (!empty($row['zdjecie'])) {
        $img = 'data:image/jpeg;base64,' . base64_encode($row['zdjecie']);
        }

        // karta produktu
        // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
        echo '
                    <div class="karta-produktu">
                        <div class="produkt-foto">
                            <img src="'.$img.'" alt="Produkt">
                        </div>

                        <div class="produkt-tresc">
                            <h3>'.htmlspecialchars($row['tytul']).'</h3>
                            <p class="cena">'.htmlspecialchars($row['cena_netto']).' PLN <small>+ VAT</small></p>
                            <p class="stan-magazynowy">Dostępność: <b>'.htmlspecialchars($row['ilosc_sztuk']).' szt.</b></p>
                        </div>

                        <div class="produkt-przyciski">
                            <a href="admin.php?action=edit_prod&id='.$row['id'].'" class="ikona-edycji">
                                <img src="../image/ikony/edit-icon.svg" alt="Edytuj">
                            </a>
                            <a href="admin.php?action=delete_prod&id='.$row['id'].'" class="ikona-usun" onclick="return confirm(\'Czy na pewno chcesz usunąć ten produkt?\')">
                                <img src="../image/ikony/delete-icon.svg" alt="Usuń">
                            </a>
                        </div>
                    </div>';
        }
        } else {
        echo '<p class="brak-danych">Brak produktów do wyświetlenia.</p>';
        }

        echo '
                </div>
            </div>'; 
    }

    /**
    * DodajProdukt($link)
    * Dodaje nowy produkt do bazy danych
    */
    function DodajProdukt($link) {

        if (isset($_POST['add_prod_submit'])) {
            $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
            $opis = mysqli_real_escape_string($link, $_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $podatek_vat = (float)$_POST['podatek_vat'];
            $ilosc_sztuk = (int)$_POST['ilosc_sztuk'];
            $gabaryt = mysqli_real_escape_string($link, $_POST['gabaryt']);
            $data_wygasniecia = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);

            $kategoria = (int)$_POST['filtr_podkat'];

            $status_dostepnosci = ($ilosc_sztuk > 0) ? 1 : 0;
            $zdjecie_blob = NULL;

            if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] == 0) {
                $zdjecie_blob = addslashes(file_get_contents($_FILES['zdjecie']['tmp_name']));
            }

            $query = "INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt_produktu, zdjecie, data_utworzenia, data_wygasniecia) 
            VALUES ('$tytul', '$opis', $cena_netto, $podatek_vat, $ilosc_sztuk, $status_dostepnosci, $kategoria, '$gabaryt', '$zdjecie_blob', NOW(), '$data_wygasniecia')";

            if (mysqli_query($link, $query)) {
                echo '<script>window.location.href="admin.php?action=sklep";</script>';
                exit;
            }
        }

        // Formularz 
        // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
        if (isset($_GET['action']) && $_GET['action'] == 'add_prod') {

            $kat_id = isset($_POST['filtr_kat']) ? (int)$_POST['filtr_kat'] : 0;
            $podkat_id = isset($_POST['filtr_podkat']) ? (int)$_POST['filtr_podkat'] : 0;

            $v_tytul = isset($_POST['tytul']) ? htmlspecialchars($_POST['tytul']) : '';
            $v_opis = isset($_POST['opis']) ? htmlspecialchars($_POST['opis']) : '';
            $v_cena = isset($_POST['cena_netto']) ? $_POST['cena_netto'] : '';
            $v_vat = isset($_POST['podatek_vat']) ? $_POST['podatek_vat'] : '23.00';
            $v_ilosc = isset($_POST['ilosc_sztuk']) ? $_POST['ilosc_sztuk'] : '1';
            $v_gabaryt = isset($_POST['gabaryt']) ? htmlspecialchars($_POST['gabaryt']) : '';
            $v_data = isset($_POST['data_wygasniecia']) ? $_POST['data_wygasniecia'] : date('Y-m-d');

            // Blokowanie dodania produktu, gdy podkategoria nie jest wybrana
            $przycisk_blokada = ($podkat_id == 0) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '';
            $napis_przycisku = ($podkat_id == 0) ? 'WYBIERZ PODKATEGORIĘ' : 'UTWÓRZ PRODUKT';

            echo '
                <div class="edycja-kontener">

                    <form method="post" action="admin.php?action=add_prod" class="formularz-sklepowy" enctype="multipart/form-data">

                        <div class="grupa-form">
                            <label>Nazwa produktu:</label>
                            <input type="text" name="tytul" value="'.$v_tytul.'" required>
                        </div>

                        <div class="grupa-form">
                            <label>Opis:</label>
                            <textarea id="edytor_opis" name="opis">'.$v_opis.'</textarea>
                        </div>

                        <div class="wiersz-form">
                            <div class="grupa-form">
                                <label>Kategoria główna:</label>
                                <select name="filtr_kat" onchange="this.form.submit();" class="pole-wyboru">
                                    <option value="0">-- Wybierz --</option>
                                    '. PobierzOpcjeKategoriiAdmin($link, $kat_id, 0) .'
                                </select>
                            </div>';

            $disabled_select = ($kat_id == 0) ? 'disabled' : '';
            echo '
                            <div class="grupa-form">
                                <label>Podkategoria (wymagana):</label>
                                <select name="filtr_podkat" onchange="this.form.submit();" class="pole-wyboru" '.$disabled_select.'>
                                    <option value="0">-- Wybierz podkategorię --</option>';
            if ($kat_id > 0) echo PobierzOpcjeKategoriiAdmin($link, $podkat_id, $kat_id);
            echo '
                                        </select>
                                </div>
                            </div>

                            <div class="wiersz-form">
                            <div class="grupa-form"><label>Cena netto:</label><input type="number" step="0.01" name="cena_netto" value="'.$v_cena.'" required></div>
                            <div class="grupa-form"><label>VAT (%):</label><input type="number" step="0.01" name="podatek_vat" value="'.$v_vat.'"></div>
                            <div class="grupa-form"><label>Ilość:</label><input type="number" name="ilosc_sztuk" value="'.$v_ilosc.'"></div>
                        </div>

                        <div class="wiersz-form">
                            <div class="grupa-form"><label>Gabaryt:</label><input type="text" name="gabaryt" value="'.$v_gabaryt.'"></div>
                            <div class="grupa-form"><label>Data wygaśnięcia:</label><input type="date" name="data_wygasniecia" required value="'.$v_data.'"></div>
                        </div>

                        <div class="grupa-form">
                            <label>Zdjęcie:</label>
                            <input type="file" name="zdjecie">
                        </div>

                        <div class="przyciski-form">
                            <input type="submit" name="add_prod_submit" value="'.$napis_przycisku.'" class="btn-zapisz" '.$przycisk_blokada.' onclick="editor.save()">
                            <a href="admin.php?action=sklep" class="main-style-button">Anuluj</a>
                        </div>
                    </form>
                </div>';
            }
    }

    /**
    * EdytujProdukt($link)
    * Edytuje dane produktu i zapisuje zmiany do bazy
    */
    function EdytujProdukt($link) {

        if (isset($_POST['edit_prod_submit'])) {
            $id_edycji = (int)$_POST['id_produktu'];
            $tytul = mysqli_real_escape_string($link, $_POST['tytul']);
            $opis = mysqli_real_escape_string($link, $_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $podatek_vat = (float)$_POST['podatek_vat'];
            $ilosc_sztuk = (int)$_POST['ilosc_sztuk'];
            $gabaryt = mysqli_real_escape_string($link, $_POST['gabaryt']);
            $data_wygasniecia = mysqli_real_escape_string($link, $_POST['data_wygasniecia']);

            if (!isset($_POST['filtr_podkat']) || (int)$_POST['filtr_podkat'] == 0) {
                echo '<script>alert("Błąd: Musisz wybrać podkategorię!"); history.back();</script>';
                exit;
            }

            $kategoria = (int)$_POST['filtr_podkat'];
            $status_dostepnosci = ($ilosc_sztuk > 0) ? 1 : 0;

            $foto_query = "";
            if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] == 0) {
                $zdjecie_blob = addslashes(file_get_contents($_FILES['zdjecie']['tmp_name']));
                $foto_query = ", zdjecie = '$zdjecie_blob'";
            }

            // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
            $query = "UPDATE produkty SET tytul = '$tytul', opis = '$opis', cena_netto = $cena_netto, 
            podatek_vat = $podatek_vat, ilosc_sztuk = $ilosc_sztuk, status_dostepnosci = $status_dostepnosci, 
            kategoria = $kategoria, gabaryt_produktu = '$gabaryt', data_wygasniecia = '$data_wygasniecia', 
            data_modyfikacji = NOW() $foto_query WHERE id = $id_edycji LIMIT 1";

            if (mysqli_query($link, $query)) {
                echo '<script>window.location.href="admin.php?action=sklep";</script>';
                exit;
            }
        }

        // Formularz
        // LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
        // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
        if (isset($_GET['action']) && $_GET['action'] == 'edit_prod' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $res = mysqli_query($link, "SELECT * FROM produkty WHERE id = $id LIMIT 1");
            $row = mysqli_fetch_assoc($res);

            if ($row) {
                $v_tytul = isset($_POST['tytul']) ? htmlspecialchars($_POST['tytul']) : htmlspecialchars($row['tytul']);
                $v_opis = isset($_POST['opis'])  ? htmlspecialchars($_POST['opis'])  : htmlspecialchars($row['opis']);
                $v_cena = isset($_POST['cena_netto']) ? $_POST['cena_netto'] : $row['cena_netto'];
                $v_vat = isset($_POST['podatek_vat']) ? $_POST['podatek_vat'] : $row['podatek_vat'];
                $v_ilosc = isset($_POST['ilosc_sztuk']) ? $_POST['ilosc_sztuk'] : $row['ilosc_sztuk'];
                $v_gabaryt = isset($_POST['gabaryt']) ? htmlspecialchars($_POST['gabaryt']) : htmlspecialchars($row['gabaryt_produktu']);
                $v_data = isset($_POST['data_wygasniecia']) ? $_POST['data_wygasniecia'] : $row['data_wygasniecia'];

                if (isset($_POST['filtr_kat'])) {
                    $kat_id = (int)$_POST['filtr_kat'];
                    $podkat_id = isset($_POST['filtr_podkat']) ? (int)$_POST['filtr_podkat'] : 0;
                } else {
                    $curr_kat = (int)$row['kategoria'];
                    $check = mysqli_query($link, "SELECT matka FROM kategorie WHERE id = $curr_kat LIMIT 1");
                    $c_row = mysqli_fetch_assoc($check);

                    if ($c_row && $c_row['matka'] != 0) {
                        $kat_id = $c_row['matka'];
                        $podkat_id = $curr_kat;
                    } else {
                        $kat_id = $curr_kat;
                        $podkat_id = 0;
                    }
                }

                // Blokowanie zapisania produktu, gdy podkategoria nie jest wybrana
                $przycisk_blokada = ($podkat_id == 0) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '';
                $napis_przycisku = ($podkat_id == 0) ? 'WYBIERZ PODKATEGORIĘ' : 'ZAPISZ ZMIANY';

                echo '
                    <div class="edycja-kontener">
                        <form method="post" action="admin.php?action=edit_prod&id='.$id.'" class="formularz-sklepowy" enctype="multipart/form-data">
                            <input type="hidden" name="id_produktu" value="'.$id.'">

                            <div class="grupa-form">
                                <label>Nazwa produktu:</label>
                                <input type="text" name="tytul" value="'.$v_tytul.'" required>
                            </div>

                            <div class="grupa-form">
                                <label>Opis:</label>
                                <textarea id="edytor_opis_edit" name="opis">'.$v_opis.'</textarea>
                            </div>

                            <div class="wiersz-form">
                                <div class="grupa-form">
                                    <label>Kategoria główna:</label>
                                        <select name="filtr_kat" onchange="const p = this.form.filtr_podkat; if(p) p.value=\'\'; this.form.submit();" class="pole-wyboru">
                                            <option value="0">-- Wybierz --</option>
                                            '. PobierzOpcjeKategoriiAdmin($link, $kat_id, 0) .'
                                        </select>
                                </div>';

                $disabled_select = ($kat_id == 0) ? 'disabled' : '';
                echo '
                                <div class="grupa-form">
                                    <label>Podkategoria (wymagana):</label>
                                    <select name="filtr_podkat" onchange="this.form.submit();" class="pole-wyboru" '.$disabled_select.'>
                                    <option value="0">-- Wybierz podkategorię --</option>';
                                
                if ($kat_id > 0) echo PobierzOpcjeKategoriiAdmin($link, $podkat_id, $kat_id);
                echo '
                                        </select>
                                </div>
                            </div>

                            <div class="wiersz-form">
                                <div class="grupa-form"><label>Cena netto:</label><input type="number" step="0.01" name="cena_netto" value="'.$v_cena.'" required></div>
                                <div class="grupa-form"><label>VAT (%):</label><input type="number" step="0.01" name="podatek_vat" value="'.$v_vat.'"></div>
                                <div class="grupa-form"><label>Ilość:</label><input type="number" name="ilosc_sztuk" value="'.$v_ilosc.'"></div>
                            </div>

                            <div class="wiersz-form">
                                <div class="grupa-form"><label>Gabaryt:</label><input type="text" name="gabaryt" value="'.$v_gabaryt.'"></div>
                                <div class="grupa-form"><label>Data wygaśnięcia:</label><input type="date" name="data_wygasniecia" value="'.$v_data.'" required></div>
                            </div>

                            <div class="grupa-form">
                                <label>Zdjęcie:</label><br>
                                '.(!empty($row['zdjecie']) ? '<img src="data:image/jpeg;base64,'.base64_encode($row['zdjecie']).'" style="max-height:100px; margin-bottom:10px;"><br>' : '').'
                                <input type="file" name="zdjecie">
                            </div>

                            <div class="przyciski-form">
                                <input type="submit" name="edit_prod_submit" value="'.$napis_przycisku.'" class="btn-zapisz" '.$przycisk_blokada.' onclick="editor_edit.save()">
                                <a href="admin.php?action=sklep" class="main-style-button">Anuluj</a>
                            </div>
                        </form>
                    </div>';
            }
        }
    }

    /**
    * UsunProdukt($link) 
    * Usuwa Produkt z bazy danych
    * LIMIT 1 - Zapewnienie dla bezpieczeństwa i optymalizacji.
    */
    function UsunProdukt($link, $id) {
    
        $id = (int)$id;
        $query_delete = "DELETE FROM produkty WHERE id = $id LIMIT 1"; 

        if (mysqli_query($link, $query_delete)) {
            echo "<script>window.location.href='admin.php?action=sklep';</script>";
            exit;
        }
    }

    /**
     * PobierzOpcjeKategoriiAdmin()
     * Metoda Pomocnicza do generowania listy rozwijanej kategorii lub podkategorii
     * htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
     */
    function PobierzOpcjeKategoriiAdmin($link, $selected_id, $parent_id = 0) {

        $options = "";
        $where = "WHERE matka = " . (int)$parent_id;

        $query = "SELECT id, nazwa FROM kategorie $where ORDER BY nazwa ASC";
        $res = mysqli_query($link, $query);

        while ($row = mysqli_fetch_assoc($res)) {
            $sel = ($row['id'] == $selected_id) ? 'selected' : '';
            $options .= '<option value="'.$row['id'].'" '.$sel.'>'.htmlspecialchars($row['nazwa']).'</option>';
        }
        return $options;
    }
?>