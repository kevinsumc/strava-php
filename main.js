document.addEventListener('DOMContentLoaded', function() {
    const analyzeBtn = document.getElementById('analyzeBtn');
    const container = document.getElementById('chartsContainer');
    
    if (!analyzeBtn || !container) {
        console.error('Elementos esenciales no encontrados');
        return;
    }
    
    analyzeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        console.log("Botón 'Analizar Datos' clickeado");
        
        if (window.stravaData && window.stravaData.ready) {
            console.log("Datos listos:", window.stravaData.stats);
            localStorage.setItem('stravaStats', JSON.stringify(window.stravaData.stats));
            window.open('http://localhost:81/strava-php/graficos.html', '_blank');

        } else {
            console.warn("Datos de Strava no están listos");
        }
    });
    
});