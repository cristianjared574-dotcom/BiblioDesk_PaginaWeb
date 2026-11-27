<?php
require_once __DIR__ . '/../config/conexion.php';

$nombre = '';
$correo = '';
$asunto = 'Consulta de catálogo y disponibilidad';
$mensaje = '';
$error = '';
$enviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $asunto = trim($_POST['asunto'] ?? 'Consulta de catálogo y disponibilidad');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre === '' || $correo === '' || $mensaje === '') {
        $error = 'Por favor completa todos los campos obligatorios (*).';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un correo electrónico válido.';
    } else {
        $enviado = true;
        $nombre = '';
        $correo = '';
        $asunto = 'Consulta de catálogo y disponibilidad';
        $mensaje = '';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Contacto</title>
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
        .caja-contacto {
            background-color: #ffffff; border: 1px solid #ebe4d5;
            border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(44, 36, 30, 0.04);
        }
        .form-control-bd, .form-select-bd {
            background-color: #f7f4ee; border: 1px solid #e3dfd7;
            border-radius: 8px; font-size: 0.92rem;
        }
        .form-control-bd:focus, .form-select-bd:focus {
            background-color: #ffffff; border-color: #a67c37;
            box-shadow: 0 0 0 0.15rem rgba(166, 124, 55, 0.2);
        }
        .btn-rojo-bd {
            background-color: #b02a37; color: #ffffff; border: none;
            font-size: 0.92rem; padding: 0.6rem 1.5rem; border-radius: 8px; font-weight: 600;
        }
        .btn-rojo-bd:hover { background-color: #92202b; color: #ffffff; }
        .footer-bd a { color: #e8dccb; text-decoration: none; }
        .footer-bd a:hover { color: #ffffff; text-decoration: underline; }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <nav class="navbar navbar-expand-lg navbar-bd px-3 px-md-5 py-3 sticky-top">
        <div class="container-fluid px-0">
            <a class="navbar-brand text-white fw-bold fs-4 mb-0" href="index.php">BiblioDesk</a>
            <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menuPrincipalBD" aria-controls="menuPrincipalBD"
                    aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="menuPrincipalBD">
                <ul class="navbar-nav align-items-lg-center gap-lg-2 my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownQuienesSomos"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">¿Quiénes somos?</a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-bd" aria-labelledby="dropdownQuienesSomos">
                            <li><a class="dropdown-item" href="historia.php">Historia</a></li>
                            <li><a class="dropdown-item" href="mision.php">Misión</a></li>
                            <li><a class="dropdown-item" href="vision.php">Visión</a></li>
                            <li><a class="dropdown-item" href="objetivos.php">Objetivos</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="index.php#catalogo">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link" href="noticias.php">Noticias</a></li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="../privado/login.php" class="btn btn-light btn-sm fw-semibold px-3">Iniciar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-9 col-lg-7">
                <div class="text-center mb-4">
                    <small class="text-uppercase fw-bold" style="color: #a67c37; letter-spacing: 1px;">Atención al lector</small>
                    <h1 class="fw-bold mt-1 mb-1">Contacto</h1>
                    <p class="text-muted mb-0">Escríbenos para resolver dudas sobre el catálogo, préstamos, servicios o visitas.</p>
                </div>

                <div class="caja-contacto">
                    <h2 class="h5 fw-bold mb-3">Envíanos un mensaje</h2>

                    <?php if ($enviado): ?>
                        <div class="alert alert-success small" role="alert">
                            Tu mensaje se envió correctamente. Gracias por comunicarte con la biblioteca.
                        </div>
                    <?php elseif ($error !== ''): ?>
                        <div class="alert alert-danger small" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contacto.php" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-semibold mb-1">Nombre completo *</label>
                            <input type="text"
                                   id="nombre"
                                   name="nombre"
                                   class="form-control form-control-bd"
                                   value="<?= htmlspecialchars($nombre) ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label small fw-semibold mb-1">Correo electrónico *</label>
                            <input type="email"
                                   id="correo"
                                   name="correo"
                                   class="form-control form-control-bd"
                                   value="<?= htmlspecialchars($correo) ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="mensaje" class="form-label small fw-semibold mb-1">Mensaje *</label>
                            <textarea id="mensaje"
                                      name="mensaje"
                                      rows="4"
                                      class="form-control form-control-bd"
                                      placeholder="Escribe aquí tu pregunta o comentario..."
                                      required><?= htmlspecialchars($mensaje) ?></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-rojo-bd px-4">Enviar mensaje</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="footer-bd py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <h5 class="fw-bold mb-2">BiblioDesk</h5>
                    <p class="small mb-1 text-light">Un espacio para descubrir.</p>
                    <p class="small mb-1 text-light">Biblioteca Pública Regional</p>
                    <p class="small mb-3 text-light">Lic. Benito Pablo Juárez García</p>
                    <p class="small mb-0" style="color: #cbbba6; font-size: 0.78rem;">&copy; 2026 BiblioDesk · Proyecto académico UTHH</p>
                </div>
                <div class="col-12 col-sm-4 col-lg-2 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Ayuda y servicios</div>
                    <a href="servicios.php">Servicios</a>
                    <a href="preguntas_frecuentes.php">Preguntas frecuentes</a>
                </div>
                <div class="col-12 col-sm-4 col-lg-3 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Contacto y ubicación</div>
                    <a href="contacto.php">Contacto</a>
                    <a href="ubicacion.php">Ubicación</a>
                </div>
                <div class="col-12 col-sm-4 col-lg-3 small d-flex flex-column gap-2">
                    <div class="fw-bold text-white mb-1">Información legal</div>
                    <a href="condiciones.php">Condiciones de uso</a>
                    <a href="reglamento.php">Reglamento</a>
                    <a href="privacidad.php">Aviso de privacidad</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>