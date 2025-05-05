<?php
class StravaAPI {
    private $token;
    
    public function __construct($token) {
        $this->token = $token;
    }
    
    public function getActivities($perPage = 30) {
        $url = "https://www.strava.com/api/v3/athlete/activities?per_page={$perPage}";
        
        try {
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$this->token}"
                ],
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_FAILONERROR => true
            ]);
            
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('Error en la solicitud: ' . curl_error($ch));
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode !== 200) {
                throw new Exception("Error en la API. Código HTTP: {$httpCode}");
            }
            
            curl_close($ch);
            
            return json_decode($response, true) ?: [];
            
        } catch (Exception $e) {
            // Registrar el error para diagnóstico
            error_log("Error StravaAPI: " . $e->getMessage());
            return [];
        }
    }
    
    // Función para refrescar el token si es necesario
    public static function refreshToken($clientId, $clientSecret, $refreshToken) {
        $url = "https://www.strava.com/oauth/token";
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
?>