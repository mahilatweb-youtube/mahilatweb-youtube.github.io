<?php
session_start();
$pdo = new PDO("sqlite:bots.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['buy'])) {
    $productId = $_POST['product_id'];
    $quantity = 1; // Solo una unidad por compra

    try {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $totalPrice = $product['price'];

            $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $productId, $quantity, $totalPrice]);

            $successMessage = "Compra realizada exitosamente.";
        } else {
            $errorMessage = "Producto no encontrado.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error al realizar la compra: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Productos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Comprar Suscriptores y Likes</h2>

        <?php if (isset($successMessage)): ?>
            <p class="success"><?= $successMessage ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p class="error"><?= $errorMessage ?></p>
        <?php endif; ?>

        <form action="buy.php" method="POST">
            <label for="product">Selecciona un Producto:</label>
            <select name="product_id" id="product" required>
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, name, price FROM products");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['name']} - \${$row['price']}</option>";
                    }
                } catch (PDOException $e) {
                    echo "<option disabled>Error al cargar productos.</option>";
                }
                ?>
            </select>
            <button type="submit" name="buy">Comprar</button>
        </form>
    </div>
</body>
</html>
