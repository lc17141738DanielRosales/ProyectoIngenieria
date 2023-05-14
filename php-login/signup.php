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

// Se define una variable para mostrar mensajes de error o éxito al usuario
$message = '';

// Si el usuario ha enviado el formulario de registro
if (!empty($_POST['email']) && !empty($_POST['password'])) {
  // Se inserta el nuevo usuario en la base de datos
  $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':email', $_POST['email']);
  // Se encripta la contraseña antes de almacenarla
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $stmt->bindParam(':password', $password);

  if($stmt->execute()) {
    // Si se insertó el usuario correctamente, se muestra un mensaje de éxito
    $message = 'Usuario creado exitosamente';
  } else {
    // Si hubo algún error al insertar el usuario, se muestra un mensaje de error
    $message = 'Lo sentimos, hubo un problema al crear su cuenta';
  }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Registro</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php include 'partials/header.php'; ?>
    <h1>Registro</h1>
    <span>o <a href="login.php">iniciar sesión</a></span>

    <?php if(!empty($message)): ?>
      <p><?= $message ?></p>
    <?php endif; ?>

    <form action="signup.php" method="POST">
      <input name="email" type="text" placeholder="Ingrese su correo electrónico">
      <input name="password" type="password" placeholder="Ingrese su contraseña">
      <input name="confirm_password" type="password" placeholder="Confirmar contraseña">
      <input type="submit" value="Enviar">
    </form>
  </body>
</html>