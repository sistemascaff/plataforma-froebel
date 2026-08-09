<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR ASIGNATURA</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_asignatura en caso de editar -->
                    <input type="hidden" name="id_asignatura" value="0">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="asignatura" class="form-label">Asignatura <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="asignatura" name="asignatura" min="3"
                                max="100" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo_calificacion" class="form-label">Tipo de calificación <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_calificacion" name="tipo_calificacion" required>
                                <option value="" disabled selected>Seleccione un tipo de calificación</option>
                                <option value="cualitativa">CUALITATIVA</option>
                                <option value="cuantitativa">CUANTITATIVA</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tipo_bloque" class="form-label">Tipo de bloque <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_bloque" name="tipo_bloque" required>
                                <option value="" disabled selected>Seleccione un tipo de bloque</option>
                                <option value="curso">CURSO</option>
                                <option value="mixto">MIXTO</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="curso" class="form-label">Curso</label>
                            <select class="form-select select2" id="curso" name="id_curso" required>
                                <option value="" disabled selected>Seleccione un curso</option>
                                @foreach ($cursos as $curso)
                                    <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Solo se aplicará si el tipo de bloque es "curso"</div>
                        </div>
                        <div class="col-md-6">
                            <label for="aula" class="form-label">Aula <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="aula" name="id_aula" required>
                                <option value="" disabled selected>Seleccione un aula</option>
                                @foreach ($aulas as $aula)
                                    <option value="{{ $aula->id_aula }}">{{ $aula->aula }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">El espacio físico donde se desarrolla la asignatura.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="materia" class="form-label">Materia (Interna) <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" id="materia" name="id_materia" required>
                                <option value="" disabled selected>Seleccione una materia</option>
                                @foreach ($materias as $materia)
                                    <option value="{{ $materia->id_materia }}">{{ $materia->abreviatura }} -
                                        {{ $materia->materia }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="area" class="form-label">Área (SIE) <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" id="area" name="id_area" required>
                                <option value="" disabled selected>Seleccione un área</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id_area }}">{{ $area->abreviatura }} - {{ $area->area }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nivel" class="form-label">Nivel <span class="text-danger">*</span></label>
                            <select class="form-select" id="nivel" name="id_nivel" required>
                                <option value="" disabled selected>Seleccione un nivel</option>
                                @foreach ($niveles as $nivel)
                                    <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Permite que un <b>director de nivel</b> pueda gestionar la
                                asignatura.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="coordinacion" class="form-label">Coordinación</label>
                            <select class="form-select" id="coordinacion" name="id_coordinacion" required>
                                <option value="" selected>Seleccione una coordinación (opcional)</option>
                                @foreach ($coordinaciones as $coordinacion)
                                    <option value="{{ $coordinacion->id_coordinacion }}">
                                        {{ $coordinacion->coordinacion }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Permite que un <b>coordinador</b> pueda gestionar la asignatura.
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                        class="fa-solid fa-duotone fa-close me-1"></i>Cerrar</button>
                <button type="submit" id="btn-guardar" form="form-crear-o-editar" class="btn btn-primary">
                    <i class="fa-solid fa-duotone fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
