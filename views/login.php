<?php
session_start();
require_once __DIR__ . '/../controller/AuthController.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = trim($_POST['clave'] ?? '');

    if ($correo === '' || $clave === '') {
        $mensaje = 'Completa todos los campos';
    } elseif ((new AuthController())->login($correo, $clave)) {
        header('Location: home.php');
        exit;
    } else {
        $mensaje = 'Correo o clave incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Calculadora de área y perímetro de la circunferencia">
    <meta name="author" content="BC TECH">
    <link rel="icon" href="../imagenes/icono.png">
    <link rel="stylesheet" href="../assetes/css/style.css">
    <title>Circunferencia · Ingresar</title>
</head>
<body>
    <main class="login">
        <section class="portada" aria-hidden="true">
            <!-- Cambia por tu imagen: ../imagenes/portada.jpg -->
            <img src="../imagenes/portada.jpg" alt="" onerror="this.remove()">
            <svg viewBox="0 0 200 200" class="compas">
                <circle cx="100" cy="100" r="80"/>
                <line x1="100" y1="100" x2="180" y2="100"/>
                <circle cx="100" cy="100" r="3" class="centro"/>
            </svg>
            <p class="formula">A = πr²<br>P = 2πr</p>
        </section>

        <form action="" method="post" id="frmLogin" class="panel">
            <img src="../imagenes/logo.png" alt="Logo" class="logo">
            <h1>Bienvenido</h1>
            <p class="sub">Ingresa para calcular el área y el perímetro de una circunferencia.</p>

            <?php if ($mensaje !== '') : ?>
                <p class="error" role="alert"><?php echo htmlspecialchars($mensaje); ?></p>
            <?php endif; ?>

            <label for="correo">Correo</label>
            <input type="email" name="correo" id="correo" autocomplete="username" required>
            <label for="clave">Clave</label>
            <input type="password" name="clave" id="clave" autocomplete="current-password" required>
            <button type="submit" id="btnIngresar">Ingresar</button>
        </form>
    </main>
</body>
</html>