<?php

use App\Http\Controllers\LibroController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(LibroController::class)->group(function () {
    Route::get('libros', 'listar_public')->name('libros.public.listar');
});

// Route::get('/ejemplo', function () {
//     return response()->json(['mensaje' => 'Este es un ejemplo de ruta API']);
// });