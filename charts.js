Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.plugins.legend.position = 'bottom';

// 1. Primero definimos todas las funciones auxiliares
function createDistanceChart(container, stats) {
    const canvas = document.createElement('canvas');
    canvas.height = 300;
    container.appendChild(canvas);
    
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['Carreras', 'Ciclismo', 'Natación'],
            datasets: [{
                label: 'Distancia (km)',
                data: [
                    stats.totalDistance.runs || 0,
                    stats.totalDistance.rides || 0,
                    stats.totalDistance.swims || 0
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Distancia por Tipo de Actividad',
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kilómetros'
                    }
                }
            }
        }
    });
}
function renderAllCharts() {
    const container = document.getElementById('chartsContainer');
    if (!container) {
        console.error('Contenedor de gráficos no encontrado');
        return;
    }

    container.innerHTML = '';
    
    if (!window.stravaData || !window.stravaData.ready || !window.stravaData.stats) {
        container.innerHTML = '<div class="error">Datos no disponibles</div>';
        container.style.display = 'block';
        return;
    }

    try {
        const runs = window.stravaData.stats.runs;
        
        // 1. Gráfico de distancias de carreras (existente)
        createDistanceChart(container, window.stravaData.stats);
            
        // 2. Gráfico de progresión de distancia
        createDistanceProgressionChart(container, runs);
        
        // 3. Gráfico de correlación distancia-duración
        createDistanceDurationChart(container, runs);

    } catch (error) {
        console.error('Error en renderAllCharts:', error);
        container.innerHTML = `<div class="error">Error al generar gráficos: ${error.message}</div>`;
    }
}

// [El código existente de createDistanceChart permanece igual]

function createDistanceProgressionChart(container, runs) {
    if (!runs || runs.length === 0) return;
    
    const canvas = document.createElement('canvas');
    canvas.height = 300;
    container.appendChild(canvas);
    
    // Ordenar carreras por fecha
    const sortedRuns = [...runs].sort((a, b) => new Date(a.start_date) - new Date(b.start_date));
    
    const distanceData = sortedRuns.map(run => (run.distance / 1000).toFixed(2));
    const labels = sortedRuns.map(run => new Date(run.start_date).toLocaleDateString());
    
    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Distancia (km)',
                data: distanceData,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Progresión de Distancia en Carreras',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: ${context.raw} km`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kilómetros'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Fecha de la carrera'
                    }
                }
            }
        }
    });
}

function createDistanceDurationChart(container, runs) {
    if (!runs || runs.length === 0) return;
    
    const canvas = document.createElement('canvas');
    canvas.height = 300;
    container.appendChild(canvas);
    
    const data = runs.map(run => ({
        x: run.distance / 1000, // km
        y: run.moving_time / 60 // minutos
    }));
    
    new Chart(canvas, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Carreras',
                data: data,
                backgroundColor: 'rgba(153, 102, 255, 0.7)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Relación Distancia-Duración',
                    font: { size: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Distancia: ${context.parsed.x.toFixed(2)} km\nDuración: ${context.parsed.y.toFixed(1)} min`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Distancia (km)'
                    },
                    beginAtZero: true
                },
                y: {
                    title: {
                        display: true,
                        text: 'Duración (minutos)'
                    },
                    beginAtZero: true
                }
            }
        }
    });
}