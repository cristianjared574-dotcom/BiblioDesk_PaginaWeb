<?php
$bloquesCondiciones = [
    [
        'titulo' => '1. Alcance y uso responsable de la plataforma',
        'texto' => 'BiblioDesk es la plataforma digital de apoyo de la Biblioteca Pública Regional Lic. Benito Pablo Juárez García para consultar el catálogo bibliográfico, conocer los servicios de la sede y gestionar trámites relacionados con el acervo. El usuario se compromete a emplear el sitio con fines educativos, culturales e informativos, absteniéndose de realizar acciones que alteren su funcionamiento, intenten vulnerar la seguridad del sistema o afecten el acceso de otros lectores.'
    ],
    [
        'titulo' => '2. Registro, veracidad de datos y seguridad de la cuenta',
        'texto' => 'Para acceder a funciones de solicitud y consulta vinculadas a su perfil de lector es necesario contar con registro vigente proporcionado por la biblioteca o mediante los canales autorizados. El usuario es responsable de ingresar información verídica y actualizada, mantener la confidencialidad de su contraseña y notificar de inmediato cualquier uso no autorizado de su cuenta.'
    ],
    [
        'titulo' => '3. Solicitudes en línea, disponibilidad y cambios',
        'texto' => 'Realizar una solicitud desde la plataforma o revisar el estado de un título no garantiza por sí solo la entrega inmediata del ejemplar. Toda solicitud queda sujeta a revisión del personal bibliotecario, disponibilidad física real en sala o estantería, vigencia de la cuenta del usuario y cumplimiento de las normas de préstamo. La biblioteca puede actualizar la disponibilidad o el estado de los materiales conforme a la operación diaria.'
    ],
    [
        'titulo' => '4. Derechos de autor y uso de la información bibliográfica',
        'texto' => 'Las fichas técnicas, portadas, resúmenes, fragmentos y demás materiales mostrados en BiblioDesk se presentan con fines de orientación, consulta y fomento a la lectura. Los títulos, portadas, contenidos y marcas pertenecen a sus respectivos autores, editoriales o titulares de derechos. Queda prohibida la reproducción, distribución o explotación comercial no autorizada conforme a la legislación aplicable en materia de derechos de autor.'
    ],
    [
        'titulo' => '5. Modificaciones, soporte y canales de atención',
        'texto' => 'La Biblioteca Pública Regional Lic. Benito Pablo Juárez García podrá actualizar los contenidos, servicios o condiciones de uso de este sitio cuando resulte necesario para mejorar la atención o adecuar la operación. Para reportar incidencias, solicitar aclaraciones sobre su cuenta o resolver dudas sobre el portal, puede acudir al mostrador de atención o comunicarse mediante la sección Contacto.'
    ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Condiciones de uso</title>
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
        .tarjeta-legal {
            background-color: #ffffff;
            border: 1px solid #f0eadc;
            border-radius: 12px;
            padding: 1.65rem 1.85rem;
            box-shadow: 0 2px 6px rgba(44, 36, 30, 0.02);
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

    <main class="container py-5" style="max-width: 960px;">
        <div class="mb-4">
            <h1 class="display-6 fw-normal mb-2" style="font-family: Georgia, 'Times New Roman', serif;">Condiciones de uso</h1>
            <p class="text-muted mb-0">Lineamientos generales para el uso responsable del portal, la información del catálogo y los servicios digitales de BiblioDesk.</p>
        </div>

        <div class="d-flex flex-column gap-3">
            <?php foreach ($bloquesCondiciones as $bloque): ?>
                <section class="tarjeta-legal">
                    <h2 class="h5 fw-bold mb-2" style="color: #3b2316;">
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
                    <h5 class="fw-bold mb-2" style="font-family: Georgia, 'Times New Roman', serif;">BiblioDesk</h5>
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