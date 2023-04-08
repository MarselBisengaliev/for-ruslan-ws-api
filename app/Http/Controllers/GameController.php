<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\GameScore;
use App\Models\GameVersion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 0);
        $size = $request->query('size', 10);
        $sortBy = $request->query('sort', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $sortByFields = ['title', 'popular', 'uploaddate'];
        $sortDirFields = ['asc', 'desc'];

        if (!in_array($sortBy, $sortByFields)) {
            $sortBy = 'title';
        }
        if (!in_array($sortDir, $sortDirFields)) {
            $sortDir = 'asc';
        }

        $games = Game::query()->whereHas('gameVersions')->skip(ceil($page * $size))->take($size);

        if ($sortBy === 'title') {
            $games->orderBy('title', $sortDir);
        }

        if ($sortBy === 'popular') {
            $games
                ->join('game_versions', 'games.id', '=', 'game_versions.game_id')
                ->join('game_scores', 'game_versions.id', '=', 'game_scores.game_version_id')
                ->orderBy('game_scores.count', $sortDir);
        }

        if ($sortBy === 'uploaddate') {
            $games
                ->join('game_versions', 'games.id', '=', 'game_versions.game_id')
                ->orderBy('game_versions.created_at', $sortDir);
        }

        $games = $games->get();
        $content = [];
        foreach ($games as $game) {
            $gameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($gameVersion) {
                $scoreCount = GameScore::query()->where('game_version_id', $gameVersion->id)->count();

                $content[] = [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description,
                    'thumbnail' => $game->optional_thumbnail,
                    'uploadTimestamp' => $gameVersion->created_at,
                    'author' => $game->author->username,
                    'scoreCount' => $scoreCount
                ];
            }
        }

        $response = [
            'page' => $page,
            'size' => $size,
            'totalElements' => Game::count(),
            'content' => $content
        ];

        return response()->json($response, 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        $title = $request->get('title');
        $description = $request->get('description');
        $slug = Str::slug($title);

        $existingsGame = Game::query()->where('slug', $slug)->first();
        if ($existingsGame) {
            return response()->json([
                'status' => 'invalid',
                'slug' => 'Game title already exists'
            ], 400);
        }

        $newGame = Game::create([
            'title' => $title,
            'description' => $description,
            'slug' => $slug,
            'optional_thumbnail' => null,
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'slug' => $newGame->slug
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $game = Game::query()->with(['author'])->where('slug', $slug)->firstOrFail();
            $lastGameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->firstOrFail();
            $scoreCount = GameScore::query()->where('game_version_id', $lastGameVersion->id)->count();

            return response()->json([
                'slug' => $game->slug,
                'title' => $game->title,
                'description' => $game->description,
                'thumbnail' => $game->optional_thumbnail,
                'uploadTimestamp' => $lastGameVersion->created_at,
                'author' => $game->author->username,
                'scoreCount' => $scoreCount,
                'gamePath' => $lastGameVersion->path_to_game_files
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, string $slug)
    {
        try {
            $game = Game::query()->where('slug', $slug)->firstOrFail();
            if (+$game->user_id !== +$request->user()->id) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => "You are not the game author"
                ], 403);
            }

            $game->title = $request->get('title') ?? '';
            $game->description = $request->get('description') ?? '';
            $game->save();

            return response()->json([
                'status' => 'success'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $slug)
    {
        try {
            $game = Game::query()->with(['gameVersions'])->where('slug', $slug)->firstOrFail();

            if (+$game->user_id !== +$request->user()->id) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => "You are not the game author"
                ], 403);
            }

            foreach ($game->gameVersions as $version) {
                GameScore::query()->where('game_version_id', $version->id)->delete();
                $version->delete();
            }

            $game->delete();

            return response('', 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }
}
