<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

// Route login (tidak perlu middleware auth)
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ]);
});

// Route logout (harus login dulu)
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out successfully']);
});

// Route mendapatkan data user yang sedang login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Di sini Anda bisa menambahkan route lain yang dilindungi sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Contoh: Route untuk update profil
    // Route::put('/user/profile', [UserController::class, 'updateProfile']);

    // Route untuk mendapatkan daftar sesuatu
    // Route::apiResource('/posts', PostController::class);
}); 