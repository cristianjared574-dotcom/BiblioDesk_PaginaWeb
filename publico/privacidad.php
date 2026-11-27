<?php
$bloquesPrivacidad = [
    [
        'titulo' => 'Responsable del tratamiento',
        'texto' => 'La Biblioteca Pública Regional Lic. Benito Pablo Juárez García, ubicada en Avenida Nuevo León 144, C.P. 43000, Huejutla de Reyes, Hidalgo, es responsable del resguardo de los datos personales recabados en BiblioDesk.'
    ],
    [
        'titulo' => 'Datos recabados y finalidad',
        'texto' => 'Se recaban datos básicos de identificación y contacto (como nombre, matrícula, correo electrónico y teléfono) para administrar el registro de lectores, gestionar solicitudes y préstamos, y atender consultas de forma segura.'
    ],
    [
        'titulo' => 'Confidencialidad y no transferencia',
        'texto' => 'La información de los usuarios se protege mediante controles adecuados y se emplea únicamente para los fines del servicio bibliotecario; no se comparte ni comercializa con terceros ajenos sin causa legal o requerimiento de autoridad.'
    ],
    [
        'titulo' => 'Derechos ARCO y atención',
        'texto' => 'Puedes solicitar el acceso, rectificación, cancelación u oposición sobre tus datos personales (derechos ARCO) acudiendo directamente a la biblioteca o enviando un mensaje desde la sección de Contacto.'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Aviso de privacidad</title>
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
        .tarjeta-paso {
            background-color: #ffffff;
            border: 1px solid #f1ece0;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
        }
        .footer-bd a { color: #e8dccb; text-decoration: none; }
        .footer-bd a:hover { color: #ffffff; text-decoration: underline; }
    </style>
</head>
<body>

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

    <main class="container py-5" style="max-width: 920px;">
        <div class="mb-4">
            <h1 class="display-6 fw-normal titulo-serif mb-2">Aviso de privacidad</h1>
            <p class="text-muted mb-0">Protección y uso responsable de los datos personales de nuestros lectores.</p>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php foreach ($bloquesPrivacidad as $bloque): ?>
                <section class="tarjeta-paso">
                    <h2 class="h5 fw-bold titulo-serif mb-2">
                        <?= htmlspecialchars($bloque['titulo'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <p class="text-muted small mb-0" style="line-height: 1.65;">
                        <?= htmlspecialchars($bloque['texto'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </section>
            <?php endforeach; ?>
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