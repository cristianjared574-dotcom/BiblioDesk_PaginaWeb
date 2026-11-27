<?php
// Datos de ubicación de la Biblioteca Pública Regional
$nombreBiblioteca = 'Biblioteca Pública Regional';
$subtituloBiblioteca = 'Lic. Benito Pablo Juárez García';
$direccion = 'Avenida Nuevo León 144';
$codigoPostal = 'C.P. 43000';
$ciudadEstado = 'Huejutla de Reyes, Hidalgo';
$latitud = 21.142934799194336;
$longitud = -98.41561889648438;

$urlMapaExterno = 'https://www.bing.com/maps/directions?name=Biblioteca+P%C3%BAblica+Regional&ismp=1&FORM=MPSRPL&style=r&rtp=%7Epos.'
    . $latitud . '_' . $longitud
    . '_Avenida%2520Nuevo%2520Le%25C3%25B3n%2520144%252C%252043000%2520Huejutla%2520de%2520Reyes%252C%2520Hidalgo_Biblioteca%2520P%25C3%25BAblica%2520Regional_ypid%3AYN9FC990D937087E8F&cp='
    . $latitud . '%7E' . $longitud . '&lvl=18';

$latJson = json_encode($latitud);
$lngJson = json_encode($longitud);
$nombreEsc = htmlspecialchars($nombreBiblioteca . ' ' . $subtituloBiblioteca, ENT_QUOTES, 'UTF-8');
$dirEsc = htmlspecialchars($direccion . ', ' . $ciudadEstado, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Ubicación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body { background-color: #fcfaf5; color: #2c241e; }
        .navbar-bd, .footer-bd { background-color: #3b2316; color: #f5efe6; }
        .navbar-bd .nav-link {
            color: #f5efe6;
            font-size: 0.92rem;
            padding: 0.4rem 0.75rem;
        }
        .navbar-bd .nav-link:hover,
        .navbar-bd .nav-link:focus,
        .navbar-bd .nav-link.active {
            color: #f3ead3;
        }
        .dropdown-menu-bd {
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding: 0.5rem 0 0 0;
            min-width: 195px;
        }
        .dropdown-menu-bd .dropdown-item {
            background-color: #f3ead3;
            color: #2c241e;
            border-radius: 6px;
            padding: 0.55rem 1rem;
            margin-bottom: 0.45rem;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 6px rgba(44, 36, 30, 0.14);
        }
        .dropdown-menu-bd .dropdown-item:hover,
        .dropdown-menu-bd .dropdown-item:focus {
            background-color: #e7d8b5;
            color: #2c241e;
        }
        #mapaBiblioteca {
            height: 330px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e6decb;
            z-index: 1;
        }
        .tarjeta-esperamos, .tarjeta-acceso {
            background-color: #ffffff;
            border: 1px solid #ebe4d5;
            border-radius: 12px;
            padding: 1.5rem;
        }
        .btn-vino {
            background-color: #7a2323;
            color: #ffffff;
            border: none;
            font-size: 0.88rem;
            padding: 0.5rem 1.1rem;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-vino:hover {
            background-color: #5e1919;
            color: #ffffff;
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdownQuienesSomos"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            ¿Quiénes somos?
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-bd" aria-labelledby="dropdownQuienesSomos">
                            <li><a class="dropdown-item" href="historia.php">Historia</a></li>
                            <li><a class="dropdown-item" href="mision.php">Misión</a></li>
                            <li><a class="dropdown-item" href="vision.php">Visión</a></li>
                            <li><a class="dropdown-item" href="objetivos.php">Objetivos</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="index.php#catalogo">Catálogo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="noticias.php">Noticias</a>
                    </li>

                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a href="../privado/login.php" class="btn btn-light btn-sm fw-semibold px-3">
                            Iniciar sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="mb-4">
            <h1 class="fw-bold mt-1 mb-1">Ubicación</h1>
            <p class="text-muted mb-0">
                Visita la <?= htmlspecialchars($nombreBiblioteca . ' ' . $subtituloBiblioteca, ENT_QUOTES, 'UTF-8') ?>.
            </p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-7">
                <div id="mapaBiblioteca" class="shadow-sm mb-2"></div>
                <div class="fw-semibold small"><?= htmlspecialchars($direccion . ' · ' . $ciudadEstado, ENT_QUOTES, 'UTF-8') ?></div>
                <p class="text-muted small mb-0">
                    Croquis interactivo con OpenStreetMap / Leaflet. Acerca o aleja el mapa para ubicar calles cercanas.
                </p>
            </div>

            <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                <div class="tarjeta-esperamos shadow-sm">
                    <h2 class="h5 fw-bold mb-2">Te esperamos</h2>
                    <p class="small text-muted mb-1"><?= htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="small text-muted mb-1"><?= htmlspecialchars($codigoPostal, ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="small text-muted mb-3"><?= htmlspecialchars($ciudadEstado, ENT_QUOTES, 'UTF-8') ?></p>

                    <a href="<?= htmlspecialchars($urlMapaExterno, ENT_QUOTES, 'UTF-8') ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-vino mb-2">
                        Cómo llegar · Abrir mapa
                    </a>

                    <p class="text-muted small mb-0" style="font-size: 0.78rem;">
                        Para dudas previas a tu visita, escríbenos en la sección <a href="contacto.php" class="text-dark fw-semibold">Contacto</a>.
                    </p>
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
                    <p class="small mb-0" style="color: #cbbba6; font-size: 0.78rem;">
                        &copy; 2026 BiblioDesk · Proyecto académico UTHH
                    </p>
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const lat = <?= $latJson ?>;
            const lng = <?= $lngJson ?>;
            const mapa = L.map('mapaBiblioteca').setView([lat, lng], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(mapa);

            L.marker([lat, lng])
                .addTo(mapa)
                .bindPopup('<strong><?= $nombreEsc ?></strong><br><?= $dirEsc ?>')
                .openPopup();
        })();
    </script>
</body>
</html>