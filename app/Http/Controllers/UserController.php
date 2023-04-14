<?php

namespace App\Http\Controllers;

use App\Models\BlockedUser;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(string $username)
    {
        try {
            $existingUser = User::query()
                ->with(['uploadedGames', 'gameScores', 'blocked'])
                ->where('username', $username)
                ->firstOrFail();

            if ($existingUser->blocked) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }

            $highscores = [];

            foreach ($existingUser->gameScores as $score) {
                $game = Game::query()->where('id', $score->gameVersion->game_id)->first();
                $highscores[] = [
                    'game' => $game,
                    'score' => $score->score,
                    'timestamp' => $score->created_at
                ];
            }

            return response()->json([
                'username' => $existingUser->username,
                'registeredTimestamp' => $existingUser->registered_timestamp,
                'authoredGames' => $existingUser->uploadedGames,
                'highscores' => $highscores
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }
}
