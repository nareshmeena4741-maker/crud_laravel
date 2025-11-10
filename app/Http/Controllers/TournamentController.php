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
