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






<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;

class ResultController extends Controller
{
    // show current dynamic bracket (just generate on demand; no DB storage)
    public function show($tournament_id)
    {
        $tournament = Tournament::with('teams')->findOrFail($tournament_id);
        // We'll not store results in DB here; we generate on request (random)
        // If you want persistent results, we can add a results table later.
        return view('results.show', compact('tournament'));
    }

    // Generate random bracket -> redirect to page that shows generated bracket (we pass via session or re-compute in view)
    public function generate(Request $request, $tournament_id)
    {
        try {
            $tournament = Tournament::with('teams')->findOrFail($tournament_id);
            $teams = $tournament->teams->pluck('team_name')->toArray();

            if (count($teams) != $tournament->team_size) {
                return back()->with('error', 'Please add all teams before generating results.');
            }

            // Shuffle teams for random pairing
            shuffle($teams);

            $allRounds = [];
            $current = $teams;

            while (count($current) > 1) {
                $matches = [];
                $next = [];

                for ($i = 0; $i < count($current); $i += 2) {
                    $t1 = $current[$i];
                    $t2 = $current[$i + 1] ?? null;

                    if ($t2 === null) {
                        $winner = $t1;
                    } else {
                        $winner = rand(0, 1) ? $t1 : $t2;
                    }

                    $matches[] = ['team1' => $t1, 'team2' => $t2, 'winner' => $winner];
                    $next[] = $winner;
                }

                $allRounds[] = $matches;
                $current = $next;
            }

            $finalWinner = $current[0] ?? null;

            // ✅ Directly pass data to view
            return view('results.show', [
                'tournament' => $tournament,
                'bracket' => $allRounds,
                'finalWinner' => $finalWinner
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Team;

class TeamController extends Controller
{
    public function index($tournament_id)
    {
        $tournament = Tournament::with('teams')->findOrFail($tournament_id);
        $teams = $tournament->teams;
        $disabled = $teams->count() >= $tournament->team_size;
        return view('teams.index', compact('tournament','teams','disabled'));
    }

    public function store(Request $request, $tournament_id)
    {
        try {
            $request->validate(['team_name'=>'required|string|max:255']);
            $tournament = Tournament::findOrFail($tournament_id);

            $current = Team::where('tournament_id', $tournament_id)->count();
            if ($current >= $tournament->team_size) {
                return back()->with('error','Team limit reached.');
            }

            Team::create([
                'tournament_id' => $tournament_id,
                'team_name' => $request->team_name
            ]);
            return back()->with('success','Team added.');
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit($tournament_id, $team_id)
    {
        $tournament = Tournament::findOrFail($tournament_id);
        $team = Team::where('tournament_id',$tournament_id)->findOrFail($team_id);
        return view('teams.edit', compact('tournament','team'));
    }

    public function update(Request $request, $tournament_id, $team_id)
    {
        try {
            $request->validate(['team_name'=>'required|string|max:255']);
            $team = Team::where('tournament_id',$tournament_id)->findOrFail($team_id);
            $team->team_name = $request->team_name;
            $team->save();
            return redirect()->route('teams.index',$tournament_id)->with('success','Team updated.');
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Team::findOrFail($id)->delete();
            return back()->with('success','Team deleted.');
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }
}






<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::withCount('teams')->get();
        return view('tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('tournaments.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'team_size' => 'required|integer|min:2',
            ]);

            // require power of two (optional but recommended)
            $size = (int) $request->team_size;
            if (($size & ($size - 1)) !== 0) {
                return back()->withInput()->with('error','Team Size must be power of two (eg. 4,8,16)');
            }

            Tournament::create($request->only(['name','team_size']));
            return redirect()->route('tournaments.index')->with('success','Tournament created.');
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }

    public function edit($id)
    {
        $tournament = Tournament::findOrFail($id);
        return view('tournaments.edit', compact('tournament'));
    }

    public function update(Request $request)
    {
        // dd($request);
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'team_size' => 'required|integer|min:2',
            ]);
            $tournament = Tournament::findOrFail($request->id);
            // dd($tournament);
            $size = (int) $request->team_size;
            if (($size & ($size - 1)) !== 0) {
                return back()->withInput()->with('error','team_size must be power of two (eg. 4,8,16)');
            }

            $tournament->update($request->only(['name','team_size']));
            return redirect()->route('tournaments.index')->with('success','Tournament updated.');
        } catch (\Exception $e) {
            return back()->with('error',$e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Tournament::findOrFail($id)->delete();
            return redirect()->route('tournaments.index')->with('success','Tournament deleted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}








<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['tournament_id','team_name'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}







<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;
    protected $fillable = ['name','team_size'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}





<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {


         Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('team_size');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};



<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {



        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained('tournaments')->onDelete('cascade');
            $table->string('team_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};




<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tournament App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* simple small style for bracket columns */
    .round-column { min-width:220px; }
    .match-box { border:1px solid #ddd; padding:8px; margin-bottom:8px; background:#fff; }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
  <h2 class="mb-3">Tournament App</h2>

  @if(session('success'))
      <div class="alert alert-success alert-message">{{ session('success') }}</div>
  @endif
  @if(session('error'))
      <div class="alert alert-danger alert-message">{{ session('error') }}</div>
  @endif

  @yield('content')
</div>

<script>
  $(document).ready(function(){
      // Hide success/error alert after 3 seconds smoothly
      setTimeout(function(){
          $(".alert-message").fadeOut('slow');
      }, 3000);
  });
</script>
</body>
</html>



@extends('layouts.app')
@section('content')
<a href="{{ route('tournaments.index') }}" class="btn btn-light mb-3">Back</a>
<h4>{{ $tournament->name }} — Results</h4>

<div class="mb-2">
    <form method="POST" action="{{ route('results.generate', $tournament->id) }}" style="display:inline;">
        @csrf
        <button class="btn btn-primary">Generate Random Results</button>
    </form>
</div>

@if(empty($bracket))
    <div class="alert alert-info">No results generated yet. Click "Generate Random Results".</div>
@endif

@if(!empty($bracket))
    <div class="row">
        {{-- For each round -> a column --}}
        @foreach($bracket as $rIndex => $matches)
            <div class="col round-column">
                <h6 class="text-center">Round {{ $rIndex + 1 }}</h6>
                @foreach($matches as $m)
                    <div class="match-box">
                        <div>{{ $m['team1'] }}</div>
                        <div class="text-muted">vs</div>
                        <div>{{ $m['team2'] }}</div>
                        <hr class="my-1">
                        <div><strong>Winner: {{ $m['winner'] }}</strong></div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Final Winner column --}}
        <div class="col round-column">
            <h6 class="text-center">Champion</h6>
            @if($finalWinner)
                <div class="match-box text-center">
                    <h5>{{ $finalWinner }}</h5>
                </div>
            @else
                <div class="match-box text-center">—</div>
            @endif
        </div>
    </div>
@endif
@endsection





@extends('layouts.app')
@section('content')
<h4>Edit Team for {{ $tournament->name }}</h4>
<form method="POST" action="{{ route('teams.update', [$tournament->id, $team->id]) }}">
    @csrf
    <div class="mb-3">
        <label>Team Name</label>
        <input name="team_name" class="form-control" value="{{ old('team_name', $team->team_name) }}">
    </div>
    <button class="btn btn-primary">Update</button>
    <a href="{{ route('teams.index', $tournament->id) }}" class="btn btn-secondary">Back</a>
</form>
@endsection





@extends('layouts.app')
@section('content')
<a href="{{ route('tournaments.index') }}" class="btn btn-light mb-3">Back</a>
<h4>{{ $tournament->name }} — Teams ({{ $teams->count() }}/{{ $tournament->team_size }})</h4>

@if(!$disabled)
<form method="POST" action="{{ route('teams.store', $tournament->id) }}" class="mb-3">
    @csrf
    <div class="input-group">
        <input name="team_name" class="form-control" placeholder="Team name" required>
        <button class="btn btn-success">Add</button>
    </div>
</form>
@else
<div class="alert alert-info">Team limit reached. Cannot add more.</div>
@endif

<table class="table table-bordered">
    <thead><tr><th>#</th><th>Team Name</th><th>Action</th></tr></thead>
    <tbody>
    @foreach($teams as $team)
    <tr>
        <td>{{ $team->id }}</td>
        <td>{{ $team->team_name }}</td>
        <td>
            <a href="{{ route('teams.edit', [$tournament->id, $team->id]) }}" class="btn btn-sm btn-warning">Edit</a>
            <form style="display:inline" method="POST" action="{{ route('teams.destroy', $team->id) }}">
                @csrf
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete team?')">Delete</button>
            </form>
        </td>
    </tr>
    @endforeach
    </tbody>
</table>
@endsection





@extends('layouts.app')
@section('content')
<h4>Create Tournament</h4>


<form method="POST" action="{{ route('tournaments.store') }}">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="{{ old('name') }}">
    </div>
    <div class="mb-3">
        <label>Team Size</label>
        <input name="team_size" type="number" class="form-control" value="{{ old('team_size') }}">
        <div class="form-text">Use power of two (4,8,16) for knockout.</div>
    </div>
    <button class="btn btn-success">Save</button>
        <a href="{{ route('tournaments.index') }}" class="btn btn-light">Back</a>

</form>
@endsection




@extends('layouts.app')
@section('content')
<h4>Edit Tournament</h4>
<form method="POST" action="{{ route('tournaments.update') }}">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="{{ old('name', $tournament->name) }}">
    </div>
    <div class="mb-3">
        <label>Team Size</label>
        <input name="team_size" type="number" class="form-control" value="{{ old('team_size', $tournament->team_size) }}">
    </div>
    <input type="hidden" name="id" class="form-control" value="{{ $tournament->id }}">

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('tournaments.index') }}" class="btn btn-secondary">Back</a>
</form>
@endsection




@extends('layouts.app')
@section('content')
<a class="btn btn-primary mb-3" href="{{ route('tournaments.create') }}">Create Tournament</a>

<table class="table table-bordered">
    <thead><tr><th>#</th><th>Name</th><th>Team Size</th><th>Teams Added</th><th>Action</th></tr></thead>
    <tbody>
    @foreach($tournaments as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->name }}</td>
            <td>{{ $t->team_size }}</td>
            <td>{{ $t->teams_count }}</td>
            <td>
                <a class="btn btn-sm btn-info" href="{{ route('teams.index',$t->id) }}">Teams</a>
                <a class="btn btn-sm btn-success" href="{{ route('results.show',$t->id) }}">Result</a>
                <a class="btn btn-sm btn-warning" href="{{ route('tournaments.edit',$t->id) }}">Edit</a>

                <form style="display:inline" method="POST" action="{{ route('tournaments.destroy',$t->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete tournament?')">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection







