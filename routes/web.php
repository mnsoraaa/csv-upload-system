<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FileController::class, 'index'])->name('files.index');
Route::post('/files', [FileController::class, 'store'])->name('files.store');
Route::get('/files/list', [FileController::class, 'list'])->name('files.list');
Route::get('/files/{file}/status', [FileController::class, 'status'])->name('files.status');
Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
