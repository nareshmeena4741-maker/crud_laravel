<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ResultController;

Route::get('/', [TournamentController::class,'index'])->name('tournaments.index');

// Tournament CRUD
Route::get('/tournaments', [TournamentController::class,'index'])->name('tournaments.index');
Route::get('/create', [TournamentController::class,'create'])->name('tournaments.create');
Route::post('/store', [TournamentController::class,'store'])->name('tournaments.store');
Route::get('/edit/{id}', [TournamentController::class,'edit'])->name('tournaments.edit');
Route::post('update', [TournamentController::class,'update'])->name('tournaments.update');
Route::post('delete', [TournamentController::class,'destroy'])->name('tournaments.destroy');

// Teams (nested)
Route::get('/tournaments/{id}/teams', [TeamController::class,'index'])->name('teams.index');
Route::post('/tournaments/{id}/teams/store', [TeamController::class,'store'])->name('teams.store');
Route::get('/tournaments/{tournament_id}/teams/{team_id}/edit', [TeamController::class,'edit'])->name('teams.edit');
Route::post('/tournaments/{tournament_id}/teams/{team_id}/update', [TeamController::class,'update'])->name('teams.update');
Route::post('/teams/{id}/delete', [TeamController::class,'destroy'])->name('teams.destroy');

// Results
Route::get('/tournaments/{id}/results', [ResultController::class,'show'])->name('results.show');
Route::post('/tournaments/{id}/results/generate', [ResultController::class,'generate'])->name('results.generate');
Route::post('/tournaments/{id}/results/clear', [ResultController::class,'clear'])->name('results.clear');
