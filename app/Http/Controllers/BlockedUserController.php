<?php

namespace App\Http\Controllers;

use App\Models\BlockedUser;
use App\Http\Requests\StoreBlockedUserRequest;
use App\Http\Requests\UpdateBlockedUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlockedUserController extends Controller
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
    public function store(StoreBlockedUserRequest $request, string $username)
    {
        try {
            $user = User::query()->where('username', $username)->firstOrFail();

            BlockedUser::create([
                'user_id' => $user->id,
                'reason' => $request->get('reason')
            ]);

            return redirect()->route('user', ['username' => $username]);
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
    public function show(BlockedUser $blockedUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockedUser $blockedUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlockedUserRequest $request, BlockedUser $blockedUser)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username)
    {
        try {
            $user = User::query()->where('username', $username)->firstOrFail();

            $blockedUser = BlockedUser::query()->where('user_id', $user->id)->firstOrFail();
            $blockedUser->delete();

            return redirect()->route('user', ['username' => $username]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'not-found',
                'message' => 'Not found'
            ], 404);
        }
    }
}
