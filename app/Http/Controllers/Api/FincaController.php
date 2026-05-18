<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Finca;

class FincaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
       $user = auth()->user();

    // Administrador
    if ($user->tipo_id == 1) {

        $fincas = Finca::with('usuario')->get();

    } else {

        $fincas = Finca::with('usuario')
            ->where('usuario_id', $user->id)
            ->get();
    }

    return response()->json($fincas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'usuario_id' => 'required|exists:users,id',
        'nombre' => 'required|string|max:255',
        'ubicacion' => 'required|string|max:255',
        'area' => 'required|numeric|min:0',
        'numero_finca' => 'required|string|unique:fincas,numero_finca'
    ]);

    $finca = Finca::create($validated);

    return response()->json([
        'message' => 'Finca registrada correctamente',
        'data' => $finca
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $finca = Finca::with('usuario', 'ganados')->findOrFail($id);

    return response()->json($finca);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $finca = Finca::findOrFail($id);

    $validated = $request->validate([
        'usuario_id' => 'required|exists:users,id',
        'nombre' => 'required|string|max:255',
        'ubicacion' => 'required|string|max:255',
        'area' => 'required|numeric|min:0',
        'numero_finca' => 'required|string|unique:fincas,numero_finca,' . $finca->id
    ]);

    $finca->update($validated);

    return response()->json([
        'message' => 'Finca actualizada correctamente',
        'data' => $finca
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $finca = Finca::findOrFail($id);

    if ($finca->ganados()->count() > 0) {
        return response()->json([
            'message' => 'No se puede eliminar la finca porque tiene ganado asociado'
        ], 400);
    }

    $finca->delete();

    return response()->json([
        'message' => 'Finca eliminada correctamente'
    ]);
    }
}
