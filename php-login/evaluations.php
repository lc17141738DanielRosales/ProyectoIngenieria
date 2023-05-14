<?php
session_start();
require 'database.php';

// Si el usuario no ha iniciado sesión, se redirige a login.php
if(!isset($_SESSION['user_id'])){
  header('Location: login.php');
  exit;
}

// Se consulta a la base de datos por las evaluaciones que tiene disponibles el usuario
$records = $conn->prepare('SELECT * FROM evaluations WHERE user_id = :user_id AND available = 1');
$records->bindParam(':user_id', $_SESSION['user_id']);
$records->execute();
$evaluations = $records->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Evaluaciones</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <?php include 'partials/header.php'; ?>
    <h1>Evaluaciones</h1>
    <table>
      <thead>
        <tr>
          <th>Materia</th>
          <th>Profesor</th>
          <th>Disponible hasta</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($evaluations as $evaluation): ?>
        <tr>
          <td><?= $evaluation['subject'] ?></td>
          <td><?= $evaluation['teacher'] ?></td>
          <td><?= $evaluation['expiration_date'] ?></td>
          <td><a href="evaluate.php?id=<?= $evaluation['id'] ?>">Evaluar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </body>
</html>