<script>
    $(document).ready(function() {
        // Inicializa DataTables en todas las tablas que tengan la CLASE .dataTable
        $(".dataTable").DataTable({
            ...dtDefaults,
            pageLength: 10,
        });

        const MAX_BARRAS_CHART = 20;

        // ==========================================
        // 1. Libros más prestados
        // ==========================================
        const librosLabels = {!! json_encode($libros_mas_prestados->pluck('titulo')) !!}.slice(0, MAX_BARRAS_CHART);
        const librosData = {!! json_encode($libros_mas_prestados->pluck('total')) !!}.slice(0, MAX_BARRAS_CHART);
        const coloresLibros = generarColoresChartJS(librosData.length);

        initOrUpdateChart(null, 'chart-libros-mas-prestados', {
            type: 'bar',
            data: {
                labels: librosLabels,
                datasets: [{
                    label: 'Veces prestado',
                    data: librosData,
                    backgroundColor: coloresLibros.backgroundColors,
                    borderColor: coloresLibros.borderColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });

        // ==========================================
        // 2. Préstamos por categoría
        // ==========================================
        const categoriaLabels = {!! json_encode($prestamos_por_categoria->pluck('categoria')) !!}.slice(0, MAX_BARRAS_CHART);
        const categoriaData = {!! json_encode($prestamos_por_categoria->pluck('total')) !!}.slice(0, MAX_BARRAS_CHART);
        const coloresCategoria = generarColoresChartJS(categoriaData.length);

        initOrUpdateChart(null, 'chart-prestamos-por-categoria', {
            type: 'bar',
            data: {
                labels: categoriaLabels,
                datasets: [{
                    label: 'Veces prestado',
                    data: categoriaData,
                    backgroundColor: coloresCategoria.backgroundColors,
                    borderColor: coloresCategoria.borderColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });

        // ==========================================
        // 3. Préstamos por curso
        // ==========================================
        const cursoLabels = {!! json_encode($prestamos_por_curso->pluck('curso')) !!};
        const cursoData = {!! json_encode($prestamos_por_curso->pluck('total')) !!};
        const coloresCurso = generarColoresChartJS(cursoData.length);

        initOrUpdateChart(null, 'chart-prestamos-por-curso', {
            type: 'bar',
            data: {
                labels: cursoLabels,
                datasets: [{
                    label: 'Veces prestado',
                    data: cursoData,
                    backgroundColor: coloresCurso.backgroundColors,
                    borderColor: coloresCurso.borderColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });

        // ==========================================
        // 4. Préstamos por tipo_perfil
        // ==========================================
        const tipoPerfilLabels = {!! json_encode($prestamos_por_tipo_perfil->pluck('tipo_perfil')) !!};
        const tipoPerfilData = {!! json_encode($prestamos_por_tipo_perfil->pluck('total')) !!};
        const coloresTipoPerfil = generarColoresChartJS(tipoPerfilData.length);

        initOrUpdateChart(null, 'chart-prestamos-por-tipo-perfil', {
            type: 'bar',
            data: {
                labels: tipoPerfilLabels,
                datasets: [{
                    label: 'Veces prestado',
                    data: tipoPerfilData,
                    backgroundColor: coloresTipoPerfil.backgroundColors,
                    borderColor: coloresTipoPerfil.borderColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });

        // ==========================================
        // 5. Préstamos por persona
        // ==========================================
        const personaLabels = {!! json_encode($prestamos_por_persona->pluck('persona')) !!}.slice(0, MAX_BARRAS_CHART);
        const personaData = {!! json_encode($prestamos_por_persona->pluck('total')) !!}.slice(0, MAX_BARRAS_CHART);
        const coloresPersona = generarColoresChartJS(personaData.length);

        initOrUpdateChart(null, 'chart-prestamos-por-persona', {
            type: 'bar',
            data: {
                labels: personaLabels,
                datasets: [{
                    label: 'Veces prestado',
                    data: personaData,
                    backgroundColor: coloresPersona.backgroundColors,
                    borderColor: coloresPersona.borderColors,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });

        // ==========================================
        // 6. Préstamos totales y pendientes por persona
        // ==========================================
        const prestamosLabels = {!! json_encode(
            $prestamos_totales->map(function ($p) {
                return "({$p->persona->tipo_perfil}) {$p->persona->apellidos_nombres}";
            }),
        ) !!}.slice(0, MAX_BARRAS_CHART);
        const prestamosTotalesData = {!! json_encode($prestamos_totales->pluck('total_libros')) !!}.slice(0, MAX_BARRAS_CHART);
        const prestamosPendientesData = {!! json_encode($prestamos_totales->pluck('libros_debe')) !!}.slice(0, MAX_BARRAS_CHART);

        initOrUpdateChart(null, 'chart-relacion-prestamos-devoluciones', {
            type: 'bar',
            data: {
                labels: prestamosLabels,
                datasets: [{
                        label: 'Total de libros',
                        data: prestamosTotalesData,
                        backgroundColor: 'rgba(0, 255, 255, 0.2)', // cian
                        borderColor: 'rgb(0, 255, 255)',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Libros pendientes',
                        data: prestamosPendientesData,
                        backgroundColor: 'rgba(255, 40, 40, 0.2)', // rojo
                        borderColor: 'rgb(255, 40, 40)',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: ChartDefaults.getBarOptions({
                showLegend: true
            })
        });
    });
</script>
