<?php
// ========================================
// DEBUG LOGIN - HERRAMIENTA DE DIAGNÓSTICO
// ========================================
// Este archivo ayuda a diagnosticar problemas de autenticación

// Configuración de seguridad para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Debug Login - Emergency Contact</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { color: green; font-weight: bold; }";
echo ".error { color: red; font-weight: bold; }";
echo ".warning { color: orange; font-weight: bold; }";
echo ".info { color: blue; font-weight: bold; }";
echo "table { border-collapse: collapse; width: 100%; margin: 10px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo ".form-group { margin: 10px 0; }";
echo "input[type='text'], input[type='password'] { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }";
echo "button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }";
echo "button:hover { background-color: #45a049; }";
echo ".section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }";
echo "</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Debug Login - Emergency Contact</h1>";
echo "<p class='info'>Herramienta de diagnóstico para problemas de autenticación</p>";

// Conexión a la base de datos
echo "<div class='section'>";
echo "<h2>📊 Estado de la Conexión a la Base de Datos</h2>";

try {
    $serverName = "localhost,1433";
    $database = "emergency_contact";
    $username = "sa";
    $password = "1001348211A@";

    $conexion = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p class='success'>✅ Conexión exitosa a la base de datos</p>";
    echo "<p><strong>Servidor:</strong> $serverName</p>";
    echo "<p><strong>Base de datos:</strong> $database</p>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "</div></div></body></html>";
    exit;
}
echo "</div>";

// Mostrar todos los usuarios en la base de datos
echo "<div class='section'>";
echo "<h2>👥 Usuarios Registrados en la Base de Datos</h2>";

