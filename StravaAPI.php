<?php
class StravaAPI {
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function getActivities() {
        $url = 'https://www.strava.com/api/v3/athlete/activities?per_page=30';
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer {$this->token}"
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response) {
            return json_decode($response, true);
        }

        return [];
    }
}
?>
