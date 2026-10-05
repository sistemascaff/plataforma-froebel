responsive: true,
lengthChange: true,
autoWidth: true,
colReorder: true,
order: [],
pageLength: 100,
dom: 'Blfrtip',
buttons: [{
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
            columns: ':visible'
        }
    },
    {
        extend: 'pdf',
        className: 'btn btn-danger',
        exportOptions: {
            columns: ':visible'
        }
    },
    {
        extend: 'colvis',
        className: 'btn btn-info'
    },
    {
        extend: 'searchBuilder',
        className: 'btn btn-warning'
    },
],