$sql = "SELECT id_user, user_name, password, LEN(user_name) as name_length, LEN(password) as pass_length FROM users ORDER BY id_user";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($usuarios) > 0) {
    echo "<p class='success'>✅ Se encontraron " . count($usuarios) . " usuarios</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Longitud Usuario</th><th>Hash Password (primeros 30 chars)</th><th>Longitud Hash</th></tr>";
    foreach ($usuarios as $usuario) {
        $password_preview = substr($usuario['password'], 0, 30) . "...";
        echo "<tr>";
        echo "<td>" . $usuario['id_user'] . "</td>";
        echo "<td><code>'" . htmlspecialchars($usuario['user_name']) . "'</code></td>";
        echo "<td>" . $usuario['name_length'] . "</td>";
        echo "<td><code>" . htmlspecialchars($password_preview) . "</code></td>";
        echo "<td>" . $usuario['pass_length'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>⚠️ No hay usuarios en la base de datos</p>";
}
echo "</div>";

// Probar verificación de contraseña
if (isset($_POST['test_user']) && isset($_POST['test_password'])) {
    echo "<div class='section'>";
    echo "<h2>🧪 Prueba de Autenticación</h2>";
    
    $test_user = trim($_POST['test_user']);
    $test_password = $_POST['test_password'];
    
    echo "<p><strong>Usuario a probar:</strong> <code>'" . htmlspecialchars($test_user) . "'</code></p>";
    echo "<p><strong>Longitud del usuario:</strong> " . strlen($test_user) . " caracteres</p>";
    echo "<p><strong>Contraseña a probar:</strong> <code>" . str_repeat('*', strlen($test_password)) . "</code></p>";
    echo "<p><strong>Longitud de la contraseña:</strong> " . strlen($test_password) . " caracteres</p>";
    
    // Buscar usuario
    $sql = "SELECT * FROM users WHERE user_name = :user_name";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':user_name' => $test_user]);

    echo "<h3>🔍 Información de Depuración SQL:</h3>";
    echo "<p><strong>Query ejecutado:</strong> <code>" . htmlspecialchars($sql) . "</code></p>";
    echo "<p><strong>Parámetro user_name:</strong> <code>'" . htmlspecialchars($test_user) . "'</code></p>";
    echo "<p><strong>Filas encontradas:</strong> " . $stmt->rowCount() . "</p>";

    // Mostrar comparación con todos los usuarios
    echo "<h3>📋 Comparación con Todos los Usuarios:</h3>";
    $sql_all = "SELECT id_user, user_name, LEN(user_name) as len_name FROM users";
    $stmt_all = $conexion->prepare($sql_all);
    $stmt_all->execute();
    $todos_usuarios = $stmt_all->fetchAll(PDO::FETCH_ASSOC);

    echo "<table>";
    echo "<tr><th>ID</th><th>Usuario en BD</th><th>Longitud</th><th>¿Coincide?</th><th>Comparación</th></tr>";
    foreach ($todos_usuarios as $usuario) {
        $coincide = ($usuario['user_name'] === $test_user) ? "SÍ" : "NO";
        $comparacion = ($usuario['user_name'] === $test_user) ? "Exacta" : "Diferente";
        $color = ($usuario['user_name'] === $test_user) ? "success" : "error";
        echo "<tr>";
        echo "<td>" . $usuario['id_user'] . "</td>";
        echo "<td><code>'" . htmlspecialchars($usuario['user_name']) . "'</code></td>";
        echo "<td>" . $usuario['len_name'] . "</td>";
        echo "<td class='$color'>" . $coincide . "</td>";
        echo "<td>" . $comparacion . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($stmt->rowCount() === 1) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h3>✅ Usuario Encontrado</h3>";
        echo "<p><strong>ID del usuario:</strong> " . $usuario['id_user'] . "</p>";
        echo "<p><strong>Hash almacenado:</strong> <code>" . htmlspecialchars($usuario['password']) . "</code></p>";
        
        // Verificar contraseña
        if (password_verify($test_password, $usuario['password'])) {
            echo "<p class='success'>🎉 ¡Contraseña correcta! El usuario puede autenticarse.</p>";
        } else {
            echo "<p class='error'>❌ Contraseña incorrecta. El hash no coincide.</p>";
            
            // Información adicional para debug
            echo "<h4>🔧 Información Adicional de Debug:</h4>";
            echo "<p><strong>Hash generado para la contraseña de prueba:</strong> <code>" . password_hash($test_password, PASSWORD_DEFAULT) . "</code></p>";
            echo "<p><strong>¿La función password_verify está disponible?</strong> " . (function_exists('password_verify') ? 'SÍ' : 'NO') . "</p>";
        }
    } else {
        echo "<p class='error'>❌ Usuario no encontrado en la base de datos.</p>";
        echo "<p class='info'>💡 Sugerencias:</p>";
        echo "<ul>";
        echo "<li>Verifica que el nombre de usuario esté escrito correctamente</li>";
        echo "<li>Revisa si hay espacios en blanco al inicio o final</li>";
        echo "<li>Confirma que el usuario existe en la tabla 'users'</li>";
        echo "</ul>";
    }
    echo "</div>";
}

// Formulario de prueba
echo "<div class='section'>";
echo "<h2>🧪 Probar Autenticación</h2>";
echo "<form method='post'>";
echo "<div class='form-group'>";
echo "<label><strong>Usuario:</strong> <input type='text' name='test_user' required placeholder='Ingresa el nombre de usuario'></label>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label><strong>Contraseña:</strong> <input type='password' name='test_password' required placeholder='Ingresa la contraseña'></label>";
echo "</div>";
echo "<button type='submit'>🔍 Probar Autenticación</button>";
echo "</form>";
echo "</div>";

// Información del sistema
echo "<div class='section'>";
echo "<h2>⚙️ Información del Sistema</h2>";
echo "<p><strong>Versión de PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensiones PDO disponibles:</strong> " . implode(', ', PDO::getAvailableDrivers()) . "</p>";
echo "<p><strong>Función password_verify disponible:</strong> " . (function_exists('password_verify') ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>Función password_hash disponible:</strong> " . (function_exists('password_hash') ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>Algoritmo de hash por defecto:</strong> " . PASSWORD_DEFAULT . "</p>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>
