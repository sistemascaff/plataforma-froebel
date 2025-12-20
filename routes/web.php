<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibroController;
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

/* Tabla con PK FK 'colegios'*/
Route::controller(PersonaController::class)->group(function () {
    Route::get('personas/listar', 'listar')->name('personas.listar');
    Route::get('personas/{persona}', 'mostrar')->name('personas.mostrar');
});
