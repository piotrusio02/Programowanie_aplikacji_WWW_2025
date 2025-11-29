<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

 include('cfg.php');
 include('showpage.php');

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
    <link rel="stylesheet" href="style/content.css">
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
        <li><a href="index.php?id=1">Strona główna</a></li>
        <li><a href="index.php?id=2">Rodzaje</a></li>
        <li><a href="index.php?id=3">Makaron handmade</a></li>
        <li><a href="index.php?id=4">Ciekawostki</a></li>
        <li><a href="index.php?id=5">Quiz</a></li>
        <li><a href="index.php?id=6">Filmy</a></li>
        <li><a href="index.php?id=7">Kontakt</a></li>
      </ul>
    </nav>

    <nav class="sidebar">
      <ul>
        <li><a href="index.php?id=1">Strona główna</a></li>
        <li><a href="index.php?id=2">Rodzaje</a></li>
        <li><a href="index.php?id=3">Makaron handmade</a></li>
        <li><a href="index.php?id=4">Ciekawostki</a></li>
        <li><a href="index.php?id=5">Quiz</a></li>
        <li><a href="index.php?id=6">Filmy</a></li>
        <li><a href="index.php?id=7">Kontakt</a></li>
      </ul>
    </nav>

    <main>
       <?php
			  	if (isset($_GET['id']))
              {
                  $id_strony = $_GET['id'];
                  if ($id_strony === 'contact') {
                      echo PokazKontakt();
                  } elseif ($id_strony === 'forgot_pass') {
                      echo PrzypomnijHaslo();
                  } else {
                      $tresc_strony = PokazPodstrone($id_strony);
                      echo $tresc_strony;
                  }
              }
              else
              {
                  $tresc_strony = PokazPodstrone(1); 
                  echo $tresc_strony;
              }
    ?>
    </main>

    <footer>
      <p>Projekt 1.6</p>
        <?php
    $nr_indeksu = '169351';
    $nrGrupy = 'ISI3';
    echo 'Autor: Piotr Piotrowski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
  ?>
    </footer>
  </div>
</body>
</html>