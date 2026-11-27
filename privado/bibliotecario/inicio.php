<?php
session_start();

// Candado de seguridad: sesión activa y rol BIBLIOTECARIO
if (!isset($_SESSION['admin_id']) || ($_SESSION['rol'] ?? '') !== 'BIBLIOTECARIO') {
    header('Location: ../login.php?error=acceso_denegado');
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

$nombreUsuario = $_SESSION['admin_nombre'] ?? 'Usuario';
$rolEtiqueta = strtolower($_SESSION['rol'] ?? 'bibliotecario');

/**
 * Ejecuta una consulta de conteo en MySQLi procedimental y devuelve 0 si falla o no existe la columna.
 */
function bd_contar_filas($conexion, $sql) {
    if (!isset($conexion) || !($conexion instanceof mysqli)) {
        return 0;
    }
    try {
        $res = mysqli_query($conexion, $sql);
        if ($res) {
            $total = mysqli_num_rows($res);
            mysqli_free_result($res);
            return $total;
        }
    } catch (mysqli_sql_exception $e) {
        return 0;
    }
    return 0;
}

// Contadores seguros según columnas habituales en PRESTAMO y SOLICITUD
$prestamosEnCurso = bd_contar_filas(
    $conexion,
    "SELECT ID_PRESTAMO FROM PRESTAMO WHERE FECHA_DEVOLUCION IS NULL"
);

$solicitudesAbiertas = bd_contar_filas(
    $conexion,
    "SELECT ID_SOLICITUD FROM SOLICITUD"
);

$prestamosAtrasados = bd_contar_filas(
    $conexion,
    "SELECT ID_PRESTAMO FROM PRESTAMO WHERE FECHA_DEVOLUCION IS NULL AND FECHA_VENCIMIENTO < CURDATE()"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - BiblioDesk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            background-color: #fcfaf5;
            color: #2c241e;
            margin: 0;
            width: 100%;
            min-height: 100vh;
        }
        .layout-panel {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Barra lateral izquierda */
        .sidebar-bd {
            width: 230px;
            min-width: 230px;
            background-color: #3b2316;
            color: #f5efe6;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar-subtitulo {
            font-size: 0.68rem;
            letter-spacing: 0.08em;
            color: #d7b26d;
            font-weight: 700;
            margin: 1.25rem 0 0.6rem 0.5rem;
        }
        .sidebar-link {
            display: block;
            color: #f5efe6;
            text-decoration: none;
            padding: 0.55rem 0.75rem;
            border-radius: 7px;
            font-size: 0.88rem;
            margin-bottom: 0.2rem;
        }
        .sidebar-link:hover,
        .sidebar-link:focus {
            background-color: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        .sidebar-link.activo {
            background-color: #691b14;
            color: #ffffff;
            font-weight: 600;
        }
        .btn-cerrar-bd {
            background-color: #f3ead3;
            color: #2c241e;
            border: none;
            border-radius: 7px;
            padding: 0.45rem 0.9rem;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.25rem;
            width: fit-content;
        }
        .btn-cerrar-bd:hover {
            background-color: #e7d8b5;
            color: #2c241e;
        }

        /* Contenedor derecho a pantalla completa */
        .contenido-derecho {
            flex: 1;
            width: 100%;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        /* Barra superior crema con usuario hasta la esquina derecha */
        .topbar-crema {
            background-color: #f3ead3;
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
        .topbar-usuario {
            margin-left: auto;
            font-size: 0.85rem;
            color: #3b2316;
            white-space: nowrap;
        }

        .main-panel {
            width: 100%;
            flex: 1;
            padding: 2rem 2.25rem;
        }
        .titulo-serif {
            font-family: Georgia, "Times New Roman", serif;
            color: #241911;
        }
        .tarjeta-metrica {
            background-color: #f3ead3;
            border-radius: 12px;
            padding: 1.45rem 1.6rem;
            width: 100%;
        }
        .banner-prioridad {
            background-color: #f3ead3;
            border-radius: 12px;
            padding: 1.5rem 1.75rem;
            width: 100%;
        }
        .tarjeta-accion {
            background-color: #ffffff;
            border: 1px solid #f1ece0;
            border-radius: 12px;
            padding: 1.5rem;
            width: 100%;
        }
        .btn-guinda-bd {
            background-color: #691b14;
            color: #ffffff;
            border: none;
            border-radius: 7px;
            padding: 0.5rem 1.1rem;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-guinda-bd:hover {
            background-color: #51130e;
            color: #ffffff;
        }
        .btn-crema-bd {
            background-color: #f3ead3;
            color: #2c241e;
            border: none;
            border-radius: 7px;
            padding: 0.5rem 1.1rem;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-crema-bd:hover {
            background-color: #e7d8b5;
            color: #2c241e;
        }

        @media (max-width: 767.98px) {
            .layout-panel {
                flex-direction: column;
            }
            .sidebar-bd {
                width: 100%;
                min-width: 100%;
            }
            .main-panel {
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="layout-panel">
        <!-- Menú lateral -->
        <aside class="sidebar-bd">
            <a href="../../publico/index.php" class="text-decoration-none text-white d-block px-2">
                <div class="fw-bold fs-4 titulo-serif text-white">BiblioDesk</div>
                <div style="font-size: 0.65rem; letter-spacing: 0.08em; color: #e8dccb;">UN ESPACIO PARA DESCUBRIR</div>
            </a>

            <div class="sidebar-subtitulo">BIBLIOTECARIO</div>

            <nav aria-label="Navegación de bibliotecario">
                <a href="inicio.php" class="sidebar-link activo">Inicio</a>
                <a href="registros_libros.php" class="sidebar-link">Gestión de libros</a>
                <a href="ejemplares.php" class="sidebar-link">Gestión de ejemplares</a>
                <a href="lectores.php" class="sidebar-link">Gestión de lectores</a>
                <a href="categorias.php" class="sidebar-link">Categorías</a>
                <a href="autores.php" class="sidebar-link">Autores</a>
                <a href="editoriales.php" class="sidebar-link">Editoriales</a>
                <a href="solicitudes.php" class="sidebar-link">Solicitudes</a>
                <a href="prestamos.php" class="sidebar-link">Préstamos y devoluciones</a>
                <a href="reportes.php" class="sidebar-link">Reportes</a>
                <a href="perfil.php" class="sidebar-link">Mi perfil</a>
            </nav>

            <a href="../logout.php" class="btn-cerrar-bd">Cerrar sesión</a>
        </aside>

        <!-- Área principal a pantalla completa -->
        <div class="contenido-derecho">
            <header class="topbar-crema">
                <span class="small text-muted">Inicio</span>
                <span class="topbar-usuario">
                    <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars($rolEtiqueta, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </header>

            <main class="main-panel">
                <h1 class="h2 fw-normal titulo-serif mb-1">Cada libro entregado, una nueva historia.</h1>
                <p class="text-muted small mb-4">Organiza la atención del día y da seguimiento a cada movimiento.</p>

                <!-- Métricas -->
                <div class="row g-3 mb-4 w-100 mx-0">
                    <div class="col-12 col-md-4 ps-0 pe-md-2">
                        <div class="tarjeta-metrica">
                            <div class="h3 fw-normal titulo-serif mb-1"><?= $prestamosEnCurso ?></div>
                            <div class="small text-muted">Préstamos en curso</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 px-md-2">
                        <div class="tarjeta-metrica">
                            <div class="h3 fw-normal titulo-serif mb-1"><?= $solicitudesAbiertas ?></div>
                            <div class="small text-muted">Solicitudes abiertas</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 pe-0 ps-md-2">
                        <div class="tarjeta-metrica">
                            <div class="h3 fw-normal titulo-serif mb-1"><?= $prestamosAtrasados ?></div>
                            <div class="small text-muted">Préstamos atrasados</div>
                        </div>
                    </div>
                </div>

                <!-- Prioridad del día -->
                <div class="banner-prioridad mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="small text-uppercase fw-semibold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.06em;">
                            Prioridad del día
                        </div>
                        <h2 class="h4 fw-normal titulo-serif mb-1">Hay lectores esperando una respuesta.</h2>
                        <p class="small text-muted mb-0">
                            Revisa las solicitudes pendientes y asigna un ejemplar disponible para preparar su recogida.
                        </p>
                    </div>
                    <div class="d-flex flex-column align-items-md-end gap-2">
                        <a href="solicitudes.php" class="btn-guinda-bd">Revisar solicitud</a>
                        <a href="solicitudes.php" class="small fw-semibold text-decoration-none" style="color: #3b2316;">
                            Ver solicitudes
                        </a>
                    </div>
                </div>

                <!-- Acciones de mostrador -->
                <div class="row g-3 mb-4 w-100 mx-0">
                    <div class="col-12 col-md-6 ps-0 pe-md-2">
                        <div class="tarjeta-accion h-100">
                            <h3 class="h5 fw-normal titulo-serif mb-2">Entrega en mostrador</h3>
                            <p class="small text-muted mb-3">
                                Confirma el ejemplar reservado, identifica al lector y registra la entrega física.
                            </p>
                            <a href="prestamos.php?accion=entrega" class="btn-crema-bd">Registrar entrega</a>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 pe-0 ps-md-2">
                        <div class="tarjeta-accion h-100">
                            <h3 class="h5 fw-normal titulo-serif mb-2">Recibir una devolución</h3>
                            <p class="small text-muted mb-3">
                                Revisa el estado físico del libro y cierra el préstamo conservando el historial.
                            </p>
                            <a href="prestamos.php?accion=devolucion" class="btn-crema-bd">Registrar devolución</a>
                        </div>
                    </div>
                </div>

                <!-- Atención sin solicitud previa -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-2 w-100">
                    <h3 class="h5 fw-normal titulo-serif mb-0">Atención sin solicitud previa</h3>
                    <a href="prestamos.php?accion=nuevo" class="btn-crema-bd">Registrar préstamo</a>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>