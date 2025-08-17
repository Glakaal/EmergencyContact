<?php
session_start();

// Asegúrate de que el usuario esté logueado y tengamos su account_id
if (!isset($_SESSION['username']) || !isset($_SESSION['account_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Usuario';
$account_id = $_SESSION['account_id']; // ID de cuenta del usuario logueado

// Conexión a la base de datos PostgreSQL
try {
    $conexion = new PDO("pgsql:host=localhost;dbname=emergency_contact", "postgres", "1001348211A");
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$errors = [];
$success_message = '';

// Obtener relaciones de parentesco para el dropdown
$kinships = [];
try {
    $stmt = $conexion->query("SELECT kinship_id, description FROM kinship_catalog ORDER BY description");
    $kinships = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Error al cargar parentescos: " . $e->getMessage();
}

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Renombrar variables para coincidir con el esquema de la base de datos emergency_contact
    $first_name = $_POST['nombre'] ?? '';
    $middle_name = $_POST['segundo_nombre'] ?? '';
    $last_name = $_POST['apellido_paterno'] ?? '';
    $second_last_name = $_POST['apellido_materno'] ?? '';
    $phone = $_POST['telefono'] ?? '';
    $secondary_phone = $_POST['telefono_secundario'] ?? '';
    $email = $_POST['correo'] ?? '';
    $address = $_POST['direccion'] ?? '';
    $kinship_id = $_POST['id_parentesco'] ?? ''; // Ahora sí se usa, para la tabla intermedia

    // Validaciones
    if (empty($first_name)) $errors[] = "El nombre es obligatorio.";
    if (empty($last_name)) $errors[] = "El apellido paterno es obligatorio.";
    if (empty($phone)) $errors[] = "El teléfono es obligatorio.";
    if (empty($kinship_id)) $errors[] = "La relación de parentesco es obligatoria.";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    }

    if (empty($errors)) {
        try {
            $conexion->beginTransaction();

            // 1. Insertar en la tabla emergency_contact
            $sql_contact = "INSERT INTO emergency_contact (account_id, first_name, middle_name, last_name, second_last_name, phone, secondary_phone, email, address)
                            VALUES (:account_id, :first_name, :middle_name, :last_name, :second_last_name, :phone, :secondary_phone, :email, :address) RETURNING contact_id";
            $stmt_contact = $conexion->prepare($sql_contact);
            $stmt_contact->execute([
                ':account_id' => $account_id,
                ':first_name' => $first_name,
                ':middle_name' => $middle_name ?: null,
                ':last_name' => $last_name,
                ':second_last_name' => $second_last_name ?: null,
                ':phone' => $phone,
                ':secondary_phone' => $secondary_phone ?: null,
                ':email' => $email ?: null,
                ':address' => $address ?: null
            ]);
            $contact_id = $stmt_contact->fetchColumn(); // Obtener el contact_id generado

            // 2. Obtener el person_id del usuario logueado
            $sql_person_id = "SELECT person_id FROM person WHERE account_id = :account_id";
            $stmt_person_id = $conexion->prepare($sql_person_id);
            $stmt_person_id->execute([':account_id' => $account_id]);
            $person_id = $stmt_person_id->fetchColumn();

            if (!$person_id) {
                throw new Exception("No se encontró información personal para la cuenta logueada.");
            }

            // 3. Insertar en la tabla person_contact_relationship
            $sql_relationship = "INSERT INTO person_contact_relationship (person_id, contact_id, kinship_id)
                                 VALUES (:person_id, :contact_id, :kinship_id)";
            $stmt_relationship = $conexion->prepare($sql_relationship);
            $stmt_relationship->execute([
                ':person_id' => $person_id,
                ':contact_id' => $contact_id,
                ':kinship_id' => $kinship_id
            ]);

            $conexion->commit();
            $success_message = "¡Contacto de emergencia añadido exitosamente!";
            $_POST = []; // Limpiar campos después de un envío exitoso

        } catch (PDOException $e) {
            $conexion->rollBack();
            $errors[] = "Error al añadir contacto: " . $e->getMessage();
        } catch (Exception $e) {
            $conexion->rollBack();
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Contacto de Emergencia</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/form_styles.css">
    <link rel="stylesheet" href="../styles/message_styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Añadir Contacto de Emergencia para <?php echo htmlspecialchars($username); ?></h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form action="add_emergency_contact.php" method="post" class="form-container">
                <h3>Información del Contacto</h3>
                <input type="text" name="nombre" placeholder="Nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                <input type="text" name="segundo_nombre" placeholder="Segundo Nombre (opcional)" value="<?php echo htmlspecialchars($_POST['segundo_nombre'] ?? ''); ?>">
                <input type="text" name="apellido_paterno" placeholder="Apellido Paterno" required value="<?php echo htmlspecialchars($_POST['apellido_paterno'] ?? ''); ?>">
                <input type="text" name="apellido_materno" placeholder="Apellido Materno (opcional)" value="<?php echo htmlspecialchars($_POST['apellido_materno'] ?? ''); ?>">
                
                <input type="tel" name="telefono" placeholder="Teléfono" required value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
                <input type="tel" name="telefono_secundario" placeholder="Teléfono Secundario (opcional)" value="<?php echo htmlspecialchars($_POST['telefono_secundario'] ?? ''); ?>">
                <input type="email" name="correo" placeholder="Correo Electrónico (opcional)" value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>">
                <textarea name="direccion" placeholder="Dirección (opcional)" rows="3"><?php echo htmlspecialchars($_POST['direccion'] ?? ''); ?></textarea>
                
                <label for="id_parentesco">Relación de Parentesco:</label>
                <select name="id_parentesco" id="id_parentesco" required>
                    <option value="">Seleccione una relación</option>
                    <?php foreach ($kinships as $kinship): ?>
                        <option value="<?php echo htmlspecialchars($kinship['kinship_id']); ?>"
                            <?php echo (isset($_POST['id_parentesco']) && $_POST['id_parentesco'] == $kinship['kinship_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kinship['description']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit">Añadir Contacto</button>
            </form>
            
            <p><a href="home.php">Volver al Inicio</a></p>
        </div>
    </div>
</body>
</html>
