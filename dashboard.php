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
    <title>Dashboard Strava</title>
   <link rel="stylesheet" href="styles.css">
    <!-- Carga   Chart.js solo una vez -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<body>
    <div class="container">
    <?php if (!isset($_SESSION['strava_token'])): ?>
        <h2>Bienvenido a tu Plataforma de Análisis de Entrenamientos</h2>
            <a href="?connect=true" class="strava-button">Conectar con Strava</a>
            <div class="intro-message">

    <p>
        Esta plataforma está diseñada para ayudarte a comprender y optimizar tu rendimiento deportivo.
        A través de la conexión con la <strong>API de Strava</strong>, accedemos a tus datos de entrenamiento de forma automática y segura.
    </p>
    <p>
        Podrás visualizar métricas clave como <strong>distancia, tiempo, velocidad promedio y frecuencia cardíaca</strong> directamente en tu panel.
        Además, dispones de herramientas para <strong>analizar tus actividades</strong> y generar reportes detallados que te ayudarán a evaluar tu progreso a lo largo del tiempo.
    </p>
    <p>
        Explora tus datos, identifica patrones y lleva tu entrenamiento al siguiente nivel.
    </p>
</div>

            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <h1>Mis Actividades de Strava</h1>
            <button id="analyzeBtn" class="analyze-btn">Analizar Datos</button>
            
</div>


            
            <div id="activitiesTable">
                <?= $activityDisplay->displayActivities($activities) ?>
            </div>
            
            <div id="chartsContainer" class="charts-container" style="display: none;"></div>
            
            <script>
                // Versión corregida de processActivitiesData
                function processActivitiesData(activities) {
                    const safeFilter = (type) => activities ? activities.filter(a => a && a.type === type) || [] : [];
                    const sumDistance = (arr) => arr.reduce((sum, a) => sum + (parseFloat(a.distance) || 0), 0);
                    
                    return {
                        runs: safeFilter('Run'),
                        rides: safeFilter('Ride'),
                        swims: safeFilter('Swim'),
                        totalDistance: {
                            runs: sumDistance(safeFilter('Run')) / 1000,
                            rides: sumDistance(safeFilter('Ride')) / 1000,
                            swims: sumDistance(safeFilter('Swim')) / 1000
                        }
                    };
                }

                // Carga inicial de datos
                window.stravaData = {
                    activities: <?= json_encode($activities ?? []) ?>,
                    ready: false    
                };
                
                if (window.stravaData.activities && window.stravaData.activities.length > 0) {
                    window.stravaData.ready = true;
                    window.stravaData.stats = processActivitiesData(window.stravaData.activities);
                    console.log('Datos de Strava cargados:', window.stravaData);
                } else {
                    console.warn('No hay actividades disponibles');
                    document.getElementById('analyzeBtn').disabled = true;
                }
            </script>
            
            
            <script src="charts.js"></script>
            <script src="main.js"></script>
        <?php endif; ?>
    </div>
</body>
</html>