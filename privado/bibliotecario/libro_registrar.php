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

$idLibro = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;
$esEdicion = ($idLibro > 0);

// Cargar catálogos (categorías, autores y editoriales) en MySQLi procedimental
$categorias = [];
$resCat = mysqli_query($conexion, "SELECT ID_CATEGORIA, NOMBRE_CATEGORIA FROM CATEGORIA ORDER BY NOMBRE_CATEGORIA ASC");
if ($resCat) {
    while ($fila = mysqli_fetch_assoc($resCat)) {
        $categorias[] = $fila;
    }
    mysqli_free_result($resCat);
}

$autoresCatalogo = [];
$resAut = mysqli_query($conexion, "SELECT ID_AUTOR, NOMBRE_COMPLETO FROM AUTOR ORDER BY NOMBRE_COMPLETO ASC");
if ($resAut) {
    while ($fila = mysqli_fetch_assoc($resAut)) {
        $autoresCatalogo[] = $fila;
    }
    mysqli_free_result($resAut);
}

$editoriales = [];
$resEd = mysqli_query($conexion, "SELECT ID_EDITORIAL, NOMBRE_EDITORIAL FROM EDITORIAL ORDER BY NOMBRE_EDITORIAL ASC");
if ($resEd) {
    while ($fila = mysqli_fetch_assoc($resEd)) {
        $editoriales[] = $fila;
    }
    mysqli_free_result($resEd);
}

// Valores por defecto del formulario
$datos = [
    'CLAVE_LIBRO'      => '',
    'TITULO'           => '',
    'ID_CATEGORIA'     => '',
    'ID_EDITORIAL'     => '',
    'ISBN'             => '',
    'ANIO_PUBLICACION' => '',
    'EDICION'          => '',
    'NUM_PAGINAS'      => '',
    'PORTADA'          => '',
    'ESTADO'           => 'ACTIVO',
    'DESCRIPCION'      => '',
];
$autoresSeleccionados = [];
$errores = [];

