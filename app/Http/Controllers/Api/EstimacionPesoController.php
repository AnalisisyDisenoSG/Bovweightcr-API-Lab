<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistroPeso;
use App\Models\Ganado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class EstimacionPesoController extends Controller
{
    private string $mlServiceUrl;

    public function __construct()
    {
        $this->mlServiceUrl = env('ML_SERVICE_URL', 'http://127.0.0.1:5000');
    }

    public function estimar(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'ganado_id' => 'required|exists:ganados,id',
            'reference_length_cm' => 'nullable|numeric|min:1|max:500',
            'breed' => 'nullable|string|in:brahman,cebu,criollo,default',
        ]);

        $path = $request->file('image')->store('estimaciones', 'public');

        try {
            $response = Http::timeout(60)->attach(
                'image',
                file_get_contents($request->file('image')->path()),
                $request->file('image')->getClientOriginalName()
            )->post("{$this->mlServiceUrl}/api/estimate", [
                'reference_length_cm' => $request->input('reference_length_cm', 100),
                'breed' => $request->input('breed', 'default'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo conectar con el servicio de estimacion',
                'detalle' => $e->getMessage(),
            ], 503);
        }

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Error en la estimacion',
                'detalle' => $response->json(),
            ], $response->status());
        }

        $data = $response->json();

        $registro = RegistroPeso::create([
            'ganado_id' => $request->ganado_id,
            'peso_estimado' => $data['peso_estimado_kg'],
            'fecha' => now(),
            'confianza' => $data['confianza'],
            'metodo' => $data['metodo'],
            'imagen_path' => $path,
            'medidas' => $data['medidas'],
            'raza_estimacion' => $request->input('breed', 'default'),
        ]);

        return response()->json([
            'registro' => $registro,
            'estimacion' => [
                'peso_estimado_kg' => $data['peso_estimado_kg'],
                'rango_min_kg' => $data['rango_min_kg'],
                'rango_max_kg' => $data['rango_max_kg'],
                'confianza' => $data['confianza'],
                'metodo' => $data['metodo'],
                'medidas' => $data['medidas'],
                'referencia_detectada' => $data['referencia_detectada'],
            ],
            'advertencia' => $data['advertencia'],
        ], 201);
    }

    public function estimarBatch(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:2|max:5',
            'images.*' => 'image|max:10240',
            'ganado_id' => 'required|exists:ganados,id',
            'reference_length_cm' => 'nullable|numeric',
            'breed' => 'nullable|string|in:brahman,cebu,criollo,default',
        ]);

        $httpRequest = Http::timeout(120);

        foreach ($request->file('images') as $image) {
            $httpRequest = $httpRequest->attach(
                'images',
                file_get_contents($image->path()),
                $image->getClientOriginalName()
            );
        }

        try {
            $response = $httpRequest->post("{$this->mlServiceUrl}/api/estimate/batch", [
                'reference_length_cm' => $request->input('reference_length_cm', 100),
                'breed' => $request->input('breed', 'default'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo conectar con el servicio de estimacion',
            ], 503);
        }

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Error en la estimacion',
                'detalle' => $response->json(),
            ], $response->status());
        }

        $data = $response->json();

        $path = $request->file('images')[0]->store('estimaciones', 'public');

        $registro = RegistroPeso::create([
            'ganado_id' => $request->ganado_id,
            'peso_estimado' => $data['peso_estimado_kg'],
            'fecha' => now(),
            'confianza' => 0.75,
            'metodo' => 'batch_average',
            'imagen_path' => $path,
            'medidas' => ['pesos_individuales' => $data['pesos_individuales']],
            'raza_estimacion' => $request->input('breed', 'default'),
        ]);

        return response()->json([
            'registro' => $registro,
            'estimacion' => $data,
        ], 201);
    }

    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)->get("{$this->mlServiceUrl}/api/health");
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Microservicio ML no disponible',
            ], 503);
        }
    }
}