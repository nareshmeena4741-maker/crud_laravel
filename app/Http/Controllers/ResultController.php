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
