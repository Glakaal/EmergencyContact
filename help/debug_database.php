<?php
// ========================================
// DEBUG DATABASE - HERRAMIENTA DE DIAGNÓSTICO
// ========================================
// Este archivo ayuda a diagnosticar problemas de base de datos

// Configuración de seguridad para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<title>Debug Database - Emergency Contact</title>";
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
echo "input[type='text'], textarea { padding: 8px; margin: 5px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }";
echo "button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }";
echo "button:hover { background-color: #45a049; }";
echo ".section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }";
echo ".danger { background-color: #ffebee; }";
echo ".danger button { background-color: #f44336; }";
echo ".danger button:hover { background-color: #da190b; }";
echo "pre { background-color: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }";
echo ".query-result { background-color: #e8f5e8; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".query-error { background-color: #ffebee; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo "</style>";
echo "</head><body>";

echo "<div class='container'>";
echo "<h1>🔍 Debug Database - Emergency Contact</h1>";
echo "<p class='info'>Herramienta de diagnóstico para problemas de base de datos</p>";

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
    echo "<p><strong>Usuario:</strong> $username</p>";
    echo "<p><strong>Versión del servidor:</strong> " . $conexion->getAttribute(PDO::ATTR_SERVER_VERSION) . "</p>";
    echo "<p><strong>Versión del cliente:</strong> " . $conexion->getAttribute(PDO::ATTR_CLIENT_VERSION) . "</p>";

} catch (PDOException $e) {
    echo "<p class='error'>❌ Error de conexión: " . $e->getMessage() . "</p>";
    echo "</div></div></body></html>";
    exit;
}
echo "</div>";

// Información de tablas
echo "<div class='section'>";
echo "<h2>📋 Estructura de la Base de Datos</h2>";

