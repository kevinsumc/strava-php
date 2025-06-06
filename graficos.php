<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráficos de Entrenamiento - KS Running</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/styles.css">
    
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <img src="images/KS.2.PNG" alt="Logo KS Running" class="logo">
            <h1 class="platform-name">KS Running</h1>
        </div>
    </header>

    <div class="container">
    <button onclick="history.back()" class="analyze-btn">Volver al panel</button>
    <h2>Visualización de Datos</h2>

        <!-- 1. Distancia en el tiempo -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Evolución de la distancia en el tiempo</strong><br>
                📊 Gráfico: Line Chart<br>
                📐 Métrica: Distancia recorrida por sesión (km)<br>
                📈 Objetivo: Ver si hay un progreso o estancamiento del volumen de entrenamiento.<br>
                📚 <em>El volumen de entrenamiento es uno de los pilares del rendimiento. Aumentar el kilometraje semanal mejora la capacidad aeróbica.</em>
            </div>
            <canvas id="distanciaTiempo" width="400" height="200"></canvas>
        </div>

        <!-- 2. FC vs Distancia -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Frecuencia cardíaca promedio vs distancia</strong><br>
                📊 Gráfico: Scatter Plot<br>
                📐 Métrica: Relación entre distancia y FC promedio<br>
                📈 Objetivo: Evaluar la eficiencia cardiovascular.<br>
                📚 <em>Un menor pulso a igual ritmo indica una mejora de la condición aeróbica.</em>
            </div>
            <canvas id="fcVsDistancia" width="400" height="200"></canvas>
        </div>

        <!-- 3. Elevación vs Distancia -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Elevación acumulada vs distancia</strong><br>
                📊 Gráfico: Bar Chart horizontal<br>
                📐 Métrica: Elevación por sesión vs distancia<br>
                📈 Objetivo: Evaluar carga muscular y dificultad de recorridos.<br>
                📚 <em>El desnivel condiciona el esfuerzo y riesgo de lesión. Útil para preparar trail o cuestas.</em>
            </div>
            <canvas id="elevacionVsDistancia" width="400" height="200"></canvas>
        </div>

        <!-- 4. Tiempo total -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Tiempo total de entrenamiento</strong><br>
                📊 Gráfico: Bar Chart agrupado<br>
                📐 Métrica: Minutos por semana<br>
                📈 Objetivo: Medir constancia y acumulación de carga.<br>
                📚 <em>Es fundamental para evitar sobreentrenamiento y organizar la carga semanal.</em>
            </div>
            <canvas id="tiempoEntrenamiento" width="400" height="200"></canvas>
        </div>

        <!-- 5. Comparación sesiones -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Comparación de sesiones (multivariable)</strong><br>
                📊 Gráfico: Radar Chart<br>
                📐 Métrica: Tiempo, distancia, FC, elevación<br>
                📈 Objetivo: Comparar sesiones clave de forma global.<br>
                📚 <em>Ideal para ver el impacto de cada sesión desde varios ángulos.</em>
            </div>
            <canvas id="comparacionSesiones" width="400" height="200"></canvas>
        </div>

        <!-- 6. Zonas FC -->
        <div class="grafico-container">
            <div class="grafico-texto">
                ✅ <strong>Zonas de frecuencia cardíaca</strong><br>
                📊 Gráfico: Doughnut Chart<br>
                📐 Métrica: Porcentaje de sesiones en cada zona<br>
                📈 Objetivo: Evaluar si el entrenamiento se alinea con el objetivo (Z2, Z4, etc.)<br>
                📚 <em>Entrenar por zonas es clave para mejorar capacidades específicas.</em>
            </div>
            <canvas id="zonasFC" width="400" height="200"></canvas>
        </div>
    </div>

    <footer class="main-footer">
        <p>&copy; <?= date("Y") ?> KS Running. Todos los derechos reservados.</p>
    </footer>

    <script>
        const stats = JSON.parse(localStorage.getItem('stravaStats')) || {};

        const getLabels = () => stats.sessions?.map(s => s.date) || [];

        new Chart(document.getElementById('distanciaTiempo'), {
            type: 'line',
            data: {
                labels: getLabels(),
                datasets: [{
                    label: 'Distancia (km)',
                    data: stats.sessions?.map(s => s.distance),
                    borderColor: 'blue',
                    fill: false
                }]
            }
        });

        new Chart(document.getElementById('fcVsDistancia'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'FC vs Distancia',
                    data: stats.sessions?.map(s => ({ x: s.distance, y: s.avgHeartRate })),
                    backgroundColor: 'red'
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Distancia (km)' } },
                    y: { title: { display: true, text: 'FC Promedio' } }
                }
            }
        });

        new Chart(document.getElementById('elevacionVsDistancia'), {
            type: 'bar',
            data: {
                labels: getLabels(),
                datasets: [{
                    label: 'Elevación (m)',
                    data: stats.sessions?.map(s => s.elevationGain),
                    backgroundColor: 'orange'
                }]
            },
            options: { indexAxis: 'y' }
        });

        const semanas = {};
        stats.sessions?.forEach(s => {
            const fecha = new Date(s.date);
            const semana = `${fecha.getFullYear()}-W${Math.ceil(fecha.getDate() / 7)}`;
            semanas[semana] = (semanas[semana] || 0) + (s.time || (s.distance / 10) * 60);
        });

        new Chart(document.getElementById('tiempoEntrenamiento'), {
            type: 'bar',
            data: {
                labels: Object.keys(semanas),
                datasets: [{
                    label: 'Minutos',
                    data: Object.values(semanas),
                    backgroundColor: 'green'
                }]
            }
        });

        const ultimas = stats.sessions?.slice(-5) || [];
        new Chart(document.getElementById('comparacionSesiones'), {
            type: 'radar',
            data: {
                labels: ['Distancia', 'FC', 'Elevación'],
                datasets: ultimas.map(s => ({
                    label: `Sesión ${s.date}`,
                    data: [s.distance, s.avgHeartRate, s.elevationGain],
                    fill: true
                }))
            }
        });

        const zonas = [0, 0, 0, 0, 0];
        stats.sessions?.forEach(s => {
            const fc = s.avgHeartRate || 0;
            if (fc < 110) zonas[0]++;
            else if (fc < 130) zonas[1]++;
            else if (fc < 150) zonas[2]++;
            else if (fc < 170) zonas[3]++;
            else zonas[4]++;
        });

        new Chart(document.getElementById('zonasFC'), {
            type: 'doughnut',
            data: {
                labels: ['Z1', 'Z2', 'Z3', 'Z4', 'Z5'],
                datasets: [{
                    label: 'Zonas de FC',
                    data: zonas,
                    backgroundColor: ['#99d', '#77c', '#55b', '#338', '#115']
                }]
            }
        });
    </script>
</body>
</html>
