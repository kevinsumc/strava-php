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
        return "https://www.strava.com/oauth/authorize?client_id={$this->clientId}&redirect_uri={$this->redirectUri}&response_type=code&scope=activity:read_all&approval_prompt=auto";
    }

    public function getAccessToken($code) {
        $url = 'https://www.strava.com/oauth/token';
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            return $data['access_token'] ?? null;
        }

        return null;
    }
}
?>
