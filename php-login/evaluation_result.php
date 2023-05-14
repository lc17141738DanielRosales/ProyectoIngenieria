<?php
session_start();
require 'database.php';

// Verificamos que el usuario ha iniciado sesión
if(!isset($_SESSION['user_id'])){
  header('Location: login.php');
  exit;
}

// Obtenemos el id del usuario que ha iniciado sesión
$user_id = $_SESSION['user_id'];

// Obtenemos el id de la evaluación
$evaluation_id = $_GET['id'];

// Obtenemos la puntuación promedio de la evaluación del usuario actual
$stmt = $conn->prepare('SELECT AVG(score) AS average_score FROM evaluation_scores WHERE evaluation_id = :evaluation_id AND user_id = :user_id');
$stmt->bindParam(':evaluation_id', $evaluation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$average_score = $stmt->fetch(PDO::FETCH_ASSOC)['average_score'];

// Verificamos que la evaluación exista y que esté disponible para el usuario que ha iniciado sesión
$records = $conn->prepare('SELECT * FROM evaluations WHERE id = :id AND available = 1 AND user_id <> :user_id');
$records->bindParam(':id', $evaluation_id);
$records->bindParam(':user_id', $user_id);
$records->execute();
$evaluation = $records->fetch(PDO::FETCH_ASSOC);

if (!$evaluation) {
  header('Location: evaluations.php');
  exit;
}

// Mensajes según la puntuación promedio
if ($average_score >= 4.5) {
  $message = "Excelente";
} elseif ($average_score >= 4.0 && $average_score < 4.5) {
  $message = "Muy bueno";
} elseif ($average_score >= 3.0 && $average_score < 4.0) {
  $message = "Bueno";
} elseif ($average_score >= 2.0 && $average_score < 3.0) {
  $message = "Regular";
} else {
  $message = "Malo";
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Evaluación</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Evaluación</h1>
  <h2><?php echo $evaluation['title']; ?></h2>
  <p>Tu puntuación promedio es: <?php echo $average_score; ?></p>
  <p>Calificación: <?php echo $message; ?></p>
  <a href="evaluations.php">Volver a las evaluaciones</a>
</body>
</html>