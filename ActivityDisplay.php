<?php
class ActivityDisplay {
    public function translateType($type) {
        $types = [
            'Run' => 'Carrera',
            'Ride' => 'Ciclismo',
            'Swim' => 'Natación',
            'Hike' => 'Senderismo',
            'Walk' => 'Caminata'
        ];
        return $types[$type] ?? $type;
    }

    public function formatTime($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    public function displayActivities($activities) {
        if (empty($activities)) {
            return '<p>No hay actividades para mostrar.</p>';
        }

        $html = '';
        foreach ($activities as $activity) {
            $date = new DateTime($activity['start_date_local']);
            $formattedDate = $date->format('l, j F Y H:i');

            $html .= "
            <div class='activity'>
                <h3>{$activity['name']}</h3>
                <p><strong>Tipo:</strong> {$this->translateType($activity['type'])}</p>
                <p><strong>Fecha:</strong> {$formattedDate}</p>
                <div class='stats'>
                    <span class='stat'>🏃 " . number_format($activity['distance'] / 1000, 2) . " km</span>
                    <span class='stat'>⏱️ {$this->formatTime($activity['moving_time'])}</span>
                    <span class='stat'>⬆️ {$activity['total_elevation_gain']} m</span>
                </div>
            </div>";
        }
        return $html;
    }
}
?>
