responsive: true,
lengthChange: true,
autoWidth: true,
colReorder: true,
order: [],
pageLength: 100,
dom: 'Blfrtip',
buttons: [
    {
        extend: 'copy',
        className: 'btn btn-secondary',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend: 'csv',
        className: 'btn btn-success',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend: 'excel',
        className: 'btn btn-success',
        exportOptions: {
            columns: ':visible',
            stripHtml: false, // Evita que DataTables elimine el HTML prematuramente
            format: {
                body: function (data, row, column, node) {
                    if (typeof data === 'string') {
                        data = data.replace(/<br\s*\/?>/ig, '\n'); // Convierte <br> a \n
                        data = data.replace(/<[^>]*>?/gm, '');     // Limpia el resto de etiquetas HTML
                    }
                    return data;
                }
            }
        }
    },
    {
        extend: 'pdf',
        className: 'btn btn-danger',
        exportOptions: {
            columns: ':visible',
            stripHtml: false, // Evita que DataTables elimine el HTML prematuramente
            format: {
                body: function (data, row, column, node) {
                    if (typeof data === 'string') {
                        data = data.replace(/<br\s*\/?>/ig, '\n'); // Convierte <br> a \n
                        data = data.replace(/<[^>]*>?/gm, '');     // Limpia el resto de etiquetas HTML
                    }
                    return data;
                }
            }
        }
    },
    {
        extend: 'colvis',
        className: 'btn btn-info'
    },
    {
        extend: 'searchBuilder',
        className: 'btn btn-warning'
    }
],