<?php
class StravaAuth {
    private $clientId;
    private $clientSecret;
    private $redirectUri;
    
    public function __construct($clientId, $clientSecret, $redirectUri) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }
    
    public function getAuthUrl() {
        return "https://www.strava.com/oauth/authorize?" . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'activity:read_all',
            'approval_prompt' => 'auto'
        ]);
    }
    
    public function getAccessToken($code) {
        $url = 'https://www.strava.com/oauth/token';
        
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 segundos de timeout
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            error_log("cURL error: " . curl_error($ch));
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['access_token'])) {
            $_SESSION['strava_token'] = $tokenData['access_token'];
            $_SESSION['strava_refresh_token'] = $tokenData['refresh_token'];
            $_SESSION['token_expires_at'] = $tokenData['expires_at'];
            return $tokenData['access_token'];
        }
        
        return false;
    }
    
    public static function refreshToken($clientId, $clientSecret, $refreshToken) {
        $url = 'https://www.strava.com/oauth/token';
        
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        return $response ? json_decode($response, true) : false;
    }
}
?>