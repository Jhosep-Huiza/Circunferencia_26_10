<?php
session_start();
require_once __DIR__ . '/../controller/AuthController.php';
require_once __DIR__ . '/../controller/CircunferenciaController.php';

AuthController::verificarSesion();

$controller = new CircunferenciaController();
$mensaje = $_GET['mensaje'] ?? '';
$errorForm = $controller->procesarFormulario();
$esError = $errorForm !== '';
if ($esError) {
    $mensaje = $errorForm;
}

$buscar = trim($_GET['buscar'] ?? '');
$editar = isset($_GET['editar']) ? $controller->obtenerParaEditar((int) $_GET['editar']) : null;
$registros = $controller->listar($buscar);

$h = fn($v) => htmlspecialchars((string) $v);
$idActual = $editar['id_circunferencia'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cálculo del área y perímetro de la circunferencia">
    <meta name="author" content="BC TECH">
    <link rel="icon" href="../image/icono.png">
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <title>Circunferencia · Calculadora</title>
</head>
<body>
    <header class="top">
        <a class="marca" href="home.php">
            <img src="../image/pi.png" alt="Pi">
            <span>Matemáticas</span>
        </a>
        <nav>
            <a href="home.php" class="activo">Calculadora</a>
            <a href="#historial">Historial</a>
            <a href="logout.php" onclick="return confirm('¿Deseas cerrar sesión?');">Cerrar sesión</a>
        </nav>
    </header>

    <main>
        <section class="calc">
            <div class="calc-form">
                <h1>Circunferencia</h1>
                <p class="usuario">Sesión de <?php echo $h($_SESSION['usuario'] ?? ''); ?></p>

                <?php if ($mensaje !== '') : ?>
                    <p class="mensaje <?php echo $esError ? 'is-error' : ''; ?>" role="status"><?php echo $h($mensaje); ?></p>
                <?php endif; ?>

                <form action="" method="post" id="frmFormulario">
                    <input type="hidden" name="id_circunferencia" value="<?php echo $h($idActual); ?>">

                    <label for="txtRadio">Radio</label>
                    <input type="number" step="any" min="0" name="txtRadio" id="txtRadio" required
                           value="<?php echo $h(isset($editar['radio']) ? $editar['radio'] + 0 : ''); ?>">

                    <label for="txtDetalle">Comentario <small>(opcional)</small></label>
                    <input type="text" name="txtDetalle" id="txtDetalle" maxlength="255"
                           value="<?php echo $h($editar['detalle'] ?? ''); ?>">

                    <div class="resultados">
                        <div><label for="txtD">Diámetro</label>
                            <input type="text" id="txtD" readonly value="<?php echo $h($editar['diametro'] ?? ''); ?>"></div>
                        <div><label for="txtA">Área</label>
                            <input type="text" id="txtA" readonly value="<?php echo $h($editar['area'] ?? ''); ?>"></div>
                        <div><label for="txtP">Perímetro</label>
                            <input type="text" id="txtP" readonly value="<?php echo $h($editar['perimetro'] ?? ''); ?>"></div>
                    </div>

                    <div class="botones">
                        <button type="button" class="b-calc" onclick="calcularCircunferencia();">Calcular</button>
                        <a class="btn-link" href="home.php">Cancelar</a>
                        <button type="submit" class="b-guardar" name="accion" value="guardar">
                            <?php echo $idActual !== '' ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                    </div>
                </form>
            </div>

            <figure class="visor">
                <!-- Imagen opcional de fondo: ../imagenes/circulo.png (ver style_home.css) -->
                <svg viewBox="0 0 240 240" role="img" aria-label="Circunferencia con su radio">
                    <circle id="svgCirculo" cx="120" cy="120" r="70"/>
                    <line id="svgRadio" x1="120" y1="120" x2="190" y2="120"/>
                    <circle cx="120" cy="120" r="3.5" class="centro"/>
                    <text id="svgEtiqueta" x="155" y="112">r</text>
                </svg>
                <figcaption>Área = π · r² &nbsp;|&nbsp; Perímetro = 2 · π · r</figcaption>
            </figure>
        </section>

        <section class="buscar">
            <form action="home.php" method="get" id="frmBuscar">
                <input type="search" name="buscar" id="txtBuscar" placeholder="Buscar por radio, área, perímetro o comentario"
                       value="<?php echo $h($buscar); ?>">
                <button type="submit">Buscar</button>
                <a class="btn-link" href="home.php">Limpiar</a>
            </form>
        </section>

        <section class="tabla" id="historial">
            <h2>Historial de cálculos</h2>
            <div class="scroll">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Radio</th><th>Diámetro</th><th>Área</th><th>Perímetro</th><th>Comentario</th><th>Fecha</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if ($registros) : foreach ($registros as $f) : ?>
                        <tr>
                            <td><?php echo (int) $f['id_circunferencia']; ?></td>
                            <td><?php echo $h($f['radio'] + 0); ?></td>
                            <td><?php echo $h($f['diametro'] + 0); ?></td>
                            <td><?php echo $h($f['area'] + 0); ?></td>
                            <td><?php echo $h($f['perimetro'] + 0); ?></td>
                            <td><?php echo $h($f['detalle']); ?></td>
                            <td><?php echo $h($f['fecha_registro']); ?></td>
                            <td class="acciones">
                                <a class="btn-tabla editar" href="home.php?editar=<?php echo (int) $f['id_circunferencia']; ?>">Editar</a>
                                <form action="" method="post" onsubmit="return confirm('¿Eliminar este registro?');">
                                    <input type="hidden" name="id_circunferencia" value="<?php echo (int) $f['id_circunferencia']; ?>">
                                    <button class="btn-tabla eliminar" type="submit" name="accion" value="eliminar">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; else : ?>
                        <tr><td colspan="8">Aún no hay cálculos. Ingresa un radio y pulsa Guardar.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../assets/js/app.js?v=3"></script>
</body>
</html>