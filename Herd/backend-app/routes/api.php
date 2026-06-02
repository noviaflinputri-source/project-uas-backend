<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HelpRequestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InformationController; 
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;

// ==========================================
// PUBLIC ROUTES (Bisa Diakses Bebas Tanpa Auth Token)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Rute Kotak Permintaan Relawan
Route::get('/help-requests', [HelpRequestController::class, 'index']);
Route::post('/help-requests/{id}/accept', [HelpRequestController::class, 'accept']);

// Rute Chat
Route::get('/chats/{help_request_id}', [ChatController::class, 'getMessages']);
Route::post('/chats', [ChatController::class, 'sendMessage']);

// ---------------------------------------------------------------------------
// KOREKSI UTAMA: AKSES INFORMASI DENGAN FORCE INJECT CORS HEADERS
// ---------------------------------------------------------------------------
Route::get('/informations', [InformationController::class, 'index']);

Route::match(['post', 'options'], '/informations', function (Illuminate\Http\Request $request) {
    // Jika browser mengirim Preflight Request (OPTIONS), langsung izinkan tanpa memproses database
    if ($request->isMethod('options')) {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    try {
        // Panggil fungsi store dari InformationController secara manual
        $controller = new InformationController();
        $response = $controller->store($request);
        
        // Suntik paksa header CORS pada response sukses agar browser tidak memblokir
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        return $response;
    } catch (\Exception $e) {
        // Jika ada crash internal, tangkap pesan error aslinya dan kirim ke React
        return response()->json([
            'success' => false,
            'message' => 'Crash terdeteksi di Route API: ' . $e->getMessage()
        ], 500)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
});


// ==========================================
// PROTECTED ROUTES (Membutuhkan Autentikasi / Token Sanctum)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Fitur minta bantuan (Hanya store dan show)
    Route::apiResource('help-requests', HelpRequestController::class)
        ->only(['store', 'show']);

    // Berbagi cerita & komentar
    Route::apiResource('stories', StoryController::class)
        ->only(['store', 'index', 'show']);

    Route::get('/stories/{story_id}/comments', [CommentController::class, 'index']);
    Route::post('/stories/{story_id}/comments', [CommentController::class, 'store']);

    // Admin only routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
        Route::patch('/users/{id}/verify', [AdminController::class, 'verifyUser']);
        Route::get('/informations/pending', [AdminController::class, 'pendingInformations']);
        Route::patch('/informations/{id}/verify', [AdminController::class, 'verifyInformation']);
    });
});