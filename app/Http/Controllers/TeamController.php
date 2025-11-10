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
