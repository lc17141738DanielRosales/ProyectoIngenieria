<?php
  session_start();
  require 'database.php';

  // Verificamos si el usuario ha iniciado sesión
  if(isset($_SESSION['user_id'])){
    $records = $conn->prepare('SELECT * FROM users WHERE id = :id');
    $records->bindParam(':id', $_SESSION['user_id']);
    $records->execute();
    $user = $records->fetch(PDO::FETCH_ASSOC);

    // Verificamos si el usuario es un docente
    if($user['is_teacher']) {
      // Obtenemos las materias que imparte el docente
      $stmt = $conn->prepare('SELECT subject_id FROM teachers_subjects WHERE teacher_id = :teacher_id');
      $stmt->bindParam(':teacher_id', $_SESSION['user_id']);
      $stmt->execute();
      $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Obtenemos las encuestas disponibles para cada materia que imparte el docente
      $available_surveys = array();
      foreach($subjects as $subject) {
        $stmt = $conn->prepare('SELECT * FROM surveys WHERE subject_id = :subject_id');
        $stmt->bindParam(':subject_id', $subject['subject_id']);
        $stmt->execute();
        $surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $available_surveys = array_merge($available_surveys, $surveys);
      }
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Panel de Control</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>

    <?php require 'partials/header.php' ?>

    <?php if(isset($user) && $user['is_teacher']): ?>
      <h1>Encuestas Disponibles</h1>
      <?php if(count($available_surveys) > 0): ?>
        <ul>
        <?php foreach($available_surveys as $survey): ?>
          <li>
            <a href="survey.php?id=<?= $survey['id'] ?>"><?= $survey['title'] ?></a>
          </li>
        <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No hay encuestas disponibles para ti en este momento.</p>
      <?php endif; ?>
    <?php else: ?>
      <p>No tienes permiso para acceder a esta página.</p>
    <?php endif; ?>

  </body>
</html>