<?php
// die Konstanten auslagern in eigene Datei
// die dann per require_once ('konfiguration.php');
// geladen wird.

// Damit alle Fehler angezeigt werden
error_reporting(E_ALL);

// Zum Aufbau der Verbindung zur Datenbank
// die Daten erhalten Sie von Ihrem Provider
//define ( 'MYSQL_HOST',      'localhost' );
//
//// bei XAMPP ist der MYSQL_Benutzer: root
//define ( 'MYSQL_BENUTZER',  'root' );
//define ( 'MYSQL_KENNWORT',  '\$Fmrgt320' );
//// für unser Bsp. nennen wir die DB adressverwaltung
//define ( 'MYSQL_DATENBANK', 'tools' );
$user = 'speedtest';
$host = '127.0.0.1';
$pw = 'n@4z7jVjh$ZT';
$db = 'tools';
$dmax = 50;
$umax = 10;
$port = 3307;

?>