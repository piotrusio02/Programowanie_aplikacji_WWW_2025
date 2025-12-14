<!-- 
Moduł z funkcjami pobierania i wyświetlania treści na stronie.
Pobieranie i wyświetlanie treści podstron.
Generowanie listy dla menu nawigacyjnego.
-->

<?php
    include('cfg.php');         // Łączenie z bazą danych.

    /**
    * PokazPodstrone($id)
    * Pobiera i wyśwwietla treści z bazy (page_content) na podstawie ID.
    * eval() dla ddynamicznej treści PHP dla podstrony kontakt.
    */
    function PokazPodstrone($id)
    {
        global $link;

        // Zabezpiecznenie danych wejsciowych.
        $id_clear = mysqli_real_escape_string($link, $id);

        // SQL: Wybiera treść strony o podanym ID oraza statusie 1 (aktywna).
        // LIMIT 1 - Zapewnia pobranie pojedyńczego rekordu.
        $query = "SELECT page_content FROM page_list WHERE id='$id_clear' AND status=1 LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
    
        $web = '';

        // SPrawdzenie, czy strona została znaleziona.
        if(empty($row['page_content']))
        {
            $web = '[nie_znaleziono_strony]';
            echo $web;
        }
        else
        {
            $tresc_do_wykonania = $row['page_content'];
        
            // eval() - metoda wykorzystana, aby umożliwić wykonanie kodu PHP znajdującego się w bazie dla funkcji PokazKontakt().
            eval('?>'. $tresc_do_wykonania . '<?php');
        }
 
        return;
    }

    /**
    * PokazMenu()
    * Generuje listę HTML dla menu nawigacyjnego na podstawie aktywnych stron (status=1).
    */
    function PokazMenu()
    {
        global $link;
        $query = "SELECT id, page_title FROM page_list WHERE status=1 ORDER BY id ASC";
        $result = mysqli_query($link, $query);

        $menu = '';

        // Iteracyjne budowanie elementów menu
        while($row = mysqli_fetch_assoc($result)) {

            // htmlspecjalchars() - Zabezpiecza przed wstrzyknięciem kodu.
            $menu .= '<li><a href="index.php?id='.$row['id'].'">'.htmlspecialchars($row['page_title']).'</a></li>';
        }

        return $menu;
    }
?>