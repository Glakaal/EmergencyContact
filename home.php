<?php
session_start();

// Asegúrate de que el usuario esté logueado
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$mensaje = $_SESSION['mensaje'] ?? '';
$username = $_SESSION['username'] ?? 'Usuario'; // Usar 'username' de la sesión
unset($_SESSION['mensaje']);
// No limpiar username para que persista durante la sesión

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/home_components.css">
    <link rel="stylesheet" href="../styles/message_styles.css">
    <link rel="stylesheet" href="../styles/notification_styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Bienvenido, <?php echo htmlspecialchars($username); ?>!</h2>
            <?php if ($mensaje): ?>
                <div class="notificacion"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <h3>Opciones:</h3>
            <ul class="options-list">
                <li><a href="account_details.php">Detalles de la cuenta</a></li>
                <li><a href="add_emergency_contact.php">Añadir contacto de emergencia</a></li>
                <li><a href="view_emergency_contacts.php">Ver contactos de emergencia</a></li>
                <li>
                    <form action="logout.php" method="post" style="display:block; width:100%;">
                        <button type="submit" class="logout-button">Salir</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</body>
</html> 