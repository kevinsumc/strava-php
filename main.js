
document.getElementById("analyzeBtn").addEventListener("click", () => {
    const stats = JSON.parse(localStorage.getItem("stravaStats"));
    if (!stats) return;

    document.getElementById("chartsContainer").style.display = "block";

    const sessions = stats.sessions;

    // 1. Evolución de la distancia en el tiempo
    new Chart(document.getElementById('chartDistanceOverTime'), {
        type: 'line',
        data: {
            labels: sessions.map(s => s.date),
            datasets: [{
                label: 'Distancia (km)',
                data: sessions.map(s => s.distance),
                borderColor: 'blue',
                fill: false
            }]
        },
        options: { responsive: true }
    });

    // 2. FC promedio vs distancia (scatter plot)
    new Chart(document.getElementById('chartHeartRateVsDistance'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'FC vs Distancia',
                data: sessions.map(s => ({
                    x: s.distance,
                    y: s.avgHeartRate
                })),
                backgroundColor: 'red'
            }]
        },
        options: {
            scales: {
                x: { title: { display: true, text: 'Distancia (km)' } },
                y: { title: { display: true, text: 'FC promedio (bpm)' } }
            }
        }
    });

    // 3. Elevación acumulada vs distancia
    new Chart(document.getElementById('chartElevationVsDistance'), {
        type: 'bar',
        data: {
            labels: sessions.map(s => s.date),
            datasets: [{
                label: 'Elevación (m)',
                data: sessions.map(s => s.elevationGain),
                backgroundColor: 'green'
            }]
        },
        options: { indexAxis: 'y', responsive: true }
    });

    // 4. Tiempo total por semana (dummy data usando distancia como proxy de duración)
    const weeklyData = {};
    sessions.forEach(s => {
        const week = new Date(s.date);
        week.setDate(week.getDate() - week.getDay()); // lunes de esa semana
        const key = week.toLocaleDateString();
        if (!weeklyData[key]) weeklyData[key] = 0;
        weeklyData[key] += s.distance * 6; // 6 min/km como estimación
    });

    new Chart(document.getElementById('chartTrainingTime'), {
        type: 'line',
        data: {
            labels: Object.keys(weeklyData),
            datasets: [{
                label: 'Tiempo estimado (min)',
                data: Object.values(weeklyData),
                borderColor: 'orange',
                fill: true
            }]
        },
        options: { responsive: true }
    });

    // 5. Comparación multivariable (radar)
    const sample = sessions.slice(0, 5);
    new Chart(document.getElementById('chartRadarSessions'), {
        type: 'radar',
        data: {
            labels: ['Distancia (km)', 'FC promedio', 'Elevación (m)'],
            datasets: sample.map((s, i) => ({
                label: `Sesión ${i + 1}`,
                data: [s.distance, s.avgHeartRate, s.elevationGain],
                fill: true
            }))
        },
        options: { responsive: true }
    });

    // 6. Zonas cardíacas (dummy: distribuye el tiempo en Z1-Z5 aleatoriamente)
    const zones = [0, 0, 0, 0, 0];
    sessions.forEach(s => {
        const total = 100;
        const z = Array.from({ length: 5 }, () => Math.random());
        const sum = z.reduce((a, b) => a + b, 0);
        z.forEach((val, i) => zones[i] += (val / sum) * total);
    });

    new Chart(document.getElementById('chartHeartZones'), {
        type: 'doughnut',
        data: {
            labels: ['Z1', 'Z2', 'Z3', 'Z4', 'Z5'],
            datasets: [{
                label: '% Tiempo en Zona',
                data: zones.map(z => (z / sessions.length).toFixed(2)),
                backgroundColor: ['#aaf', '#7cf', '#4cf', '#2af', '#00f']
            }]
        },
        options: { responsive: true }
    });
});

