<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si ya hay sesión activa, redirigir a la carpeta de su rol
if (isset($_SESSION['admin_id']) && isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'ADMINISTRADOR') {
        header('Location: ../privado/administrador/inicio.php');
        exit;
    } elseif ($_SESSION['rol'] === 'BIBLIOTECARIO') {
        header('Location: ../privado/bibliotecario/inicio.php');
        exit;
    } elseif ($_SESSION['rol'] === 'LECTOR') {
        header('Location: ../privado/lector/inicio.php');
        exit;
    }
}

$error = '';

if (isset($_GET['error']) && $_GET['error'] === 'acceso_denegado') {
    $error = 'Debes iniciar sesión para poder acceder al panel.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = trim($_POST['rol'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($rol) && !empty($matricula) && !empty($password)) {
        if ($rol === 'LECTOR') {
            $sql = "SELECT ID_USUARIO AS id, MATRICULA_USUARIO AS matricula, NOMBRE, APELLIDOP, PASSWORD_HASH, ESTADO
                    FROM USUARIO
                    WHERE MATRICULA_USUARIO = ? AND ESTADO = 'ACTIVO'";
            $destino = '../privado/lector/inicio.php';
        } elseif ($rol === 'BIBLIOTECARIO' || $rol === 'ADMINISTRADOR') {
            $sql = "SELECT ID_EMPLEADO AS id, MATRICULA_EMPLEADO AS matricula, NOMBRE, APELLIDOP, PASSWORD_HASH, ROL, ESTADO
                    FROM EMPLEADO
                    WHERE MATRICULA_EMPLEADO = ? AND ROL = ? AND ESTADO = 'ACTIVO'";
            $destino = ($rol === 'ADMINISTRADOR') ? '../privado/administrador/inicio.php' : '../privado/bibliotecario/inicio.php';
        } else {
            $sql = '';
            $destino = '';
            $error = 'Rol no válido.';
        }

        if ($sql !== '') {
            $stmt = mysqli_prepare($conexion, $sql);

            if ($stmt) {
                if ($rol === 'LECTOR') {
                    mysqli_stmt_bind_param($stmt, "s", $matricula);
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $matricula, $rol);
                }

                mysqli_stmt_execute($stmt);
                $resultado = mysqli_stmt_get_result($stmt);
                $usuario = mysqli_fetch_assoc($resultado);

                if ($usuario && ($password === $usuario['PASSWORD_HASH'] || password_verify($password, $usuario['PASSWORD_HASH']))) {
                    $_SESSION['admin_id'] = $usuario['id'];
                    $_SESSION['admin_nombre'] = $usuario['NOMBRE'] . ' ' . $usuario['APELLIDOP'];
                    $_SESSION['rol'] = $rol;

                    mysqli_stmt_close($stmt);
                    header('Location: ' . $destino);
                    exit;
                } else {
                    $error = 'Credenciales incorrectas o cuenta inactiva.';
                }

                mysqli_stmt_close($stmt);
            } else {
                $error = 'Error en la consulta a la base de datos: ' . mysqli_error($conexion);
            }
        }
    } else {
        $error = 'Por favor llena todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - BiblioDesk</title>
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
        .tarjeta-acceso {
            background-color: #ffffff;
            border: 1px solid #f1ece0;
            border-radius: 14px;
            padding: 2.25rem 2rem;
            box-shadow: 0 4px 16px rgba(44, 36, 30, 0.03);
        }
        .form-control-bd, .form-select-bd {
            background-color: #fbf9f4;
            border: 1px solid #ece5d5;
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.92rem;
        }
        .form-control-bd:focus, .form-select-bd:focus {
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

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">
                <div class="tarjeta-acceso">
                    <h1 class="h3 fw-normal titulo-serif mb-1">Bienvenido a BiblioDesk</h1>
                    <p class="text-muted small mb-4">Ingresa con tu matrícula y contraseña.</p>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="rol" class="form-label small fw-bold mb-1">Rol *</label>
                            <select name="rol" id="rol" class="form-select form-select-bd" required>
                                <option value="">Selecciona un rol</option>
                                <option value="LECTOR">Lector</option>
                                <option value="BIBLIOTECARIO">Bibliotecario</option>
                                <option value="ADMINISTRADOR">Administrador</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="matricula" class="form-label small fw-bold mb-1">Matrícula</label>
                            <input type="text" name="matricula" id="matricula" class="form-control form-control-bd" required>
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label small fw-bold mb-1">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control form-control-bd" required>
                        </div>

                        <p class="text-muted small mb-4" style="font-size: 0.78rem;">
                            ¿Olvidaste tu contraseña? Acude al personal de la biblioteca.
                        </p>

                        <button type="submit" class="btn btn-guinda-bd mb-3">Iniciar sesión</button>

                        <div class="small text-muted">
                            ¿No tienes cuenta?
                            <a href="crear_cuenta.php" class="text-decoration-none fw-semibold" style="color: #691b14;">
                                Crea una cuenta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
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