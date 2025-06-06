<?php
session_start();

require_once 'StravaAuth.php';
require_once 'StravaAPI.php';
require_once 'ActivityDisplay.php';

// Configuración
// Configuración de Strava
$STRAVA_CLIENT_ID = '*****';
$STRAVA_CLIENT_SECRET = '******';
$REDIRECT_URI = 'http://localhost:81/strava-php/dashboard.php';  // ← Usa el puerto adecuado

$stravaAuth = new StravaAuth($STRAVA_CLIENT_ID, $STRAVA_CLIENT_SECRET, $REDIRECT_URI);
$activityDisplay = new ActivityDisplay();

// Manejar conexión
if (isset($_GET['connect'])) {
    header("Location: " . $stravaAuth->getAuthUrl());
    exit;
}

// Verificar y refrescar token si es necesario
if (isset($_SESSION['strava_token'])) {
    // Si el token está expirado, intentar refrescar
    if (time() > $_SESSION['token_expires_at']) {
        $newToken = StravaAuth::refreshToken(
            $STRAVA_CLIENT_ID,
            $STRAVA_CLIENT_SECRET,
            $_SESSION['strava_refresh_token']
        );

        if ($newToken && isset($newToken['access_token'])) {
            $_SESSION['strava_token'] = $newToken['access_token'];
            $_SESSION['strava_refresh_token'] = $newToken['refresh_token'];
            $_SESSION['token_expires_at'] = $newToken['expires_at'];
        } else {
            unset($_SESSION['strava_token']);
            $error = 'La sesión ha expirado. Por favor reconecta con Strava.';
        }
    }
}

// Procesar código de autorización
if (isset($_GET['code'])) {
    $accessToken = $stravaAuth->getAccessToken($_GET['code']);
    if (!$accessToken) {
        $error = 'Error al obtener token de acceso';
    }
}

// Obtener actividades si tenemos token
$activities = [];
if (isset($_SESSION['strava_token'])) {
    try {
        $stravaAPI = new StravaAPI($_SESSION['strava_token']);
        $activities = $stravaAPI->getActivities(200);
    } catch (Exception $e) {
        $error = 'Error al obtener actividades: ' . $e->getMessage();
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Strava - KS Running</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <img src="images/KS.2.PNG" alt="Logo KS Running" class="logo">
            <h1 class="platform-name">KS Running</h1>
        </div>
    </header>

    <div class="container">
        <?php if (!isset($_SESSION['strava_token'])): ?>
            <h2>Bienvenido a tu Plataforma de Análisis de Entrenamientos</h2>
            <a href="?connect=true" class="strava-button">Conectar con Strava</a>
            <!-- Mensaje de introducción -->
        <?php else: ?>
            <h2>Mis Actividades de Strava</h2>
            <a href="graficos.php" class="analyze-btn">Analizar Datos</a>

<!-- Contenedor para los gráficos -->
<div id="chartsContainer" style="display:none;">
    <canvas id="chartDistanceOverTime"></canvas>
    <canvas id="chartHeartRateVsDistance"></canvas>
    <canvas id="chartElevationVsDistance"></canvas>
    <canvas id="chartTrainingTime"></canvas>
    <canvas id="chartRadarSessions"></canvas>
    <canvas id="chartHeartZones"></canvas>
</div>

            <div id="activitiesTable">
                <?= $activityDisplay->displayActivities($activities) ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="main-footer">
        <p>&copy; <?= date("Y") ?> KS Running. Todos los derechos reservados.</p>
    </footer>

    <script src="main.js"></script>
</body>
</html>
