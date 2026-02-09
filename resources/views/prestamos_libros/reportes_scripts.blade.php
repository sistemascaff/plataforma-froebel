<script>
    // Esto inicializa DataTables en todas las tablas que tengan la CLASE .dataTable
    $(document).ready(function() {
        $(".dataTable").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        });
    });

    function generarColoresChartJS(cantidad) {
        const backgroundColors = [];
        const borderColors = [];

        const coloresBase = [{
                r: 255,
                g: 0,
                b: 0
            }, // Rojo
            {
                r: 0,
                g: 255,
                b: 0
            }, // Verde
            {
                r: 0,
                g: 0,
                b: 255
            }, // Azul
            {
                r: 255,
                g: 255,
                b: 0
            }, // Amarillo
            {
                r: 255,
                g: 0,
                b: 255
            }, // Magenta
            {
                r: 0,
                g: 255,
                b: 255
            }, // Cian
            {
                r: 255,
                g: 128,
                b: 0
            }, // Naranja
            {
                r: 128,
                g: 0,
                b: 255
            }, // Púrpura
            {
                r: 0,
                g: 255,
                b: 128
            }, // Verde claro
            {
                r: 255,
                g: 0,
                b: 128
            } // Rosa
        ];

        for (let i = 0; i < cantidad; i++) {
            const colorBase = coloresBase[i % coloresBase.length];

            // Si se necesita más colores que los base, añadimos variación
            const variacion = Math.floor(i / coloresBase.length) * 30;
            const r = Math.min(255, Math.max(0, colorBase.r + variacion));
            const g = Math.min(255, Math.max(0, colorBase.g + variacion));
            const b = Math.min(255, Math.max(0, colorBase.b + variacion));

            backgroundColors.push(`rgba(${r}, ${g}, ${b}, 0.2)`);
            borderColors.push(`rgb(${r}, ${g}, ${b})`);
        }

        return {
            backgroundColors,
            borderColors
        };
    }

    function generarColoresInstitucional(cantidad) {
        const bg = [
            'rgba(0, 0, 0, 0.2)', // Negro
            'rgba(214, 40, 40, 0.2)', // Rojo
            'rgba(247, 198, 0, 0.2)', // Amarillo
            'rgba(42, 157, 143, 0.2)' // Verde
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

        return {
            backgroundColors,
            borderColors
        };
    }


    // ==========================================
    // 1. Libros más prestados
    // ==========================================
    const librosLabels = {!! json_encode($libros_mas_prestados->pluck('titulo')) !!};
    const librosData = {!! json_encode($libros_mas_prestados->pluck('total')) !!};
    const coloresLibros = generarColoresChartJS(librosData.length);

    new Chart(document.getElementById('chart-libros-mas-prestados'), {
        type: 'bar',
        data: {
            labels: librosLabels,
            datasets: [{
                label: 'Veces prestado',
                data: librosData,
                backgroundColor: coloresLibros.backgroundColors,
                borderColor: coloresLibros.borderColors,
                borderWidth: 1
            }]
        }
    });

    // ==========================================
    // 2. Préstamos por categoría
    // ==========================================
    const categoriaLabels = {!! json_encode($prestamos_por_categoria->pluck('categoria')) !!};
    const categoriaData = {!! json_encode($prestamos_por_categoria->pluck('total')) !!};
    const coloresCategoria = generarColoresChartJS(categoriaData.length);

    new Chart(document.getElementById('chart-prestamos-por-categoria'), {
        type: 'bar',
        data: {
            labels: categoriaLabels,
            datasets: [{
                label: 'Veces prestado',
                data: categoriaData,
                backgroundColor: coloresCategoria.backgroundColors,
                borderColor: coloresCategoria.borderColors,
                borderWidth: 1
            }]
        }
    });

    // ==========================================
    // 3. Préstamos por curso
    // ==========================================
    const cursoLabels = {!! json_encode($prestamos_por_curso->pluck('curso')) !!};
    const cursoData = {!! json_encode($prestamos_por_curso->pluck('total')) !!};
    const coloresCurso = generarColoresChartJS(cursoData.length);

    new Chart(document.getElementById('chart-prestamos-por-curso'), {
        type: 'bar',
        data: {
            labels: cursoLabels,
            datasets: [{
                label: 'Veces prestado',
                data: cursoData,
                backgroundColor: coloresCurso.backgroundColors,
                borderColor: coloresCurso.borderColors,
                borderWidth: 1
            }]
        }
    });

    // ==========================================
    // 4. Préstamos por tipo_perfil
    // ==========================================
    const tipoPerfilLabels = {!! json_encode($prestamos_por_tipo_perfil->pluck('tipo_perfil')) !!};
    const tipoPerfilData = {!! json_encode($prestamos_por_tipo_perfil->pluck('total')) !!};
    const coloresTipoPerfil = generarColoresChartJS(tipoPerfilData.length);

    new Chart(document.getElementById('chart-prestamos-por-tipo-perfil'), {
        type: 'bar',
        data: {
            labels: tipoPerfilLabels,
            datasets: [{
                label: 'Veces prestado',
                data: tipoPerfilData,
                backgroundColor: coloresTipoPerfil.backgroundColors,
                borderColor: coloresTipoPerfil.borderColors,
                borderWidth: 1
            }]
        }
    });

    // ==========================================
    // 5. Préstamos por persona
    // ==========================================
    const personaLabels = {!! json_encode($prestamos_por_persona->pluck('persona')) !!};
    const personaData = {!! json_encode($prestamos_por_persona->pluck('total')) !!};
    const coloresPersona = generarColoresChartJS(personaData.length);

    new Chart(document.getElementById('chart-prestamos-por-persona'), {
        type: 'bar',
        data: {
            labels: personaLabels,
            datasets: [{
                label: 'Veces prestado',
                data: personaData,
                backgroundColor: coloresPersona.backgroundColors,
                borderColor: coloresPersona.borderColors,
                borderWidth: 1
            }]
        }
    });

    // ==========================================
    // 6. Préstamos totales y pendientes por persona
    // ==========================================

    const prestamosLabels = {!! json_encode(
        $prestamos_totales->map(function ($p) {
            return trim(
                '(' . $p->tipo_perfil . ') ' . $p->apellido_paterno . ' ' . $p->apellido_materno . ' ' . $p->nombres,
            );
        }),
    ) !!};
    const prestamosTotalesData = {!! json_encode($prestamos_totales->pluck('total_libros')) !!};
    const prestamosPendientesData = {!! json_encode($prestamos_totales->pluck('libros_debe')) !!};
    new Chart(document.getElementById('chart-relacion-prestamos-devoluciones'), {
        type: 'bar',
        data: {
            labels: prestamosLabels,
            datasets: [{
                    label: 'Total de libros',
                    data: prestamosTotalesData,
                    backgroundColor: 'rgba(0, 255, 255, 0.2)', // cian
                    borderColor: 'rgb(0, 255, 255)',
                    borderWidth: 1
                },
                {
                    label: 'Libros pendientes',
                    data: prestamosPendientesData,
                    backgroundColor: 'rgba(255, 40, 40, 0.2)', // rojo
                    borderColor: 'rgb(255, 40, 40)',
                    borderWidth: 1
                }
            ]
        }
    });
</script>
