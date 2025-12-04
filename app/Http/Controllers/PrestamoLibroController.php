<?php

namespace App\Http\Controllers;

use App\Models\DetallePrestamoLibro;
use App\Models\Libro;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\PrestamoLibro;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PrestamoLibroController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        return view('prestamos_libros.index', [
            'head_title' => 'GESTIÓN DE PRÉSTAMOS DE LIBROS',
        ]);
    }

    public function view_reportes(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        $fecha_inicio = $request->fecha_inicio ?? date('Y-m-d', strtotime("-3 months"));
        $fecha_fin    = $request->fecha_fin ?? date('Y-m-d');

        $fecha_fin = "$fecha_fin 23:59:59";

        $prestamos_libros = PrestamoLibro::with([
            'libros' => function ($q) {
                $q->orderBy('codigo', 'ASC'); // ordenar los libros por código
            },
            'libros.prestado',
            'persona',
            'creado',
            'modificado',
            'eliminado'
        ])
            ->orderBy('id_prestamo_libro', 'DESC')
            ->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->get();

        $libros_mas_prestados = DetallePrestamoLibro::select('libros.titulo', 'libros.categoria', DB::raw('COUNT(detalles_prestamos_libros.id_libro) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('libros.id_libro')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_categoria = DetallePrestamoLibro::select('libros.categoria', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('libros.categoria')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_curso = DetallePrestamoLibro::select('prestamos_libros.curso', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('prestamos_libros.curso')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_tipo_perfil = DetallePrestamoLibro::select('personas.tipo_perfil', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('personas.tipo_perfil')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_persona = DetallePrestamoLibro::select(
            DB::raw("CONCAT('(',personas.tipo_perfil,') ',personas.apellido_paterno,' ',personas.apellido_materno,' ',personas.nombres) AS persona"),
            DB::raw('COUNT(*) as total')
        )
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('persona')
            ->orderByDesc('total')
            ->get();

        return view('prestamos_libros.reportes', [
            'head_title' => 'PRÉSTAMOS DE LIBROS - REPORTES',
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'prestamos_libros' => $prestamos_libros,
            'libros_mas_prestados' => $libros_mas_prestados,
            'prestamos_por_categoria' => $prestamos_por_categoria,
            'prestamos_por_curso' => $prestamos_por_curso,
            'prestamos_por_tipo_perfil' => $prestamos_por_tipo_perfil,
            'prestamos_por_persona' => $prestamos_por_persona,
        ]);
    }


    public function view_reportes_imprimir(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }
        //Para evitar problemas de memoria y tiempo de ejecución al generar el reporte, se incrementan los límites.
        //Esto es necesario porque el reporte puede contener una gran cantidad de datos.
        //Se recomienda ajustar estos valores según las necesidades del servidor y la cantidad de datos a procesar.
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $fecha_inicio = $request->fecha_inicio ?? date('Y-m-d', strtotime("-3 months"));
        $fecha_fin    = $request->fecha_fin ?? date('Y-m-d');

        $fecha_fin = "$fecha_fin 23:59:59";

        $prestamos_libros = PrestamoLibro::with([
            'libros' => function ($q) {
                $q->orderBy('codigo', 'ASC'); // ordenar los libros por código
            },
            'libros.prestado',
            'persona',
            'creado',
            'modificado',
            'eliminado'
        ])
            ->orderBy('id_prestamo_libro', 'ASC')
            ->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->get();

        $libros_mas_prestados = DetallePrestamoLibro::select('libros.titulo', 'libros.categoria', DB::raw('COUNT(detalles_prestamos_libros.id_libro) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('libros.id_libro')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_categoria = DetallePrestamoLibro::select('libros.categoria', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('libros.categoria')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_curso = DetallePrestamoLibro::select('prestamos_libros.curso', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->whereBetween('fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('prestamos_libros.curso')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_tipo_perfil = DetallePrestamoLibro::select('personas.tipo_perfil', DB::raw('COUNT(*) as total'))
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('personas.tipo_perfil')
            ->orderByDesc('total')
            ->get();

        $prestamos_por_persona = DetallePrestamoLibro::select(
            DB::raw("CONCAT('(',personas.tipo_perfil,') ',personas.apellido_paterno,' ',personas.apellido_materno,' ',personas.nombres) AS persona"),
            DB::raw('COUNT(*) as total')
        )
            ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->whereBetween('prestamos_libros.fecha_registro', [$fecha_inicio, $fecha_fin])
            ->groupBy('persona')
            ->orderByDesc('total')
            ->get();

        $pdf = Pdf::loadView(
            'prestamos_libros.pdf_reporte_estadisticas_prestamos',
            compact('fecha_inicio', 'fecha_fin', 'prestamos_libros', 'libros_mas_prestados', 'prestamos_por_categoria',
            'prestamos_por_curso', 'prestamos_por_tipo_perfil', 'prestamos_por_persona')
        );

        $pdf->setOption("isPhpEnabled", true);

        return $pdf->stream(
            'REPORTE DE PRÉSTAMOS DE LIBROS ENTRE ' . date('d/m/Y', strtotime($fecha_inicio)) . ' Y ' . date('d/m/Y', strtotime($fecha_fin)) . '.pdf'
        );
    }

    public function view_details($prestamo_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        $prestamo_libro = (new PrestamoLibro())->get_prestamo_libro($prestamo_libro);

        return view('prestamos_libros.details', [
            'head_title' => 'PRÉSTAMO DE LIBROS N°' . $prestamo_libro->id_prestamo_libro,
            'prestamo_libro' => $prestamo_libro,
        ]);
    }

    public function view_create()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        return view('prestamos_libros.create', [
            'head_title' => 'CREAR PRÉSTAMO DE LIBRO',
        ]);
    }

    public function view_update($prestamo_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        $prestamo_libro = (new PrestamoLibro())->get_prestamo_libro($prestamo_libro);

        return view('prestamos_libros.update', [
            'head_title' => 'EDITAR PRÉSTAMO DE LIBROS N°' . $prestamo_libro->id_prestamo_libro,
            'prestamo_libro' => $prestamo_libro,
        ]);
    }

    public function view_imprimir($prestamo_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $prestamo_libro = (new PrestamoLibro())->get_prestamo_libro($prestamo_libro);
        $fecha = date('Y-m-d H_i_s');

        $pdf = Pdf::loadView('prestamos_libros.pdf_comprobante_prestamo', compact('prestamo_libro'));
        $pdf->setPaper('letter');
        return $pdf->stream('PRÉSTAMO DE LIBROS N° ' . $prestamo_libro->id_prestamo_libro . ' - ' . $fecha . '.pdf');
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $prestamos_libros = (new PrestamoLibro())->get_all_prestamos_libros();

        return response()->json([
            'data' => $prestamos_libros
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $prestamo_libro = (new PrestamoLibro())->get_prestamo_libro($request->prestamo_libro);

        return response()->json([
            'data' => $prestamo_libro
        ]);
    }

    public function create(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_persona'   => 'required|integer|exists:personas,id_persona',
            'celular' => 'nullable|string|max:15',
            'detalles'   => 'required|array|min:1',
            'detalles.*.id_libro' => 'required|integer|exists:libros,id_libro',
            'fecha_devolucion' => 'required|date'
        ]);

        // Validar libros antes de iniciar la transacción
        foreach ($request->detalles as $detalle) {
            $libro = Libro::find($detalle['id_libro']);
            if (!$libro) {
                return response()->json([
                    'success' => false,
                    'message' => 'El libro con ID ' . $detalle['id_libro'] . ' no existe.'
                ], 400);
            }

            if ($libro->estado != 1) {
                // Estado 2 = vendido, Estado 0 = eliminado
                $estado_texto = $libro->estado == 2 ? '<b class="text-primary">vendido</b>' : '<b class="text-secondary">eliminado</b>';
                return response()->json([
                    'success' => false,
                    'message' => 'El libro <b class="text-primary">' . $libro->codigo . ' ' . $libro->titulo . '</b> no está disponible para el préstamo (actualmente ' . $estado_texto . '), remuévalo de la lista o actualice la página.',
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            $persona = (new Persona())->get_persona($request->id_persona);
            $curso = $persona->estudiante->curso->curso ?? null;
            $celular = $request->celular ?? $persona->celular;

            $prestamo_libro = new PrestamoLibro();
            $prestamo_libro->id_persona = $request->id_persona;
            $prestamo_libro->curso = $curso ? $curso : 'N/A';
            $prestamo_libro->celular = $celular;
            $prestamo_libro->fecha_devolucion = $request->fecha_devolucion;
            $prestamo_libro->creado_por = session('id_usuario');
            $prestamo_libro->ip = session('ip');
            $prestamo_libro->dispositivo = session('dispositivo');
            $prestamo_libro->save();

            foreach ($request->detalles as $detalle) {
                $prestamo_libro->libros()->attach($detalle['id_libro'], [
                    'fecha_retorno' => null
                ]);

                $libro = Libro::find($detalle['id_libro']);
                $libro->estado = 2;
                $libro->prestado_a = $request->id_persona;
                $libro->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo de libros registrado correctamente',
                'prestamo_libro'   => $prestamo_libro->load(['libros', 'persona'])
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id_prestamo_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_persona'   => 'required|integer|exists:personas,id_persona',
            'celular' => 'nullable|string|max:15',
            'detalles'   => 'required|array|min:1',
            'detalles.*.id_libro' => 'required|integer|exists:libros,id_libro',
            'fecha_devolucion' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            // Persona y curso
            $persona = (new Persona())->get_persona($request->id_persona);
            $curso = $persona->estudiante->curso->curso ?? 'N/A';
            $celular = $request->celular ?? $persona->celular;

            // Obtener préstamo
            $prestamo = (new PrestamoLibro())->get_prestamo_libro($id_prestamo_libro);

            // LISTA DE LIBROS SOLICITADOS (nuevos)
            $libros_nuevos = collect($request->detalles)->pluck('id_libro')->toArray();

            // LISTA DE LIBROS ANTERIORES
            $libros_anteriores = $prestamo->libros->map(function ($libro) {
                return [
                    'id_libro' => $libro->id_libro,
                    'fecha_retorno' => $libro->pivot->fecha_retorno
                ];
            });

            // 1. VALIDAR QUE NO SE INTENTE ELIMINAR UN LIBRO YA DEVUELTO
            foreach ($libros_anteriores as $item) {
                $id_libro = $item['id_libro'];
                $fecha_retorno = $item['fecha_retorno'];

                $sera_eliminado = !in_array($id_libro, $libros_nuevos);

                if ($sera_eliminado && $fecha_retorno !== null) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "El libro con ID $id_libro ya fue devuelto y NO puede eliminarse del préstamo."
                    ], 422);
                }
            }

            // 2. VALIDAR QUE LOS NUEVOS LIBROS ESTÉN DISPONIBLES
            foreach ($libros_nuevos as $id_libro) {
                $existe_previamente = $libros_anteriores->contains(function ($v) use ($id_libro) {
                    return $v['id_libro'] == $id_libro;
                });

                // Si es libro nuevo, validar disponibilidad
                if (!$existe_previamente) {

                    $libro = Libro::find($id_libro);

                    if (!$libro || $libro->estado == 2 || $libro->prestado_a !== null) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "El libro con ID $id_libro no está disponible para prestar."
                        ], 422);
                    }

                    // Verificar que no esté en otro préstamo en curso
                    $detalle = DB::table('detalles_prestamos_libros')
                        ->where('id_libro', $id_libro)
                        ->whereNull('fecha_retorno')
                        ->first();

                    if ($detalle) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "El libro con ID $id_libro ya está prestado en otro préstamo."
                        ], 422);
                    }
                }
            }

            // 3. ACTUALIZAR CABECERA DEL PRÉSTAMO
            $prestamo->id_persona = $request->id_persona;
            $prestamo->curso = $curso;
            $prestamo->celular = $celular;
            $prestamo->fecha_devolucion = $request->fecha_devolucion;
            $prestamo->modificado_por = session('id_usuario');
            $prestamo->ip = session('ip');
            $prestamo->dispositivo = session('dispositivo');
            $prestamo->save();

            // 4. ELIMINAR SOLO LOS LIBROS ANTERIORES SIN RETORNO
            foreach ($libros_anteriores as $item) {
                $id_libro = $item['id_libro'];
                $fecha_retorno = $item['fecha_retorno'];

                $sera_eliminado = !in_array($id_libro, $libros_nuevos);

                if ($sera_eliminado && $fecha_retorno === null) {
                    // Quitar detalle
                    DB::table('detalles_prestamos_libros')
                        ->where('id_prestamo_libro', $prestamo->id_prestamo_libro)
                        ->where('id_libro', $id_libro)
                        ->delete();

                    // Restaurar disponibilidad
                    $libro = Libro::find($id_libro);
                    $libro->estado = 1;
                    $libro->prestado_a = null;
                    $libro->save();
                }
            }

            // 5. AGREGAR NUEVOS LIBROS
            foreach ($libros_nuevos as $id_libro) {

                $existe_previamente = $libros_anteriores->contains(function ($v) use ($id_libro) {
                    return $v['id_libro'] == $id_libro;
                });

                if (!$existe_previamente) {
                    // Insert detalle
                    DB::table('detalles_prestamos_libros')->insert([
                        'id_prestamo_libro' => $prestamo->id_prestamo_libro,
                        'id_libro' => $id_libro,
                        'fecha_retorno' => null
                    ]);

                    // Marcar libro como prestado
                    $libro = Libro::find($id_libro);
                    $libro->estado = 2; // en uso
                    $libro->prestado_a = $prestamo->id_persona;
                    $libro->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo de libros actualizado correctamente',
                'prestamo_libro' => $prestamo->load(['libros', 'persona'])
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function delete($id_prestamo_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        DB::beginTransaction();
        try {

            $prestamo = (new PrestamoLibro())->get_prestamo_libro($id_prestamo_libro);

            if (!$prestamo || $prestamo->estado == 0) {
                return response()->json(['success' => false, 'message' => 'Préstamo no encontrado o ya anulado'], 404);
            }

            // VALIDAR QUE NINGÚN LIBRO TENGA FECHA DE RETORNO
            foreach ($prestamo->libros as $libro) {
                if ($libro->pivot->fecha_retorno !== null) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "No se puede anular el préstamo porque el libro '{$libro->titulo}' ya fue devuelto."
                    ], 422);
                }
            }

            // Restaurar disponibilidad de TODOS los libros prestados
            foreach ($prestamo->libros as $libro) {
                $libro->estado = 1;
                $libro->prestado_a = null;
                $libro->save();
            }

            // Marcar préstamo como ANULADO
            $prestamo->estado = 0;
            $prestamo->modificado_por = session('id_usuario');
            $prestamo->fecha_actualizacion = now();
            $prestamo->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Préstamo anulado correctamente.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function marcar_devolucion($id_prestamo_libro, $id_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        DB::beginTransaction();
        try {
            $prestamo = (new PrestamoLibro())->get_prestamo_libro($id_prestamo_libro);

            if (!$prestamo || $prestamo->estado == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El préstamo no existe o está anulado.'
                ], 404);
            }

            // Verificar si el libro pertenece al préstamo
            $detalle = $prestamo->libros()->where('libros.id_libro', $id_libro)->first();
            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'El libro no pertenece a este préstamo.'
                ], 422);
            }

            $pivot = $detalle->pivot;
            $libro = Libro::find($id_libro);

            // -----------------------------------------
            // CASO A: El libro está pendiente (marcar como devuelto)
            // -----------------------------------------
            if ($pivot->fecha_retorno === null) {

                // marcar devolución
                $prestamo->libros()->updateExistingPivot($id_libro, [
                    'fecha_retorno' => Carbon::now()
                ]);

                // actualizar libro a disponible
                if ($libro) {
                    $libro->estado = 1;
                    $libro->prestado_a = null;
                    $libro->save();
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Libro marcado como devuelto.',
                    'type'    => 'devuelto'
                ]);
            }


            // -----------------------------------------
            // CASO B: El libro está devuelto (revertir devolución)
            // -----------------------------------------

            // Verificar que el libro no esté prestado en otro préstamo activo
            if ($libro->estado == 2 && $libro->prestado_a != $prestamo->id_persona) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede revertir porque el libro está prestado en otro préstamo.'
                ], 409);
            }

            // revertir devolución → volver a "en uso"
            $prestamo->libros()->updateExistingPivot($id_libro, [
                'fecha_retorno' => null
            ]);

            // marcar libro como EN USO nuevamente
            if ($libro) {
                $libro->estado = 2;
                $libro->prestado_a = $prestamo->id_persona;
                $libro->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Devolución revertida correctamente.',
                'type'    => 'revertido'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
