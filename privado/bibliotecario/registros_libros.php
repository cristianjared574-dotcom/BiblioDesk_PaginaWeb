<?php
session_start();

// Candado de seguridad: sesión activa y rol BIBLIOTECARIO
if (empty($_SESSION['admin_id']) || strtoupper((string)($_SESSION['rol'] ?? '')) !== 'BIBLIOTECARIO') {
    header('Location: ../login.php?error=acceso_denegado');
    exit;
}

require_once __DIR__ . '/../../config/conexion.php';

if (!isset($conexion) || !($conexion instanceof mysqli) || mysqli_connect_errno()) {
    http_response_code(500);
    exit('Error de conexión con la base de datos.');
}

mysqli_set_charset($conexion, 'utf8mb4');

$nombre = (string)($_SESSION['admin_nombre'] ?? 'Alex');
$rol = strtoupper((string)($_SESSION['rol'] ?? 'BIBLIOTECARIO'));

$buscar = trim((string)($_GET['buscar'] ?? ''));
$estado = strtoupper(trim((string)($_GET['estado'] ?? '')));
$estadoValido = in_array($estado, ['ACTIVO', 'INACTIVO'], true) ? $estado : '';
$hayFiltro = ($buscar !== '' || $estadoValido !== '');

$mensajes = [
    'registrado'  => ['tipo' => 'success', 'texto' => 'El libro se registró correctamente.'],
    'actualizado' => ['tipo' => 'success', 'texto' => 'El libro se actualizó correctamente.'],
    'eliminado'   => ['tipo' => 'warning', 'texto' => 'El estado del libro se actualizó.'],
];
$alerta = $mensajes[$_GET['mensaje'] ?? ''] ?? null;

$sql = "SELECT
            L.ID_LIBRO,
            L.CLAVE_LIBRO,
            L.TITULO,
            L.ESTADO,
            C.NOMBRE_CATEGORIA,
            GROUP_CONCAT(DISTINCT A.NOMBRE_COMPLETO ORDER BY A.NOMBRE_COMPLETO SEPARATOR ', ') AS AUTORES
        FROM LIBRO L
        INNER JOIN CATEGORIA C ON L.ID_CATEGORIA = C.ID_CATEGORIA
        LEFT JOIN LIBRO_AUTOR LA ON L.ID_LIBRO = LA.ID_LIBRO
        LEFT JOIN AUTOR A ON LA.ID_AUTOR = A.ID_AUTOR
        WHERE 1=1";

$tipos = '';
$params = [];

if ($buscar !== '') {
    $sql .= " AND (L.CLAVE_LIBRO LIKE ? OR L.TITULO LIKE ? OR C.NOMBRE_CATEGORIA LIKE ? OR A.NOMBRE_COMPLETO LIKE ?)";
    $like = "%{$buscar}%";
    $tipos .= 'ssss';
    array_push($params, $like, $like, $like, $like);
}

if ($estadoValido !== '') {
    $sql .= " AND L.ESTADO = ?";
    $tipos .= 's';
    $params[] = $estadoValido;
}

$sql .= " GROUP BY L.ID_LIBRO, L.CLAVE_LIBRO, L.TITULO, L.ESTADO, C.NOMBRE_CATEGORIA
          ORDER BY L.ID_LIBRO DESC";

$stmt = mysqli_prepare($conexion, $sql);
if (!$stmt) {
    http_response_code(500);
    exit('No se pudo preparar la consulta de libros.');
}

if ($tipos !== '') {
    mysqli_stmt_bind_param($stmt, $tipos, ...$params);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

$libros = [];
if ($resultado) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $libros[] = $fila;
    }
    mysqli_free_result($resultado);
}

mysqli_stmt_close($stmt);

