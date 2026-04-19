<?php

use App\Http\Controllers\MangaController;
use Illuminate\Support\Facades\Route;

// API Routes
Route::prefix('manga')->group(function () {
    
    // GET /api/manga
    Route::get('/', [MangaController::class, 'index']);                 
    
    // GET /api/manga/{id}
    Route::get('/{id}', [MangaController::class, 'show'])               
        ->whereNumber('id');
    
    // POST /api/manga
    Route::post('/', [MangaController::class, 'store']);                
    
    // PUT /api/manga/{id}
    Route::put('/{id}', [MangaController::class, 'update'])             
        ->whereNumber('id');
    
    // PATCH /api/manga/{id}
    Route::patch('/{id}', [MangaController::class, 'partialUpdate'])    
        ->whereNumber('id');
    
    // DELETE /api/manga/{id}
    Route::delete('/{id}', [MangaController::class, 'destroy'])         
        ->whereNumber('id');
});

// Error handling
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint tidak ditemukan',
    ], 404);
});
