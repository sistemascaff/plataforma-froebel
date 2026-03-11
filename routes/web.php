<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\CampoController;
use App\Http\Controllers\CoordinacionController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DimensionController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\HorarioAsignaturaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ListaAsignaturaController;
use App\Http\Controllers\MallaCurricularController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\PrestamoLibroController;
use App\Http\Controllers\UsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ruta por defecto
//Route::get('/', function () {
//    return redirect()->route('main.index');
//});

/*Estructura de Laravel => Route::get(URL web, método de controlador)->name('nombre.para.referenciar.ruta')*/

/* Tabla con PK FK 'personas'*/

Route::controller(UsuarioController::class)->group(function () {
    /* Rutas para gestionar la sesión del usuario y el panel de administración */
    Route::get('/', 'view_main_index')->name('main.index');
    Route::get('panel', 'view_dashboard')->name('dashboard');
    Route::get('iniciar-sesion', 'view_iniciar_sesion')->name('login');
    Route::get('cerrar-sesion', 'cerrar_sesion')->name('logout');
    Route::post('verificar', 'verificar')->name('login.verificar');
});

/* Tabla con PK FK 'colegios', 'personas' (atributo 'prestado_a') y relación muchos a muchos con 'prestamos_libros' mediante 'detalles_prestamos_libros'*/
Route::controller(LibroController::class)->group(function () {
    Route::get('biblioteca', 'view_public')->name('libros.public.index');
    //nota: hay una api para listar los libros publicamente, accesible en api.php
    Route::get('libros', 'view_index')->name('libros.index');
    Route::get('libros/listar', 'listar')->name('libros.listar');
    Route::get('libros/{libro}', 'mostrar')->name('libros.mostrar');
    Route::post('libros', 'create')->name('libros.create');
    Route::put('libros/{libro}', 'update')->name('libros.update');
    Route::patch('libros/{libro}', 'delete')->name('libros.delete');

    Route::get('libros/{libro}/detalles', 'view_details')->name('libros.detalles');
});

/* Tabla con PK FK 'personas' y relación muchos a muchos con 'productos' mediante 'detalles_prestamos_libros'*/
Route::controller(PrestamoLibroController::class)->group(function () {
    // Vistas web
    Route::get('prestamos_libros', 'view_index')->name('prestamos_libros.index');
    Route::get('prestamos_libros/crear', 'view_create')->name('prestamos_libros.crear');
    Route::get('prestamos_libros/reportes', 'view_reportes')->name('prestamos_libros.reportes');
    Route::get('prestamos_libros/reportes/imprimir', 'view_reportes_imprimir')->name('prestamos_libros.reportes.imprimir');
    Route::get('prestamos_libros/{prestamo_libro}/detalles', 'view_details')->name('prestamos_libros.detalles');
    Route::get('prestamos_libros/{prestamo_libro}/editar', 'view_update')->name('prestamos_libros.editar');
    Route::get('prestamos_libros/{prestamo_libro}/comprobante', 'view_imprimir')->name('prestamos_libros.imprimir');

    // Operaciones CRUD
    Route::get('prestamos_libros/listar', 'listar')->name('prestamos_libros.listar');
    Route::get('prestamos_libros/{prestamo_libro}', 'mostrar')->name('prestamos_libros.mostrar');
    Route::post('prestamos_libros', 'create')->name('prestamos_libros.create');
    Route::put('prestamos_libros/{prestamo_libro}', 'update')->name('prestamos_libros.update');
    Route::patch('prestamos_libros/{prestamo_libro}', 'delete')->name('prestamos_libros.delete');
    // Operaciones de detalles de préstamos
    Route::post('prestamos_libros/{prestamo_libro}/marcar/{libro}', 'marcar_devolucion')->name('prestamos_libros.marcar');
});

Route::controller(PersonaController::class)->group(function () {
    Route::get('personas/listar', 'listar')->name('personas.listar');
    Route::get('personas/{persona}', 'mostrar')->name('personas.mostrar');
});

Route::controller(GestionController::class)->group(function () {
    Route::get('gestiones', 'view_index')->name('gestiones.index');
    Route::get('gestiones/listar', 'listar')->name('gestiones.listar');
    Route::get('gestiones/{gestion}', 'mostrar')->name('gestiones.mostrar');
    Route::post('gestiones', 'create')->name('gestiones.create');
    Route::put('gestiones/{gestion}', 'update')->name('gestiones.update');
    Route::patch('gestiones/{gestion}', 'delete')->name('gestiones.delete');

    Route::get('gestiones/{gestion}/detalles', 'view_details')->name('gestiones.detalles');
});

