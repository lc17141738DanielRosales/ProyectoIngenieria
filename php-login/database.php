<?php
// Se definen los datos para la conexión a la base de datos
$server = 'localhost:3306';
$username = 'root';
$password = '';
$database = 'php_login_database';

try {
  // Se crea una instancia de la clase PDO para conectarse a la base de datos
  $conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch (PDOException $e) {
  // En caso de que no se pueda conectar, se muestra un mensaje de error y se detiene la ejecución del script
  die('Error al conectar con la base de datos: ' . $e->getMessage());
}

?>