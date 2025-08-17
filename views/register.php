<?php
// Conexión a la base de datos PostgreSQL
try {
    $conexion = new PDO("pgsql:host=localhost;dbname=emergency_contact", "postgres", "1001348211A");
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Recibir datos del formulario
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$accept_terms = $_POST['accept_terms'] ?? false;

// Datos personales
$first_name = $_POST['first_name'] ?? '';
$middle_name = $_POST['middle_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$second_last_name = $_POST['second_last_name'] ?? '';
$birth_date = $_POST['birth_date'] ?? null;
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

// Validaciones
$errors = [];

// Validar campos obligatorios
if (empty($username)) $errors[] = "El nombre de usuario es obligatorio";
if (empty($email)) $errors[] = "El correo electrónico es obligatorio";
if (empty($password)) $errors[] = "La contraseña es obligatoria";
if (empty($first_name)) $errors[] = "El primer nombre es obligatorio";
if (empty($last_name)) $errors[] = "El primer apellido es obligatorio";

// Validar aceptación de términos
if (!$accept_terms) {
    $errors[] = "Debe aceptar los Términos y Condiciones para registrarse";
}

// Validar formato de email
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El formato del correo electrónico no es válido";
}

// Validar que las contraseñas coincidan
if ($password !== $confirm_password) {
    $errors[] = "Las contraseñas no coinciden";
}

// Validar longitud de contraseña
if (strlen($password) < 6) {
    $errors[] = "La contraseña debe tener al menos 6 caracteres";
}

// Si hay errores, mostrarlos
if (!empty($errors)) {
    echo "<h2>Errores de validación:</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "<a href='register.html'>Volver al formulario</a>";
    exit();
}

try {
    // Iniciar transacción
    $conexion->beginTransaction();
    
    // Generar hash seguro de la contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // 1. Insertar en la tabla account
    $sql_account = "INSERT INTO account (username, email, password_hash) VALUES (:username, :email, :password_hash) RETURNING account_id";
    $stmt_account = $conexion->prepare($sql_account);
    $stmt_account->execute([
        ':username' => $username,
        ':email' => $email,
        ':password_hash' => $password_hash
    ]);
    
    // Obtener el account_id generado
    $result = $stmt_account->fetch(PDO::FETCH_ASSOC);
    $account_id = $result['account_id'];
    
    // 2. Insertar en la tabla person
    $sql_person = "INSERT INTO person (account_id, first_name, middle_name, last_name, second_last_name, birth_date, phone, address) 
                   VALUES (:account_id, :first_name, :middle_name, :last_name, :second_last_name, :birth_date, :phone, :address)";
    $stmt_person = $conexion->prepare($sql_person);
    $stmt_person->execute([
        ':account_id' => $account_id,
        ':first_name' => $first_name,
        ':middle_name' => $middle_name ?: null,
        ':last_name' => $last_name,
        ':second_last_name' => $second_last_name ?: null,
        ':birth_date' => $birth_date ?: null,
        ':phone' => $phone ?: null,
        ':address' => $address ?: null
    ]);
    
    // Confirmar transacción
    $conexion->commit();
    
    // Redirigir con mensaje de éxito
    session_start();
    $_SESSION['mensaje'] = "¡Cuenta creada exitosamente! Ya puedes iniciar sesión.";
    header("Location: index.php");
    exit();
    
} catch (PDOException $e) {
    // Revertir transacción en caso de error
    $conexion->rollBack();
    
    // Verificar si es un error de duplicado
    if (strpos($e->getMessage(), 'duplicate key') !== false) {
        if (strpos($e->getMessage(), 'username') !== false) {
            echo "Error: El nombre de usuario ya está en uso.";
        } elseif (strpos($e->getMessage(), 'email') !== false) {
            echo "Error: El correo electrónico ya está registrado.";
        } else {
            echo "Error: Los datos ya existen en el sistema.";
        }
    } else {
        echo "Error al registrar el usuario: " . $e->getMessage();
    }
    
    echo "<br><a href='register.html'>Volver al formulario</a>";
}
?>
