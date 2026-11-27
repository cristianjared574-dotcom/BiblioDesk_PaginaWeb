<?php
require_once __DIR__ . '/../config/conexion.php';



$serviciosPrincipales = [
    [
        'titulo' => 'Consulta del acervo y catálogo en línea',
        'descripcion' => 'Explora los libros disponibles por título, autor o categoría, revisa sus fichas técnicas y verifica cuántos ejemplares hay disponibles antes de acudir.'
    ],
    [
        'titulo' => 'Acceso público y préstamo de materiales',
        'descripcion' => 'Consulta libros en sala o solicita el préstamo a domicilio de ejemplares físicos conforme a los requisitos y plazos establecidos por la biblioteca.'
    ],
    [
        'titulo' => 'Servicios de referencia y orientación',
        'descripcion' => 'Recibe apoyo del personal bibliotecario para localizar materiales, realizar búsquedas temáticas y resolver dudas sobre el uso del acervo.'
    ],
    [
        'titulo' => 'Formación, talleres y actividades culturales',
        'descripcion' => 'Participa en círculos de lectura, exposiciones, talleres de alfabetización informacional y actividades de difusión comunitaria.'
    ],
    [
        'titulo' => 'Catalogación y gestión de colecciones',
        'descripcion' => 'Organización, clasificación y actualización constante de metadatos y bases de datos para mantener un catálogo ordenado y accesible.'
    ],
    [
        'titulo' => 'Preservación y conservación preventiva',
        'descripcion' => 'Cuidado, resguardo y mantenimiento preventivo de los libros, documentos y fondos bibliográficos para garantizar su conservación.'
    ]
];

$derechosUsuario = [
    'Recibir información clara, veraz y suficiente sobre la disponibilidad, características y condiciones de préstamo de los materiales.',
    'Acceder a un servicio público de calidad, con trato digno, respetuoso e igualitario, sin discriminación alguna.',
    'Contar con la protección y confidencialidad de sus datos personales proporcionados al registrarse o utilizar los servicios.',
    'Conocer de manera transparente las reglas de acceso, plazos de entrega, posibles sanciones o costos aplicables en servicios especiales.',
    'Recibir asistencia, orientación bibliotecaria y soporte informativo adecuado para localizar materiales o usar la plataforma.',
    'Contar con condiciones razonables de accesibilidad y adaptaciones dentro de las instalaciones para personas con discapacidad.',
    'Recibir información clara sobre derechos de autor, licencias y usos permitidos respecto a la reproducción o consulta de obras.',
    'Presentar dudas, sugerencias o reclamaciones a través de los canales de contacto y recibir seguimiento oportuno.'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Servicios de la biblioteca</title>
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
        .tarjeta-servicio {
            background-color: #ffffff; border: 1px solid #ebe4d5; border-radius: 10px;
            padding: 1.5rem; height: 100%; box-shadow: 0 2px 6px rgba(44, 36, 30, 0.04);
        }
        .caja-derechos {
            background-color: #fefdf9; border: 1px solid #e9e2d2;
            border-radius: 12px; padding: 2rem;
        }
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
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <small class="text-uppercase fw-bold" style="color: #a67c37; letter-spacing: 1px;">Oferta bibliotecaria</small>
                <h1 class="fw-bold mt-1 mb-1">Servicios de la biblioteca</h1>
                <p class="text-muted mb-0">Conoce lo que ofrece la Biblioteca Pública Regional Lic. Benito Pablo Juárez García.</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <?php foreach ($serviciosPrincipales as $servicio): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="tarjeta-servicio">
                        <h2 class="h5 fw-bold mb-2"><?= htmlspecialchars($servicio['titulo']) ?></h2>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            <?= htmlspecialchars($servicio['descripcion']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <section class="caja-derechos shadow-sm">
            <h2 class="h4 fw-bold mb-1">Derechos de los usuarios y lectores</h2>
            <p class="text-muted small mb-4">Compromisos de transparencia, accesibilidad y calidad en la atención bibliotecaria.</p>

            <div class="row g-3">
                <?php foreach ($derechosUsuario as $derecho): ?>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-start gap-2 small">
                            <span class="fw-bold" style="color: #a67c37;">&bull;</span>
                            <span><?= htmlspecialchars($derecho) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
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