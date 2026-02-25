<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR MALLA CURRICULAR</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_malla_curricular en caso de editar -->
                    <input type="hidden" name="id_malla_curricular" value="0">

                    <div class="mb-3">
                        <label for="grado" class="form-label">Grado <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="grado" name="id_grado" required>
                            <option value="" disabled selected>Seleccione un grado</option>
                            @foreach ($grados as $grado)
                                <option value="{{ $grado->id_grado }}">{{ $grado->grado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="materia" class="form-label">Materia <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="materia" name="id_materia" required>
                            <option value="" disabled selected>Seleccione una materia</option>
                            @foreach ($materias as $materia)
                                <option value="{{ $materia->id_materia }}">{{ $materia->abreviatura }} - {{ $materia->materia }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="area" class="form-label">Área <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="area" name="id_area" required>
                            <option value="" disabled selected>Seleccione un área</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id_area }}">{{ $area->abreviatura }} - {{ $area->area }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gestion" class="form-label">Gestión <span class="text-danger">*</span></label>
                        <select class="form-select" id="gestion" name="id_gestion" required>
                            <option value="" disabled selected>Seleccione una gestión</option>
                            @foreach ($gestiones as $gestion)
                                <option value="{{ $gestion->id_gestion }}">{{ $gestion->anio }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                        class="fa-solid fa-duotone fa-close"></i>Cerrar</button>
                <button type="submit" id="btn-guardar" form="form-crear-o-editar" class="btn btn-primary">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
