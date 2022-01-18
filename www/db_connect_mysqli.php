<?php
$host = 'localhost';
$db   = 'game_schema';
$user = 'root';
$pass = '';
$charset = 'utf8';

// Создание подключения
$conn = new mysqli($host, $root, $pass, $game_schema);

// Проверяем соединение
if ($conn->connect_error) {
  die("Ошибка подключения: " . $conn->connect_error);
}
?>