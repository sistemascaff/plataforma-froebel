<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR HORARIO DE ASIGNATURAS</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_horario_asignatura en caso de editar -->
                    <input type="hidden" name="id_horario_asignatura" value="0">

                    <div class="mb-3">
                        <label for="denominacion" class="form-label">Denominacion <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="denominacion" name="denominacion" min="3"
                            max="45" required>
                    </div>

                    <div class="mb-3">
                        <label for="hora_inicio" class="form-label">Hora de inicio <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" min="3"
                            max="45" required>
                    </div>

                    <div class="mb-3">
                        <label for="hora_fin" class="form-label">Hora de fin <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="hora_fin" name="hora_fin" min="3"
                            max="45" required>
                    </div>

                    <div class="mb-3">
                        <label for="nivel" class="form-label">Nivel <span class="text-danger">*</span></label> 
                        <select class="form-select" id="nivel" name="id_nivel" required>
                            <option value="" disabled selected>Seleccione un nivel</option>
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Nota: en las asignaturas, el nivel del horario debe coincidir con el nivel de la asignatura para poder designar ese horario en la asignatura.</div>
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
