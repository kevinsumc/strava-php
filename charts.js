document.addEventListener('DOMContentLoaded', function() {
    const stats = JSON.parse(localStorage.getItem('stravaStats'));

    if (!stats) {
        document.body.innerHTML = '<p class="error">No hay datos disponibles para mostrar.</p>';
        return;
    }

    // 1. Gráfico de Distancia
    const ctxDistance = document.getElementById('distanceChart').getContext('2d');
    new Chart(ctxDistance, {
        type: 'bar',
        data: {
            labels: ['Correr', 'Bicicleta', 'Natación'],
            datasets: [{
                label: 'Distancia (km)',
                data: [stats.totalDistance.runs, stats.totalDistance.rides, stats.totalDistance.swims],
                backgroundColor: ['#FF6384', '#36A2EB', '#4BC0C0']
            }]
        },
        options: { responsive: true }
    });

    // 2. Gráfico de Frecuencia Cardíaca vs Distancia
    const ctxHR = document.getElementById('heartRateChart').getContext('2d');
    new Chart(ctxHR, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'FC vs Distancia',
                data: stats.sessions.map(s => ({ x: s.distance, y: s.avgHeartRate })),
                backgroundColor: '#FF9F40'
            }]
        },
        options: {
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            return `Distancia: ${ctx.raw.x} km, FC: ${ctx.raw.y} bpm`;
                        }
                    }
                }
            },
            scales: {
                x: { title: { display: true, text: 'Distancia (km)' } },
                y: { title: { display: true, text: 'FC Promedio (bpm)' } }
            }
        }
    });

    // 3. Gráfico de Elevación por Sesión
    const ctxElevation = document.getElementById('elevationChart').getContext('2d');
    new Chart(ctxElevation, {
        type: 'bar',
        data: {
            labels: stats.sessions.map(s => s.date),
            datasets: [{
                label: 'Elevación (m)',
                data: stats.sessions.map(s => s.elevationGain),
                backgroundColor: '#9966FF'
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { title: { display: true, text: 'Sesión' } },
                y: { title: { display: true, text: 'Elevación (m)' } }
            }
        }
    });

    // 4. Gráfico de Tiempo Total Semanal
    const ctxTime = document.getElementById('timeChart').getContext('2d');
    new Chart(ctxTime, {
        type: 'line',
        data: {
            labels: stats.weeks.map(w => w.weekLabel),
            datasets: [{
                label: 'Tiempo total (min)',
                data: stats.weeks.map(w => w.totalMinutes),
                borderColor: '#4BC0C0',
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { title: { display: true, text: 'Minutos' } }
            }
        }
    });

    // 5. Gráfico de Zonas de Frecuencia Cardíaca
    const ctxZones = document.getElementById('zonesChart').getContext('2d');
    new Chart(ctxZones, {
        type: 'doughnut',
        data: {
            labels: ['Z1', 'Z2', 'Z3', 'Z4', 'Z5'],
            datasets: [{
                label: 'Distribución de Zonas FC',
                data: stats.heartRateZones || [10, 40, 30, 15, 5],
                backgroundColor: ['#C0C0C0', '#8BC34A', '#FFEB3B', '#FF9800', '#F44336']
            }]
        },
        options: { responsive: true }
    });
});
