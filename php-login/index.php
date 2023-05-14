<?php
  session_start();

  require 'database.php';

  // Verificar si el usuario ya ha iniciado sesión
  if (isset($_SESSION['user_id'])) {
    // Obtener los datos del usuario actual
    $records = $conn->prepare('SELECT id, email, password FROM users WHERE id = :id');
    $records->bindParam(':id', $_SESSION['user_id']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $user = null;

    // Si se encontraron datos del usuario, asignarlos a la variable $user
    if (count($results) > 0) {
      $user = $results;
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Bienvenido a tu aplicación web</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php require 'partials/header.php'; ?>

    <?php if(!empty($user)): ?>
      <!-- Si el usuario ha iniciado sesión, mostrar un mensaje de bienvenida con su correo electrónico y un enlace para cerrar sesión -->
      <br> Bienvenido. <?= $user['email']; ?>
      <br>Has iniciado sesión correctamente.
      <a href="logout.php">
        Cerrar sesión
      </a>
    <?php else: ?>
      <!-- Si el usuario no ha iniciado sesión, mostrar un mensaje para que inicie sesión o se registre -->
      <h1>Por favor inicia sesión o regístrate</h1>

      <a href="login.php">Iniciar sesión</a> o
      <a href="signup.php">Registrarse</a>
    <?php endif; ?>
  </body>
</html>