<script>
    $(document).ready(function() {
        const idPersonaSeleccionada = {{ $prestamo_libro->id_persona ?? 'null' }};

        $('#persona').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
        });

        function recargarPersonasSelect(idSeleccionado = null) {
            $.ajax({
                url: "{{ route('personas.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#persona");
                    $select.empty();
                    $select.append('<option value="">-- Seleccione una persona --</option>');

                    $.each(response.data, function(i, persona) {
                        // Omitir personas inactivos
                        if (persona.estado == '0') return;
                        // Se verifica si la persona es un estudiante y de ser así se obtiene el curso.
                        let curso = persona.estudiante ?
                            ` - ${persona.estudiante.curso.curso}` : '';
                        let nombre_completo = persona.apellido_paterno + ' ' + persona
                            .apellido_materno + ' ' + persona.nombres;
                        nombre_completo.trim();
                        // Se verifica si la persona se ha prestado libros y de ser así se agrega dicha información a la fila.
                        let datos_libros = persona.cantidad_total_prestamos > 0 ?
                            ` - Total ${persona.cantidad_total_prestamos}, debe ${persona.cantidad_libros_debe}` :
                            '';

                        // Se construye la fila en base al nombre completo, tipo de perfil, curso, correo y la información de los libros prestados.
                        let fila =
                            `(${persona.tipo_perfil}${curso}) ${nombre_completo} - ${persona.usuario.correo}${datos_libros}`
                            .trim();

                        $select.append(
                            `<option value="${persona.id_persona}">
                                ${fila}
                            </option>`
                        );
                    });

                    if (idSeleccionado) {
                        $select.val(idSeleccionado).trigger('change');
                    }
                }
            });
        }

        recargarPersonasSelect(idPersonaSeleccionada);

        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('libros.listar') }}", // Ruta de Laravel
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr, error, thrown) {
                    console.error("Error al cargar los datos:", error);
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // número de iteración
                    }
                },
                {
                    data: "categoria",
                },
                {
                    data: "codigo",
                    render: function(data, type, rowData, row) {
                        if (rowData.estado == 1) {
                            return `<span class="text-success fw-bold">${data}</span>`;
                        } else if (rowData.estado == 0) {
                            return `<span class="text-secondary fw-bold">${data}</span>`;
                        } else if (rowData.estado == 2) {
                            return `<span class="text-primary fw-bold">${data}</span>`;
                        } else {
                            return `<span class="text-warning fw-bold">${data}</span>`;
                        }
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData.estado == 1) {
                            $(td).addClass('table-success border border-success'); // Disponible
                        } else if (rowData.estado == 0) {
                            $(td).addClass(
                                'table-secondary border border-secondary'); // Eliminado
                        } else if (rowData.estado == 2) {
                            $(td).addClass('table-primary border border-primary'); // En uso
                        }
                    }
                },
                {
                    data: "titulo",
                },
                {
                    data: "autor",
                },
                {
                    data: "editorial",
                },
                {
                    data: "anio",
                },
                {
                    data: "descripcion",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "costo",
                },
                {
                    data: "adquisicion",
                    render: function(data, type, row) {
                        adquisicion = data == 1 ? 'COMPRA' : data == 2 ? 'DONACIÓN' : 'OTRO';
                        return adquisicion;
                    }
                },
                {
                    data: "presentacion",
                },
                {
                    data: "observacion",
                },
                {
                    data: "fecha_ingreso_cooperativa",
                    render: function(data, type, row) {
                        const fecha = new Date(data); // tu fecha
                        const anio = fecha.getFullYear();
                        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        const dia = String(fecha.getDate()).padStart(2, '0');
                        return `${anio}-${mes}-${dia}`;
                        //return `${dia}/${mes}/${anio}`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const cursoRaw = row.prestado?.estudiante?.curso?.curso || '';

                        const abreviar = (window.Helpers && typeof window.Helpers
                                .abreviarCurso === 'function') ?
                            window.Helpers.abreviarCurso(cursoRaw) :
                            cursoRaw;

                        const curso = abreviar ? ` (${abreviar})` : '';

                        const prestado_a = row.prestado ?
                            `${row.prestado.tipo_perfil} ${curso} - ${row.prestado.apellido_paterno} ${row.prestado.apellido_materno} ${row.prestado.nombres}` :
                            '-';

                        return `<b class="text-info">${prestado_a.trim()}</b>`;
                    }
                },
                {
                    data: "prestamos_libros_count",
                },
                {
                    data: "estado",
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">DISPONIBLE</span>';
                        } else if (data == 0) {
                            return '<span class="badge bg-secondary">ELIMINADO</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-primary">EN USO</span>';
                        } else {
                            return '<span class="badge bg-warning">DESCONOCIDO</span>';
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (row.estado == 1) {
                            // Escapar comillas dobles y otras entidades HTML
                            const tituloEscapado = row.titulo
                                .replace(/&/g, '&amp;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#39;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;');

                            return `
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success btn-sm btn-agregar" 
                                                data-id="${row.id_libro}" 
                                                data-codigo="${row.codigo}" 
                                                data-titulo="${tituloEscapado}" 
                                                data-toggle="tooltip" 
                                                title="Agregar">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>`;
                        } else {
                            return `-`;
                        }
                    }
                }
            ],
            columnDefs: [{
                targets: [3, 4, 5, 13],
                width: '500px',
            }, ],
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
            colReorder: true,
            order: [],
            pageLength: 10,
            /* @include('components.datatables.datatables_global_properties') */
            @include('components.datatables.datatables_language_property')
        });

        $(document).on('click', '.btn-agregar', function() {
            const numeroFilas = $('#detalles tbody tr').length;
            const id = $(this).data('id');
            const codigo = $(this).data('codigo');
            const titulo = $(this).data('titulo');
            let existe = false;

            $("#detalles tbody tr").each(function() {
                let id_libro = $(this).find('.id_libro').text();
                if (id_libro == id) {
                    existe = true;
                }
            });

            if (existe) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: "info",
                    title: "",
                    html: `¡El libro <b class="text-primary">${codigo} - ${titulo}</b> ya se encuentra en la lista!`,
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1500,
                });
                return;
            }

            $('#detalles tbody').append(
                `<tr>
                        <td class="fw-bold text-primary">${numeroFilas + 1}</td>
                        <td class="visually-hidden id_libro">${id}</td>
                        <td class="codigo">${codigo}</td>
                        <td class="titulo">${titulo}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-remover" 
                                    data-id="${id}" data-toggle="tooltip" title="Remover">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>`
            );
        });

        $(document).on('click', '.btn-remover', function() {
            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: "Confirmación",
                text: "¿Estás seguro de remover este libro del préstamo?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, quitar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).closest('tr').remove();
                    $('#detalles tbody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        icon: "success",
                        title: "",
                        html: `¡Hecho!`,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        timer: 1500,
                    });
                }
            });
        });

        $(document).on('click', '#btnSave', function() {
            const id_persona = $('#persona').val();
            const celular = $('#celular').val();
            let detalles = [];
            const fecha_devolucion = $('#fecha_devolucion').val();

            $("#detalles tbody tr").each(function() {
                let fila = $(this);
                let id_libro = fila.find('.id_libro').text().trim();
                detalles.push({
                    id_libro: id_libro,
                });
            });

            if (!id_persona) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: "¡No válido!",
                    html: "Selecciona una <b>persona</b> para guardar el préstamo de libros",
                    icon: "info"
                });
                return;
            }

            if (detalles.length === 0) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: "¡No válido!",
                    html: "¡Ingresa al menos un libro a la lista!",
                    icon: "warning"
                });
                return;
            }

            if (!fecha_devolucion) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: "¡No válido!",
                    html: "¡Ingresa la fecha de devolución!",
                    icon: "warning"
                });
                return;
            }

            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: "Confirmación",
                text: "¿Estás seguro de haber llenado la información correctamente?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, guardar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    editarPrestamoLibrosAJAX(id_persona, celular, detalles,
                        fecha_devolucion);
                }
            });
        });

        function editarPrestamoLibrosAJAX(id_persona, celular, detalles, fecha_devolucion) {
            const btn = $('#btnSave');
            // Deshabilitar el botón para evitar múltiples clics y cambiar el texto
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: "{{ route('prestamos_libros.update', ':id') }}".replace(':id',
                    {{ $prestamo_libro->id_prestamo_libro }}),
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id_persona: id_persona,
                    celular: celular,
                    detalles: detalles,
                    fecha_devolucion: fecha_devolucion,
                },
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success'
                    });
                    btn.html('<i class="fa-solid fa-duotone fa-circle-check"></i> ¡Éxito!');
                    window.open("{{ route('prestamos_libros.imprimir', ':id') }}".replace(':id',
                            {{ $prestamo_libro->id_prestamo_libro }}), '_blank',
                        'noopener,noreferrer');
                },
                error: function(xhr) {
                    let respuesta = {};
                    try {
                        respuesta = JSON.parse(xhr.responseText);
                    } catch (e) {
                        respuesta = {
                            message: "Error desconocido"
                        };
                    }

                    let htmlError = "";

                    if (respuesta.errors) {
                        // Errores de validación (422)
                        htmlError = Object.values(respuesta.errors)
                            .flat()
                            .join("<br>");
                    } else if (respuesta.message) {
                        // Errores manuales (400, 403, 500...)
                        htmlError = respuesta.message;
                    } else {
                        htmlError = "Ocurrió un error inesperado.";
                    }

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error'
                    });

                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-save"></i> Guardar');
                }
            });
        }

        $(document).on('click', '#btnDelete', function() {
            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: "Confirmación",
                html: "¿Estás seguro de <b>ANULAR</b> este préstamo de libros?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, anular",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    anularPrestamoLibrosAJAX();
                }
            });
        });

        function anularPrestamoLibrosAJAX() {
            const btn = $('#btnDelete');

            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Anulando...');

            $.ajax({
                url: "{{ route('prestamos_libros.delete', ':id') }}".replace(':id',
                    {{ $prestamo_libro->id_prestamo_libro }}),
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success'
                    });

                    btn.html('<i class="fa-solid fa-duotone fa-circle-check"></i> ¡Éxito!');

                    // Recargar la vista o redirigir
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr) {
                    let respuesta = {};
                    try {
                        respuesta = JSON.parse(xhr.responseText);
                    } catch (e) {
                        respuesta = {
                            message: "Error desconocido"
                        };
                    }

                    let htmlError = "";

                    if (respuesta.errors) {
                        htmlError = Object.values(respuesta.errors).flat().join("<br>");
                    } else if (respuesta.message) {
                        htmlError = respuesta.message;
                    } else {
                        htmlError = "Ocurrió un error inesperado.";
                    }

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error'
                    });

                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-file-xmark"></i> Anular');
                }
            });
        }

    });
</script>
