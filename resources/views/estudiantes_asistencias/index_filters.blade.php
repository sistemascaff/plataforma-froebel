<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-filter me-1"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter_rango_fechas" class="form-label text-muted fw-bold">Rango de fechas:</label>
                <select class="form-select" id="filter_rango_fechas">
                    <option value="">Todo</option>
                    <option value="hoy" selected>Hoy</option>
                    <option value="esta_semana">Esta Semana</option>
                    <option value="este_mes">Este Mes</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                <select class="form-select" id="filter_id_nivel">
                    <option value="">Todos</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_coordinacion" class="form-label text-muted fw-bold">Coordinación:</label>
                <select class="form-select" id="filter_id_coordinacion">
                    <option value="">Todos</option>
                    @foreach ($coordinaciones as $coordinacion)
                        <option value="{{ $coordinacion->id_coordinacion }}">
                            {{ $coordinacion->coordinacion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_docente" class="form-label text-muted fw-bold">Docente:</label>
                <select class="form-select" id="filter_id_docente">
                    @if ($tipo_perfil === 'DOCENTE')
                        <option value="">{{ Auth::user()->persona?->nombres_apellidos }}</option>
                    @else
                        <option value="">Todos</option>
                        @foreach ($docentes as $docente)
                            <option value="{{ $docente->id_docente }}">{{ $docente->persona->nombres_apellidos }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_periodo" class="form-label text-muted fw-bold">Periodo:</label>
                <select class="form-select" id="filter_id_periodo">
                    <option value="">Todos</option>
                    @foreach ($periodos as $periodo)
                        <option value="{{ $periodo->id_periodo }}" @selected(now()->between(\Carbon\Carbon::parse($periodo->fecha_inicio)->startOfDay(), \Carbon\Carbon::parse($periodo->fecha_fin)->endOfDay()))>
                            {{ "{$periodo->gestion->anio} - {$periodo->periodo} (De " . \Carbon\Carbon::parse($periodo->fecha_inicio)->locale('es')->translatedFormat('l d/m/Y') . ' a ' . \Carbon\Carbon::parse($periodo->fecha_fin)->locale('es')->translatedFormat('l d/m/Y') . ')' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                    <i class="fa-duotone fa-search me-1"></i>Filtrar
                </button>
            </div>
        </div>
    </div>
</div>