try {
    // Obtener todas las tablas
    $sql_tables = "SELECT TABLE_NAME, TABLE_TYPE FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME";
    $stmt_tables = $conexion->prepare($sql_tables);
    $stmt_tables->execute();
    $tablas = $stmt_tables->fetchAll(PDO::FETCH_ASSOC);

    if (count($tablas) > 0) {
        echo "<p class='success'>✅ Se encontraron " . count($tablas) . " tablas</p>";
        echo "<table>";
        echo "<tr><th>Tabla</th><th>Tipo</th><th>Registros</th><th>Acciones</th></tr>";
        
        foreach ($tablas as $tabla) {
            $table_name = $tabla['TABLE_NAME'];
            
            // Contar registros
            $sql_count = "SELECT COUNT(*) as total FROM [$table_name]";
            $stmt_count = $conexion->prepare($sql_count);
            $stmt_count->execute();
            $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);
            $total_registros = $count_result['total'];
            
            echo "<tr>";
            echo "<td><code>$table_name</code></td>";
            echo "<td>" . $tabla['TABLE_TYPE'] . "</td>";
            echo "<td>$total_registros</td>";
            echo "<td>";
            echo "<button onclick='showTableData(\"$table_name\")'>👁️ Ver Datos</button>";
            echo "<button onclick='showTableStructure(\"$table_name\")'>🔧 Estructura</button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ No se encontraron tablas en la base de datos</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Error al obtener información de tablas: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Detalles de tablas específicas
if (isset($_GET['table'])) {
    $table_name = $_GET['table'];
    echo "<div class='section'>";
    echo "<h2>📊 Detalles de la Tabla: $table_name</h2>";
    
    try {
        // Estructura de la tabla
        echo "<h3>🔧 Estructura de la Tabla</h3>";
        $sql_structure = "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, CHARACTER_MAXIMUM_LENGTH 
                         FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_NAME = :table_name 
                         ORDER BY ORDINAL_POSITION";
        $stmt_structure = $conexion->prepare($sql_structure);
        $stmt_structure->execute([':table_name' => $table_name]);
        $columnas = $stmt_structure->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($columnas) > 0) {
            echo "<table>";
            echo "<tr><th>Columna</th><th>Tipo</th><th>Permite NULL</th><th>Valor por Defecto</th><th>Longitud Máxima</th></tr>";
            foreach ($columnas as $columna) {
                echo "<tr>";
                echo "<td><code>" . $columna['COLUMN_NAME'] . "</code></td>";
                echo "<td>" . $columna['DATA_TYPE'] . "</td>";
                echo "<td>" . $columna['IS_NULLABLE'] . "</td>";
                echo "<td>" . ($columna['COLUMN_DEFAULT'] ?: 'NULL') . "</td>";
                echo "<td>" . ($columna['CHARACTER_MAXIMUM_LENGTH'] ?: 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Datos de la tabla (primeros 10 registros)
        echo "<h3>📋 Datos de la Tabla (primeros 10 registros)</h3>";
        $sql_data = "SELECT TOP 10 * FROM [$table_name]";
        $stmt_data = $conexion->prepare($sql_data);
        $stmt_data->execute();
        $datos = $stmt_data->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($datos) > 0) {
            echo "<table>";
            // Encabezados
            echo "<tr>";
            foreach (array_keys($datos[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            // Datos
            foreach ($datos as $fila) {
                echo "<tr>";
                foreach ($fila as $valor) {
                    echo "<td>" . htmlspecialchars($valor ?: 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='warning'>⚠️ La tabla no contiene datos</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ Error al obtener detalles de la tabla: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
}

// Ejecutor de consultas SQL
echo "<div class='section'>";
echo "<h2>🛠️ Ejecutor de Consultas SQL</h2>";
echo "<form method='post'>";
echo "<div class='form-group'>";
echo "<label><strong>Consulta SQL:</strong></label>";
echo "<textarea name='sql_query' rows='4' placeholder='SELECT * FROM users LIMIT 5'>" . (isset($_POST['sql_query']) ? htmlspecialchars($_POST['sql_query']) : '') . "</textarea>";
echo "</div>";
echo "<button type='submit' name='action' value='execute'>🚀 Ejecutar Consulta</button>";
echo "<button type='submit' name='action' value='clear'>🗑️ Limpiar</button>";
echo "</form>";

// Procesar consulta SQL
if (isset($_POST['action']) && $_POST['action'] === 'execute' && !empty($_POST['sql_query'])) {
    $sql_query = trim($_POST['sql_query']);
    
    echo "<div class='query-result'>";
    echo "<h3>📊 Resultado de la Consulta</h3>";
    echo "<p><strong>Consulta ejecutada:</strong> <code>" . htmlspecialchars($sql_query) . "</code></p>";
    
    try {
        $stmt = $conexion->prepare($sql_query);
        $stmt->execute();
        
        // Determinar si es SELECT o no
        if (stripos($sql_query, 'SELECT') === 0) {
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($resultados) > 0) {
                echo "<p class='success'>✅ Consulta ejecutada exitosamente. Se encontraron " . count($resultados) . " registros.</p>";
                
                echo "<table>";
                // Encabezados
                echo "<tr>";
                foreach (array_keys($resultados[0]) as $header) {
                    echo "<th>" . htmlspecialchars($header) . "</th>";
                }
                echo "</tr>";
                
                // Datos (limitar a 20 registros para evitar sobrecarga)
                $registros_mostrados = min(count($resultados), 20);
                for ($i = 0; $i < $registros_mostrados; $i++) {
                    echo "<tr>";
                    foreach ($resultados[$i] as $valor) {
                        echo "<td>" . htmlspecialchars($valor ?: 'NULL') . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                
                if (count($resultados) > 20) {
                    echo "<p class='info'>ℹ️ Mostrando solo los primeros 20 registros de " . count($resultados) . " totales.</p>";
                }
            } else {
                echo "<p class='warning'>⚠️ La consulta no devolvió resultados.</p>";
            }
        } else {
            $filas_afectadas = $stmt->rowCount();
            echo "<p class='success'>✅ Consulta ejecutada exitosamente. Filas afectadas: $filas_afectadas</p>";
        }
        
    } catch (PDOException $e) {
        echo "<div class='query-error'>";
        echo "<p class='error'>❌ Error en la consulta: " . $e->getMessage() . "</p>";
        echo "</div>";
    }
    echo "</div>";
}
echo "</div>";

// Información del sistema
echo "<div class='section'>";
echo "<h2>💻 Información del Sistema</h2>";
echo "<p><strong>Versión de PHP:</strong> " . phpversion() . "</p>";
echo "<p><strong>Extensiones PDO disponibles:</strong> " . implode(', ', PDO::getAvailableDrivers()) . "</p>";
echo "<p><strong>Driver SQL Server disponible:</strong> " . (in_array('sqlsrv', PDO::getAvailableDrivers()) ? "<span class='success'>SÍ</span>" : "<span class='error'>NO</span>") . "</p>";
echo "<p><strong>Función PDO disponible:</strong> " . (class_exists('PDO') ? 'SÍ' : 'NO') . "</p>";
echo "</div>";

// JavaScript para las funciones de tabla
echo "<script>";
echo "function showTableData(tableName) {";
echo "    window.location.href = '?table=' + encodeURIComponent(tableName);";
echo "}";
echo "function showTableStructure(tableName) {";
echo "    window.location.href = '?table=' + encodeURIComponent(tableName) + '&view=structure';";
echo "}";
echo "</script>";

echo "</div>";
echo "</body></html>";
?>
