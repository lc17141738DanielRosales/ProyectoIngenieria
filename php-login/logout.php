<?php
  session_start(); // Inicia la sesión

  session_unset(); // Elimina todas las variables de sesión

  session_destroy(); // Destruye la sesión actual

  header('Location: /php-login'); // Redirige a la página principal
?>