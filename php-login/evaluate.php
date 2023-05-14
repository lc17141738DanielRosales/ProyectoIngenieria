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

// Si el usuario ha enviado el formulario de evaluación
if (!empty($_POST)) {
  // Insertamos las respuestas del usuario en la tabla evaluations_answers
  $stmt = $conn->prepare('INSERT INTO evaluations_answers (evaluation_id, question_id, answer) VALUES (:evaluation_id, :question_id, :answer)');
  foreach ($_POST as $question_id => $answer) {
    $stmt->bindParam(':evaluation_id', $evaluation_id);
    $stmt->bindParam(':question_id', $question_id);
    $stmt->bindParam(':answer', $answer);
    $stmt->execute();
  }

// Calculamos la puntuación promedio de las respuestas del usuario
$total_score = 0;
foreach ($_POST as $answer) {
  $total_score += $answer;
}
$average_score = $total_score / count($_POST);

// Insertamos la puntuación promedio en la tabla evaluation_scores
$stmt = $conn->prepare('INSERT INTO evaluation_scores (evaluation_id, user_id, score) VALUES (:evaluation_id, :user_id, :score)');
$stmt->bindParam(':evaluation_id', $evaluation_id);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':score', $average_score);
$stmt->execute();

  // Marcamos la evaluación como completada
  $stmt = $conn->prepare('UPDATE evaluations SET completed = 1 WHERE id = :id');
  $stmt->bindParam(':id', $evaluation_id);
  $stmt->execute();

  // Redirigimos al usuario a la lista de evaluaciones disponibles
  header('Location: evaluations.php');
  exit;
}

// Obtenemos las preguntas de la evaluación
$stmt = $conn->prepare('SELECT * FROM questions WHERE evaluation_id = :evaluation_id');
$stmt->bindParam(':evaluation_id', $evaluation_id);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <?php include 'partials/header.php'; ?>
    <h1>Evaluación</h1>
    <p>Evaluación para <?php echo $evaluation['name']; ?></p>
    <form action="evaluate.php?id=<?php echo $evaluation_id; ?>" method="POST">
      <?php foreach ($questions as $question): ?>
        <div class="form-group">
          <label for="<?php echo $question['id']; ?>"><?php echo $question['text']; ?></label>
          <select class="form-control" id="<?php echo $question['id']; ?>" name="<?php echo $question['id']; ?>">
            <option value="5">Excelente</option>
            <option value="4">Muy bueno</option>
            <option value="3">Bueno</option>
            <option value="2">Regular</option>
            <option value="1">Malo</option>
            </select>
        </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
    <?php include 'partials/footer.php'; ?>
  </body>
</html>