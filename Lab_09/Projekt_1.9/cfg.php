<!-- 
Moduł konfiguracyjny połączenie z bazą danych.
Parametry połączenia się z bazą danych MySQL.
Stałe logowania administratora.
-->

<?php
	$dbhost = 'localhost';			// Adres hosta bazy danych.
	$dbuser = 'root';				// Nazwa użytkownika bazy danych.
	$dbpass = '';					// Hasło użytkownika bazy danych.
	$baza = 'moja_strona';			// Nazwa wybranej bazy danych.
	
	// Nawiązanie połączenia z bazą danych.
	$link = mysqli_connect($dbhost,$dbuser,$dbpass);

	// Sprawdzenie, czy połączenie z serwerem zakończyło się pomyślnie.
	if (!$link) echo '<b>przerwane polaczenie</b>';
	if(!mysqli_select_db($link,$baza)) echo 'nie wybrano bazy';

	// Użycie stałych do logowania.
	$login = 'admin_placeholder@example.com';
    $pass = 'admin';
?>