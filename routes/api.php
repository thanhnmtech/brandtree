<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LadipageController;

Route::post('/ladipage', [LadipageController::class, 'store'])->name('api.ladipage.store');

?>
