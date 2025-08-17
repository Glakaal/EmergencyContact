<?php
// ========================================
// DEBUG SESSION - HERRAMIENTA DE DIAGNÓSTICO
// ========================================
// Este archivo ayuda a diagnosticar problemas de sesiones

// Configuración de seguridad para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Debug Session - Emergency Contact</title>";
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
echo "input[type='text'] { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; }";
echo "button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
echo "button:hover { background-color: #45a049; }";
echo ".section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }";
echo ".danger { background-color: #ffebee; }";
echo ".danger button { background-color: #f44336; }";
echo ".danger button:hover { background-color: #da190b; }";
echo "pre { background-color: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }";
echo "</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Debug Session - Emergency Contact</h1>";
echo "<p class='info'>Herramienta de diagnóstico para problemas de sesiones</p>";

// Información básica de la sesión
echo "<div class='section'>";
echo "<h2>📊 Información Básica de la Sesión</h2>";
echo "<p><strong>ID de Sesión:</strong> <code>" . session_id() . "</code></p>";
echo "<p><strong>Nombre de Sesión:</strong> <code>" . session_name() . "</code></p>";
echo "<p><strong>Estado de la Sesión:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "<span class='success'>Activa</span>" : "<span class='error'>Inactiva</span>") . "</p>";
echo "<p><strong>Ruta de Guardado:</strong> <code>" . session_save_path() . "</code></p>";
echo "<p><strong>Cookie de Sesión:</strong> " . (ini_get('session.use_cookies') ? "<span class='success'>Habilitada</span>" : "<span class='error'>Deshabilitada</span>") . "</p>";
echo "</div>";

// Variables de sesión
echo "<div class='section'>";
echo "<h2>📋 Variables de Sesión</h2>";

