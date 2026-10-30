// 1. Registro global de ChartDataLabels
if (typeof ChartDataLabels !== 'undefined') {
    Chart.register(ChartDataLabels);
}

// 2. Función global para resolver colores según el tema activo (Claro / Oscuro)
function getChartThemeColors() {
    const temaPreferido = localStorage.getItem('theme') || document.documentElement.getAttribute('data-bs-theme') || 'light';
    return {
        textColor: temaPreferido === 'dark' ? '#f8f9fa' : '#212529',
        gridColor: temaPreferido === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
    };
}

// 3. Plugin global para mostrar la SUMA TOTAL encima de barras apiladas (Stacked Bars)
const stackedTotalsPlugin = {
    id: 'stackedTotals',
    afterDatasetsDraw: (chart) => {
        if (chart.config.type !== 'bar') return;
        const isStacked = chart.options.scales?.x?.stacked || chart.options.scales?.y?.stacked;
        if (!isStacked) return;

        const { ctx, options, data } = chart;
        const isHorizontal = options.indexAxis === 'y';
        const { textColor } = getChartThemeColors();

        ctx.save();
        ctx.fillStyle = textColor;
        ctx.font = 'bold 12px Arial';
        ctx.textAlign = isHorizontal ? 'left' : 'center';
        ctx.textBaseline = isHorizontal ? 'middle' : 'bottom';

        data.labels.forEach((label, i) => {
            let total = 0;
            let lastMeta = null;

            data.datasets.forEach((dataset, j) => {
                const meta = chart.getDatasetMeta(j);
                if (!meta.hidden && Number(dataset.data[i]) > 0) {
                    total += Number(dataset.data[i]);
                    lastMeta = meta.data[i];
                }
            });

            if (total > 0 && lastMeta) {
                if (isHorizontal) {
                    ctx.fillText(total, lastMeta.x + 8, lastMeta.y);
                } else {
                    ctx.fillText(total, lastMeta.x, lastMeta.y - 6);
                }
            }
        });
        ctx.restore();
    }
};

Chart.register(stackedTotalsPlugin);

// 4. Paletas de colores globales
const chartPalettes = {
    incidencias: {
        atraso: '#ffc107',   // Warning
        falta: '#dc3545',    // Danger
        licencia: '#0dcaf0', // Info
        permiso: '#0d6efd'   // Primary
    },
    ui: {
        primary: '#0d6efd',
        info: '#0dcaf0',
        success: '#198754',
        warning: '#ffc107',
        danger: '#dc3545',
        secondary: '#6c757d'
    }
};

// Generador de colores variados (para reportes de biblioteca o múltiples categorías)
function generarColoresChartJS(cantidad) {
    const backgroundColors = [];
    const borderColors = [];

    const coloresBase = [
        { r: 255, g: 0, b: 0 },     // Rojo
        { r: 0, g: 255, b: 0 },     // Verde
        { r: 0, g: 0, b: 255 },     // Azul
        { r: 255, g: 255, b: 0 },   // Amarillo
        { r: 255, g: 0, b: 255 },   // Magenta
        { r: 0, g: 255, b: 255 },   // Cian
        { r: 255, g: 128, b: 0 },   // Naranja
        { r: 128, g: 0, b: 255 },   // Púrpura
        { r: 0, g: 255, b: 128 },   // Verde claro
        { r: 255, g: 0, b: 128 }    // Rosa
    ];

    for (let i = 0; i < cantidad; i++) {
        const colorBase = coloresBase[i % coloresBase.length];
        const variacion = Math.floor(i / coloresBase.length) * 30;
        const r = Math.min(255, Math.max(0, colorBase.r + variacion));
        const g = Math.min(255, Math.max(0, colorBase.g + variacion));
        const b = Math.min(255, Math.max(0, colorBase.b + variacion));

        backgroundColors.push(`rgba(${r}, ${g}, ${b}, 0.2)`);
        borderColors.push(`rgb(${r}, ${g}, ${b})`);
    }

    return { backgroundColors, borderColors };
}

function generarColoresInstitucional(cantidad) {
    const bg = [
        'rgba(0, 0, 0, 0.2)',       // Negro
        'rgba(214, 40, 40, 0.2)',   // Rojo
        'rgba(247, 198, 0, 0.2)',   // Amarillo
        'rgba(42, 157, 143, 0.2)'   // Verde
    ];
    const border = [
        'rgb(0, 0, 0)',
        'rgb(214, 40, 40)',
        'rgb(247, 198, 0)',
        'rgb(42, 157, 143)'
    ];

    const backgroundColors = [];
    const borderColors = [];

    for (let i = 0; i < cantidad; i++) {
        backgroundColors.push(bg[i % bg.length]);
        borderColors.push(border[i % border.length]);
    }

    return { backgroundColors, borderColors };
}

// 5. Función Core para inicializar o actualizar cualquier gráfico de forma segura
function initOrUpdateChart(chartInstance, canvasId, configOptions) {
    const canvasEl = document.getElementById(canvasId);
    if (!canvasEl) return null;

    const existingChart = Chart.getChart(canvasEl) || chartInstance;
    if (existingChart) {
        existingChart.data = configOptions.data;
        existingChart.options = configOptions.options;
        existingChart.update();
        return existingChart;
    }
    return new Chart(canvasEl.getContext('2d'), configOptions);
}

