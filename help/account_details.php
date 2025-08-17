<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['account_id'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Usuario';
$account_id = $_SESSION['account_id'] ?? '';

// Conexión a la base de datos PostgreSQL
try {
    $conexion = new PDO("pgsql:host=localhost;dbname=emergency_contact", "postgres", "1001348211A");
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$errors = [];
$account_details = null;
$person_details = null;

try {
    // Obtener detalles de la cuenta
    $sql_account = "SELECT account_id, username, email, created_at, last_access FROM account WHERE account_id = :account_id";
    $stmt_account = $conexion->prepare($sql_account);
    $stmt_account->execute([':account_id' => $account_id]);
    $account_details = $stmt_account->fetch(PDO::FETCH_ASSOC);

    // Obtener detalles personales
    $sql_person = "SELECT person_id, first_name, middle_name, last_name, second_last_name, birth_date, phone, address FROM person WHERE account_id = :account_id";
    $stmt_person = $conexion->prepare($sql_person);
    $stmt_person->execute([':account_id' => $account_id]);
    $person_details = $stmt_person->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errors[] = "Error al obtener los detalles: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Cuenta</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/table_styles.css">
    <link rel="stylesheet" href="../styles/message_styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Detalles de la Cuenta de <?php echo htmlspecialchars($username); ?></h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($account_details): ?>
                <h3>Información de la Cuenta</h3>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Campo</th>
                            <th>Valor</th>
                        </tr>
                        <tr>
                            <td><strong>ID de Cuenta</strong></td>
                            <td><?php echo htmlspecialchars($account_details['account_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre de Usuario</strong></td>
                            <td><?php echo htmlspecialchars($account_details['username']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Correo Electrónico</strong></td>
                            <td><?php echo htmlspecialchars($account_details['email']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha de Creación</strong></td>
                            <td><?php echo htmlspecialchars($account_details['created_at']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Último Acceso</strong></td>
                            <td><?php echo htmlspecialchars($account_details['last_access'] ?? 'No registrado'); ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>

            <?php if ($person_details): ?>
                <h3>Información Personal</h3>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Campo</th>
                            <th>Valor</th>
                        </tr>
                        <tr>
                            <td><strong>ID de Persona</strong></td>
                            <td><?php echo htmlspecialchars($person_details['person_id']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Primer Nombre</strong></td>
                            <td><?php echo htmlspecialchars($person_details['first_name']); ?></td>
                        </tr>
                        <?php if ($person_details['middle_name']): ?>
                        <tr>
                            <td><strong>Segundo Nombre</strong></td>
                            <td><?php echo htmlspecialchars($person_details['middle_name']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Primer Apellido</strong></td>
                            <td><?php echo htmlspecialchars($person_details['last_name']); ?></td>
                        </tr>
                        <?php if ($person_details['second_last_name']): ?>
                        <tr>
                            <td><strong>Segundo Apellido</strong></td>
                            <td><?php echo htmlspecialchars($person_details['second_last_name']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($person_details['birth_date']): ?>
                        <tr>
                            <td><strong>Fecha de Nacimiento</strong></td>
                            <td><?php echo htmlspecialchars($person_details['birth_date']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($person_details['phone']): ?>
                        <tr>
                            <td><strong>Teléfono</strong></td>
                            <td><?php echo htmlspecialchars($person_details['phone']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($person_details['address']): ?>
                        <tr>
                            <td><strong>Dirección</strong></td>
                            <td><?php echo htmlspecialchars($person_details['address']); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            <?php endif; ?>

            <p><a href="home.php">Volver al Inicio</a></p>
        </div>
    </div>
</body>
</html>