$enlacesMenu = [
    ['href' => 'inicio.php', 'label' => 'Inicio'],
    ['href' => 'registros_libros.php', 'label' => 'Gestión de libros', 'activo' => true],
    ['href' => 'ejemplares.php', 'label' => 'Gestión de ejemplares'],
    ['href' => 'lectores.php', 'label' => 'Gestión de lectores'],
    ['href' => 'categorias.php', 'label' => 'Categorías'],
    ['href' => 'autores.php', 'label' => 'Autores'],
    ['href' => 'editoriales.php', 'label' => 'Editoriales'],
    ['href' => 'solicitudes.php', 'label' => 'Solicitudes'],
    ['href' => 'prestamos.php', 'label' => 'Préstamos y devoluciones'],
    ['href' => 'reportes.php', 'label' => 'Reportes'],
    ['href' => 'perfil.php', 'label' => 'Mi perfil'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioDesk - Gestión de libros</title>
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
        .contenido-derecho {
            flex: 1;
            width: 100%;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .topbar-crema {
            background-color: #f3ead3;
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border-bottom: 1px solid #e5dcc8;
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
        .panel-filtro {
            background-color: #f3ead3;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            width: 100%;
        }
        .tarjeta-tabla {
            background-color: #fefdf9;
            border: 1px solid #e9e2d2;
            border-radius: 12px;
            padding: 1.5rem;
            width: 100%;
        }
        .tabla-bibliodesk thead th {
            background-color: #f3ead3;
            color: #a66a4e;
            font-weight: 600;
            border: none;
            padding: 12px 14px;
        }
        .tabla-bibliodesk tbody td {
            padding: 14px;
            border-color: #f0e9d8;
            vertical-align: middle;
        }
        .form-control, .form-select {
            background-color: #fcfaf5;
            border-color: #e6decb;
        }
        .btn-guinda {
            background-color: #7a1c1c;
            color: #fff;
            border: none;
        }
        .btn-guinda:hover {
            background-color: #5e1313;
            color: #fff;
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
    <aside class="sidebar-bd">
        <a href="../../publico/index.php" class="text-decoration-none text-white d-block px-2">
            <div class="fw-bold fs-4 titulo-serif text-white">BiblioDesk</div>
            <div style="font-size: 0.65rem; letter-spacing: 0.08em; color: #e8dccb;">UN ESPACIO PARA DESCUBRIR</div>
        </a>

        <div class="sidebar-subtitulo"><?= htmlspecialchars($rol) ?></div>

        <nav aria-label="Navegación de bibliotecario">
            <?php foreach ($enlacesMenu as $item): ?>
                <a href="<?= htmlspecialchars($item['href']) ?>"
                   class="sidebar-link <?= !empty($item['activo']) ? 'activo' : '' ?>">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <a href="../logout.php" class="btn-cerrar-bd">Cerrar sesión</a>
    </aside>

    <div class="contenido-derecho">
        <header class="topbar-crema sticky-top">
            <span class="small fw-bold">Gestión de libros</span>
            <span class="topbar-usuario">
                <?= htmlspecialchars($nombre) ?> &middot; <?= htmlspecialchars(strtolower($rol)) ?>
            </span>
        </header>

        <main class="main-panel">
            <small class="text-uppercase fw-bold" style="color: #a66a4e; letter-spacing: 1px;">
                BIBLIODESK &middot; BIBLIOTECA
            </small>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-1 mb-1">
                <h1 class="mb-0 titulo-serif">Gestión de libros</h1>
                <a href="libro_registrar.php" class="btn btn-guinda px-3 py-2">+ Registrar</a>
            </div>
            <p class="text-muted mb-4">Consulta y organiza los registros de la biblioteca.</p>

            <?php if ($alerta !== null): ?>
                <div class="alert alert-<?= htmlspecialchars($alerta['tipo']) ?> py-2">
                    <?= htmlspecialchars($alerta['texto']) ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="registros_libros.php" class="panel-filtro mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-7 col-lg-8">
                        <label for="buscar" class="form-label fw-semibold small">
                            Buscar por nombre, código o datos del registro
                        </label>
                        <input type="text" name="buscar" id="buscar" class="form-control"
                               value="<?= htmlspecialchars($buscar) ?>">
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 col-lg-2">
                        <label for="estado" class="form-label fw-semibold small">Estado</label>
                        <select name="estado" id="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="ACTIVO" <?= $estadoValido === 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                            <option value="INACTIVO" <?= $estadoValido === 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6 col-md-2 col-lg-2 d-grid">
                        <button type="submit" class="btn btn-guinda">Buscar</button>
                    </div>
                </div>
            </form>

            <div class="tarjeta-tabla">
                <div class="table-responsive">
                    <table class="table tabla-bibliodesk mb-0">
                        <thead>
                            <tr>
                                <th>Clave</th>
                                <th>Libro</th>
                                <th>Autores</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($libros !== []): ?>
                                <?php foreach ($libros as $libro): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$libro['CLAVE_LIBRO']) ?></td>
                                        <td><?= htmlspecialchars((string)$libro['TITULO']) ?></td>
                                        <td><?= htmlspecialchars((string)($libro['AUTORES'] ?? 'Sin autor')) ?></td>
                                        <td><?= htmlspecialchars((string)$libro['NOMBRE_CATEGORIA']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst(strtolower((string)$libro['ESTADO']))) ?></td>
                                        <td class="text-nowrap">
                                            <a href="libro_registrar.php?id=<?= (int)$libro['ID_LIBRO'] ?>"
                                               class="text-decoration-none text-dark">Ver / editar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <?= $hayFiltro
                                            ? 'No se encontraron libros que coincidan con el filtro.'
                                            : 'No hay libros registrados todavía.' ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>