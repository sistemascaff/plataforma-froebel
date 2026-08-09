<!-- Modal para crear y editar estudiantes -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-formulario-titulo">
                    <i class="fa-solid fa-duotone fa-user-plus me-2"></i> CREAR ESTUDIANTE
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="form-crear-o-editar" class="container-fluid px-0">
                    <input type="hidden" name="id_estudiante" value="0">

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-address-card me-2"></i> Datos Personales
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="apellido_paterno" class="form-label fw-semibold">Apellido paterno <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="apellido_paterno"
                                        name="apellido_paterno" minlength="1" maxlength="50" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="apellido_materno" class="form-label fw-semibold">Apellido materno <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="apellido_materno"
                                        name="apellido_materno" minlength="1" maxlength="50" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nombres" class="form-label fw-semibold">Nombres <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombres" name="nombres"
                                        minlength="2" maxlength="50" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="documento_identificacion" class="form-label fw-semibold">C.I. <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="documento_identificacion"
                                        name="documento_identificacion" minlength="1" maxlength="15" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="documento_complemento" class="form-label fw-semibold">C.I.
                                        Complemento</label>
                                    <input type="text" class="form-control" id="documento_complemento"
                                        name="documento_complemento" maxlength="10">
                                </div>
                                <div class="col-md-4">
                                    <label for="documento_expedido" class="form-label fw-semibold">C.I. Expedido <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="documento_expedido" name="documento_expedido"
                                        required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option>LA PAZ</option>
                                        <option>COCHABAMBA</option>
                                        <option>SANTA CRUZ</option>
                                        <option>ORURO</option>
                                        <option>POTOSÍ</option>
                                        <option>TARIJA</option>
                                        <option>BENI</option>
                                        <option>PANDO</option>
                                        <option>CHUQUISACA</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="fecha_nacimiento" class="form-label fw-semibold">Fecha de nacimiento
                                        <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="fecha_nacimiento"
                                        name="fecha_nacimiento" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="sexo" class="form-label fw-semibold">Género <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="sexo" name="sexo" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option value="M">MASCULINO</option>
                                        <option value="F">FEMENINO</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="idioma" class="form-label fw-semibold">Idioma <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="idioma" name="idioma" required>
                                        <option value="" disabled selected>Seleccione...</option>
                                        <option>ESPAÑOL</option>
                                        <option>ALEMÁN</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="celular" class="form-label fw-semibold">Celular</label>
                                    <input type="text" class="form-control" id="celular" name="celular"
                                        maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label fw-semibold">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono"
                                        maxlength="20">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="text-info border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-key me-2"></i> Datos de Acceso
                            </h5>
                            <div class="row g-3 align-items-center">
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="correo" class="form-label fw-semibold">Correo electrónico
                                                <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="correo" name="correo"
                                                maxlength="60" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="contrasenha" class="form-label fw-semibold">Contraseña <span
                                                    class="text-danger"
                                                    id="label-contrasenha-requerida">*</span></label>
                                            <input type="password" class="form-control" id="contrasenha"
                                                name="contrasenha">
                                            <small class="text-muted">Dejar vacío para no cambiar (al editar)</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirmar_contrasenha"
                                                class="form-label fw-semibold">Confirmar contraseña <span
                                                    class="text-danger"
                                                    id="label-confirmar-requerida">*</span></label>
                                            <input type="password" class="form-control" id="confirmar_contrasenha"
                                                name="confirmar_contrasenha">
                                        </div>
                                        <div class="col-12">
                                            <label for="foto_perfil" class="form-label fw-semibold">Foto de
                                                perfil</label>
                                            <input type="file" class="form-control" id="foto_perfil"
                                                name="foto_perfil" accept="image/*">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <img class="img-thumbnail rounded-circle shadow-sm"
                                        style="width: 150px; height: 150px; object-fit: cover; display: none;"
                                        alt="Foto de perfil" id="preview_foto_perfil" src="#">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-2">
                        <div class="card-body">
                            <h5 class="text-success border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-graduation-cap me-2"></i> Detalles del Estudiante
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="curso" class="form-label fw-semibold">Curso asignado</label>
                                    <select class="form-select" id="curso" name="id_curso">
                                        <option value="">Ninguno</option>
                                        @foreach ($cursos as $curso)
                                            <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="nacimiento_pais" class="form-label fw-semibold">Nacimiento
                                        país</label>
                                    <input type="text" class="form-control" id="nacimiento_pais"
                                        name="nacimiento_pais" maxlength="45">
                                </div>
                                <div class="col-md-3">
                                    <label for="nacimiento_departamento"
                                        class="form-label fw-semibold">Departamento</label>
                                    <input type="text" class="form-control" id="nacimiento_departamento"
                                        name="nacimiento_departamento" maxlength="45">
                                </div>
                                <div class="col-md-3">
                                    <label for="nacimiento_provincia" class="form-label fw-semibold">Provincia</label>
                                    <input type="text" class="form-control" id="nacimiento_provincia"
                                        name="nacimiento_provincia" maxlength="45">
                                </div>
                                <div class="col-md-3">
                                    <label for="nacimiento_localidad" class="form-label fw-semibold">Localidad</label>
                                    <input type="text" class="form-control" id="nacimiento_localidad"
                                        name="nacimiento_localidad" maxlength="45">
                                </div>

                                <div class="col-md-4">
                                    <label for="salud_tipo_sangre" class="form-label fw-semibold">Tipo de
                                        sangre</label>
                                    <select class="form-select" id="salud_tipo_sangre" name="salud_tipo_sangre">
                                        <option>NO ESTABLECIDO</option>
                                        <option>O+</option>
                                        <option>O-</option>
                                        <option>A+</option>
                                        <option>A-</option>
                                        <option>B+</option>
                                        <option>B-</option>
                                        <option>AB+</option>
                                        <option>AB-</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="salud_alergias" class="form-label fw-semibold">Alergias</label>
                                    <input type="text" class="form-control" id="salud_alergias"
                                        name="salud_alergias" maxlength="45" placeholder="Ej. Animales, alimentos...">
                                </div>
                                <div class="col-md-12">
                                    <label for="salud_datos" class="form-label fw-semibold">Datos médicos
                                        importantes</label>
                                    <textarea class="form-control" id="salud_datos" name="salud_datos" rows="3"
                                        placeholder="Enfermedades crónicas, tratamientos actuales, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-duotone fa-xmark me-1"></i> Cancelar
                </button>
                <button type="button" id="btn-guardar" class="btn btn-primary">
                    <i class="fa-solid fa-duotone fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
