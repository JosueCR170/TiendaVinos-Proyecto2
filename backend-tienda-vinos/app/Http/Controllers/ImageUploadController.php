<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('productos', $filename, 'public');

            $url = '/storage/' . $path;

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida correctamente.',
                'data' => [
                    'imagen_url' => $url,
                    'path' => $path
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'path' => 'required|string'
            ]);

            if (Storage::disk('public')->exists($request->input('path'))) {
                Storage::disk('public')->delete($request->input('path'));
            }

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
