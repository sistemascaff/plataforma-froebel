<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbastecimientoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\PedidoEmpresaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SaldoEmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;

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

/* Tabla con PK FK 'empleados'*/
Route::controller(UsuarioController::class)->group(function () {
    /* Rutas para gestionar la sesión del usuario y el panel de administración */
    Route::get('/', 'view_main_index')->name('main.index');
    Route::get('panel', 'view_dashboard')->name('dashboard');
    Route::get('iniciar-sesion', 'view_iniciar_sesion')->name('login');
    Route::get('cerrar-sesion', 'cerrar_sesion')->name('logout');
    Route::post('verificar', 'verificar')->name('login.verificar');
});

/* Tabla con PK FK 'empresas','marcas','abastecimientos' y relación muchos a muchos con 'ventas' mediante 'detalles_ventas'*/
Route::controller(LibroController::class)->group(function () {
    Route::get('biblioteca', 'view_public')->name('libros.public.index');
    //nota: hay una api para listar los libros publicamente, accesible en api.php
    Route::get('libros', 'view_index')->name('libros.index');
    Route::get('libros/listar', 'listar')->name('libros.listar');
    Route::get('libros/{libro}', 'mostrar')->name('libros.mostrar');
    Route::post('libros', 'create')->name('libros.create');
    Route::put('libros/{libro}', 'update')->name('libros.update');
    Route::patch('libros/{libro}', 'delete')->name('libros.delete');
});