// Si es edición, cargar datos actuales del libro
if ($esEdicion && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmtLib = mysqli_prepare($conexion, "SELECT * FROM LIBRO WHERE ID_LIBRO = ? LIMIT 1");
    if ($stmtLib) {
        mysqli_stmt_bind_param($stmtLib, "i", $idLibro);
        mysqli_stmt_execute($stmtLib);
        $resLib = mysqli_stmt_get_result($stmtLib);
        $libroActual = $resLib ? mysqli_fetch_assoc($resLib) : null;
        if ($resLib) {
            mysqli_free_result($resLib);
        }
        mysqli_stmt_close($stmtLib);

        if ($libroActual) {
            foreach ($datos as $campo => $valorDefecto) {
                if (array_key_exists($campo, $libroActual) && $libroActual[$campo] !== null) {
                    $datos[$campo] = (string)$libroActual[$campo];
                }
            }

            $stmtAutLib = mysqli_prepare($conexion, "SELECT ID_AUTOR FROM LIBRO_AUTOR WHERE ID_LIBRO = ?");
            if ($stmtAutLib) {
                mysqli_stmt_bind_param($stmtAutLib, "i", $idLibro);
                mysqli_stmt_execute($stmtAutLib);
                $resAutLib = mysqli_stmt_get_result($stmtAutLib);
                while ($rowAut = mysqli_fetch_assoc($resAutLib)) {
                    $autoresSeleccionados[] = (int)$rowAut['ID_AUTOR'];
                }
                if ($resAutLib) {
                    mysqli_free_result($resAutLib);
                }
                mysqli_stmt_close($stmtAutLib);
            }
        } else {
            header('Location: registros_libros.php');
            exit;
        }
    }
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLibroPost = isset($_POST['id_libro']) && ctype_digit((string)$_POST['id_libro']) ? (int)$_POST['id_libro'] : 0;
    if ($idLibroPost > 0) {
        $idLibro = $idLibroPost;
        $esEdicion = true;
    }

    $datos['TITULO']           = trim((string)($_POST['titulo'] ?? ''));
    $datos['ID_CATEGORIA']     = trim((string)($_POST['id_categoria'] ?? ''));
    $datos['ID_EDITORIAL']     = trim((string)($_POST['id_editorial'] ?? ''));
    $datos['ISBN']             = trim((string)($_POST['isbn'] ?? ''));
    $datos['ANIO_PUBLICACION'] = trim((string)($_POST['anio_publicacion'] ?? ''));
    $datos['EDICION']          = trim((string)($_POST['edicion'] ?? ''));
    $datos['NUM_PAGINAS']      = trim((string)($_POST['num_paginas'] ?? ''));
    $datos['ESTADO']           = strtoupper(trim((string)($_POST['estado'] ?? 'ACTIVO')));
    $datos['DESCRIPCION']      = trim((string)($_POST['descripcion'] ?? ''));
    $datos['PORTADA']          = trim((string)($_POST['portada_actual'] ?? ''));

    $autoresSeleccionados = [];
    if (!empty($_POST['autores']) && is_array($_POST['autores'])) {
        foreach ($_POST['autores'] as $idAut) {
            if (ctype_digit((string)$idAut)) {
                $autoresSeleccionados[] = (int)$idAut;
            }
        }
    }

    if ($datos['TITULO'] === '') {
        $errores[] = 'El campo Título es obligatorio.';
    }
    if ($datos['ID_CATEGORIA'] === '' || !ctype_digit($datos['ID_CATEGORIA'])) {
        $errores[] = 'Debes seleccionar una Categoría.';
    }
    if (empty($autoresSeleccionados)) {
        $errores[] = 'Debes seleccionar al menos un Autor.';
    }
    if ($datos['ID_EDITORIAL'] === '' || !ctype_digit($datos['ID_EDITORIAL'])) {
        $errores[] = 'Debes seleccionar una Editorial.';
    }
    if (!in_array($datos['ESTADO'], ['ACTIVO', 'INACTIVO'], true)) {
        $errores[] = 'Debes seleccionar un Estado válido.';
    }
    if ($datos['ANIO_PUBLICACION'] !== '' && (!ctype_digit($datos['ANIO_PUBLICACION']) || (int)$datos['ANIO_PUBLICACION'] < 1000 || (int)$datos['ANIO_PUBLICACION'] > 2100)) {
        $errores[] = 'Ingresa un año de publicación válido.';
    }
    if ($datos['NUM_PAGINAS'] !== '' && (!ctype_digit($datos['NUM_PAGINAS']) || (int)$datos['NUM_PAGINAS'] <= 0)) {
        $errores[] = 'Ingresa un número de páginas válido.';
    }

    // Subida de portada (JPG, PNG o WebP, máximo 3 MB)
    $rutaNuevaPortada = null;
    if (isset($_FILES['portada']) && (int)$_FILES['portada']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ((int)$_FILES['portada']['error'] !== UPLOAD_ERR_OK) {
            $errores[] = 'Ocurrió un error al subir la imagen de portada.';
        } else {
            $tamanoBytes = (int)$_FILES['portada']['size'];
            $maxBytes = 3 * 1024 * 1024;
            if ($tamanoBytes > $maxBytes) {
                $errores[] = 'La portada no puede superar los 3 MB.';
            }

            $infoImg = @getimagesize($_FILES['portada']['tmp_name']);
            $mimePermitidos = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
            ];
            $mimeDetectado = $infoImg['mime'] ?? '';
            if (!isset($mimePermitidos[$mimeDetectado])) {
                $errores[] = 'Solo se permiten imágenes JPG, PNG o WebP.';
            }

            if (empty($errores)) {
                $carpetaPortadas = __DIR__ . '/../../uploads/galeria';
                if (!is_dir($carpetaPortadas)) {
                    @mkdir($carpetaPortadas, 0777, true);
                }
                $ext = $mimePermitidos[$mimeDetectado];
                $nombreArchivo = 'portada_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                $destinoFisico = $carpetaPortadas . '/' . $nombreArchivo;

                if (move_uploaded_file($_FILES['portada']['tmp_name'], $destinoFisico)) {
                    $rutaNuevaPortada = 'uploads/portadas/' . $nombreArchivo;
                    $datos['PORTADA'] = $rutaNuevaPortada;
                } else {
                    $errores[] = 'No se pudo guardar la imagen en la carpeta de portadas.';
                }
            }
        }
    }

    if (empty($errores)) {
        $idCat = (int)$datos['ID_CATEGORIA'];
        $idEd  = (int)$datos['ID_EDITORIAL'];
        $isbnVal = $datos['ISBN'] !== '' ? $datos['ISBN'] : null;
        $anioVal = $datos['ANIO_PUBLICACION'] !== '' ? (int)$datos['ANIO_PUBLICACION'] : null;
        $edicionVal = $datos['EDICION'] !== '' ? $datos['EDICION'] : null;
        $paginasVal = $datos['NUM_PAGINAS'] !== '' ? (int)$datos['NUM_PAGINAS'] : null;
        $portadaVal = $datos['PORTADA'] !== '' ? $datos['PORTADA'] : null;
        $descVal = $datos['DESCRIPCION'] !== '' ? $datos['DESCRIPCION'] : null;

        mysqli_begin_transaction($conexion);
        $okOperacion = false;

        if ($esEdicion) {
            $sqlUpdate = "UPDATE LIBRO
                          SET TITULO = ?, ID_CATEGORIA = ?, ID_EDITORIAL = ?, ISBN = ?,
                              ANIO_PUBLICACION = ?, EDICION = ?, NUM_PAGINAS = ?,
                              PORTADA = ?, ESTADO = ?, DESCRIPCION = ?
                          WHERE ID_LIBRO = ?";
            $stmtUpd = mysqli_prepare($conexion, $sqlUpdate);
            if ($stmtUpd) {
                mysqli_stmt_bind_param(
                    $stmtUpd,
                    "siisisisssi",
                    $datos['TITULO'],
                    $idCat,
                    $idEd,
                    $isbnVal,
                    $anioVal,
                    $edicionVal,
                    $paginasVal,
                    $portadaVal,
                    $datos['ESTADO'],
                    $descVal,
                    $idLibro
                );
                if (mysqli_stmt_execute($stmtUpd)) {
                    $okOperacion = true;
                } else {
                    $errores[] = 'No se pudo actualizar el libro: ' . mysqli_error($conexion);
                }
                mysqli_stmt_close($stmtUpd);
            }

            if ($okOperacion) {
                $stmtDelAut = mysqli_prepare($conexion, "DELETE FROM LIBRO_AUTOR WHERE ID_LIBRO = ?");
                if ($stmtDelAut) {
                    mysqli_stmt_bind_param($stmtDelAut, "i", $idLibro);
                    $okOperacion = mysqli_stmt_execute($stmtDelAut);
                    mysqli_stmt_close($stmtDelAut);
                } else {
                    $okOperacion = false;
                }
            }
        } else {
            // Generar clave automática LIB0001, LIB0002...
            $siguienteNum = 1;
            $resMax = mysqli_query($conexion, "SELECT COALESCE(MAX(ID_LIBRO), 0) + 1 AS SIGUIENTE FROM LIBRO");
            if ($resMax) {
                $rowMax = mysqli_fetch_assoc($resMax);
                $siguienteNum = (int)($rowMax['SIGUIENTE'] ?? 1);
                mysqli_free_result($resMax);
            }
            $claveGenerada = 'LIB' . str_pad((string)$siguienteNum, 4, '0', STR_PAD_LEFT);

            $sqlInsert = "INSERT INTO LIBRO
                            (CLAVE_LIBRO, TITULO, ID_CATEGORIA, ID_EDITORIAL, ISBN,
                             ANIO_PUBLICACION, EDICION, NUM_PAGINAS, PORTADA, ESTADO, DESCRIPCION)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtIns = mysqli_prepare($conexion, $sqlInsert);
            if ($stmtIns) {
                mysqli_stmt_bind_param(
                    $stmtIns,
                    "ssiisisisss",
                    $claveGenerada,
                    $datos['TITULO'],
                    $idCat,
                    $idEd,
                    $isbnVal,
                    $anioVal,
                    $edicionVal,
                    $paginasVal,
                    $portadaVal,
                    $datos['ESTADO'],
                    $descVal
                );
                if (mysqli_stmt_execute($stmtIns)) {
                    $idLibro = (int)mysqli_insert_id($conexion);
                    $okOperacion = ($idLibro > 0);
                } else {
                    $errores[] = 'No se pudo registrar el libro: ' . mysqli_error($conexion);
                }
                mysqli_stmt_close($stmtIns);
            }
        }

        // Guardar autores seleccionados en LIBRO_AUTOR
        if ($okOperacion && $idLibro > 0) {
            $stmtRel = mysqli_prepare($conexion, "INSERT INTO LIBRO_AUTOR (ID_LIBRO, ID_AUTOR) VALUES (?, ?)");
            if ($stmtRel) {
                foreach ($autoresSeleccionados as $idAutorSel) {
                    mysqli_stmt_bind_param($stmtRel, "ii", $idLibro, $idAutorSel);
                    if (!mysqli_stmt_execute($stmtRel)) {
                        $okOperacion = false;
                        $errores[] = 'No se pudo asignar el autor al libro.';
                        break;
                    }
                }
                mysqli_stmt_close($stmtRel);
            } else {
                $okOperacion = false;
            }
        }

        if ($okOperacion) {
            mysqli_commit($conexion);
            $msg = $esEdicion ? 'actualizado' : 'registrado';
            header('Location: registros_libros.php?mensaje=' . $msg);
            exit;
        } else {
            mysqli_rollback($conexion);
            if ($rutaNuevaPortada !== null) {
                $archivoSubido = __DIR__ . '/../../' . $rutaNuevaPortada;
                if (is_file($archivoSubido)) {
                    @unlink($archivoSubido);
                }
            }
        }
    }
}

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
    <title>BiblioDesk - <?= $esEdicion ? 'Editar libro' : 'Registrar libro' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fcfaf5; color: #2c241e; }
        .font-serif-bd { font-family: Georgia, "Times New Roman", serif; color: #241911; }
        .sidebar-bd { width: 230px; min-width: 230px; background-color: #3b2316; color: #f5efe6; }
        .sidebar-bd .nav-link { color: #f5efe6; }
        .sidebar-bd .nav-link:hover { background-color: rgba(255, 255, 255, 0.08); color: #fff; }
        .sidebar-bd .nav-link.active { background-color: #691b14; color: #fff; }
        .topbar-bd { background-color: #f3ead3; border-bottom: 1px solid #e5dcc8; color: #3b2316; }
        .bg-crema-bd { background-color: #f3ead3; color: #2c241e; }
        .bg-crema-bd:hover { background-color: #e7d8b5; color: #2c241e; }
        .btn-guinda-bd { background-color: #691b14; color: #fff; }
        .btn-guinda-bd:hover { background-color: #51130e; color: #fff; }
        .form-control, .form-select { background-color: #fcfaf5; border-color: #e6decb; }
        @media (max-width: 767.98px) {
            .sidebar-bd { width: 100%; min-width: 100%; }
        }
    </style>
</head>
<body>

<div class="d-flex flex-column flex-md-row min-vh-100 w-100">
    <aside class="sidebar-bd p-3 d-flex flex-column">
        <a href="../../publico/index.php" class="text-decoration-none text-white px-2 mb-3">
            <div class="fw-bold fs-4 font-serif-bd text-white">BiblioDesk</div>
            <div class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.08em; color: #e8dccb;">Un espacio para descubrir</div>
        </a>

        <div class="text-uppercase fw-bold px-2 mb-2" style="font-size: 0.68rem; letter-spacing: 0.08em; color: #d7b26d;">
            <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <nav class="nav flex-column gap-1" aria-label="Navegación de bibliotecario">
            <?php foreach ($enlacesMenu as $item): ?>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                   class="nav-link px-3 py-2 rounded <?= !empty($item['activo']) ? 'active fw-semibold' : '' ?>">
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <a href="../logout.php" class="btn bg-crema-bd btn-sm fw-bold mt-4 align-self-start px-3 py-2">Cerrar sesión</a>
    </aside>

    <div class="flex-grow-1 w-100 d-flex flex-column">
        <header class="topbar-bd sticky-top w-100 px-4 py-2 d-flex align-items-center">
            <span class="small fw-semibold">Gestión de libros</span>
            <span class="small ms-auto text-nowrap">
                <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?> &middot; <?= htmlspecialchars(strtolower($rol), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </header>

        <main class="flex-grow-1 w-100 p-3 p-md-5">
            <div class="text-uppercase fw-bold small" style="color: #a66a4e; letter-spacing: 1px;">
                BiblioDesk &middot; Biblioteca
            </div>
            <h1 class="font-serif-bd mt-1 mb-1">Gestión de libros</h1>
            <p class="text-muted mb-4">Consulta y organiza los registros de la biblioteca.</p>

            <div class="bg-white border rounded-3 p-3 p-md-4 w-100" style="border-color: #e9e2d2 !important;">
                <h2 class="h4 font-serif-bd mb-1"><?= $esEdicion ? 'Editar libro' : 'Registro' ?></h2>
                <p class="text-muted small mb-4">Los campos con * son obligatorios.</p>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger small" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errores as $err): ?>
                                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="libro_registrar.php<?= $esEdicion ? '?id=' . (int)$idLibro : '' ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id_libro" value="<?= (int)$idLibro ?>">
                    <input type="hidden" name="portada_actual" value="<?= htmlspecialchars($datos['PORTADA'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="titulo" class="form-label small fw-bold">Título *</label>
                            <input type="text" name="titulo" id="titulo" class="form-control"
                                   value="<?= htmlspecialchars($datos['TITULO'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="id_categoria" class="form-label small fw-bold">Categoría *</label>
                            <select name="id_categoria" id="id_categoria" class="form-select" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= (int)$cat['ID_CATEGORIA'] ?>"
                                        <?= (string)$datos['ID_CATEGORIA'] === (string)$cat['ID_CATEGORIA'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string)$cat['NOMBRE_CATEGORIA'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="autores" class="form-label small fw-bold">Autores *</label>
                            <select name="autores[]" id="autores" class="form-select" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($autoresCatalogo as $aut): ?>
                                    <option value="<?= (int)$aut['ID_AUTOR'] ?>"
                                        <?= in_array((int)$aut['ID_AUTOR'], $autoresSeleccionados, true) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string)$aut['NOMBRE_COMPLETO'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="id_editorial" class="form-label small fw-bold">Editorial *</label>
                            <select name="id_editorial" id="id_editorial" class="form-select" required>
                                <option value="">Selecciona una opción</option>
                                <?php foreach ($editoriales as $ed): ?>
                                    <option value="<?= (int)$ed['ID_EDITORIAL'] ?>"
                                        <?= (string)$datos['ID_EDITORIAL'] === (string)$ed['ID_EDITORIAL'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars((string)$ed['NOMBRE_EDITORIAL'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="isbn" class="form-label small fw-bold">ISBN</label>
                            <input type="text" name="isbn" id="isbn" class="form-control"
                                   value="<?= htmlspecialchars($datos['ISBN'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="anio_publicacion" class="form-label small fw-bold">Año de publicación</label>
                            <input type="number" name="anio_publicacion" id="anio_publicacion" class="form-control"
                                   min="1000" max="2100"
                                   value="<?= htmlspecialchars($datos['ANIO_PUBLICACION'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="edicion" class="form-label small fw-bold">Edición</label>
                            <input type="text" name="edicion" id="edicion" class="form-control"
                                   value="<?= htmlspecialchars($datos['EDICION'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="num_paginas" class="form-label small fw-bold">Número de páginas</label>
                            <input type="number" name="num_paginas" id="num_paginas" class="form-control"
                                   min="1"
                                   value="<?= htmlspecialchars($datos['NUM_PAGINAS'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="portada" class="form-label small fw-bold">Subir portada</label>
                            <input type="file" name="portada" id="portada" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                            <div class="form-text small">
                                JPG, PNG o WebP. Máximo 3 MB. Deja vacío para conservar la portada actual.
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="estado" class="form-label small fw-bold">Estado *</label>
                            <select name="estado" id="estado" class="form-select" required>
                                <option value="ACTIVO" <?= $datos['ESTADO'] === 'ACTIVO' ? 'selected' : '' ?>>Activo</option>
                                <option value="INACTIVO" <?= $datos['ESTADO'] === 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label small fw-bold">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control" placeholder="Escribe aquí"
                                      rows="3"><?= htmlspecialchars($datos['DESCRIPCION'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-guinda-bd fw-semibold px-4">Guardar</button>
                        <a href="registros_libros.php" class="btn bg-crema-bd fw-semibold px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>