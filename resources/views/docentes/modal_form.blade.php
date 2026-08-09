<!-- Modal para crear y editar docentes -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-formulario-titulo">
                    <i class="fa-solid fa-duotone fa-chalkboard-user me-2"></i> CREAR DOCENTE
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="form-crear-o-editar" class="container-fluid px-0">
                    <input type="hidden" name="id_docente" value="0">

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
                                            <small class="text-muted">Dejar vacío para no cambiar</small>
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
                            <h5 class="text-warning text-darken border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-briefcase me-2"></i> Perfil Profesional
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nivel" class="form-label fw-semibold">Responsable de nivel</label>
                                    <select class="form-select" id="nivel" name="id_nivel">
                                        <option value="">Ninguno</option>
                                        @foreach ($niveles as $nivel)
                                            <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="coordinacion" class="form-label fw-semibold">Responsable de
                                        coordinación</label>
                                    <select class="form-select" id="coordinacion" name="id_coordinacion">
                                        <option value="">Ninguna</option>
                                        @foreach ($coordinaciones as $coordinacion)
                                            <option value="{{ $coordinacion->id_coordinacion }}">
                                                {{ $coordinacion->coordinacion }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="especialidad" class="form-label fw-semibold">Especialidad</label>
                                    <input type="text" class="form-control" id="especialidad" name="especialidad"
                                        maxlength="45" placeholder="Ej. Matemáticas, Lenguaje...">
                                </div>
                                <div class="col-md-6">
                                    <label for="grado_estudios" class="form-label fw-semibold">Grado de
                                        estudios</label>
                                    <input type="text" class="form-control" id="grado_estudios"
                                        name="grado_estudios" maxlength="45"
                                        placeholder="Ej. Licenciatura, Maestría...">
                                </div>

                                <div class="col-md-12">
                                    <label for="domicilio" class="form-label fw-semibold">Domicilio</label>
                                    <input type="text" class="form-control" id="domicilio" name="domicilio"
                                        maxlength="250" placeholder="Dirección completa">
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
