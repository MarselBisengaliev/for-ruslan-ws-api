<?php

namespace App\Http\Controllers;

use App\Models\GameVersion;
use App\Http\Requests\StoreGameVersionRequest;
use App\Http\Requests\UpdateGameVersionRequest;
use App\Models\Game;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Str;

class GameVersionController extends Controller
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
    public function store(StoreGameVersionRequest $request, string $slug)
    {
        $zipfile = $request->file('zipfile');
        $zip = new ZipArchive;
        $zipName = Str::replace('.zip', '', $zipfile->getClientOriginalName());

        try {
            $game = Game::query()->with(['deletedGame'])->where('slug', $slug)->firstOrFail();
            if ($game->deletedGame) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }
            if (+$game->user_id !== +$request->user()->id) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => 'You are not the game author'
                ], 403);
            }

            $extractTo = Storage::path("games/$game->slug");
            $newGameVersion = GameVersion::query()->where('game_id', $game->id)->count() + 1;

            if ($zip->open($zipfile) === TRUE) {
                $zip->extractTo($extractTo);
                $zip->close();
            }

            Storage::move("games/$game->slug/$zipName", "games/$game->slug/$newGameVersion");
            GameVersion::create([
                'game_id' => $game->id,
                'path_to_game_files' => "/games/$game->slug/$newGameVersion/"
            ]);

            if (Storage::exists("games/$game->slug/$newGameVersion/thumbnail.png")) {
                $game->optional_thumbnail = "/games/$game->slug/$newGameVersion/thumbnail.png";
                $game->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Succefully uploaded'
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
    public function show(string $slug, int $version)
    {
        $game = Game::query()->with(['deletedGame'])->where('slug', $slug)->firstOrFail();
        if ($game->deletedGame) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        $path = "games/$game->slug/$version/index.html";

        if (!Storage::exists($path)) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }

        return response()->json("/storage/$path", 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameVersion $gameVersion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameVersionRequest $request, GameVersion $gameVersion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameVersion $gameVersion)
    {
        //
    }
}
