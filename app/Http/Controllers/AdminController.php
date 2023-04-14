<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Game;
use App\Models\GameScore;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.admin');
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
    public function store(StoreAdminRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }

    public function signin(Request $request)
    {
        try {
            $username = $request->input('username');
            $password = $request->input('password');

            $existingAdmin = Admin::query()
                ->where('username', $username)
                ->where('password', $password)
                ->firstOrFail();

            Auth::login($existingAdmin, true);

            return redirect()->route('home');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }
    }

    public function signout(Request $request)
    {
        try {
            Auth::logout();

            return redirect()->route('login');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Wrong username or password'
            ], 401);
        }
    }

    public function home()
    {
        $admins = Admin::all();
        $users = User::all();
        $games = Game::all();

        return view('pages.home', ['admins' => $admins, 'users' => $users, 'games' => $games]);
    }

    public function manageUser(string $username)
    {
        try {
            $user = User::query()->where('username', $username)->with(['blocked'])->firstOrFail();

            return view('pages.user', ['user' => $user]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    public function manageGame(string $slug) {
        try {
            $game = Game::query()->where('slug', $slug)->with(['deletedGame'])->firstOrFail();

            return view('pages.game', ['game' => $game]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    public function deleteScore(int $scoreId) {
        try {
            $score = GameScore::query()->with(['gameVersion'])->where('id', $scoreId)->firstOrFail();
            $game = Game::query()->where('id', $score->gameVersion->game_id)->firstOrFail();
            $score->delete();

            return redirect()->route('game', ['slug' => $game->slug]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    public function deleteAllUserScores(string $slug, int $userId) {
        try {
            $user = User::query()->with(['gameScores'])->where('id', $userId)->firstOrFail();
            foreach ($user->gameScores as $score) {
                $score->delete();
            }

            return redirect()->route('game', ['slug' => $slug]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }

    public function resetAllHighScores(string $slug) {
        try {
            $game = Game::query()->where('slug', $slug)->with(['scoreCount'])->firstOrFail();
            foreach ($game->scoreCount as $score) {
                $score->delete();
            }

            return redirect()->route('game', ['slug' => $slug]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }
}
