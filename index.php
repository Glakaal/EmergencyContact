<?php
session_start();
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
</head>
<body>
    <header> 
    <?php if ($mensaje): ?>
      <div class="notificacion"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    </header>
    <form action="login.php" method="post" class="login-form">
        <div class="contenido" >    
            <h1>Inicio de sesión </h1>  
            <label for="username">Usuario o Correo electrónico:</label>
            <input type="text" id="username" name="username" placeholder="Ingresa tu usuario o correo electrónico">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required autocomplete="off">
            <button type="submit">Iniciar sesión</button> 
        </div>
            <a href="register.html" class="button"><br>Registrarse</a>
</form>
    
</body>
</html>