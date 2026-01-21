<!-- 
Moduł obsługujący widok sklepu na stronie.
Wyświetla listę produktów, pozwala dodać produkt do koszuka.
Obsługuje funkcje filtrowania po kategoriach produktu.
-->

<?php

    /**
     * PokazSklep($link)
     * Pobiera i wyświetla listę produktów. Pozwala na filtorwanie po kategoriach i podkategoriach
     */
    function PokazSklep($link) {
        $kat_id = isset($_GET['filtr_kat']) ? (int)$_GET['filtr_kat'] : 0;
        $podkat_id = isset($_GET['filtr_podkat']) ? (int)$_GET['filtr_podkat'] : 0;
    
        $where_sql = " WHERE status_dostepnosci = 1 AND ilosc_sztuk > 0";

        if ($podkat_id > 0) {
            $where_sql .= " AND kategoria = $podkat_id";
        } elseif ($kat_id > 0) {
            $where_sql .= " AND (kategoria = $kat_id OR kategoria IN (SELECT id FROM kategorie WHERE matka = $kat_id))";
        }

        // Filtry produktów
        echo '
            <div class="filtry-produkty">
                <form method="GET" action="index.php">
                    <input type="hidden" name="id" value="8"> 
                    <div class="filtry-produkty-grupa">
                        <label class="etykieta-filtra"><b>Kategoria: </b></label>
                        <select name="filtr_kat" class="filtry-produkty-select" onchange="this.form.filtr_podkat.value=0; this.form.submit();">
                            <option value="0">Wszystkie produkty</option> 
                            '. PobierzOpcjeKategorii($link, $kat_id, 0) .'
                        </select>
                    </div>';

        $disabled = ($kat_id == 0) ? 'disabled' : '';

        echo '
                    <div class="filtry-produkty-grupa">
                        <label class="etykieta-filtra"><b>Podkategoria:</b></label>
                        <select name="filtr_podkat" class="filtry-produkty-select" onchange="this.form.submit()" '.$disabled.'>
                            <option value="0">Wszystkie podkategorie</option>';

        if ($kat_id > 0) {
            echo PobierzOpcjeKategorii($link, $podkat_id, $kat_id);
        }

        echo '
                        </select>
                    </div>
                    <a href="index.php?id=koszyk" class="btn-pokaz-koszyk">
                        <img src="../image/ikony/cart.png" alt="">
                    </a>
                </form>
            </div>';

        // Lista produktów
        $query = "SELECT * FROM produkty $where_sql ORDER BY data_utworzenia DESC";
        $result = mysqli_query($link, $query);

        echo '<div class="lista-produktow">'; 

        if ($result && mysqli_num_rows($result) > 0) {
            while ($kolumna = mysqli_fetch_assoc($result)) {
                $img = (!empty($kolumna['zdjecie'])) ? 'data:image/jpeg;base64,' . base64_encode($kolumna['zdjecie']) : '../image/produkty/no-photo-available.png';
                $cena_brutto = $kolumna['cena_netto'] * (1 + $kolumna['podatek_vat'] / 100);
            
                // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
                echo '
                    <div class="karta-produktu">
                        <div class="karta-produktu-foto">
                            <img src="'.$img.'">
                        </div>
                        <div class="karta-produktu-info">
                            <div class="karta-produktu-tekst">
                                <h3 class="karta-produktu-nazwa">'.htmlspecialchars($kolumna['tytul']).'</h3>
                                <p class="karta-produktu-opis">'.htmlspecialchars($kolumna['opis']).'</p>
                            </div>
                            <div class="karta-produktu-koszyk">
                                <div class="dostepnosc">Dostępność:&nbsp;<b>'.$kolumna['ilosc_sztuk'].' szt.</b></div>
                                    <div class="zakup">
                                        <p class="cena-sklep"><b>'.number_format($cena_brutto, 2, ',', ' ').'</b> PLN</p>
                                        <form method="POST" action="index.php?id=8&action=add">
                                            <input type="hidden" name="id_prod" value="'.$kolumna['id'].'">
                                            <input type="number" class="input-ilosc" name="ile_sztuk" value="1" min="1" max="'.$kolumna['ilosc_sztuk'].'">
                                            <button type="submit" class="btn-dodaj">Dodaj</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>';
             }
        } else {
            echo '
                <p style="padding: 40px; text-align: center; color: #77766faf;">Brak produktów do wyświetlenia.</p>';
        }
            echo '</div>';
    }

    /**
     * PobierzOpcjeKategorii()
     * Metoda Pomocnicza do generowania listy rozwijanej kategorii lub podkategorii.
     * htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
     */
    function PobierzOpcjeKategorii($link, $selected_id, $parent_id = 0) {
        
        $options = "";
        $where = "WHERE matka = " . (int)$parent_id;
        $query = "SELECT id, nazwa FROM kategorie $where ORDER BY nazwa ASC";
        $res = mysqli_query($link, $query);
        
        while ($kolumna = mysqli_fetch_assoc($res)) {
            $sel = ($kolumna['id'] == $selected_id) ? 'selected' : '';
            $options .= '<option value="'.$kolumna['id'].'" '.$sel.'>'.htmlspecialchars($kolumna['nazwa']).'</option>';
        }
        return $options;
    }


    /**
     * PokazKoszyk($link)
     * Wyświetla dynamiczną listę produktów dodanych do koszyka. 
     * Zlicza dodaną ilość produktu oraz podlicza cenę za cały koszyk.
     * Umożliwia usunięcie produktu z koszyka.
     */
    function PokazKoszyk($link) {
        
        echo '<h2>Twój Koszyk</h2>';

        $pusty_koszyk = true;
        if (isset($_SESSION['count']) && $_SESSION['count'] > 0) {
            for ($i = 1; $i <= $_SESSION['count']; $i++) {
                if (isset($_SESSION[$i . '_1'])) {
                    $pusty_koszyk = false;
                    break;
                }
            }
        }

        if ($pusty_koszyk) {
            echo '<p style="padding: 40px; text-align: center; color: #77766faf;">Koszyk jest pusty.</p>';
            echo '<a href="index.php?id=8">Wróć do sklepu</a>';
        } else {
        
            echo '
                <table class="tabela-koszyk">
                    <tr>
                        <th>Produkt</th>
                        <th>Cena</th>
                        <th>Ilość</th>
                        <th>Wartość</th>
                        <th></th>
                    </tr>';

            $suma_brutto_koszyka = 0;

            for ($i = 1; $i <= $_SESSION['count']; $i++) {
                if (isset($_SESSION[$i . '_1'])) {
                    $id_produktu = $_SESSION[$i . '_1'];
                    $ile = $_SESSION[$i . '_2'];

                    // LIMIT 1 - Zapewnia pobranie pojedyńczego rekordu.
                    $query = mysqli_query($link, "SELECT tytul, cena_netto, podatek_vat FROM produkty WHERE id = ".(int)$id_produktu." LIMIT 1");
                    $kolumna = mysqli_fetch_assoc($query);

                    $cena_brutto = $kolumna['cena_netto'] * (1 + $kolumna['podatek_vat'] / 100);
                    $wartosc = $cena_brutto * $ile;
                    $suma_brutto_koszyka += $wartosc;

                    // htmlspecialchars() - Zabezpiecza przed wstrzyknięciem kodu.
                    echo '
                        <tr>
                            <td>'.htmlspecialchars($kolumna['tytul']).'</td>
                            <td>'.number_format($cena_brutto, 2).' PLN</td>
                            <td>'.$ile.'</td>
                            <td>'.number_format($wartosc, 2).' PLN</td>
                            <td>
                                <a href="index.php?id=koszyk&action=remove&nr='.$i.'" class="btn-usun-ikona">
                                    <img src="../image/ikony/delete-icon.svg" alt="Usuń" style="width: 20px; height: auto;">
                                </a>
                            </td>
                        </tr>';
                }
            }

            echo '
                <tr>
                    <td colspan="3" align="right"><b>SUMA:</b></td>
                    <td colspan="2"><b>'.number_format($suma_brutto_koszyka, 2).' PLN</b></td>
                </tr>
                </table>';
        
            echo '<br><a href="index.php?id=8" class="btn-powrot">Wróć do sklepu</a>';
        }
    }

    /**
     * AddToCart($link)
     * Dodaje wybraną ilosć produktu do koszyka.
     */
    function addToCart($link, $id_produktu, $ile) {

        // LIMIT 1 - Zapewnia pobranie pojedyńczego rekordu.
        $query = mysqli_query($link, "SELECT ilosc_sztuk FROM produkty WHERE id = ".(int)$id_produktu." LIMIT 1");
        $kolumna = mysqli_fetch_assoc($query);
        $dostepnosc = $kolumna['ilosc_sztuk'];

        if (!isset($_SESSION['count'])) {
            $_SESSION['count'] = 0;
        }

        for ($i = 1; $i <= $_SESSION['count']; $i++) {
            if (isset($_SESSION[$i . '_1']) && $_SESSION[$i . '_1'] == $id_produktu) {
                $nowa_ilosc = $_SESSION[$i . '_2'] + $ile;

                if ($nowa_ilosc > $dostepnosc) {
                    $_SESSION[$i . '_2'] = $dostepnosc;
                } else {
                    $_SESSION[$i . '_2'] = $nowa_ilosc;
                }
                return;
            }
        }

        if ($ile > $dostepnosc) {
            $ile = $dostepnosc;
        }

        $_SESSION['count']++;
        $nr = $_SESSION['count'];

        $_SESSION[$nr . '_1'] = $id_produktu;
        $_SESSION[$nr . '_2'] = $ile;
        $_SESSION[$nr . '_3'] = time();
    }

    /**
     * RemoveFromCart($nr)
     * Usuwa wybrany produkt z koszyka
     */
    function removeFromCart($nr) {
        unset($_SESSION[$nr . '_1']);
        unset($_SESSION[$nr . '_2']);
        unset($_SESSION[$nr . '_3']);
    }
?>