if (empty($_SESSION)) {
    echo "<p class='warning'>⚠️ No hay variables de sesión definidas</p>";
} else {
    echo "<p class='success'>✅ Se encontraron " . count($_SESSION) . " variables de sesión</p>";
    echo "<table>";
    echo "<tr><th>Clave</th><th>Valor</th><th>Tipo</th><th>Longitud</th></tr>";
    
    foreach ($_SESSION as $key => $value) {
        $tipo = gettype($value);
        $longitud = is_string($value) ? strlen($value) : (is_array($value) ? count($value) : 'N/A');
        $valor_mostrado = is_string($value) ? htmlspecialchars($value) : (is_array($value) ? 'Array (' . count($value) . ' elementos)' : var_export($value, true));
        
        echo "<tr>";
        echo "<td><code>" . htmlspecialchars($key) . "</code></td>";
        echo "<td><code>" . $valor_mostrado . "</code></td>";
        echo "<td>" . $tipo . "</td>";
        echo "<td>" . $longitud . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Mostrar variables específicas importantes
    echo "<h3>🔍 Variables Específicas:</h3>";
    $variables_importantes = ['user_name', 'user_id', 'mensaje', 'error', 'success'];
    
    foreach ($variables_importantes as $var) {
        if (isset($_SESSION[$var])) {
            echo "<p><strong>$var:</strong> <code>" . htmlspecialchars($_SESSION[$var]) . "</code></p>";
        } else {
            echo "<p><strong>$var:</strong> <span class='warning'>NO DEFINIDO</span></p>";
        }
    }
}
echo "</div>";

// Información de cookies
echo "<div class='section'>";
echo "<h2>🍪 Información de Cookies</h2>";

if (empty($_COOKIE)) {
    echo "<p class='warning'>⚠️ No hay cookies definidas</p>";
} else {
    echo "<p class='success'>✅ Se encontraron " . count($_COOKIE) . " cookies</p>";
    echo "<table>";
    echo "<tr><th>Nombre</th><th>Valor</th><th>Longitud</th></tr>";
    
    foreach ($_COOKIE as $name => $value) {
        echo "<tr>";
        echo "<td><code>" . htmlspecialchars($name) . "</code></td>";
        echo "<td><code>" . htmlspecialchars($value) . "</code></td>";
        echo "<td>" . strlen($value) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Verificar cookie de sesión específica
$session_cookie_name = session_name();
if (isset($_COOKIE[$session_cookie_name])) {
    echo "<p class='success'>✅ Cookie de sesión encontrada: <code>$session_cookie_name</code></p>";
} else {
    echo "<p class='error'>❌ Cookie de sesión no encontrada: <code>$session_cookie_name</code></p>";
}
echo "</div>";

// Configuración de sesión
echo "<div class='section'>";
echo "<h2>⚙️ Configuración de Sesión</h2>";
echo "<table>";
echo "<tr><th>Configuración</th><th>Valor</th><th>Estado</th></tr>";

$configs = [
    'session.gc_maxlifetime' => 'Tiempo de vida máximo',
    'session.cookie_lifetime' => 'Tiempo de vida de cookie',
    'session.use_strict_mode' => 'Modo estricto',
    'session.use_cookies' => 'Uso de cookies',
    'session.use_only_cookies' => 'Solo cookies',
    'session.cookie_httponly' => 'Cookie HttpOnly',
    'session.cookie_secure' => 'Cookie segura',
    'session.cookie_samesite' => 'SameSite'
];

foreach ($configs as $config => $descripcion) {
    $valor = ini_get($config);
    $estado = $valor ? "<span class='success'>Habilitado</span>" : "<span class='error'>Deshabilitado</span>";
    echo "<tr>";
    echo "<td><code>$config</code></td>";
    echo "<td>" . ($valor ?: '0') . "</td>";
    echo "<td>$estado</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Herramientas de gestión de sesión
echo "<div class='section'>";
echo "<h2>🛠️ Herramientas de Gestión</h2>";

// Formulario para establecer variables de sesión
echo "<h3>➕ Establecer Variable de Sesión</h3>";
echo "<form method='post'>";
echo "<div class='form-group'>";
echo "<label><strong>Clave:</strong> <input type='text' name='set_key' placeholder='Nombre de la variable'></label>";
echo "</div>";
echo "<div class='form-group'>";
echo "<label><strong>Valor:</strong> <input type='text' name='set_value' placeholder='Valor de la variable'></label>";
echo "</div>";
echo "<button type='submit' name='action' value='set'>➕ Establecer Variable</button>";
echo "</form>";

// Procesar formulario
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'set' && !empty($_POST['set_key'])) {
        $_SESSION[$_POST['set_key']] = $_POST['set_value'];
        echo "<p class='success'>✅ Variable <code>" . htmlspecialchars($_POST['set_key']) . "</code> establecida correctamente</p>";
        echo "<script>setTimeout(function(){ location.reload(); }, 1000);</script>";
    }
}

echo "<br>";

// Botones de acción
echo "<h3>🔄 Acciones de Sesión</h3>";
echo "<form method='post' style='display: inline;'>";
echo "<button type='submit' name='action' value='refresh'>🔄 Actualizar</button>";
echo "</form>";

echo "<form method='post' style='display: inline;'>";
echo "<button type='submit' name='action' value='regenerate'>🔄 Regenerar ID</button>";
echo "</form>";

echo "<form method='post' style='display: inline;'>";
echo "<button type='submit' name='action' value='clear' class='danger'>🗑️ Limpiar Sesión</button>";
echo "</form>";

// Procesar acciones
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'refresh':
            echo "<p class='info'>ℹ️ Página actualizada</p>";
            break;
        case 'regenerate':
            session_regenerate_id(true);
            echo "<p class='success'>✅ ID de sesión regenerado: <code>" . session_id() . "</code></p>";
            break;
        case 'clear':
            session_unset();
            session_destroy();
            echo "<p class='warning'>⚠️ Sesión limpiada y destruida</p>";
            echo "<script>setTimeout(function(){ location.reload(); }, 1000);</script>";
            break;
    }
}
echo "</div>";

// Información del sistema
echo "<div class='section'>";
echo "<h2>💻 Información del Sistema</h2>";
echo "<p><strong>Versión de PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensión de sesiones:</strong> " . (extension_loaded('session') ? "<span class='success'>Cargada</span>" : "<span class='error'>No cargada</span>") . "</p>";
echo "<p><strong>Función session_start disponible:</strong> " . (function_exists('session_start') ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>Función session_id disponible:</strong> " . (function_exists('session_id') ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>Función session_regenerate_id disponible:</strong> " . (function_exists('session_regenerate_id') ? 'SÍ' : 'NO') . "</p>";
echo "</div>";

// Debug completo de $_SESSION
echo "<div class='section'>";
echo "<h2>🔍 Debug Completo de \$_SESSION</h2>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";
echo "</div>";

echo "</div>";
echo "</body></html>";
?>
