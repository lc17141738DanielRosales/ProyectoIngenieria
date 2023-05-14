<?php
session_start();
require 'database.php';

// Si el usuario ya inició sesión, se redirige a evaluations.php
if(isset($_SESSION['user_id'])){
  $records = $conn->prepare('SELECT * FROM users WHERE id = :id');
  $records->bindParam(':id', $_SESSION['user_id']);
  $records->execute();
  $user = $records->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    header('Location: evaluations.php');
    exit;
  }
}

// Se define una variable para mostrar mensajes de error al usuario
$message = '';

// Si el usuario ha enviado el formulario de login
if (!empty($_POST['email']) && !empty($_POST['password'])) {
  // Se consulta a la base de datos por el usuario que corresponde a la dirección de email ingresada
  $records = $conn->prepare('SELECT id, email, password FROM users WHERE email = :email');
  $records->bindParam(':email', $_POST['email']);
  $records->execute();
  $results = $records->fetch(PDO::FETCH_ASSOC);

  // Si se encuentra un usuario y la contraseña ingresada coincide con la almacenada en la base de datos, se inicia sesión
  if (count($results) > 0 && password_verify($_POST['password'], $results['password'])) {
    $_SESSION['user_id'] = $results['id'];
    header("Location: evaluations.php");
    exit;
  } else {
    // Si no se encuentra un usuario con las credenciales ingresadas, se muestra un mensaje de error
    $message = 'Lo siento, esas credenciales no coinciden';
  }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php include 'partials/header.php'; ?>
    <h1>Login</h1>
    <span>o <a href="signup.php">regístrese aquí</a></span>

    <?php if(!empty($message)): ?>
      <p><?= $message ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <input name="email" type="text" placeholder="Ingrese su correo electrónico">
      <input name="password" type="password" placeholder="Ingrese su contraseña">
      <input type="submit" value="Enviar">
    </form>
  </body>
</html>