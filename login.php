<?php
// Conexión a la base de datos
try {
    //credenciales de la base de datos
    $conexion = new PDO("pgsql:host=localhost;dbname=emergency_contact", "postgres", "1001348211A");
    //manejo de errores
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Recibir datos del formulario index.html
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validar que los campos no estén vacíos
if (empty($username) || empty($password)) {
    die("Error: El usuario/correo electrónico y la contraseña son obligatorios");
}

// Buscar usuario en la base de datos por nombre de usuario O correo electrónico
$sql = "SELECT * FROM account WHERE username = :username OR email = :email";
$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':username' => $username,
    ':email' => $username
]);

// Verificar resultado - SQL Server puede devolver -1 en rowCount()
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario) {
    // Verificar la contraseña usando password_verify() de PHP
    if (password_verify($password, $usuario['password_hash'])) {
        session_start();
        $_SESSION['username'] = $usuario['username'];
        $_SESSION['account_id'] = $usuario['account_id'];
        $_SESSION['mensaje'] = "¡Bienvenido! Has iniciado sesión correctamente.";
        
        // Debug: mostrar información antes del redirect
        echo "Usuario encontrado: " . $usuario['username'] . "<br>";
        echo "Sesión iniciada. Redirigiendo...<br>";
        
        header("Location: home.php");
        exit();
    } else {
        echo "Inicio de sesión fallido. Contraseña incorrecta.";
    }
} else {
    echo "Inicio de sesión fallido. Usuario o correo electrónico no encontrado.";
}
?>