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
        $sortBy = $request->query('sortBy', 'title');
        $sortDir = $request->query('sortDir', 'asc');

        $sortByFields = ['title', 'popular', 'uploaddate'];
        $sortDirFields = ['asc', 'desc'];

        if (!in_array($sortBy, $sortByFields)) {
            $sortBy = 'title';
        }
        if (!in_array($sortDir, $sortDirFields)) {
            $sortDir = 'asc';
        }

        $games = Game::query()
            ->whereHas('gameVersions')
            ->withCount(['scoreCount'])
            ->skip(ceil($page * $size))
            ->take($size);


        if ($sortBy === 'title') {
            $games->orderBy('title', $sortDir);
        }

        if ($sortBy === 'uploaddate') {
            $games
                ->select('games.*')
                ->join('game_versions', 'games.id', '=', 'game_versions.game_id')
                ->orderBy('game_versions.created_at', $sortDir);
        }

        $games = $games->distinct()->get();

        if ($sortBy === 'popular') {
            if ($sortDir === 'asc') {
                $games = $games->sortBy(function ($game) {
                    return $game->scoreCount->count();
                });
            }
            if ($sortDir === 'desc') {
                $games = $games->sortBy(function ($game) {
                    return !$game->scoreCount->count();
                });
            }
        }

        $content = [];
        foreach ($games as $game) {
            $gameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$game->auhtor->blocked && $gameVersion) {
                $content[] = [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description,
                    'thumbnail' => $game->optional_thumbnail,
                    'uploadTimestamp' => $gameVersion->created_at,
                    'author' => $game->author->username,
                    'scoreCount' => $game->scoreCount->count()
                ];
            }
        }

        $response = [
            'page' => $page,
            'size' => $size,
            'totalElements' => Game::count(),
            'content' => $content
        ];

        return response()->json($response, 200);
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
            $game = Game::query()->with(['author', 'deletedGame'])->where('slug', $slug)->firstOrFail();

            if ($game->deletedGame) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }

            if ($game->author->blocked) {
                return response()->json([
                    'status' => 'not-found',
                    'message' => 'Not found'
                ], 404);
            }

            $lastGameVersion = GameVersion::query()
                ->where('game_id', $game->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $scoreCount = $lastGameVersion ? GameScore::query()
                ->where('game_version_id', $lastGameVersion->id)
                ->count() : 0;

            return response()->json([
                'slug' => $game->slug,
                'title' => $game->title,
                'description' => $game->description,
                'thumbnail' => $game->optional_thumbnail,
                'uploadTimestamp' => $lastGameVersion ? $lastGameVersion->created_at : null,
                'author' => $game->author->username,
                'scoreCount' => $scoreCount,
                'gamePath' => $lastGameVersion ? $lastGameVersion->path_to_game_files : null
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
