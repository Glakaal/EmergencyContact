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
$emergency_contacts = [];

try {
    // Obtener contactos de emergencia del usuario
    $sql_contacts = "
        SELECT 
            ec.contact_id,
            ec.first_name,
            ec.middle_name,
            ec.last_name,
            ec.second_last_name,
            ec.phone,
            ec.secondary_phone,
            ec.email,
            ec.address,
            ec.registered_at,
            kc.description as kinship_description
        FROM emergency_contact ec
        LEFT JOIN person_contact_relationship pcr ON ec.contact_id = pcr.contact_id
        LEFT JOIN person p ON pcr.person_id = p.person_id
        LEFT JOIN kinship_catalog kc ON pcr.kinship_id = kc.kinship_id
        WHERE p.account_id = :account_id
        ORDER BY ec.registered_at DESC
    ";
    $stmt_contacts = $conexion->prepare($sql_contacts);
    $stmt_contacts->execute([':account_id' => $account_id]);
    $emergency_contacts = $stmt_contacts->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errors[] = "Error al obtener los contactos: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Contactos de Emergencia</title>
    <link rel="stylesheet" href="../styles/base.css">
    <link rel="stylesheet" href="../styles/table_styles.css">
    <link rel="stylesheet" href="../styles/message_styles.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <h2>Contactos de Emergencia de <?php echo htmlspecialchars($username); ?></h2>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($emergency_contacts)): ?>
                <h3>Lista de Contactos de Emergencia (<?php echo count($emergency_contacts); ?>)</h3>
                <div class="table-container">
                    <table>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Teléfono</th>
                            <th>Teléfono Secundario</th>
                            <th>Correo</th>
                            <th>Relación</th>
                            <th>Fecha de Registro</th>
                        </tr>
                        <?php foreach ($emergency_contacts as $contact): ?>
                        <tr>
                            <td>
                                <?php 
                                $full_name = $contact['first_name'];
                                if ($contact['middle_name']) $full_name .= ' ' . $contact['middle_name'];
                                $full_name .= ' ' . $contact['last_name'];
                                if ($contact['second_last_name']) $full_name .= ' ' . $contact['second_last_name'];
                                echo htmlspecialchars($full_name);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                            <td><?php echo htmlspecialchars($contact['secondary_phone'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($contact['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($contact['kinship_description'] ?? 'No especificado'); ?></td>
                            <td><?php echo htmlspecialchars($contact['registered_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php else: ?>
                <h3>Contactos de Emergencia</h3>
                <p>No tienes contactos de emergencia registrados.</p>
                <p><a href="add_emergency_contact.php">Añadir contacto de emergencia</a></p>
            <?php endif; ?>

            <p><a href="home.php">Volver al Inicio</a></p>
        </div>
    </div>
</body>
</html>
