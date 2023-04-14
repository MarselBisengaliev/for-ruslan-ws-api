<?php

namespace App\Http\Controllers;

use App\Models\DeletedGame;
use App\Http\Requests\StoreDeletedGameRequest;
use App\Http\Requests\UpdateDeletedGameRequest;
use App\Models\Game;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeletedGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();

            DeletedGame::create([
                'game_id' => $game->id,
            ]);

            return view('pages.game', ['game' => $game]);
        } catch (ModelNotFoundException $e) {
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DeletedGame $deletedGame)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeletedGame $deletedGame)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeletedGameRequest $request, DeletedGame $deletedGame)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();

            $deletedGame = DeletedGame::query()->where('game_id', $game->id)->firstOrFail();
            $deletedGame->delete();

            return redirect()->route('game', ['slug' => $game->slug]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }
}
