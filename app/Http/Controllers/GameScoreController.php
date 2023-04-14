<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Http\Requests\StoreGameScoreRequest;
use App\Http\Requests\UpdateGameScoreRequest;
use App\Models\Game;
use App\Models\GameVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GameScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)
                ->with('deletedGame')
                ->firstOrFail();

            if ($game->deletedGame) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }

            $lastGameVersion = GameVersion::query()
                ->orderBy('created_at', 'desc')
                ->where('game_id', $game->id)
                ->firstOrFail();

            $scores = GameScore::query()
                ->where('game_version_id', $lastGameVersion->id)
                ->orderBy('score', 'desc')
                ->get();

            $parsedScores = [];
            foreach ($scores as $score) {
                if (!$score->user->blocked) {
                    $parsedScores[] = [
                        'username' => $score->user->username,
                        'score' => $score->score,
                        'timestamp' => $score->created_at,
                    ];
                }
            }

            return response()->json([
                'scores' => $parsedScores
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameScoreRequest $request, string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)
                ->with('deletedGame')
                ->firstOrFail();

            if ($game->deletedGame) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }

            $lastGameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();

            GameScore::create([
                'user_id' => $request->user()->id,
                'game_version_id' => $lastGameVersion->id,
                'score' => $request->get('score')
            ]);

            return response()->json([
                'status' => 'success',
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GameScore $gameScore)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameScore $gameScore)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameScoreRequest $request, GameScore $gameScore)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameScore $gameScore)
    {
        //
    }
}
