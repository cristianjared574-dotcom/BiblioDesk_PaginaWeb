<?php
session_start();
// Destruir la sesión
session_destroy();
// Regresar al formulario de login
header('Location: login.php');
exit;
?>