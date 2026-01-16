<!DOCTYPE html>

<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

if($_GET['idp'] == '') $strona = 'html/glowna.html';
if($_GET['idp'] == 'rodzaje') $strona = 'html/rodzaje.html';
if($_GET['idp'] == 'handmade') $strona = 'html/handmade.html';
if($_GET['idp'] == 'ciekawostki') $strona = 'html/ciekawostki.html';
if($_GET['idp'] == 'quiz') $strona = 'html/quiz.html';
if($_GET['idp'] == 'kontakt') $strona = 'html/kontakt.html';
if($_GET['idp'] == 'filmy') $strona = 'html/filmy.html';

if(!file_exists($strona)) $strona = 'html/glowna.html';
?>

<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JaavaScript">
    <meta name="author" content="Piotr Piotrowski">
    <title>penne.pl</title>
    <link rel="shortcut icon" href="image/penne-icon.png">
    <link rel="stylesheet" href="style/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amarante&family=Luckiest+Guy&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/kucharz.js"></script>
    <script src="js/quiz.js"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>penne.pl - Poznaj świat makaronów!</h1>
    </header>
    
    <nav class="mobile-menu">
      <ul>
        <li><a href="index.php">Strona główna</a></li>
        <li><a href="index.php?idp=rodzaje">Rodzaje</a></li>
        <li><a href="index.php?idp=handmade">Makaron handmade</a></li>
        <li><a href="index.php?idp=ciekawostki">Ciekawostki</a></li>
        <li><a href="index.php?idp=quiz">Quiz</a></li>
        <li><a href="index.php?idp=filmy">Filmy</a></li>
        <li><a href="index.php?idp=kontakt">Kontakt</a></li>
      </ul>
    </nav>

    <nav class="sidebar">
      <ul>
        <li><a href="index.php">Strona główna</a></li>
        <li><a href="index.php?idp=rodzaje">Rodzaje</a></li>
        <li><a href="index.php?idp=handmade">Makaron handmade</a></li>
        <li><a href="index.php?idp=ciekawostki">Ciekawostki</a></li>
        <li><a href="index.php?idp=quiz">Quiz</a></li>
        <li><a href="index.php?idp=filmy">Filmy</a></li>
        <li><a href="index.php?idp=kontakt">Kontakt</a></li>
      </ul>
    </nav>

    <main>
      <?php include($strona); ?>
    </main>

    <footer>
      <p>Projekt 1.5</p>
        <?php
    $nr_indeksu = '169351';
    $nrGrupy = 'ISI3';
    echo 'Autor: Piotr Piotrowski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
  ?>
    </footer>
  </div>
</body>
</html>