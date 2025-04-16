<?php
session_start();

// Incluir las clases
require_once 'StravaAuth.php';
require_once 'StravaAPI.php';
require_once 'ActivityDisplay.php';

// Configuración de Strava
$STRAVA_CLIENT_ID = 'tu cliente_id';
$STRAVA_CLIENT_SECRET = 'tu cliente_secret';
$REDIRECT_URI = 'http://localhost:81/strava-php/dashboard.php';  // ← Usa el puerto adecuado

// Crear instancias de las clases
$stravaAuth = new StravaAuth($STRAVA_CLIENT_ID, $STRAVA_CLIENT_SECRET, $REDIRECT_URI);
$activityDisplay = new ActivityDisplay();

// Manejar la conexión con Strava
if (isset($_GET['connect'])) {
    $stravaAuthUrl = $stravaAuth->getAuthUrl();
    header("Location: $stravaAuthUrl");
    exit;
}

// Procesar el código de autorización cuando volvemos de Strava
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $accessToken = $stravaAuth->getAccessToken($code);

    if ($accessToken) {
        $_SESSION['strava_token'] = $accessToken;

        // Obtener las actividades
        $stravaAPI = new StravaAPI($accessToken);
        $activities = $stravaAPI->getActivities();
    } else {
        $error = 'No se pudo obtener el token de acceso';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Strava</title>
</head>
<body>
    <?php if (!isset($_SESSION['strava_token'])): ?>
        <button onclick="window.location.href='?connect=true'">Conectar con Strava</button>
    <?php elseif (isset($activities)): ?>
        <h3>Actividades de Strava</h3>
        <?php echo $activityDisplay->displayActivities($activities); ?>
    <?php else: ?>
        <p style="color: red;"><?php echo $error ?? 'Error al conectar con Strava'; ?></p>
    <?php endif; ?>
</body>
</html>
