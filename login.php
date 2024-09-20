<?php
session_start();
$pdo = new PDO("sqlite:bots.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['login'])) {
    $input = $_POST['input'];
    $password = $_POST['password'];

    try {
        // Consultar por nombre de usuario o correo
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$input, $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: buy.php");
            exit();
        } else {
            $errorMessage = "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Inicio de Sesión</h2>

        <?php if (isset($errorMessage)): ?>
            <p class="error"><?= $errorMessage ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="input">Usuario o Correo:</label>
            <input type="text" id="input" name="input" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
