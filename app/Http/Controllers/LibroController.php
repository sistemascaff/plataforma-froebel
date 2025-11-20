<?php

namespace App\Http\Controllers;

use App\Http\Requests\LibroValidation;
use App\Models\Libro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return redirect()->route('login');
        }

        return view('libros.index', [
            'head_title' => 'GESTIÓN DE LIBROS',
        ]);
    }

    public function view_public()
    {
        return view('libros.public', [
            'head_title' => 'BIBLIOTECA',
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $libros = (new Libro())->get_all_libros();
        return response()->json([
            'data' => $libros
        ]);
    }

    public function listar_public()
    {
        $libros = (new Libro())->get_all_libros_public();
        return response()->json([
            'data' => $libros
        ]);
    }


    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $libro = (new Libro())->get_libro($request->libro);
        return response()->json([
            'data' => $libro
        ]);
    }

    public function create(LibroValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $libro = new Libro();
        $libro->id_colegio = session('id_colegio');
        $libro->titulo = strtoupper($request->titulo);
        $libro->codigo = $request->codigo;
        $libro->autor = strtoupper($request->autor);
        $libro->categoria = strtoupper($request->categoria);
        $libro->editorial = strtoupper($request->editorial);
        $libro->presentacion = strtoupper($request->presentacion);
        $libro->anio = $request->anio;
        $libro->costo = $request->costo;
        $libro->observacion = $request->observacion;
        $libro->descripcion = $request->descripcion;
        $libro->adquisicion = $request->adquisicion;
        $libro->fecha_ingreso_cooperativa = $request->fecha_ingreso_cooperativa;
        $libro->creado_por = session('id_usuario');
        $libro->save();

        return response()->json([
            'success' => true,
            'message' => 'Libro registrado correctamente',
            'libro' => $libro
        ]);
    }

    public function update(LibroValidation $request, $id_libro)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $libro = (new Libro())->get_libro($id_libro);
        $libro->titulo = strtoupper($request->titulo);
        $libro->codigo = $request->codigo;
        $libro->autor = strtoupper($request->autor);
        $libro->categoria = strtoupper($request->categoria);
        $libro->editorial = strtoupper($request->editorial);
        $libro->presentacion = strtoupper($request->presentacion);
        $libro->anio = $request->anio;
        $libro->costo = $request->costo;
        $libro->observacion = $request->observacion;
        $libro->descripcion = $request->descripcion;
        $libro->adquisicion = $request->adquisicion;
        $libro->fecha_ingreso_cooperativa = $request->fecha_ingreso_cooperativa;
        $libro->modificado_por = session('id_usuario');
        $libro->save();

        return response()->json([
            'success' => true,
            'message' => 'Libro actualizado correctamente',
            'libro' => $libro
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_libro' => ['required', 'numeric', 'integer']
        ]);

        $libro = (new Libro())->get_libro($request->id_libro);
        if ($libro->estado != '2') {
            $libro->estado = $libro->estado == '1' ? '0' : '1';
            $libro->fecha_eliminacion = $libro->estado == '0' ? Carbon::now() : null;
            $libro->eliminado_por = $libro->estado == '0' ? session('id_usuario') : null;
            $libro->save();
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Actualmente el libro está en uso, no se puede eliminar o restaurar',
                'libro' => $libro
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $libro->estado == '1' ? 'El libro fue restaurado con éxito y ahora está disponible para su préstamo.' : 'El libro fue eliminado con éxito y ya no está disponible para su préstamo.',
            'libro' => $libro
        ]);
    }
}
