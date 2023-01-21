<?php

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/info', [ApiController::class, 'info']);
Route::get('/info-dashboard', [DashboardController::class, 'index']);
Route::get('/info-daftar-permohonan-surat', [DashboardController::class, 'daftarPermohonanSurat']);
Route::get('/info-log-surat', [DashboardController::class, 'logSurat']);
Route::get('/info-data-kepala-desa', [DashboardController::class, 'infoKepalaDesa']);
Route::get('/analisis/sdgs/{village_code}', [DashboardController::class, 'apiScoreSdgs'])->name('survey.analisis.content.sdgs');
Route::get('/analisis/detail-score-sdgs/{village_code}/{goals}', [DashboardController::class, 'detailSdgs'])->name('survey.analisis.content.detail.score.sdgs');