Route::controller(PeriodoController::class)->group(function () {
    Route::get('periodos', 'view_index')->name('periodos.index');
    Route::get('periodos/listar', 'listar')->name('periodos.listar');
    Route::get('periodos/{periodo}', 'mostrar')->name('periodos.mostrar');
    Route::post('periodos', 'create')->name('periodos.create');
    Route::put('periodos/{periodo}', 'update')->name('periodos.update');
    Route::patch('periodos/{periodo}', 'delete')->name('periodos.delete');
});

Route::controller(DimensionController::class)->group(function () {
    Route::get('dimensiones', 'view_index')->name('dimensiones.index');
    Route::get('dimensiones/listar', 'listar')->name('dimensiones.listar');
    Route::get('dimensiones/{dimension}', 'mostrar')->name('dimensiones.mostrar');
    Route::post('dimensiones', 'create')->name('dimensiones.create');
    Route::put('dimensiones/{dimension}', 'update')->name('dimensiones.update');
    Route::patch('dimensiones/{dimension}', 'delete')->name('dimensiones.delete');
});

Route::controller(AulaController::class)->group(function () {
    Route::get('aulas', 'view_index')->name('aulas.index');
    Route::get('aulas/listar', 'listar')->name('aulas.listar');
    Route::get('aulas/{aula}', 'mostrar')->name('aulas.mostrar');
    Route::post('aulas', 'create')->name('aulas.create');
    Route::put('aulas/{aula}', 'update')->name('aulas.update');
    Route::patch('aulas/{aula}', 'delete')->name('aulas.delete');
});

Route::controller(NivelController::class)->group(function () {
    Route::get('niveles', 'view_index')->name('niveles.index');
    Route::get('niveles/listar', 'listar')->name('niveles.listar');
    Route::get('niveles/{nivel}', 'mostrar')->name('niveles.mostrar');
    Route::post('niveles', 'create')->name('niveles.create');
    Route::put('niveles/{nivel}', 'update')->name('niveles.update');
    Route::patch('niveles/{nivel}', 'delete')->name('niveles.delete');

    Route::get('niveles/{nivel}/detalles', 'view_details')->name('niveles.detalles');
});

Route::controller(GradoController::class)->group(function () {
    Route::get('grados', 'view_index')->name('grados.index');
    Route::get('grados/listar', 'listar')->name('grados.listar');
    Route::get('grados/{grado}', 'mostrar')->name('grados.mostrar');
    Route::post('grados', 'create')->name('grados.create');
    Route::put('grados/{grado}', 'update')->name('grados.update');
    Route::patch('grados/{grado}', 'delete')->name('grados.delete');

    Route::get('grados/{grado}/detalles', 'view_details')->name('grados.detalles');
});

Route::controller(CursoController::class)->group(function () {
    Route::get('cursos', 'view_index')->name('cursos.index');
    Route::get('cursos/listar', 'listar')->name('cursos.listar');
    Route::get('cursos/{curso}', 'mostrar')->name('cursos.mostrar');
    Route::post('cursos', 'create')->name('cursos.create');
    Route::put('cursos/{curso}', 'update')->name('cursos.update');
    Route::patch('cursos/{curso}', 'delete')->name('cursos.delete');
});

Route::controller(CampoController::class)->group(function () {
    Route::get('campos', 'view_index')->name('campos.index');
    Route::get('campos/listar', 'listar')->name('campos.listar');
    Route::get('campos/{campo}', 'mostrar')->name('campos.mostrar');
    Route::post('campos', 'create')->name('campos.create');
    Route::put('campos/{campo}', 'update')->name('campos.update');
    Route::patch('campos/{campo}', 'delete')->name('campos.delete');

    Route::get('campos/{campo}/detalles', 'view_details')->name('campos.detalles');
});

Route::controller(AreaController::class)->group(function () {
    Route::get('areas', 'view_index')->name('areas.index');
    Route::get('areas/listar', 'listar')->name('areas.listar');
    Route::get('areas/{area}', 'mostrar')->name('areas.mostrar');
    Route::post('areas', 'create')->name('areas.create');
    Route::put('areas/{area}', 'update')->name('areas.update');
    Route::patch('areas/{area}', 'delete')->name('areas.delete');

    Route::get('areas/{area}/detalles', 'view_details')->name('areas.detalles');
});

