<!-- 
Główny plik aplikacji.
Odpowiada za konfiguracje, ładowanie modułów oraz wyświetlanie szablonu i treści.
-->

<?php
    session_start();
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    include 'cfg.php';
    include '../php/showpage.php';
    include '../php/contact.php';
    include '../php/sklep.php';


    if (isset($_GET['action'])) {
    if ($_GET['action'] == 'add' && isset($_POST['id_prod'])) {
        addToCart($link, $_POST['id_prod'], $_POST['ile_sztuk']);
        header("Location: index.php?id=8");
        exit;
    }
    if ($_GET['action'] == 'remove' && isset($_GET['nr'])) {
        removeFromCart($_GET['nr']);
        header("Location: index.php?id=koszyk");
        exit;
    }
}

?>
<!DOCTYPE html>
    <html lang="pl-PL">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="Projekt 1">
            <meta name="keywords" content="HTML5, CSS3, JavaScript">
            <meta name="author" content="Piotr Piotrowski">
            <title>penne.pl</title>

            <link rel="shortcut icon" href="../image/ikony/penne-icon.png">
            <link rel="stylesheet" href="../style/style.css">
            <link rel="stylesheet" href="../style/content.css">
            <link rel="stylesheet" href="../style/sklep.css">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Amarante&family=Luckiest+Guy&display=swap" rel="stylesheet">
            
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="../js/kucharz.js"></script>
            <script src="../js/quiz.js"></script>
        </head>

        <body>
            <div class="container">

                    <header>
                        <h1>penne.pl - Poznaj świat makaronów!</h1>
                    </header>
    
                    <nav class="sidebar">
                        <ul>
                            <?php echo PokazMenu(); ?>
                        </ul>
                    </nav>

                    <nav class="mobile-menu">
                        <ul>
                            <?php echo PokazMenu(); ?>
                        </ul>
                    </nav>

                <main>
                    <?php
                        if (isset($_GET['id'])) {
                            $id_strony = $_GET['id'];

                            if ($id_strony == 'koszyk') {
                                PokazKoszyk($link);
                            } else {
                                PokazPodstrone($id_strony);
                            }

                        } else {
                            PokazPodstrone(1);
                        }
                    ?>
                </main>

                <footer>
                    <p>Projekt 1.12</p>
                
                    <?php
                        $nr_indeksu = '169351';
                        $nrGrupy = 'ISI3';
                        echo 'Autor: Piotr Piotrowski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';     
                    ?>
                </footer>
            </div>
        </body>
    </html>