// 6. Configuraciones por defecto por tipo de gráfico (ChartDefaults)
const ChartDefaults = {
    // a) Barras simples o agrupadas (Cantidades encima de cada barra)
    getBarOptions: function ({ showLegend = false, indexAxis = 'x' } = {}) {
        const { textColor, gridColor } = getChartThemeColors();
        const isHorizontal = indexAxis === 'y';

        return {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: indexAxis,
            layout: {
                padding: { top: isHorizontal ? 10 : 25, right: isHorizontal ? 35 : 15 }
            },
            plugins: {
                legend: {
                    display: showLegend,
                    labels: { color: textColor }
                },
                datalabels: {
                    color: textColor,
                    anchor: 'end',
                    align: isHorizontal ? 'right' : 'top',
                    offset: 2,
                    font: { weight: 'bold', size: 12 },
                    formatter: v => v > 0 ? v : ''
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor, precision: 0 },
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: isHorizontal ? '10%' : 0
                },
                y: {
                    ticks: { color: textColor, precision: 0 },
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: isHorizontal ? 0 : '10%'
                }
            }
        };
    },

    // b) Barras apiladas (Muestra cantidades internas por color + Suma total arriba vía stackedTotalsPlugin)
    getStackedBarOptions: function ({ indexAxis = 'x' } = {}) {
        const { textColor, gridColor } = getChartThemeColors();
        const isHorizontal = indexAxis === 'y';

        return {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: indexAxis,
            layout: {
                padding: { top: 25, right: 35 }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: { color: textColor }
                },
                datalabels: {
                    color: '#fff',
                    anchor: 'center',
                    align: 'center',
                    font: { weight: 'bold', size: 11 },
                    textShadowBlur: 3,
                    textShadowColor: 'rgba(0,0,0,0.75)',
                    formatter: v => v > 0 ? v : ''
                }
            },
            scales: {
                x: {
                    stacked: true,
                    ticks: { color: textColor, precision: 0 },
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: isHorizontal ? '10%' : 0
                },
                y: {
                    stacked: true,
                    ticks: { color: textColor, precision: 0 },
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: isHorizontal ? 0 : '10%'
                }
            }
        };
    },

    // c) Gráficos de Líneas (Cantidades encima de cada punto)
    getLineOptions: function ({ showLegend = true } = {}) {
        const { textColor, gridColor } = getChartThemeColors();
        return {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: { top: 25, right: 20 } },
            plugins: {
                legend: {
                    display: showLegend,
                    labels: { color: textColor }
                },
                datalabels: {
                    color: textColor,
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold', size: 12 },
                    formatter: v => v > 0 ? v : ''
                }
            },
            scales: {
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                },
                y: {
                    ticks: { color: textColor, precision: 0 },
                    grid: { color: gridColor },
                    beginAtZero: true,
                    grace: '12%'
                }
            }
        };
    },

    // d) Gráficos de Dona / Pastel
    getDoughnutOptions: function () {
        const { textColor } = getChartThemeColors();
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor }
                },
                datalabels: {
                    color: '#fff',
                    font: { weight: 'bold', size: 14 },
                    textShadowBlur: 4,
                    textShadowColor: 'rgba(0,0,0,0.8)',
                    formatter: val => val > 0 ? val : ''
                }
            }
        };
    }
};

// 7. Listeners Globales Automáticos (Tabs y Cambio de Tema Claro/Oscuro)
$(document).ready(function () {
    // Redimensionar automáticamente todos los gráficos activos al cambiar de pestaña en Bootstrap
    $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"], a[data-bs-toggle="tab"]', function () {
        Object.values(Chart.instances).forEach(chart => {
            if (chart) chart.resize();
        });
    });

    // Actualizar colores de todos los gráficos activos cuando cambia el atributo data-bs-theme
    const themeObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-bs-theme') {
                const { textColor, gridColor } = getChartThemeColors();

                Object.values(Chart.instances).forEach(chart => {
                    if (!chart) return;

                    if (chart.options.scales?.x) {
                        chart.options.scales.x.ticks.color = textColor;
                        chart.options.scales.x.grid.color = gridColor;
                    }
                    if (chart.options.scales?.y) {
                        chart.options.scales.y.ticks.color = textColor;
                        chart.options.scales.y.grid.color = gridColor;
                    }
                    if (chart.options.plugins?.legend?.labels) {
                        chart.options.plugins.legend.labels.color = textColor;
                    }

                    // Mantener texto blanco con sombra en donas y en el interior de barras apiladas;
                    // cambiar a textColor en barras simples/agrupadas y líneas.
                    const isDoughnut = chart.config.type === 'doughnut' || chart.config.type === 'pie';
                    const isStacked = chart.options.scales?.x?.stacked || chart.options.scales?.y?.stacked;
                    if (!isDoughnut && !isStacked && chart.options.plugins?.datalabels) {
                        chart.options.plugins.datalabels.color = textColor;
                    }

                    chart.update();
                });
            }
        });
    });

    themeObserver.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });
});