Route::controller(MateriaController::class)->group(function () {
    Route::get('materias', 'view_index')->name('materias.index');
    Route::get('materias/listar', 'listar')->name('materias.listar');
    Route::get('materias/{materia}', 'mostrar')->name('materias.mostrar');
    Route::post('materias', 'create')->name('materias.create');
    Route::put('materias/{materia}', 'update')->name('materias.update');
    Route::patch('materias/{materia}', 'delete')->name('materias.delete');

    Route::get('materias/{materia}/detalles', 'view_details')->name('materias.detalles');
});

Route::controller(CoordinacionController::class)->group(function () {
    Route::get('coordinaciones', 'view_index')->name('coordinaciones.index');
    Route::get('coordinaciones/listar', 'listar')->name('coordinaciones.listar');
    Route::get('coordinaciones/{coordinacion}', 'mostrar')->name('coordinaciones.mostrar');
    Route::post('coordinaciones', 'create')->name('coordinaciones.create');
    Route::put('coordinaciones/{coordinacion}', 'update')->name('coordinaciones.update');
    Route::patch('coordinaciones/{coordinacion}', 'delete')->name('coordinaciones.delete');

    Route::get('coordinaciones/{coordinacion}/detalles', 'view_details')->name('coordinaciones.detalles');
});

Route::controller(MallaCurricularController::class)->group(function () {
    Route::get('mallas_curriculares', 'view_index')->name('mallas_curriculares.index');
    Route::get('mallas_curriculares/listar', 'listar')->name('mallas_curriculares.listar');
    Route::get('mallas_curriculares/{malla_curricular}', 'mostrar')->name('mallas_curriculares.mostrar');
    Route::post('mallas_curriculares', 'create')->name('mallas_curriculares.create');
    Route::put('mallas_curriculares/{malla_curricular}', 'update')->name('mallas_curriculares.update');
    Route::patch('mallas_curriculares/{malla_curricular}', 'delete')->name('mallas_curriculares.delete');

    Route::get('mallas_curriculares/{malla_curricular}/detalles', 'view_details')->name('mallas_curriculares.detalles');
});

Route::controller(AsignaturaController::class)->group(function () {
    Route::get('asignaturas', 'view_index')->name('asignaturas.index');
    Route::get('asignaturas/listar', 'listar')->name('asignaturas.listar');
    Route::get('asignaturas/{asignatura}', 'mostrar')->name('asignaturas.mostrar');
    Route::post('asignaturas', 'create')->name('asignaturas.create');
    Route::put('asignaturas/{asignatura}', 'update')->name('asignaturas.update');
    Route::patch('asignaturas/{asignatura}', 'delete')->name('asignaturas.delete');

    Route::get('asignaturas/{asignatura}/detalles', 'view_details')->name('asignaturas.detalles');
    Route::post('asignaturas/{asignatura}/horarios/sync', 'sync_horarios')->name('asignaturas.horarios.sync');
});

Route::controller(ListaAsignaturaController::class)->group(function () {
    Route::patch('listas_asignaturas/{lista_asignatura}/docente', 'actualizar_docente')->name('listas_asignaturas.actualizar_docente');
});

Route::controller(HorarioAsignaturaController::class)->group(function () {
    Route::get('horarios_asignaturas', 'view_index')->name('horarios_asignaturas.index');
    Route::get('horarios_asignaturas/listar', 'listar')->name('horarios_asignaturas.listar');
    Route::get('horarios_asignaturas/{horario_asignatura}', 'mostrar')->name('horarios_asignaturas.mostrar');
    Route::post('horarios_asignaturas', 'create')->name('horarios_asignaturas.create');
    Route::put('horarios_asignaturas/{horario_asignatura}', 'update')->name('horarios_asignaturas.update');
    Route::patch('horarios_asignaturas/{horario_asignatura}', 'delete')->name('horarios_asignaturas.delete');

    Route::get('horarios_asignaturas/{horario_asignatura}/detalles', 'view_details')->name('horarios_asignaturas.detalles');
});

Route::controller(DocenteController::class)->group(function () {
    Route::get('docentes', 'view_index')->name('docentes.index');
    Route::get('docentes/listar', 'listar')->name('docentes.listar');
    Route::get('docentes/{docente}', 'mostrar')->name('docentes.mostrar');
    Route::post('docentes', 'create')->name('docentes.create');
    Route::put('docentes/{docente}', 'update')->name('docentes.update');
    Route::patch('docentes/{docente}', 'delete')->name('docentes.delete');

    Route::get('docentes/{docente}/detalles', 'view_details')->name('docentes.detalles');
});
