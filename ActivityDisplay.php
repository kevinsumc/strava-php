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
    
        $html = '<table class="activities-table">';
        $html .= '
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Fecha</th>
                <th>Distancia</th>
                <th>Tiempo</th>
                <th>Elevación</th>
            </tr>
        </thead>
        <tbody>';
    
        foreach ($activities as $activity) {
            $html .= $this->renderActivityRow($activity);
        }
    
        $html .= '</tbody></table>';
        return $html;
    }
    
    private function renderActivityRow($activity) {
        $date = new DateTime($activity['start_date_local']);
        return "
        <tr>
            <td>{$activity['name']}</td>
            <td>{$this->translateType($activity['type'])}</td>
            <td>{$date->format('d/m/Y H:i')}</td>
            <td>" . number_format($activity['distance'] / 1000, 2) . " km</td>
            <td>{$this->formatTime($activity['moving_time'])}</td>
            <td>{$activity['total_elevation_gain']} m</td>
        </tr>";
    }
}
?>