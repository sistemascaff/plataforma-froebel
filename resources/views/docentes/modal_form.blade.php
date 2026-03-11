<!-- Modal para crear y editar docentes -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR DOCENTE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_docente en caso de editar -->
                    <input type="hidden" name="id_docente" value="0">

                    <h2 class="text-info fw-bold">Datos personales</h2>

                    <div class="mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido paterno <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno"
                            minlength="1" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="apellido_materno" class="form-label">Apellido materno <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno"
                            minlength="1" maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="nombres" class="form-label">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombres" name="nombres" minlength="2"
                            maxlength="50" required>
                    </div>

                    <div class="mb-3">
                        <label for="documento_identificacion" class="form-label">C.I. <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="documento_identificacion"
                            name="documento_identificacion" minlength="1" maxlength="15" required>
                    </div>

                    <div class="mb-3">
                        <label for="documento_complemento" class="form-label">C.I. Complemento</label>
                        {{-- No es obligatorio, puede estar vacío --}}
                        <input type="text" class="form-control" id="documento_complemento"
                            name="documento_complemento" maxlength="10">
                    </div>

                    <div class="mb-3">
                        <label for="documento_expedido" class="form-label">C.I. Expedido <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="documento_expedido" name="documento_expedido" required>
                            <option value="" disabled selected>Seleccione una opción</option>
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

                    <div class="mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="sexo" class="form-label">Género <span class="text-danger">*</span></label>
                        <select class="form-select" id="sexo" name="sexo" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="idioma" class="form-label">Idioma <span class="text-danger">*</span></label>
                        <select class="form-select" id="idioma" name="idioma" required>
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option>ESPAÑOL</option>
                            <option>ALEMÁN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular" maxlength="20">
                    </div>

                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" maxlength="20">
                    </div>

                    <h2 class="text-info fw-bold">Datos de acceso</h2>

                    <div class="mb-3 d-flex justify-content-center">
                        <img class="rounded" style="max-width: 200px; display: none;"
                            alt="Foto de perfil" id="preview_foto_perfil" src="#">
                    </div>

                    <div class="mb-3">
                        <label for="foto_perfil" class="form-label">Foto de perfil</label>
                        <input type="file" class="form-control" id="foto_perfil" name="foto_perfil"
                            accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo electrónico <span
                                class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="correo" name="correo" maxlength="60"
                            required>
                    </div>

                    {{-- Al editar, la contraseña es opcional (dejar vacío = no cambiar) --}}
                    <div class="mb-3">
                        <label for="contrasenha" class="form-label">Contraseña <span class="text-danger"
                                id="label-contrasenha-requerida">*</span></label>
                        <input type="password" class="form-control" id="contrasenha" name="contrasenha">
                    </div>

                    <div class="mb-3">
                        <label for="confirmar_contrasenha" class="form-label">Confirmar contraseña <span
                                class="text-danger" id="label-confirmar-requerida">*</span></label>
                        <input type="password" class="form-control" id="confirmar_contrasenha"
                            name="confirmar_contrasenha">
                    </div>

                    <h2 class="text-info fw-bold">Datos del docente</h2>

                    <div class="mb-3">
                        <label for="nivel" class="form-label">Responsable de nivel</label>
                        <select class="form-select" id="nivel" name="id_nivel">
                            <option value="">Ninguno</option>
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="coordinacion" class="form-label">Responsable de coordinación</label>
                        <select class="form-select" id="coordinacion" name="id_coordinacion">
                            <option value="">Ninguna</option>
                            @foreach ($coordinaciones as $coordinacion)
                                <option value="{{ $coordinacion->id_coordinacion }}">{{ $coordinacion->coordinacion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <input type="text" class="form-control" id="especialidad" name="especialidad"
                            maxlength="45">
                    </div>

                    <div class="mb-3">
                        <label for="grado_estudios" class="form-label">Grado de estudios</label>
                        <input type="text" class="form-control" id="grado_estudios" name="grado_estudios"
                            maxlength="45">
                    </div>

                    <div class="mb-3">
                        <label for="domicilio" class="form-label">Domicilio</label>
                        <input type="text" class="form-control" id="domicilio" name="domicilio" maxlength="250">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-duotone fa-close"></i> Cerrar
                </button>
                <button type="button" id="btn-guardar" class="btn btn-primary">
                    <i class="fa-solid fa-duotone fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>
