<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;

// Route::get('/', [TournamentController::class,'index'])->name('tournaments.index');


// Home redirect
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) return redirect()->route('admin.dashboard');
        return redirect()->route('staff.dashboard');
    }
    return redirect()->route('login.form');
});

// Auth
Route::get('/login', [AuthController::class,'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class,'login'])->name('login');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

// Register (public - creates staff)
Route::get('/register', [AuthController::class,'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class,'register'])->name('register');

// Admin group
Route::group(['prefix'=>'admin','middleware'=>['auth','role:admin']], function () {
    Route::get('dashboard', [AdminController::class,'dashboard'])->name('admin.dashboard');
    Route::get('user/create', [AdminController::class,'createUserForm'])->name('admin.user.create');
    Route::post('user/store', [AdminController::class,'storeUser'])->name('admin.user.store');
    Route::post('user/{id}/toggle', [AdminController::class,'toggleActive'])->name('admin.user.toggle');
});

// Staff group
Route::group(['prefix'=>'staff','middleware'=>['auth','role:staff']], function () {
    Route::get('dashboard', [StaffController::class,'dashboard'])->name('staff.dashboard');
});






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
