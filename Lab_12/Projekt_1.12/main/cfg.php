<!-- 
Moduł konfiguracyjny połączenie z bazą danych.
Parametry połączenia się z bazą danych MySQL.
Stałe logowania administratora.
-->

<?php
	$dbhost = 'localhost';
	$dbuser = 'root';
	$dbpass = '';
	$baza = 'moja_strona';

	$link = mysqli_connect($dbhost,$dbuser,$dbpass);

	// Sprawdzenie, czy połączenie z serwerem zakończyło się pomyślnie.
	if (!$link) echo '<b>przerwane polaczenie</b>';
	if(!mysqli_select_db($link,$baza)) echo 'nie wybrano bazy';


	$login = 'admin_placeholder@example.com';
    $pass = 'admin';
?>