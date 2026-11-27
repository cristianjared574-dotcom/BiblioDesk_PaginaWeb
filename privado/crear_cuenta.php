<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

$matricula = '';
$nombre = '';
$apellidoP = '';
$apellidoM = '';
$correo = '';
$telefono = '';
$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidoP = trim($_POST['apellido_p'] ?? '');
    $apellidoM = trim($_POST['apellido_m'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';

    $longitudPassword = strlen($password);

    if ($matricula === '' || $nombre === '' || $apellidoP === '' || $correo === '' || $password === '' || $confirmarPassword === '') {
        $error = 'Por favor completa todos los campos obligatorios (*).';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un correo electrónico válido.';
    } elseif ($longitudPassword < 10 || $longitudPassword > 72) {
        $error = 'La contraseña debe tener entre 10 y 72 caracteres.';
    } elseif ($password !== $confirmarPassword) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!isset($conexion) || !($conexion instanceof mysqli)) {
        $error = 'No hay conexión disponible con la base de datos.';
    } else {
        $sqlExiste = "SELECT ID_USUARIO FROM USUARIO WHERE MATRICULA_USUARIO = ? OR CORREO = ? LIMIT 1";
        $stmtExiste = mysqli_prepare($conexion, $sqlExiste);

        if ($stmtExiste) {
            mysqli_stmt_bind_param($stmtExiste, 'ss', $matricula, $correo);
            mysqli_stmt_execute($stmtExiste);
            $resExiste = mysqli_stmt_get_result($stmtExiste);
            $yaExiste = $resExiste && mysqli_num_rows($resExiste) > 0;
            if ($resExiste) {
                mysqli_free_result($resExiste);
            }
            mysqli_stmt_close($stmtExiste);

            if ($yaExiste) {
                $error = 'Ya existe un lector registrado con esa matrícula o correo electrónico.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $apellidoMParam = $apellidoM !== '' ? $apellidoM : null;
                $telefonoParam = $telefono !== '' ? $telefono : null;

                $sqlInsert = "INSERT INTO USUARIO
                                (MATRICULA_USUARIO, NOMBRE, APELLIDOP, APELLIDOM, CORREO, TELEFONO, PASSWORD_HASH, ESTADO)
                              VALUES (?, ?, ?, ?, ?, ?, ?, 'ACTIVO')";
                $stmtInsert = mysqli_prepare($conexion, $sqlInsert);

                if ($stmtInsert) {
                    mysqli_stmt_bind_param(
                        $stmtInsert,
                        'sssssss',
                        $matricula,
                        $nombre,
                        $apellidoP,
                        $apellidoMParam,
                        $correo,
                        $telefonoParam,
                        $passwordHash
                    );

                    if (mysqli_stmt_execute($stmtInsert)) {
                        $exito = 'Cuenta creada correctamente como lector. Ya puedes iniciar sesión con tu matrícula.';
                        $matricula = '';
                        $nombre = '';
                        $apellidoP = '';
                        $apellidoM = '';
                        $correo = '';
                        $telefono = '';
                    } else {
                        $error = 'No se pudo registrar la cuenta en este momento.';
                    }
                    mysqli_stmt_close($stmtInsert);
                } else {
                    $error = 'Error al preparar el registro del lector.';
                }
            }
        } else {
            $error = 'Error al validar la matrícula y el correo.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Crear cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fcfaf5; color: #2c241e; }
        .navbar-bd, .footer-bd { background-color: #3b2316; color: #f5efe6; }
        .navbar-bd .nav-link { color: #f5efe6; font-size: 0.92rem; padding: 0.4rem 0.75rem; }
        .navbar-bd .nav-link:hover, .navbar-bd .nav-link:focus, .navbar-bd .nav-link.active { color: #f3ead3; }
        .dropdown-menu-bd { background-color: transparent; border: none; box-shadow: none; padding: 0.5rem 0 0 0; min-width: 195px; }
        .dropdown-menu-bd .dropdown-item {
            background-color: #f3ead3; color: #2c241e; border-radius: 6px;
            padding: 0.55rem 1rem; margin-bottom: 0.45rem; font-size: 0.9rem; font-weight: 500;
            box-shadow: 0 2px 6px rgba(44, 36, 30, 0.14);
        }
        .dropdown-menu-bd .dropdown-item:hover, .dropdown-menu-bd .dropdown-item:focus { background-color: #e7d8b5; color: #2c241e; }
        .titulo-serif { font-family: Georgia, "Times New Roman", serif; color: #241911; }
        .tarjeta-registro {
            background-color: #ffffff;
            border: 1px solid #f1ece0;
            border-radius: 14px;
            padding: 2.25rem 2rem;
            box-shadow: 0 4px 16px rgba(44, 36, 30, 0.03);
        }
        .form-control-bd {
            background-color: #fbf9f4;
            border: 1px solid #ece5d5;
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.92rem;
        }
        .form-control-bd:focus {
            background-color: #ffffff;
            border-color: #a67c37;
            box-shadow: 0 0 0 0.15rem rgba(166, 124, 55, 0.18);
        }
        .btn-guinda-bd {
            background-color: #691b14;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.35rem;
            font-size: 0.92rem;
            font-weight: 600;
        }
        .btn-guinda-bd:hover, .btn-guinda-bd:focus {
            background-color: #51130e;
            color: #ffffff;
        }
        .footer-bd a { color: #e8dccb; text-decoration: none; }
        .footer-bd a:hover { color: #ffffff; text-decoration: underline; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-bd px-3 px-md-5 py-3">
        <div class="container-fluid px-0">
            <a class="navbar-brand text-white fw-bold fs-4 mb-0" href="../publico/index.php">BiblioDesk</a>
            <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menuPrincipalBD" aria-controls="menuPrincipalBD"
                    aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="menuPrincipalBD">
                <ul class="navbar-nav align-items-lg-center gap-lg-2 my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="../publico/index.php">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownQuienesSomos"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">¿Quiénes somos?</a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-bd" aria-labelledby="dropdownQuienesSomos">
                            <li><a class="dropdown-item" href="../publico/historia.php">Historia</a></li>
                            <li><a class="dropdown-item" href="../publico/mision.php">Misión</a></li>
                            <li><a class="dropdown-item" href="../publico/vision.php">Visión</a></li>
                            <li><a class="dropdown-item" href="../publico/objetivos.php">Objetivos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="../publico/index.php#catalogo">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="../publico/noticias.php">Noticias</a></li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="login.php" class="btn btn-light btn-sm fw-semibold px-3">Iniciar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5" style="max-width: 920px;">
        <div class="tarjeta-registro">
            <h1 class="h2 fw-normal titulo-serif mb-1">Crear cuenta</h1>
            <p class="text-muted small mb-4">Usa los mismos datos de lector en la web y en la aplicación móvil.</p>

            <?php if ($exito !== ''): ?>
                <div class="alert alert-success small py-2" role="alert">
                    <?= htmlspecialchars($exito, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif ($error !== ''): ?>
                <div class="alert alert-danger small py-2" role="alert">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="crear_cuenta.php" novalidate>
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="matricula" class="form-label small fw-bold mb-1">Matrícula *</label>
                        <input type="text"
                               id="matricula"
                               name="matricula"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($matricula, ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="nombre" class="form-label small fw-bold mb-1">Nombre(s) *</label>
                        <input type="text"
                               id="nombre"
                               name="nombre"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="apellido_p" class="form-label small fw-bold mb-1">Apellido paterno *</label>
                        <input type="text"
                               id="apellido_p"
                               name="apellido_p"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($apellidoP, ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="apellido_m" class="form-label small fw-bold mb-1">Apellido materno</label>
                        <input type="text"
                               id="apellido_m"
                               name="apellido_m"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($apellidoM, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="correo" class="form-label small fw-bold mb-1">Correo electrónico *</label>
                        <input type="email"
                               id="correo"
                               name="correo"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($correo, ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="telefono" class="form-label small fw-bold mb-1">Teléfono</label>
                        <input type="text"
                               id="telefono"
                               name="telefono"
                               class="form-control form-control-bd"
                               value="<?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-2">
                    <div class="col-12 col-md-6">
                        <label for="password" class="form-label small fw-bold mb-1">Contraseña *</label>
                        <input type="password"
                               id="password"
                               name="password"
                               minlength="10"
                               maxlength="72"
                               class="form-control form-control-bd"
                               required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="confirmar_password" class="form-label small fw-bold mb-1">Confirmar contraseña *</label>
                        <input type="password"
                               id="confirmar_password"
                               name="confirmar_password"
                               minlength="10"
                               maxlength="72"
                               class="form-control form-control-bd"
                               required>
                    </div>
                </div>

                <p class="text-muted small mb-4" style="font-size: 0.78rem;">
                    Contraseña de 10 a 72 caracteres. Tu cuenta se crea como lector.
                </p>

                <button type="submit" class="btn btn-guinda-bd mb-3">Crear cuenta</button>

                <div class="small text-muted">
                    ¿Ya tienes cuenta?
                    <a href="login.php" class="text-decoration-none fw-semibold" style="color: #691b14;">
                        Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer-bd py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <h5 class="fw-bold mb-2 titulo-serif text-white">BiblioDesk</h5>
                    <p class="small mb-1 text-light">Un espacio para descubrir.</p>
                    <p class="small mb-1 text-light">Biblioteca Pública Regional</p>
                    <p class="small mb-3 text-light">Lic. Benito Pablo Juárez García</p>
                    <p class="small mb-0" style="color: #cbbba6; font-size: 0.78rem;">&copy; 2026 BiblioDesk · Proyecto académico UTHH</p>
                </div>
                <div class="col-12 col-sm-4 col-lg-2 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Ayuda y servicios</div>
                    <a href="../publico/servicios.php">Servicios</a>
                    <a href="../publico/preguntas_frecuentes.php">Preguntas frecuentes</a>
                </div>
                <div class="col-12 col-sm-4 col-lg-3 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Contacto y ubicación</div>
                    <a href="../publico/contacto.php">Contacto</a>
                    <a href="../publico/ubicacion.php">Ubicación</a>
                </div>
                <div class="col-12 col-sm-4 col-lg-3 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Información legal</div>
                    <a href="../publico/condiciones.php">Condiciones de uso</a>
                    <a href="../publico/reglamento.php">Reglamento</a>
                    <a href="../publico/privacidad.php">Aviso de privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>